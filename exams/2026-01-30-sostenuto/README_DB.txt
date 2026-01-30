/* =====================================================================
   COMPITO C - iot_db
   ===================================================================== */

CREATE DATABASE IF NOT EXISTS iot_db;
USE iot_db;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS `sensors` (
  `id` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `measurements` (
  `id` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `sensors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sensors_type` (`type`);

ALTER TABLE `measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_measurements_sensor` (`sensor_id`),
  ADD KEY `idx_measurements_timestamp` (`timestamp`),
  ADD CONSTRAINT `fk_measurements_sensors`
    FOREIGN KEY (`sensor_id`) REFERENCES `sensors` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sensors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `measurements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Dati demo
INSERT INTO `sensors` (`id`, `location`, `type`) VALUES
(1, 'Ufficio', 'temperature'),
(2, 'Magazzino', 'humidity'),
(3, 'Esterno', 'temperature')
ON DUPLICATE KEY UPDATE location=VALUES(location), type=VALUES(type);

INSERT INTO `measurements` (`id`, `sensor_id`, `value`, `timestamp`) VALUES
(1, 1, 22.50, '2026-01-27 09:00:00'),
(2, 1, 23.10, '2026-01-27 12:00:00'),
(3, 2, 55.00, '2026-01-27 09:30:00'),
(4, 3, 10.20, '2026-01-27 07:45:00')
ON DUPLICATE KEY UPDATE sensor_id=VALUES(sensor_id), value=VALUES(value), timestamp=VALUES(timestamp);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
