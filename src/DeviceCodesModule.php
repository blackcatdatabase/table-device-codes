<?php
declare(strict_types=1);

namespace BlackCat\Database\Packages\DeviceCodes;

use BlackCat\Database\SqlDialect;
use BlackCat\Database\Contracts\ModuleInterface;
use BlackCat\Database\Support\SqlIdentifier;
use BlackCat\Database\Support\SqlDirectoryRunner;
use BlackCat\Database\Support\SchemaIntrospector;
use BlackCat\Core\Database as Database;

final class DeviceCodesModule implements ModuleInterface
{
    public function name(): string { return 'table-device_codes'; }
    public function table(): string { return 'device_codes'; }
    public function version(): string { return '1.0.0'; }

    /** @return string[] */
    public function dialects(): array { return [ 'mysql', 'postgres' ]; }
    /** @return string[] */
    public function dependencies(): array { return []; }

    public static function contractView(): string { return 'vw_device_codes'; }

    public function install(Database $db, SqlDialect $d): void
    {
        SqlDirectoryRunner::run($db, $d, __DIR__ . '/../schema');

        $table = SqlIdentifier::qi($db, $this->table());
        $view  = SqlIdentifier::qi($db, self::contractView());

        if ($d->isMysql()) {
            $createViewSql = <<<'SQL'
CREATE OR REPLACE ALGORITHM=MERGE SQL SECURITY INVOKER VIEW vw_device_codes AS
SELECT
  id,
  device_code_hash_key_version,
  device_code_hash,
  CAST(LPAD(HEX(device_code_hash), 64, '0') AS CHAR(64)) AS device_code_hash_hex,
  device_code_key_version,
  user_code_hash_key_version,
  user_code_hash,
  CAST(LPAD(HEX(user_code_hash), 64, '0') AS CHAR(64)) AS user_code_hash_hex,
  client_id,
  scopes,
  interval_sec,
  approved_at,
  expires_at,
  created_at,
  (token_payload IS NOT NULL) AS is_approved,
  token_payload,
  token_payload_key_version,
  CAST(UPPER(SHA2(device_code, 256)) AS CHAR(64)) AS device_code_hex,
  CAST(UPPER(SHA2(token_payload, 256)) AS CHAR(64)) AS token_payload_hex
FROM device_codes;
SQL;
        } else {
            $createViewSql = <<<'SQL'
CREATE OR REPLACE VIEW vw_device_codes AS
SELECT
  id,
  device_code_hash_key_version,
  device_code_hash,
  UPPER(encode(device_code_hash,'hex')) AS device_code_hash_hex,
  device_code_key_version,
  user_code_hash_key_version,
  user_code_hash,
  UPPER(encode(user_code_hash,'hex')) AS user_code_hash_hex,
  client_id,
  scopes,
  interval_sec,
  approved_at,
  expires_at,
  created_at,
  (token_payload IS NOT NULL) AS is_approved,
  token_payload,
  token_payload_key_version,
  UPPER(encode(digest(device_code,'sha256'),'hex')) AS device_code_hex,
  UPPER(encode(digest(token_payload,'sha256'),'hex')) AS token_payload_hex
FROM device_codes;
SQL;
        }

        if (\class_exists('\\BlackCat\\Database\\Support\\DdlGuard')) {
            (new \BlackCat\Database\Support\DdlGuard($db, $d))->applyCreateView($createViewSql);
        } else {
            $db->exec($createViewSql);
        }
    }

    public function upgrade(Database $db, SqlDialect $d, string $from): void
    {
    }

    public function uninstall(Database $db, SqlDialect $d): void
    {
        $qiV = SqlIdentifier::qi($db, self::contractView());
        try {
            $db->exec("DROP VIEW IF EXISTS {$qiV}" . ($d->isMysql() ? "" : " CASCADE"));
        } catch (\Throwable) {
        }
    }

    public function status(Database $db, SqlDialect $d): array
    {
        $table = $this->table();
        $view  = self::contractView();

        $hasTable = SchemaIntrospector::hasTable($db, $d, $table);
        $hasView  = SchemaIntrospector::hasView($db, $d, $view);

        $expectedIdx = [ 'idx_device_codes_expires', 'idx_device_codes_client', 'idx_device_codes_approved' ];
        if ($d->isMysql()) {
            $expectedIdx = array_values(array_filter(
                $expectedIdx,
                static fn(string $n): bool => !str_starts_with($n, 'gin_') && !str_starts_with($n, 'gist_')
            ));
        }
        $expectedFk  = [];

        $haveIdx = $hasTable ? SchemaIntrospector::listIndexes($db, $d, $table)     : [];
        $haveFk  = $hasTable ? SchemaIntrospector::listForeignKeys($db, $d, $table) : [];

        $missingIdx = array_values(array_diff($expectedIdx, $haveIdx));
        $missingFk  = array_values(array_diff($expectedFk, $haveFk));

        return [
            'table'       => $hasTable,
            'view'        => $hasView,
            'missing_idx' => $missingIdx,
            'missing_fk'  => $missingFk,
            'version'     => $this->version(),
        ];
    }

    public function info(): array
    {
        return [
            'table'       => $this->table(),
            'view'        => self::contractView(),
            'columns'     => Definitions::columns(),
            'version'     => $this->version(),
            'dialects'    => [ 'mysql', 'postgres' ],
            'indexes'     => [ 'idx_device_codes_expires', 'idx_device_codes_client', 'idx_device_codes_approved' ],
            'foreignKeys' => [],
        ];
    }
}
