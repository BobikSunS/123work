-- Database structure for Belarus Delivery Site with OSRM integration

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `delivery_db`;
USE `delivery_db`;

-- Table for delivery operators
CREATE TABLE `operators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `tariff_per_km` decimal(5,2) NOT NULL DEFAULT 0.00,
  `color` varchar(7) NOT NULL DEFAULT '#000000',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample operators
INSERT INTO `operators` (`name`, `tariff_per_km`, `color`, `description`) VALUES
('Белпочта', 0.25, '#FF0000', 'Национальная почтовая служба Республики Беларусь'),
('Европочта', 0.30, '#0000FF', 'Частная курьерская служба'),
('ДЭК', 0.28, '#00AA00', 'Доставка экспресс курьером'),
('Belpost Express', 0.35, '#FFA500', 'Экспресс-доставка Белпочты');

-- Table for delivery offices
CREATE TABLE `offices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operator_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `address` varchar(500) NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `city` varchar(100) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `operator_id` (`operator_id`),
  CONSTRAINT `offices_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample offices for Belarus (limited sample for demonstration)
INSERT INTO `offices` (`operator_id`, `title`, `address`, `lat`, `lon`, `city`) VALUES
(1, 'Белпочта Минск Центральная', 'г. Минск, ул. Советская, 10', 53.904133, 27.557541, 'Минск'),
(1, 'Белпочта Гродно', 'г. Гродно, ул. Ожешко, 25', 53.669320, 23.824230, 'Гродно'),
(1, 'Белпочта Брест', 'г. Брест, ул. Советская, 150', 52.096910, 23.728330, 'Брест'),
(1, 'Белпочта Витебск', 'г. Витебск, ул. Ленина, 30', 55.190030, 30.209490, 'Витебск'),
(1, 'Белпочта Гомель', 'г. Гомель, ул. Кирова, 18', 52.441450, 30.994350, 'Гомель'),
(1, 'Белпочта Могилев', 'г. Могилев, ул. Первомайская, 5', 53.904100, 30.337100, 'Могилев'),
(2, 'Европочта Минск', 'г. Минск, ул. Карвата, 84', 53.794210, 27.445000, 'Минск'),
(2, 'Европочта Гродно', 'г. Гродно, ул. Врублевского, 7', 53.674700, 23.834400, 'Гродно'),
(2, 'Европочта Брест', 'г. Брест, ул. Янки Купалы, 1', 52.097600, 23.685900, 'Брест'),
(2, 'Европочта Витебск', 'г. Витебск, пр-т Строителей, 10', 55.186800, 30.195000, 'Витебск'),
(2, 'Европочта Гомель', 'г. Гомель, ул. Речицкое шоссе, 15', 52.412000, 31.020500, 'Гомель'),
(2, 'Европочта Могилев', 'г. Могилев, ул. Ботаническая, 1', 53.910000, 30.352000, 'Могилев'),
(3, 'ДЭК Минск', 'г. Минск, ул. Карвата, 1', 53.848200, 27.527100, 'Минск'),
(3, 'ДЭК Гродно', 'г. Гродно, ул. Машерова, 2', 53.669500, 23.821000, 'Гродно'),
(3, 'ДЭК Брест', 'г. Брест, ул. Московская, 278', 52.099000, 23.752000, 'Брест'),
(3, 'ДЭК Витебск', 'г. Витебск, ул. Гагарина, 22', 55.181000, 30.203000, 'Витебск'),
(3, 'ДЭК Гомель', 'г. Гомель, ул. Машерова, 30', 52.439000, 30.994000, 'Гомель'),
(3, 'ДЭК Могилев', 'г. Могилев, ул. Космонавтов, 2', 53.904000, 30.337000, 'Могилев'),
(4, 'Belpost Express Минск', 'г. Минск, ул. Немига, 5', 53.901000, 27.559000, 'Минск'),
(4, 'Belpost Express Гродно', 'г. Гродно, ул. Захарова, 10', 53.669000, 23.820000, 'Гродно');

-- Table for orders
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operator_id` int(11) NOT NULL,
  `from_office_id` int(11) NOT NULL,
  `to_office_id` int(11) NOT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_phone` varchar(50) NOT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `recipient_phone` varchar(50) NOT NULL,
  `recipient_address` varchar(500) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `distance_km` decimal(7,2) DEFAULT NULL,
  `duration_min` int(11) DEFAULT NULL,
  `final_price` decimal(8,2) DEFAULT NULL,
  `insurance` tinyint(1) DEFAULT 0,
  `fragile` tinyint(1) DEFAULT 0,
  `packaging` tinyint(1) DEFAULT 0,
  `payment_method` enum('card','cash') DEFAULT 'cash',
  `status` enum('pending','in_transit','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `comment` text,
  PRIMARY KEY (`id`),
  KEY `operator_id` (`operator_id`),
  KEY `from_office_id` (`from_office_id`),
  KEY `to_office_id` (`to_office_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`from_office_id`) REFERENCES `offices` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`to_office_id`) REFERENCES `offices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Additional indexes for performance
CREATE INDEX idx_offices_operator ON offices(operator_id);
CREATE INDEX idx_offices_city ON offices(city);
CREATE INDEX idx_orders_date ON orders(created_at);

COMMIT;