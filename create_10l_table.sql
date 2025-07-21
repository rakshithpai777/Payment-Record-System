CREATE TABLE IF NOT EXISTS data_10L (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(20) NOT NULL,
    balance_start DECIMAL(15,2) NOT NULL,
    interest DECIMAL(15,2) NOT NULL,
    int_paid DECIMAL(15,2) NOT NULL,
    grant_amount DECIMAL(15,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    expenditure DECIMAL(15,2) NOT NULL,
    balance_end DECIMAL(15,2) NOT NULL,
    row_total DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 

SELECT 
    YEAR(date) AS year,
    SUM(CASE WHEN amount = '10L' THEN amount ELSE 0 END) AS total_10L,
    SUM(CASE WHEN amount = '2L' THEN amount ELSE 0 END) AS total_2L
FROM payments
GROUP BY YEAR(date)
ORDER BY year;