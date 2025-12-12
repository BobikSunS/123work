-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Дек 12 2025 г., 17:57
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `delivery_by`
--

-- --------------------------------------------------------

--
-- Структура таблицы `calculated_routes`
--

CREATE TABLE `calculated_routes` (
  `id` int(11) NOT NULL,
  `from_office_id` int(11) NOT NULL,
  `to_office_id` int(11) NOT NULL,
  `distance_km` decimal(8,2) NOT NULL,
  `duration_min` int(11) NOT NULL,
  `route_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `carriers`
--

CREATE TABLE `carriers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `max_weight` decimal(6,2) NOT NULL,
  `base_cost` decimal(8,2) NOT NULL,
  `cost_per_kg` decimal(8,3) NOT NULL,
  `cost_per_km` decimal(8,4) NOT NULL,
  `speed_kmh` decimal(6,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `carriers`
--

INSERT INTO `carriers` (`id`, `name`, `color`, `max_weight`, `base_cost`, `cost_per_kg`, `cost_per_km`, `speed_kmh`, `description`) VALUES
(1, 'Белпочта', '#d32f2f', 30.00, 4.50, 0.250, 0.0080, 60.00, 'Государственная почта'),
(2, 'DPD', '#0066cc', 30.00, 9.00, 0.800, 0.0180, 95.00, 'Международная доставка'),
(3, 'СДЭК (CDEK)', '#ff9800', 20.00, 8.50, 0.700, 0.0200, 90.00, 'Курьерская служба'),
(4, 'Европочта', '#4caf50', 25.00, 6.50, 0.500, 0.0120, 80.00, 'Пункты выдачи'),
(5, 'Boxberry', '#9c27b0', 15.00, 8.00, 0.900, 0.0220, 85.00, 'Пункты выдачи'),
(6, 'Autolight Express', '#e91e63', 30.00, 9.50, 0.750, 0.0190, 92.00, 'Быстрая доставка');

-- --------------------------------------------------------

--
-- Структура таблицы `offices`
--

CREATE TABLE `offices` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `offices`
--

INSERT INTO `offices` (`id`, `carrier_id`, `city`, `address`, `lat`, `lng`) VALUES
(1, 1, 'Минск', 'пр-т Независимости, 10', NULL, NULL),
(2, 1, 'Гомель', 'ул. Советская, 21', NULL, NULL),
(3, 1, 'Брест', 'ул. Советская, 46', NULL, NULL),
(4, 1, 'Гродно', 'ул. Ожешко, 1', NULL, NULL),
(5, 1, 'Витебск', 'ул. Ленина, 20', NULL, NULL),
(6, 1, 'Могилёв', 'ул. Первомайская, 42', NULL, NULL),
(7, 1, 'Борисов', 'ул. 3 Интернационала, 15', NULL, NULL),
(8, 1, 'Барановичи', 'ул. Советская, 89', NULL, NULL),
(9, 2, 'Минск', 'ул. Притыцкого, 29', NULL, NULL),
(10, 2, 'Минск', 'ул. Немига, 5', NULL, NULL),
(11, 2, 'Гомель', 'пр-т Ленина, 10', NULL, NULL),
(12, 2, 'Брест', 'ул. Гоголя, 15', NULL, NULL),
(13, 2, 'Гродно', 'ул. Горького, 50', NULL, NULL),
(14, 2, 'Минск', 'ТЦ Dana Mall', NULL, NULL),
(15, 3, 'Минск', 'ТЦ Столица', NULL, NULL),
(16, 3, 'Минск', 'ТЦ Galleria', NULL, NULL),
(17, 3, 'Гомель', 'ТЦ Секрет', NULL, NULL),
(18, 3, 'Брест', 'ТЦ Евроопт', NULL, NULL),
(19, 3, 'Витебск', 'ТЦ Беларусь', NULL, NULL),
(20, 3, 'Могилёв', 'ТЦ Перекрёсток', NULL, NULL),
(21, 4, 'Минск', 'ул. Кульман, 9', NULL, NULL),
(22, 4, 'Минск', 'ст.м. Площадь Победы', NULL, NULL),
(23, 4, 'Гродно', 'ул. Поповича, 5', NULL, NULL),
(24, 4, 'Минск', 'ул. Сурганова, 57', NULL, NULL),
(25, 4, 'Брест', 'ул. Московская, 202', NULL, NULL),
(26, 5, 'Минск', 'ул. Притыцкого, 156', NULL, NULL),
(27, 5, 'Минск', 'ТЦ Galileo', NULL, NULL),
(28, 5, 'Гомель', 'ул. Ильича, 33', NULL, NULL),
(29, 5, 'Могилёв', 'пр-т Мира, 21', NULL, NULL),
(30, 6, 'Минск', 'ул. Тимирязева, 123', NULL, NULL),
(31, 6, 'Минск', 'ул. Победителей, 89', NULL, NULL),
(32, 6, 'Гомель', 'ул. Крестьянская, 12', NULL, NULL),
(33, 6, 'Брест', 'ул. 17 Сентября, 10', NULL, NULL),
(34, 6, 'Минск', 'ТЦ Экспобел', NULL, NULL),
(35, 1, 'Мозырь', 'ул. Ленинская, 10', NULL, NULL),
(36, 1, 'Солигорск', 'пр-т Мира, 15', NULL, NULL),
(37, 1, 'Пинск', 'ул. Гагарина, 22', NULL, NULL),
(38, 1, 'Лида', 'ул. Советская, 33', NULL, NULL),
(39, 1, 'Слоним', 'ул. Октябрьская, 8', NULL, NULL),
(40, 1, 'Новополоцк', 'ул. Карвата, 5', NULL, NULL),
(41, 1, 'Орша', 'ул. Интернациональная, 45', NULL, NULL),
(42, 1, 'Молодечно', 'ул. Советская, 67', NULL, NULL),
(43, 1, 'Слуцк', 'ул. Ленина, 18', NULL, NULL),
(44, 1, 'Дзержинск', 'ул. Победы, 25', NULL, NULL),
(45, 2, 'Мозырь', 'ул. Гагарина, 30', NULL, NULL),
(46, 2, 'Пинск', 'ул. Космонавтов, 12', NULL, NULL),
(47, 2, 'Лида', 'ул. Октябрьская, 45', NULL, NULL),
(48, 2, 'Новополоцк', 'ул. Мира, 78', NULL, NULL),
(49, 2, 'Орша', 'ул. Ленинская, 34', NULL, NULL),
(50, 2, 'Молодечно', 'ул. Центральная, 19', NULL, NULL),
(51, 2, 'Солигорск', 'пр-т Независимости, 56', NULL, NULL),
(52, 3, 'Мозырь', 'ТЦ Универсам', NULL, NULL),
(53, 3, 'Пинск', 'ул. Кирова, 23', NULL, NULL),
(54, 3, 'Лида', 'ТЦ Спортивный', NULL, NULL),
(55, 3, 'Новополоцк', 'ул. Брестская, 44', NULL, NULL),
(56, 3, 'Орша', 'ул. Карвата, 12', NULL, NULL),
(57, 3, 'Молодечно', 'ТЦ Спектр', NULL, NULL),
(58, 3, 'Солигорск', 'ул. Молодежная, 89', NULL, NULL),
(59, 4, 'Мозырь', 'ул. Партизанская, 15', NULL, NULL),
(60, 4, 'Пинск', 'ул. Строителей, 33', NULL, NULL),
(61, 4, 'Лида', 'ул. Маяковского, 7', NULL, NULL),
(62, 4, 'Новополоцк', 'ул. Октябрьская, 41', NULL, NULL),
(63, 4, 'Орша', 'ул. Свободы, 29', NULL, NULL),
(64, 4, 'Молодечно', 'ул. Ленина, 37', NULL, NULL),
(65, 4, 'Солигорск', 'ул. Космонавтов, 14', NULL, NULL),
(66, 5, 'Мозырь', 'ул. Гагарина, 88', NULL, NULL),
(67, 5, 'Пинск', 'ул. Ленина, 55', NULL, NULL),
(68, 5, 'Лида', 'ул. Интернациональная, 22', NULL, NULL),
(69, 5, 'Новополоцк', 'ул. Строителей, 18', NULL, NULL),
(70, 5, 'Орша', 'ул. Мира, 66', NULL, NULL),
(71, 5, 'Молодечно', 'ул. Советская, 44', NULL, NULL),
(72, 5, 'Солигорск', 'ул. Победы, 33', NULL, NULL),
(73, 6, 'Мозырь', 'ул. Октябрьская, 12', NULL, NULL),
(74, 6, 'Пинск', 'ул. Маяковского, 45', NULL, NULL),
(75, 6, 'Лида', 'ул. Ленина, 78', NULL, NULL),
(76, 6, 'Новополоцк', 'ул. Карвата, 34', NULL, NULL),
(77, 6, 'Орша', 'ул. Космонавтов, 56', NULL, NULL),
(78, 6, 'Молодечно', 'ул. Свободы, 23', NULL, NULL),
(79, 6, 'Солигорск', 'ул. Мира, 89', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `from_office` int(11) DEFAULT NULL,
  `to_office` int(11) DEFAULT NULL,
  `weight` decimal(8,3) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `delivery_hours` decimal(8,2) DEFAULT NULL,
  `track_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_status` varchar(20) DEFAULT 'pending',
  `tracking_status` varchar(50) DEFAULT 'created',
  `full_name` varchar(255) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `pickup_city` varchar(100) DEFAULT NULL,
  `pickup_address` text DEFAULT NULL,
  `delivery_city` varchar(100) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `desired_date` date DEFAULT NULL,
  `insurance` tinyint(1) DEFAULT 0,
  `packaging` tinyint(1) DEFAULT 0,
  `fragile` tinyint(1) DEFAULT 0,
  `payment_method` varchar(50) DEFAULT 'cash',
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `carrier_id`, `from_office`, `to_office`, `weight`, `cost`, `delivery_hours`, `track_number`, `created_at`, `payment_status`, `tracking_status`, `full_name`, `home_address`, `pickup_city`, `pickup_address`, `delivery_city`, `delivery_address`, `desired_date`, `insurance`, `packaging`, `fragile`, `payment_method`, `comment`) VALUES
(1, 2, 4, 23, 22, 30.000, 49.28, 1.60, '7493FC43F2BC', '2025-12-04 03:43:01', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(2, 1, 1, 8, 3, 1.000, 8.60, 8.00, '3E93445DFB40', '2025-12-10 21:10:10', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(3, 1, 2, 11, 13, 1.000, 19.12, 5.50, '14DA538AF5EC', '2025-12-10 21:10:14', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(4, 1, 2, 11, 13, 1.000, 19.12, 5.50, '614C04987B6D', '2025-12-10 21:22:43', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(5, 1, 2, 11, 13, 1.000, 19.12, 5.50, '793FA52FC4C1', '2025-12-10 21:22:45', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(6, 2, 3, 18, 17, 20.000, 34.68, 6.40, 'BE1E0039F0E1', '2025-12-10 23:06:46', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(7, 2, 5, 26, 29, 15.000, 37.69, 8.70, '28BD4368DF28', '2025-12-10 23:07:14', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(8, 1, 2, 12, 11, 25.000, 34.89, 3.40, 'F3F6FDC39031', '2025-12-10 23:37:25', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(9, 1, 5, 28, 27, 1.000, 16.12, 3.90, 'A05B842C6F32', '2025-12-10 23:37:30', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(10, 1, 1, 7, 5, 20.000, 13.45, 8.20, '5C570435A40B', '2025-12-10 23:45:07', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(11, 1, 2, 12, 13, 14.000, 27.85, 4.50, '74AF25CBE2BB', '2025-12-10 23:56:26', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(12, 1, 5, 28, 26, 1.000, 18.10, 4.90, 'F08FBD9B5A40', '2025-12-10 23:56:32', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(13, 1, 5, NULL, NULL, 1.000, 8.90, NULL, '697BF70D4F5E', '2025-12-11 00:21:05', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(14, 1, 2, 11, 12, 1.000, 15.69, 3.40, '5C12CA9A2444', '2025-12-11 00:28:32', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(15, 2, 3, NULL, NULL, 1.000, 9.20, NULL, 'D0EF1CFA0C3D', '2025-12-11 00:51:10', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(16, 1, 1, NULL, NULL, 1.000, 4.75, NULL, '85294E7B1F26', '2025-12-11 01:36:06', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(17, 1, 3, NULL, NULL, 20.000, 27.50, NULL, '1B8D3E650FC3', '2025-12-11 01:43:38', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(18, 1, 5, NULL, NULL, 1.000, 8.90, NULL, 'E52192163F55', '2025-12-11 01:47:19', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(19, 1, 1, NULL, NULL, 1.000, 4.75, NULL, '72EB923A6942', '2025-12-11 01:49:08', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(20, 1, 1, NULL, NULL, 1.000, 9.75, NULL, '282F6ADD9436', '2025-12-11 01:51:53', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(21, 1, 1, 4, 8, 1.000, 8.70, NULL, '4143234C5D6F', '2025-12-11 02:24:26', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(22, 1, 6, 32, 33, 1.000, 10.25, NULL, 'B6500BD7BE6E', '2025-12-11 03:05:41', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(23, 1, 2, 33, 28, 1.000, 9.80, NULL, 'E65372640861', '2025-12-11 03:06:10', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(24, 1, 5, 33, 30, 1.000, 8.90, NULL, 'C9CA5F4BDE8F', '2025-12-11 03:09:28', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(25, 1, 6, 32, 34, 1.000, 10.25, NULL, 'E066182601F0', '2025-12-11 03:12:57', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(26, 1, 6, 8, 29, 1.000, 10.25, NULL, 'FFBA88B5EC8C', '2025-12-11 03:20:51', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL),
(27, 1, 6, 8, 29, 1.000, 10.25, NULL, '8D308F9FAFC0', '2025-12-11 03:23:49', 'pending', 'delayed', 'Журко Александр Сергеевич', 'у3у33у3у', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'card', ''),
(28, 1, 1, 35, 7, 1.000, 9.87, NULL, 'B21DD1FBD3B9', '2025-12-11 03:40:21', 'paid', 'delayed', 'Журко Александр Сергеевич', '2ууууууц', NULL, NULL, NULL, NULL, '2025-12-27', 0, 0, 0, 'card', ''),
(29, 1, 1, 32, 33, 1.000, 9.07, NULL, '2378BFE432F1', '2025-12-11 04:08:41', 'paid', 'in_transit', 'Журко Александр Сергеевич', 'ккккукуу', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'cash', ''),
(30, 1, 5, 27, 77, 1.000, 14.04, NULL, '4500771A939B', '2025-12-11 16:29:34', 'paid', 'delivered', 'Журко Александр Сергеевич', 'к3к3к', NULL, NULL, NULL, NULL, '0000-00-00', 0, 1, 1, 'cash', ''),
(31, 1, 5, 29, 79, 1.000, 14.22, NULL, '999BE30355BF', '2025-12-11 17:52:46', 'paid', 'processed', 'Журко Александр Сергеевич', 'епеееп', NULL, NULL, NULL, NULL, '2025-12-27', 1, 1, 1, 'card', ''),
(32, 1, 2, 8, 7, 25.000, 60.00, NULL, '3688F351906A', '2025-12-11 18:13:35', 'paid', 'processed', 'Журко Александр Сергеевич', 'ррпрпр', NULL, NULL, NULL, NULL, '2025-12-26', 1, 1, 0, 'card', ''),
(33, 1, 1, 45, 13, 15.000, 11.11, NULL, 'E1703B14B348', '2025-12-11 18:15:59', 'paid', 'processed', 'Журко Александр Сергеевич', '6666', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'cash', ''),
(34, 1, 5, 33, 66, 15.000, 21.93, NULL, 'AE35BCEEF03D', '2025-12-11 18:26:58', 'paid', 'out_for_delivery', 'Журко Александр Сергеевич', 'вувувув', NULL, NULL, NULL, NULL, '2025-12-13', 1, 0, 0, 'card', ''),
(35, 1, 6, 73, 30, 25.000, 33.82, NULL, '0C3B97658374', '2025-12-11 19:14:07', 'paid', 'processed', 'Журко Александр Сергеевич', 'fffefef', NULL, NULL, NULL, NULL, '2025-12-13', 1, 1, 0, 'card', 'оставить у двери'),
(36, 1, 1, 8, 6, 15.000, 7.37, NULL, 'F6995BB62470', '2025-12-11 20:01:39', 'paid', 'processed', 'Журко Александр Сергеевич', 'trrgr', NULL, NULL, NULL, NULL, '2025-12-14', 0, 0, 0, 'card', ''),
(37, 1, 1, 12, 13, 15.000, 11.88, NULL, 'C0EA3227C447', '2025-12-12 12:27:44', 'paid', 'cancelled', 'Журко Александр Сергеевич', 'grgr', NULL, NULL, NULL, NULL, '2025-12-17', 1, 1, 1, 'card', 'оставить у двери'),
(38, 2, 6, 32, 34, 15.000, 26.43, NULL, '07B52F4D89B0', '2025-12-12 12:53:21', 'paid', 'returned', 'Журко Александр Сергеевич', 'к3к3к', NULL, NULL, NULL, NULL, '2025-12-26', 1, 1, 1, 'cash', 'у2у2у2у'),
(39, 1, 6, 31, 78, 15.000, 26.43, NULL, 'B1B9C1114E7B', '2025-12-12 13:59:57', 'pending', 'created', 'Журко Александр Сергеевич', '444к', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'к3к3к3'),
(40, 1, 6, 32, 78, 15.000, 26.43, NULL, 'AF72317BA15A', '2025-12-12 14:00:10', 'pending', 'created', 'Журко Александр Сергеевич', '444к', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'к3к3к3'),
(41, 1, 6, 32, 78, 15.000, 26.43, NULL, '039DD009F679', '2025-12-12 14:02:38', 'pending', 'created', 'Журко Александр Сергеевич', '444к', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'к3к3к3'),
(42, 1, 1, 8, 3, 15.000, 12.34, NULL, 'EA3526982A1B', '2025-12-12 14:03:09', 'pending', 'created', 'Журко Александр Сергеевич', '44r4r', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'dwertyuil'),
(43, 1, 6, 74, 78, 14.000, 25.65, NULL, 'B5C9A194AF6E', '2025-12-12 14:09:23', 'paid', 'delivered', 'Журко Александр Сергеевич', 'фывапро', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'фывапро'),
(44, 1, 1, 12, 13, 15.000, 8.15, NULL, '4E7EA5569B19', '2025-12-12 14:27:30', 'paid', 'paid', 'Журко Александр Сергеевич', 'ывапролд', NULL, NULL, NULL, NULL, '2025-12-17', 1, 1, 1, 'cash', 'вапролд'),
(45, 1, 6, 73, 34, 15.000, 24.41, NULL, 'DF943F68B429', '2025-12-12 14:43:33', 'paid', 'paid', 'Журко Александр Сергеевич', 'укенг', NULL, NULL, NULL, NULL, '2025-12-26', 1, 1, 1, 'cash', 'вапролбрпав'),
(46, 1, 1, 7, 5, 15.000, 12.45, NULL, '35FF980A97F3', '2025-12-12 15:20:54', 'paid', 'delayed', 'Журко Александр Сергеевич', 'вапро', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', 'ваенгшщ'),
(47, 1, 6, 77, 73, 15.000, 24.41, NULL, '92D7F49D4DC4', '2025-12-12 18:13:21', 'paid', 'delivered', 'Журко Александр Сергеевич', '3456', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', '3456'),
(48, 1, 1, 33, 34, 1.000, 8.65, NULL, 'F6BBF0B976B1', '2025-12-12 19:54:45', 'paid', 'paid', 'Журко Александр Сергеевич', 'll', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', '234rth');

-- --------------------------------------------------------

--
-- Структура таблицы `routes`
--

CREATE TABLE `routes` (
  `from_office` int(11) NOT NULL,
  `to_office` int(11) NOT NULL,
  `distance_km` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `routes`
--

INSERT INTO `routes` (`from_office`, `to_office`, `distance_km`) VALUES
(1, 3, 266),
(1, 4, 238),
(1, 11, 221),
(1, 12, 333),
(1, 13, 297),
(1, 16, 242),
(1, 23, 451),
(1, 28, 525),
(1, 33, 423),
(1, 36, 45),
(1, 37, 100),
(1, 38, 150),
(1, 39, 170),
(1, 41, 280),
(1, 42, 120),
(1, 43, 40),
(2, 17, 198),
(2, 19, 499),
(2, 21, 243),
(2, 22, 537),
(3, 4, 369),
(3, 5, 310),
(3, 10, 413),
(3, 12, 144),
(3, 15, 257),
(3, 16, 639),
(3, 19, 77),
(3, 22, 538),
(3, 24, 295),
(3, 26, 547),
(3, 27, 459),
(3, 34, 301),
(4, 15, 584),
(4, 16, 439),
(4, 17, 236),
(4, 19, 147),
(4, 24, 561),
(4, 28, 73),
(4, 29, 523),
(4, 32, 164),
(5, 11, 79),
(5, 16, 326),
(5, 19, 637),
(5, 22, 222),
(5, 23, 423),
(5, 25, 476),
(5, 27, 95),
(5, 28, 519),
(5, 30, 163),
(6, 11, 622),
(6, 17, 62),
(6, 20, 628),
(6, 21, 612),
(6, 22, 135),
(6, 23, 210),
(6, 26, 454),
(6, 29, 525),
(6, 33, 505),
(6, 34, 435),
(7, 11, 415),
(7, 25, 499),
(7, 27, 474),
(7, 28, 249),
(8, 12, 488),
(8, 13, 300),
(8, 21, 170),
(8, 26, 538),
(8, 27, 433),
(8, 28, 548),
(8, 30, 59),
(8, 31, 625),
(8, 33, 118),
(9, 10, 204),
(9, 11, 320),
(9, 13, 443),
(9, 19, 537),
(9, 27, 82),
(9, 34, 51),
(10, 14, 193),
(10, 23, 525),
(10, 24, 490),
(10, 25, 540),
(10, 26, 605),
(10, 28, 624),
(10, 29, 394),
(10, 33, 232),
(11, 12, 422),
(11, 17, 584),
(11, 18, 77),
(12, 16, 387),
(12, 19, 359),
(12, 22, 172),
(12, 24, 377),
(12, 25, 405),
(12, 26, 255),
(12, 27, 153),
(12, 29, 481),
(12, 30, 646),
(12, 34, 512),
(13, 22, 253),
(13, 28, 483),
(14, 15, 631),
(14, 25, 454),
(14, 31, 159),
(15, 25, 454),
(15, 26, 396),
(15, 30, 165),
(15, 32, 617),
(16, 29, 250),
(16, 34, 521),
(17, 22, 203),
(17, 23, 266),
(17, 27, 450),
(17, 28, 560),
(17, 30, 392),
(17, 32, 502),
(17, 33, 608),
(18, 26, 265),
(18, 28, 334),
(18, 31, 164),
(19, 20, 63),
(19, 24, 572),
(19, 25, 609),
(19, 30, 353),
(19, 32, 500),
(20, 24, 236),
(20, 26, 636),
(20, 28, 525),
(20, 31, 385),
(20, 33, 332),
(21, 25, 524),
(21, 30, 138),
(21, 34, 258),
(22, 23, 179),
(22, 26, 192),
(22, 27, 600),
(22, 28, 226),
(22, 31, 261),
(24, 30, 543),
(25, 30, 627),
(25, 33, 385),
(26, 34, 476),
(27, 29, 579),
(27, 34, 107),
(28, 29, 424),
(28, 31, 237),
(28, 34, 221),
(29, 30, 498),
(29, 33, 365),
(30, 31, 537),
(31, 32, 555),
(31, 33, 66),
(35, 36, 45),
(35, 37, 100),
(35, 38, 150),
(35, 39, 170),
(35, 40, 200),
(35, 41, 280),
(35, 42, 120),
(35, 43, 40),
(35, 44, 110),
(35, 45, 60),
(36, 1, 45),
(36, 35, 45),
(36, 37, 120),
(36, 38, 80),
(36, 39, 220),
(36, 41, 350),
(36, 42, 180),
(36, 43, 120),
(37, 1, 100),
(37, 35, 100),
(37, 36, 120),
(37, 38, 180),
(37, 39, 150),
(37, 41, 250),
(37, 42, 150),
(37, 43, 70),
(38, 1, 150),
(38, 35, 150),
(38, 36, 80),
(38, 37, 180),
(38, 39, 200),
(38, 41, 400),
(38, 42, 250),
(38, 43, 160),
(39, 1, 170),
(39, 35, 170),
(39, 36, 220),
(39, 37, 150),
(39, 38, 200),
(39, 40, 80),
(39, 41, 300),
(39, 42, 200),
(39, 43, 180),
(40, 1, 200),
(40, 35, 200),
(40, 39, 80),
(40, 41, 350),
(40, 42, 220),
(40, 43, 200),
(41, 1, 280),
(41, 35, 280),
(41, 36, 350),
(41, 37, 250),
(41, 38, 400),
(41, 39, 300),
(41, 40, 350),
(41, 42, 120),
(41, 43, 250),
(42, 1, 120),
(42, 35, 120),
(42, 36, 180),
(42, 37, 150),
(42, 38, 250),
(42, 39, 200),
(42, 40, 220),
(42, 41, 120),
(42, 43, 150),
(43, 1, 40),
(43, 35, 40),
(43, 36, 120),
(43, 37, 70),
(43, 38, 160),
(43, 39, 180),
(43, 40, 200),
(43, 41, 250),
(43, 42, 150),
(44, 1, 110),
(45, 1, 60);

-- --------------------------------------------------------

--
-- Структура таблицы `tracking_status_history`
--

CREATE TABLE `tracking_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tracking_status_history`
--

INSERT INTO `tracking_status_history` (`id`, `order_id`, `status`, `description`, `created_at`) VALUES
(1, 28, 'processed', 'Заказ оплачен и обработан', '2025-12-11 00:40:39'),
(2, 29, 'processed', 'Заказ оплачен и обработан', '2025-12-11 01:08:44'),
(3, 30, 'processed', 'Заказ оплачен и обработан', '2025-12-11 13:30:16'),
(4, 31, 'processed', 'Заказ оплачен и обработан', '2025-12-11 14:52:57'),
(5, 32, 'processed', 'Заказ оплачен и обработан', '2025-12-11 15:13:45'),
(6, 33, 'processed', 'Заказ оплачен и обработан', '2025-12-11 15:16:02'),
(7, 34, 'processed', 'Заказ оплачен и обработан', '2025-12-11 15:28:02'),
(8, 35, 'processed', 'Заказ оплачен и обработан', '2025-12-11 16:14:24'),
(9, 36, 'processed', 'Заказ оплачен и обработан', '2025-12-11 17:02:01'),
(10, 37, 'processed', 'Заказ оплачен и обработан', '2025-12-12 09:28:06'),
(11, 38, 'processed', 'Заказ оплачен и обработан', '2025-12-12 09:53:25'),
(12, 41, 'created', 'Заказ создан', '2025-12-12 11:02:38'),
(13, 42, 'created', 'Заказ создан', '2025-12-12 11:03:09'),
(14, 43, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 11:09:23'),
(15, 43, 'paid', 'Заказ оплачен и обработан', '2025-12-12 11:09:39'),
(16, 44, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 11:27:30'),
(17, 44, 'paid', 'Заказ оплачен и обработан', '2025-12-12 11:27:34'),
(18, 45, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 11:43:33'),
(19, 45, 'paid', 'Заказ оплачен и обработан', '2025-12-12 11:43:35'),
(20, 46, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 12:20:55'),
(21, 46, 'paid', 'Заказ оплачен и обработан', '2025-12-12 12:21:03'),
(22, 46, 'delayed', 'Статус изменен с \'paid\' на \'delayed\'', '2025-12-12 12:23:05'),
(23, 47, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 15:13:21'),
(24, 47, 'paid', 'Заказ оплачен и обработан', '2025-12-12 15:13:24'),
(25, 47, 'sort_center', 'Статус изменен с \'paid\' на \'sort_center\'', '2025-12-12 16:29:23'),
(26, 47, 'delayed', 'Статус изменен с \'sort_center\' на \'delayed\'', '2025-12-12 16:53:44'),
(27, 47, 'cancelled', 'Статус изменен с \'delayed\' на \'cancelled\'', '2025-12-12 16:53:52'),
(28, 48, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 16:54:45'),
(29, 48, 'paid', 'Заказ оплачен и обработан', '2025-12-12 16:54:48');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'admin', 'admin', 'Администратор', NULL, NULL, 'admin', '2025-12-04 03:19:52'),
(2, 'Aliaksandr', '123456', 'Александр', 'szhurko005@gmail.com', NULL, 'user', '2025-12-04 03:41:51');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `calculated_routes`
--
ALTER TABLE `calculated_routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_route` (`from_office_id`,`to_office_id`),
  ADD KEY `to_office_id` (`to_office_id`);

--
-- Индексы таблицы `carriers`
--
ALTER TABLE `carriers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carrier_id` (`carrier_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `track_number` (`track_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carrier_id` (`carrier_id`),
  ADD KEY `from_office` (`from_office`),
  ADD KEY `to_office` (`to_office`);

--
-- Индексы таблицы `tracking_status_history`
--
ALTER TABLE `tracking_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `calculated_routes`
--
ALTER TABLE `calculated_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `carriers`
--
ALTER TABLE `carriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT для таблицы `tracking_status_history`
--
ALTER TABLE `tracking_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `calculated_routes`
--
ALTER TABLE `calculated_routes`
  ADD CONSTRAINT `calculated_routes_ibfk_1` FOREIGN KEY (`from_office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calculated_routes_ibfk_2` FOREIGN KEY (`to_office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `offices`
--
ALTER TABLE `offices`
  ADD CONSTRAINT `offices_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`from_office`) REFERENCES `offices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`to_office`) REFERENCES `offices` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `tracking_status_history`
--
ALTER TABLE `tracking_status_history`
  ADD CONSTRAINT `tracking_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
