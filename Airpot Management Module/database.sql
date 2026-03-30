-- Airport Management System Database Schema
-- Run this script to create the database structure

CREATE DATABASE IF NOT EXISTS airport_management;
USE airport_management;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    phone VARCHAR(20),
    passport_number VARCHAR(50),
    date_of_birth DATE,
    role ENUM('user', 'admin') DEFAULT 'user',
    language ENUM('en', 'hi') DEFAULT 'en',
    theme ENUM('dark', 'light') DEFAULT 'dark',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Flights Table
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_code VARCHAR(20) NOT NULL UNIQUE,
    airline VARCHAR(100) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_date DATE NOT NULL,
    arrival_time TIME NOT NULL,
    gate VARCHAR(10),
    status ENUM('On-Time', 'Boarding', 'Delayed', 'Cancelled') DEFAULT 'On-Time',
    total_seats INT DEFAULT 180,
    available_seats INT DEFAULT 180,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    flight_id INT NOT NULL,
    passenger_name VARCHAR(200) NOT NULL,
    passport_number VARCHAR(50) NOT NULL,
    seat_number VARCHAR(10),
    bags_count INT DEFAULT 1,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Confirmed', 'Cancelled', 'Checked-in') DEFAULT 'Confirmed',
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (flight_id) REFERENCES flights(id) ON DELETE CASCADE
);

-- Baggage Table
CREATE TABLE IF NOT EXISTS baggage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    bag_tag VARCHAR(20) NOT NULL UNIQUE,
    weight DECIMAL(5, 2),
    status ENUM('Checked-in', 'Loaded', 'In Transit', 'At Belt') DEFAULT 'Checked-in',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Staff Table
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(200) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    role VARCHAR(100),
    department VARCHAR(100),
    hired_date DATE,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('Flight Status', 'Gate Change', 'Boarding', 'General') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Announcements Table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'urgent') DEFAULT 'info',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL
);

-- Gates Table
CREATE TABLE IF NOT EXISTS gates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gate_number VARCHAR(10) NOT NULL UNIQUE,
    terminal VARCHAR(10),
    status ENUM('Available', 'Occupied', 'Maintenance') DEFAULT 'Available',
    current_flight_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (current_flight_id) REFERENCES flights(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@airport.com', 'admin123', 'System Administrator', 'admin');

-- Insert demo user
INSERT INTO users (username, email, password, full_name, role)
VALUES ('jaival', 'jaival@gmail.com', 'jaival01', 'Jaival Chandegara', 'user');

-- Insert sample flights
INSERT INTO flights (flight_code, airline, origin, destination, departure_date, departure_time, arrival_date, arrival_time, gate, status, price) VALUES
('AI101', 'Air India', 'Delhi (DEL)', 'Mumbai (BOM)', '2026-01-25', '08:00:00', '2026-01-25', '10:30:00', 'A12', 'On-Time', 5500.00),
('6E202', 'IndiGo', 'Mumbai (BOM)', 'Bangalore (BLR)', '2026-01-25', '14:00:00', '2026-01-25', '16:00:00', 'B05', 'Boarding', 4200.00),
('SG303', 'SpiceJet', 'Delhi (DEL)', 'Goa (GOI)', '2026-01-26', '06:30:00', '2026-01-26', '09:00:00', 'C08', 'On-Time', 6800.00),
('UK404', 'Vistara', 'Chennai (MAA)', 'Delhi (DEL)', '2026-01-26', '11:00:00', '2026-01-26', '14:00:00', 'A15', 'Delayed', 7200.00);

-- Insert sample gates
INSERT INTO gates (gate_number, terminal, status) VALUES
('A12', 'T1', 'Occupied'),
('A15', 'T1', 'Available'),
('B05', 'T2', 'Occupied'),
('C08', 'T3', 'Available');
