-- Auto-generated from schema-map-postgres.yaml (map@sha1:FAEA49A5D5F8FAAD9F850D0F430ED451C5C1D707)
-- engine: postgres
-- table:  device_codes

CREATE INDEX IF NOT EXISTS idx_device_codes_expires ON device_codes (expires_at);

CREATE INDEX IF NOT EXISTS idx_device_codes_client ON device_codes (client_id);

CREATE INDEX IF NOT EXISTS idx_device_codes_approved ON device_codes (approved_at);
