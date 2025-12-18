-- Auto-generated from schema-map-postgres.yaml (map@sha1:621FDD3D99B768B6A8AD92061FB029414184F4B3)
-- engine: postgres
-- table:  device_codes

CREATE INDEX IF NOT EXISTS idx_device_codes_expires ON device_codes (expires_at);

CREATE INDEX IF NOT EXISTS idx_device_codes_client ON device_codes (client_id);

CREATE INDEX IF NOT EXISTS idx_device_codes_approved ON device_codes (approved_at);
