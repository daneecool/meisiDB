CREATE TABLE IF NOT EXISTS meishi (
    id SERIAL PRIMARY KEY,
    received_date DATE,
    company VARCHAR(255),
    name VARCHAR(255),
    tel VARCHAR(255),
    email VARCHAR(255),
    notes TEXT,
    image_front VARCHAR(255),
    image_back VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE meishi ADD COLUMN user_id VARCHAR(255);

ALTER TABLE meishi
ADD COLUMN is_public BOOLEAN DEFAULT FALSE,
ADD COLUMN created_by TEXT;
