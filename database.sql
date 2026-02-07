CREATE DATABASE cleaning_service CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cleaning_service;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL CHECK (fullname REGEXP '^[А-Яа-яЁё\\s]+$'),
    phone VARCHAR(18) NOT NULL UNIQUE CHECK (phone REGEXP '^\\+7\\([0-9]{3}\\)-[0-9]{3}-[0-9]{2}-[0-9]{2}$'),
    email VARCHAR(100) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO services (name, description) VALUES
('Общий клининг', 'Ежедневная уборка помещений'),
('Генеральная уборка', 'Глубокая уборка всех поверхностей'),
('Послестроительная уборка', 'Уборка после ремонта/строительства'),
('Химчистка ковров и мебели', 'Профессиональная чистка текстиля');

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(18) NOT NULL,
    service_id INT,
    custom_service TEXT,
    service_date DATE NOT NULL,
    service_time TIME NOT NULL,
    payment_type ENUM('cash', 'card') NOT NULL,
    status ENUM('new', 'in_progress', 'completed', 'cancelled') DEFAULT 'new',
    cancel_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Администратор
INSERT INTO users (fullname, phone, email, login, password) VALUES
('Администратор', '+7(000)-000-00-00', 'admin@cleanservice.ru', 'adminka', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Пароль: cleanservic