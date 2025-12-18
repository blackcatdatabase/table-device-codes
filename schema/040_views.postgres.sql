-- Auto-generated from schema-views-postgres.yaml (map@sha1:3C365C10BD489376A27944AE10F143E1BE4D3BCF)
-- engine: postgres
-- table:  device_codes

-- Contract view for [device_codes]
-- Exposes token_payload (expected encrypted) for device-token polling; device_code remains hidden.
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
