<?php
declare(strict_types=1);

namespace BlackCat\Database\Packages\DeviceCodes\Mapper;

use BlackCat\Database\Packages\DeviceCodes\Dto\DeviceCodeDto;
use BlackCat\Database\Packages\DeviceCodes\Definitions;
use DateTimeZone;
use BlackCat\Database\Support\DtoHydrator;

/**
 * Bidirectional mapper between DB rows and DTO DeviceCodeDto.
 */
final class DeviceCodeDtoMapper
{
    /** @var array<string,string> Column -> DTO property */
    private const COL_TO_PROP = [
        'id' => 'id',
        'device_code_hash' => 'deviceCodeHash',
        'device_code_hash_key_version' => 'deviceCodeHashKeyVersion',
        'device_code_key_version' => 'deviceCodeKeyVersion',
        'user_code_hash' => 'userCodeHash',
        'user_code_hash_key_version' => 'userCodeHashKeyVersion',
        'client_id' => 'clientId',
        'scopes' => 'scopes',
        'token_payload' => 'tokenPayload',
        'token_payload_key_version' => 'tokenPayloadKeyVersion',
        'interval_sec' => 'intervalSec',
        'approved_at' => 'approvedAt',
        'expires_at' => 'expiresAt',
        'created_at' => 'createdAt',
    ];

    /** @var string[] */
    private const BOOL_COLS   = [];
    /** @var string[] */
    private const INT_COLS    = [ 'id', 'interval_sec' ];
    /** @var string[] */
    private const FLOAT_COLS  = [];
    /** @var string[] */
    private const JSON_COLS   = [ 'scopes' ];
    /** @var string[] */
    private const DATE_COLS   = [ 'approved_at', 'expires_at', 'created_at' ];
    /** @var string[] */
    private const BIN_COLS    = [ 'device_code_hash', 'user_code_hash', 'token_payload' ];

    /** Preferred timezone for parsing/serializing dates */
    private const TZ = 'UTC';

    private static ?DateTimeZone $tzObj = null;

    private static function tz(): DateTimeZone
    {
        if (!(self::$tzObj instanceof DateTimeZone)) {
            self::$tzObj = new DateTimeZone(self::TZ);
        }
        return self::$tzObj;
    }

    /**
     * @param array<string,mixed> $row
     */
    public static function fromRow(array $row): DeviceCodeDto
    {
        /** @var DeviceCodeDto */
        return DtoHydrator::fromRow(
            DeviceCodeDto::class,
            $row,
            self::COL_TO_PROP,
            self::BOOL_COLS,
            self::INT_COLS,
            self::FLOAT_COLS,
            self::JSON_COLS,
            self::DATE_COLS,
            self::BIN_COLS,
            self::tz()
        );
    }

    public static function fromRowOrNull(?array $row): ?DeviceCodeDto
    {
        return $row === null ? null : self::fromRow($row);
    }

    /**
     * @param DeviceCodeDto $dto
     * @param string[]|null $onlyProps
     * @return array<string,mixed>
     */
    public static function toRow(DeviceCodeDto $dto, ?array $onlyProps = null): array
    {
        return DtoHydrator::toRow(
            $dto,
            self::COL_TO_PROP,
            self::BOOL_COLS,
            self::INT_COLS,
            self::FLOAT_COLS,
            self::JSON_COLS,
            self::DATE_COLS,
            self::BIN_COLS,
            self::tz(),
            $onlyProps
        );
    }

    public static function toRowNonNull(DeviceCodeDto $dto, ?array $onlyProps = null): array
    {
        $row = self::toRow($dto, $onlyProps);
        foreach ($row as $k => $v) {
            if ($v === null) unset($row[$k]);
        }
        return $row;
    }

    /**
     * @param array<string,mixed> $original
     * @param string[] $ignore
     * @return array<string,mixed>
     */
    public static function diff(DeviceCodeDto $dto, array $original, array $ignore = [], bool $coerce = true): array
    {
        $now = self::toRow($dto);

        if ($ignore) {
            $drop = array_fill_keys($ignore, true);
            $now = array_filter($now, static fn($k) => !isset($drop[$k]), ARRAY_FILTER_USE_KEY);
        }

        $out = [];
        foreach ($now as $k => $v) {
            $orig = $original[$k] ?? null;

            if ($coerce && is_scalar($v) && is_scalar($orig)) {
                $vn = is_bool($v) ? (int)$v : (string)$v;
                $on = is_bool($orig) ? (int)$orig : (string)$orig;
                if ($vn === $on) continue;
            } elseif ($v == $orig) {
                continue;
            }

            $out[$k] = $v;
        }
        return $out;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return array<int,DeviceCodeDto>
     */
    public static function hydrateList(array $rows): array
    {
        /** @var array<int,DeviceCodeDto> */
        return DtoHydrator::hydrateList(
            DeviceCodeDto::class,
            $rows,
            self::COL_TO_PROP,
            self::BOOL_COLS,
            self::INT_COLS,
            self::FLOAT_COLS,
            self::JSON_COLS,
            self::DATE_COLS,
            self::BIN_COLS,
            self::tz()
        );
    }

    /** @return array<string,mixed> */
    public static function toSafeArray(DeviceCodeDto $dto): array
    {
        $row = self::toRow($dto);

        $pii = [];
        if (\method_exists(Definitions::class, 'piiColumns')) {
            $decl = (array) Definitions::piiColumns();

            if ($decl) {
                $map = self::COL_TO_PROP;
                $rev = array_flip($map);

                foreach ($decl as $name) {
                    $name = (string) $name;
                    $col = array_key_exists($name, $row) ? $name : ($rev[$name] ?? $name);
                    $pii[$col] = true;
                }
            }
        }

        if ($pii) {
            foreach ($row as $k => &$v) {
                if (isset($pii[$k]) && $v !== null && $v !== '') {
                    $v = '***';
                }
            }
            unset($v);
        }

        return $row;
    }

    /**
     * @param iterable<int,array<string,mixed>> $rows
     * @return \Generator<int,DeviceCodeDto>
     */
    public static function hydrate(iterable $rows): \Generator
    {
        foreach ($rows as $row) {
            yield self::fromRow($row);
        }
    }
}
