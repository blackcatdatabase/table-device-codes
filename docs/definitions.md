# device_codes

OAuth device-code flow storage (issued codes + approval payload).

## Columns
| Column | Type | Null | Default | Description | Crypto |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT | NO |  | Surrogate primary key. |  |
| device_code_hash | mysql: BINARY(32) / postgres: BYTEA | NO |  | Deterministic hash of device_code (HMAC; used for polling lookup). | `hmac`<br/>ctx: `db.hmac.device_codes.device_code_hash`<br/>kv: `device_code_hash_key_version` |
| device_code_hash_key_version | VARCHAR(64) | YES |  | Key version used for device_code_hash. | key version for: `device_code_hash` |
| device_code | mysql: BLOB / postgres: BYTEA | NO |  | Encrypted device_code (envelope). |  |
| device_code_key_version | VARCHAR(64) | YES |  | Key version used for device_code encryption. |  |
| user_code_hash | mysql: BINARY(32) / postgres: BYTEA | NO |  | Deterministic hash of user_code (HMAC; used for activation lookup). | `hmac`<br/>ctx: `db.hmac.device_codes.user_code_hash`<br/>kv: `user_code_hash_key_version` |
| user_code_hash_key_version | VARCHAR(64) | YES |  | Key version used for user_code_hash. | key version for: `user_code_hash` |
| client_id | VARCHAR(128) | NO |  | OAuth client id requesting the device code. |  |
| scopes | mysql: JSON / postgres: JSONB | NO | postgres: []'::jsonb | Requested scopes (JSON array). |  |
| token_payload | mysql: BLOB / postgres: BYTEA | YES |  | Encrypted token payload issued after approval (envelope JSON). |  |
| token_payload_key_version | VARCHAR(64) | YES |  | Key version used for token_payload encryption. |  |
| interval_sec | mysql: INT / postgres: INTEGER | NO | 5 | Recommended polling interval in seconds. |  |
| approved_at | mysql: DATETIME(6) / postgres: TIMESTAMPTZ(6) | YES |  | When the device code was approved by the user (UTC). |  |
| expires_at | mysql: DATETIME(6) / postgres: TIMESTAMPTZ(6) | NO |  | Expiration timestamp (UTC). |  |
| created_at | mysql: DATETIME(6) / postgres: TIMESTAMPTZ(6) | NO | CURRENT_TIMESTAMP(6) | Creation timestamp (UTC). |  |

## Engine Details

### mysql

Unique keys:
| Name | Columns |
| --- | --- |
| ux_device_codes_device_code_hash | device_code_hash |
| ux_device_codes_user_code_hash | user_code_hash |

Indexes:
| Name | Columns | SQL |
| --- | --- | --- |
| idx_device_codes_approved | approved_at | INDEX idx_device_codes_approved (approved_at) |
| idx_device_codes_client | client_id | INDEX idx_device_codes_client (client_id) |
| idx_device_codes_expires | expires_at | INDEX idx_device_codes_expires (expires_at) |
| ux_device_codes_device_code_hash | device_code_hash | UNIQUE KEY ux_device_codes_device_code_hash (device_code_hash) |
| ux_device_codes_user_code_hash | user_code_hash | UNIQUE KEY ux_device_codes_user_code_hash (user_code_hash) |

### postgres

Unique keys:
| Name | Columns |
| --- | --- |
| ux_device_codes_device_code_hash | device_code_hash |
| ux_device_codes_user_code_hash | user_code_hash |

Indexes:
| Name | Columns | SQL |
| --- | --- | --- |
| idx_device_codes_approved | approved_at | CREATE INDEX IF NOT EXISTS idx_device_codes_approved ON device_codes (approved_at) |
| idx_device_codes_client | client_id | CREATE INDEX IF NOT EXISTS idx_device_codes_client ON device_codes (client_id) |
| idx_device_codes_expires | expires_at | CREATE INDEX IF NOT EXISTS idx_device_codes_expires ON device_codes (expires_at) |
| ux_device_codes_device_code_hash | device_code_hash | CONSTRAINT ux_device_codes_device_code_hash UNIQUE (device_code_hash) |
| ux_device_codes_user_code_hash | user_code_hash | CONSTRAINT ux_device_codes_user_code_hash UNIQUE (user_code_hash) |

## Engine differences

## Views
| View | Engine | Flags | File |
| --- | --- | --- | --- |
| vw_device_codes | mysql | algorithm=MERGE, security=INVOKER | [../schema/040_views.mysql.sql](../schema/040_views.mysql.sql) |
| vw_device_codes | postgres |  | [../schema/040_views.postgres.sql](../schema/040_views.postgres.sql) |
