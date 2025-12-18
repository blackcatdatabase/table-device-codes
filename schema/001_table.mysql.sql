-- Auto-generated from schema-map-mysql.yaml (map@sha1:7AAC4013A2623AC60C658C9BF8458EFE0C7AB741)
-- engine: mysql
-- table:  device_codes

CREATE TABLE IF NOT EXISTS device_codes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  device_code_hash BINARY(32) NOT NULL,
  device_code_hash_key_version VARCHAR(64) NULL,
  device_code BLOB NOT NULL,
  device_code_key_version VARCHAR(64) NULL,
  user_code_hash BINARY(32) NOT NULL,
  user_code_hash_key_version VARCHAR(64) NULL,
  client_id VARCHAR(128) NOT NULL,
  scopes JSON NOT NULL,
  token_payload BLOB NULL,
  token_payload_key_version VARCHAR(64) NULL,
  interval_sec INT UNSIGNED NOT NULL DEFAULT 5,
  approved_at DATETIME(6) NULL,
  expires_at DATETIME(6) NOT NULL,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  UNIQUE KEY ux_device_codes_device_code_hash (device_code_hash),
  UNIQUE KEY ux_device_codes_user_code_hash (user_code_hash),
  INDEX idx_device_codes_expires (expires_at),
  INDEX idx_device_codes_client (client_id),
  INDEX idx_device_codes_approved (approved_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
