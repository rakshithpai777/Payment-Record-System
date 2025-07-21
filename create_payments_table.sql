CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    recurring_type VARCHAR(50) DEFAULT 'Non-Recurring',
    slab VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    vendor VARCHAR(100) NOT NULL,
    type VARCHAR(50) DEFAULT 'Credit',
    reference_no VARCHAR(100) NOT NULL,
    balance DECIMAL(10,2) NOT NULL,
    comment TEXT,
    file_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);