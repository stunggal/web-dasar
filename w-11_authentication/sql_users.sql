-- SQL untuk tabel users (jalankan di MySQL)
-- Database: web_dasar (sesuaikan dengan DB di index.php)

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `role` ENUM('admin','user') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert user default (password: admin123, user123)
-- Gunakan password_hash() di PHP untuk generate hash, contoh:
-- password_hash('admin123', PASSWORD_DEFAULT)
-- password_hash('user123', PASSWORD_DEFAULT)

INSERT INTO `users` (`username`, `password`, `nama`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'),
('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pengguna Biasa', 'user');