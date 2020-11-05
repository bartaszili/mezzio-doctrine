START TRANSACTION;
CREATE DATABASE IF NOT EXISTS `mezzio-doctrine` DEFAULT CHARACTER SET `utf8mb4` DEFAULT COLLATE `utf8mb4_unicode_ci`;
USE `mysql`;
CREATE USER IF NOT EXISTS `mezzio-doctrine`@`%` IDENTIFIED VIA mysql_native_password USING PASSWORD('mezzio-doctrine');
GRANT ALL PRIVILEGES ON `mezzio-doctrine`.* TO `mezzio-doctrine`@`%`;
FLUSH PRIVILEGES;
COMMIT;