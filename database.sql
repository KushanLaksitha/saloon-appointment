-- Create Database
CREATE DATABASE IF NOT EXISTS saloon_kavisha;
USE saloon_kavisha;

-- Create Appointments Table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    service VARCHAR(50) NOT NULL,
    booking_date DATETIME NOT NULL,
    additional_notes TEXT,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Index for Performance
CREATE INDEX idx_booking_date ON appointments(booking_date);
CREATE INDEX idx_service ON appointments(service);

-- Optional: Create a stored procedure for booking
DELIMITER //
CREATE PROCEDURE BookAppointment(
    IN p_name VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_service VARCHAR(50),
    IN p_booking_date DATETIME,
    IN p_notes TEXT
)
BEGIN
    INSERT INTO appointments (
        name, 
        phone, 
        service, 
        booking_date, 
        additional_notes
    ) VALUES (
        p_name, 
        p_phone, 
        p_service, 
        p_booking_date, 
        p_notes
    );
    
    SELECT LAST_INSERT_ID() AS booking_id;
END //
DELIMITER ;

-- Create a view for upcoming appointments
CREATE VIEW upcoming_appointments AS
SELECT 
    id, 
    name, 
    phone, 
    service, 
    booking_date, 
    status
FROM 
    appointments
WHERE 
    booking_date > NOW()
ORDER BY 
    booking_date ASC;



CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_super_admin BOOLEAN DEFAULT FALSE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
