-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2025 a las 04:27:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tripmatch`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `group_messages`
--

CREATE TABLE `group_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `trip_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `group_messages`
--

INSERT INTO `group_messages` (`id`, `trip_id`, `user_id`, `message`, `created_at`) VALUES
(35, 116, 1, '¡Hola a todos! ¿Están listos para Japón?', '2026-03-25 10:00:00'),
(36, 116, 3, 'Nací listo. ¿Alguien sabe cómo llegar del aeropuerto a la ciudad?', '2026-03-25 10:05:00'),
(37, 116, 13, 'Hola John, lo mejor es tomar el Narita Express. Nos vemos allá.', '2026-03-25 10:15:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `group_typing`
--

CREATE TABLE `group_typing` (
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_typing_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `trip_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `when_at` datetime DEFAULT NULL,
  `where_text` varchar(255) DEFAULT NULL,
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT 6,
  `min_people` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `plans`
--

INSERT INTO `plans` (`id`, `trip_id`, `title`, `when_at`, `where_text`, `capacity`, `min_people`, `created_by`, `created_at`) VALUES
(214, 116, 'Cena de Sushi en Tsukiji', '2026-04-02 19:00:00', 'Mercado de Tsukiji, Puesto 4', 6, 2, 13, '2025-11-23 23:44:16'),
(215, 116, 'Visita al templo Senso-ji', '2026-04-03 09:00:00', 'Asakusa Gate', 10, 1, 1, '2025-11-23 23:44:16'),
(216, 117, 'Excursión a Chichén Itzá', '2026-02-12 07:00:00', 'Lobby del Hotel Riu', 4, 3, 2, '2025-11-23 23:44:16'),
(217, 117, 'Noche de Fiesta en Coco Bongo', '2026-02-14 23:00:00', 'Entrada principal', 8, 2, 11, '2025-11-23 23:44:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_participants`
--

CREATE TABLE `plan_participants` (
  `plan_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `plan_participants`
--

INSERT INTO `plan_participants` (`plan_id`, `user_id`) VALUES
(214, 1),
(214, 3),
(214, 13),
(216, 2),
(216, 4),
(216, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tips`
--

CREATE TABLE `tips` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `trip_id` int(10) UNSIGNED DEFAULT NULL,
  `text` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tips`
--

INSERT INTO `tips` (`id`, `user_id`, `trip_id`, `text`, `url`, `created_at`) VALUES
(313, 11, 117, 'Para el mejor cambio de moneda, eviten el aeropuerto. Usen los cajeros del centro.', NULL, '2025-11-23 23:44:16'),
(314, 13, 116, 'Recuerden comprar la tarjeta Suica para el tren, sirve para todo.', 'https://www.jreast.co.jp/e/pass/suica.html', '2025-11-23 23:44:16'),
(315, 2, NULL, 'Siempre lleven un adaptador universal, me salvó la vida en Europa.', 'https://amazon.com/adapter-example', '2025-11-23 23:44:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trips`
--

CREATE TABLE `trips` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `city` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trips`
--

INSERT INTO `trips` (`id`, `user_id`, `city`, `start_date`, `end_date`, `created_at`) VALUES
(116, 1, 'Tokio, Japón', '2026-04-01', '2026-04-15', '2025-11-23 23:44:16'),
(117, 2, 'Cancún, México', '2026-02-10', '2026-02-20', '2025-11-23 23:44:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trip_participants`
--

CREATE TABLE `trip_participants` (
  `trip_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trip_participants`
--

INSERT INTO `trip_participants` (`trip_id`, `user_id`, `created_at`) VALUES
(116, 1, '2025-11-23 23:44:16'),
(116, 2, '2025-11-23 23:44:44'),
(116, 3, '2025-11-23 23:44:16'),
(116, 13, '2025-11-23 23:44:16'),
(117, 2, '2025-11-23 23:44:16'),
(117, 4, '2025-11-23 23:44:16'),
(117, 11, '2025-11-23 23:44:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `show_bio` tinyint(1) NOT NULL DEFAULT 1,
  `sos_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `share_location` tinyint(1) NOT NULL DEFAULT 0,
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`languages`)),
  `not_traveling` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `country`, `bio`, `show_bio`, `sos_enabled`, `share_location`, `languages`, `not_traveling`, `phone`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'Luis Postorivo', 'luis@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'Argentina', 'Viajero frecuente y amante de la fotografía.ee', 1, 0, 0, '[\"Español\",\"Inglés\"]', 0, '+54911223344', '2025-11-21 23:11:32', '2025-11-22 01:03:15', NULL),
(2, 'Maria García', 'maria@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'España', 'Busco compañeros para viajes de aventura.', 1, 0, 1, '[\"Español\", \"Francés\"]', 0, '+34600112233', '2025-11-21 23:11:32', '2025-11-24 00:21:20', NULL),
(3, 'John Smith', 'john@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'USA', 'Digital nomad exploring South America.', 1, 0, 0, '[\"Inglés\"]', 1, '+15550199', '2025-11-21 23:11:32', '2025-11-22 01:16:51', NULL),
(4, 'Ana Souza', 'ana@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'Brasil', 'Adoro praias e cultura local.', 1, 0, 0, '[\"Portugués\", \"Español\"]', 0, '+5521998877', '2025-11-21 23:11:32', '2025-11-22 01:16:48', NULL),
(11, 'Carlos Ruiz', 'carlos@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'México', 'Fotógrafo de paisajes y amante de la comida picante.', 1, 0, 0, '[\"Español\", \"Inglés\"]', 0, '+525512345678', '2025-11-23 23:44:16', '2025-11-23 23:44:16', NULL),
(12, 'Sophie Dubois', 'sophie@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'Francia', 'Buscando aventuras en América Latina.', 1, 0, 0, '[\"Francés\", \"Español\", \"Inglés\"]', 0, '+33612345678', '2025-11-23 23:44:16', '2025-11-23 23:44:16', NULL),
(13, 'Kenji Sato', 'kenji@test.com', '$2y$10$yJ/KpPebjKZrlfvwplEN2Og2O0nxnnn2352l.y4sy.d92Kk7zHmPW', 'Japón', 'Viajero solitario que busca grupos para cenar.', 1, 0, 0, '[\"Japonés\", \"Inglés\"]', 1, '+819012345678', '2025-11-23 23:44:16', '2025-11-23 23:44:16', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `group_typing`
--
ALTER TABLE `group_typing`
  ADD PRIMARY KEY (`trip_id`,`user_id`);

--
-- Indices de la tabla `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plans_user` (`created_by`),
  ADD KEY `plans_trip_id_index` (`trip_id`);

--
-- Indices de la tabla `plan_participants`
--
ALTER TABLE `plan_participants`
  ADD PRIMARY KEY (`plan_id`,`user_id`),
  ADD KEY `fk_pp_user` (`user_id`);

--
-- Indices de la tabla `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tips_user` (`user_id`),
  ADD KEY `tips_trip_id_index` (`trip_id`);

--
-- Indices de la tabla `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trips_user` (`user_id`);

--
-- Indices de la tabla `trip_participants`
--
ALTER TABLE `trip_participants`
  ADD PRIMARY KEY (`trip_id`,`user_id`),
  ADD KEY `idx_tp_user` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT de la tabla `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT de la tabla `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `fk_plans_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plans_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `plan_participants`
--
ALTER TABLE `plan_participants`
  ADD CONSTRAINT `fk_pp_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tips`
--
ALTER TABLE `tips`
  ADD CONSTRAINT `fk_tips_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tips_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `fk_trips_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trip_participants`
--
ALTER TABLE `trip_participants`
  ADD CONSTRAINT `fk_tp_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
