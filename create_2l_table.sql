CREATE TABLE IF NOT EXISTS data_2L (
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
    SUM(CASE WHEN fund = '10L' THEN amount ELSE 0 END) AS total_10L,
    SUM(CASE WHEN fund = '2L' THEN amount ELSE 0 END) AS total_2L
FROM payments
GROUP BY YEAR(date)
ORDER BY year; 

-- Select all rows and add a totals row at the end
SELECT 
    year,
    balance_start,
    interest,
    int_paid,
    grant_amount,
    total,
    expenditure,
    balance_end,
    row_total
FROM data_2L

UNION ALL

SELECT 
    'Total' AS year,
    SUM(balance_start),
    SUM(interest),
    SUM(int_paid),
    SUM(grant_amount),
    SUM(total),
    SUM(expenditure),
    SUM(balance_end),
    SUM(row_total)
FROM data_2L

ORDER BY 
    CASE WHEN year = 'Total' THEN 1 ELSE 0 END, 
    year;