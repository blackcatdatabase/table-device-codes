-- Auto-generated from schema-map-postgres.yaml (map@sha1:8C4F2BC1C4D22EE71E27B5A7968C71E32D8D884D)
-- engine: postgres
-- table:  device_codes

CREATE INDEX IF NOT EXISTS idx_device_codes_expires ON device_codes (expires_at);

CREATE INDEX IF NOT EXISTS idx_device_codes_client ON device_codes (client_id);

CREATE INDEX IF NOT EXISTS idx_device_codes_approved ON device_codes (approved_at);
