-- Auto-generated from schema-views-mysql.yaml (map@sha1:FFA9A9D6FA9EE079B0DAEBB6FEE023C138E8FFA1)
-- engine: mysql
-- table:  device_codes

-- Contract view for [device_codes]
-- Exposes token_payload (expected encrypted) for device-token polling; device_code remains hidden.
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
