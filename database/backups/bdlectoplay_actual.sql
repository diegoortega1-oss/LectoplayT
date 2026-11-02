-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2026 a las 03:42:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdlectoplay`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad`
--

CREATE TABLE `actividad` (
  `id_actividad` bigint(20) UNSIGNED NOT NULL,
  `nivel_id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `contenido` text NOT NULL,
  `estado` enum('ACTIVA','INACTIVA','BORRADOR') NOT NULL DEFAULT 'BORRADOR',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividad`
--

INSERT INTO `actividad` (`id_actividad`, `nivel_id`, `titulo`, `contenido`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'El zorro y las uvas', 'Había una vez un zorro muy hambriento que vio un racimo de uvas...', 'ACTIVA', '2026-10-11 20:16:13', '2026-10-11 20:16:13', NULL),
(2, 2, 'El ciclo del agua', 'El ciclo del agua es el proceso de circulación del agua entre los distintos compartimentos...', 'ACTIVA', '2026-10-11 20:16:13', '2026-10-11 20:16:13', NULL),
(3, 1, 'La tortuga y la liebre', 'Una liebre se burlaba de una tortuga por caminar lentamente. La tortuga la retó a una carrera. La liebre corrió rápidamente y, al sentirse ganadora, decidió dormir. Mientras tanto, la tortuga continuó caminando sin detenerse y llegó primero a la meta.', 'ACTIVA', '2026-10-15 00:58:08', '2026-10-15 00:58:08', NULL),
(4, 1, 'El perro y su reflejo', 'Un perro llevaba un hueso en la boca y cruzó un puente sobre un río. Al mirar el agua, vio su reflejo y creyó que era otro perro con un hueso más grande. Intentó quitárselo y abrió la boca, dejando caer el suyo al agua.', 'ACTIVA', '2026-10-15 00:58:08', '2026-10-15 00:58:08', NULL),
(5, 2, 'El cuidado del medio ambiente', 'Cuidar el medio ambiente implica reducir los residuos, reutilizar objetos y reciclar materiales. Ahorrar agua y energía también ayuda a proteger los recursos naturales. Las pequeñas acciones de cada persona pueden contribuir a un planeta más limpio.', 'ACTIVA', '2026-10-15 00:58:08', '2026-10-15 00:58:08', NULL),
(6, 2, 'Los volcanes', 'Los volcanes son aberturas de la corteza terrestre por las que pueden salir lava, gases y cenizas. Algunos permanecen inactivos durante largos periodos. La actividad volcánica puede modificar el paisaje y contribuir a la formación de nuevas rocas.', 'ACTIVA', '2026-10-15 00:58:08', '2026-10-15 00:58:08', NULL),
(7, 3, 'La importancia de la lectura crítica', 'La lectura crítica consiste en analizar un texto, identificar sus argumentos y evaluar las evidencias que presenta. No basta con comprender las palabras: también es necesario reconocer la intención del autor, distinguir hechos de opiniones y contrastar la información con otras fuentes.', 'ACTIVA', '2026-10-15 00:58:08', '2026-10-15 00:58:08', NULL),
(8, 3, 'La tecnología en la educación', 'Las herramientas digitales permiten acceder a materiales educativos y colaborar a distancia. Sin embargo, su utilidad depende de cómo se empleen. Para aprovecharlas, es importante verificar las fuentes, proteger los datos personales y mantener un equilibrio entre las actividades digitales y las presenciales.', 'BORRADOR', '2026-10-15 00:58:08', '2026-10-15 00:58:08', NULL),
(9, 1, 'El gato y el ratón', 'Un ratón vivía en un pequeño agujero de una casa. Cada noche salía a buscar comida en la cocina. Un día encontró un trozo de queso y comenzó a comerlo. De pronto, escuchó los pasos de un gato y corrió rápidamente hacia su agujero. El gato intentó atraparlo, pero el ratón logró esconderse. Desde entonces, el ratón siempre estaba atento antes de salir.', 'ACTIVA', '2026-10-15 21:33:40', '2026-10-15 21:33:40', NULL),
(10, 1, 'El paseo de Ana al parque', 'Ana fue al parque con su hermano Luis una mañana soleada. Llevaron una pelota, dos botellas de agua y unos sándwiches. Primero jugaron a la pelota cerca de los árboles. Después descansaron en una banca y comieron sus sándwiches. Antes de regresar a casa, recogieron los envoltorios y los depositaron en un basurero.', 'ACTIVA', '2026-10-15 21:33:40', '2026-10-15 21:33:40', NULL),
(11, 1, 'La gallina de los huevos de oro', 'Un campesino tenía una gallina que ponía un huevo de oro cada día. Gracias a esos huevos, el campesino podía comprar alimentos y mejorar su casa. Sin embargo, quiso obtener toda la riqueza de una sola vez. Pensó que la gallina tenía muchos huevos de oro dentro de su cuerpo y decidió abrirla. Al hacerlo, descubrió que no había ningún huevo. Desde ese día perdió la gallina y los huevos que recibía diariamente.', 'ACTIVA', '2026-10-15 21:33:40', '2026-10-15 21:33:40', NULL),
(12, 2, 'El ciclo del agua en la naturaleza', 'El ciclo del agua es un proceso natural que permite que el agua circule continuamente por la Tierra. El calor del Sol provoca la evaporación del agua de mares, ríos y lagos. El vapor asciende, se enfría y forma pequeñas gotas que se agrupan en las nubes mediante la condensación. Cuando las gotas crecen, el agua cae como precipitación en forma de lluvia, nieve o granizo. Finalmente, el agua llega al suelo, a los ríos y a los océanos, donde el ciclo vuelve a comenzar.', 'ACTIVA', '2026-10-15 21:33:40', '2026-10-15 21:33:40', NULL),
(13, 2, 'La importancia de reciclar', 'El reciclaje consiste en transformar materiales usados para convertirlos en nuevos productos. El papel, el vidrio, algunos plásticos y los metales pueden recuperarse si se separan correctamente. Esta práctica ayuda a reducir la cantidad de basura y a aprovechar mejor los recursos naturales. Sin embargo, reciclar no es la única solución: también es importante reducir el consumo innecesario y reutilizar los objetos que todavía sirven. La participación de las familias, las escuelas y las empresas permite obtener mejores resultados.', 'ACTIVA', '2026-10-15 21:33:40', '2026-10-15 21:33:40', NULL),
(14, 2, 'Los bosques y su equilibrio natural', 'Los bosques son ecosistemas donde conviven árboles, plantas, animales, hongos y microorganismos. Los árboles absorben dióxido de carbono y liberan oxígeno durante la fotosíntesis. Además, sus raíces ayudan a mantener el suelo y a disminuir la erosión. Muchos animales encuentran alimento y refugio entre la vegetación. La tala excesiva y los incendios pueden destruir estos hábitats y afectar el equilibrio natural. Para proteger los bosques, es necesario prevenir los incendios, controlar la tala y promover la recuperación de las áreas dañadas.', 'ACTIVA', '2026-10-15 21:33:40', '2026-10-15 21:33:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_intento`
--

CREATE TABLE `actividad_intento` (
  `id_intento` bigint(20) UNSIGNED NOT NULL,
  `estudiante_id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` bigint(20) UNSIGNED NOT NULL,
  `puntaje` decimal(5,2) DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('EN_PROCESO','FINALIZADO') NOT NULL DEFAULT 'EN_PROCESO',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `puntos_obtenidos` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividad_intento`
--

INSERT INTO `actividad_intento` (`id_intento`, `estudiante_id`, `actividad_id`, `puntaje`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`, `updated_at`, `puntos_obtenidos`) VALUES
(1, 2, 1, 100.00, '2026-10-11 20:16:13', NULL, 'FINALIZADO', '2026-10-11 20:16:13', '2026-10-11 20:16:13', NULL),
(2, 1, 1, 0.00, '2026-10-15 05:49:43', '2026-10-15 05:50:27', 'FINALIZADO', '2026-10-15 05:49:43', '2026-10-15 05:50:27', NULL),
(3, 1, 1, 0.00, '2026-10-15 05:50:40', '2026-10-15 05:50:47', 'FINALIZADO', '2026-10-15 05:50:40', '2026-10-15 05:50:47', NULL),
(4, 1, 3, 50.00, '2026-10-15 06:07:12', '2026-10-15 06:07:30', 'FINALIZADO', '2026-10-15 06:07:12', '2026-10-15 06:07:30', NULL),
(5, 1, 3, NULL, '2026-10-15 06:07:38', NULL, 'EN_PROCESO', '2026-10-15 06:07:38', '2026-10-15 06:07:38', NULL),
(6, 1, 3, NULL, '2026-10-16 01:42:08', NULL, 'EN_PROCESO', '2026-10-16 01:42:08', '2026-10-16 01:42:08', NULL),
(7, 1, 3, 50.00, '2026-10-16 01:42:38', '2026-10-16 03:04:33', 'FINALIZADO', '2026-10-16 01:42:38', '2026-10-16 03:04:33', NULL),
(8, 1, 4, 100.00, '2026-10-16 02:08:53', '2026-10-16 02:09:06', 'FINALIZADO', '2026-10-16 02:08:53', '2026-10-16 02:09:06', NULL),
(9, 1, 4, NULL, '2026-10-16 02:09:24', NULL, 'EN_PROCESO', '2026-10-16 02:09:24', '2026-10-16 02:09:24', NULL),
(10, 1, 3, 100.00, '2026-10-16 02:15:32', '2026-10-16 02:15:39', 'FINALIZADO', '2026-10-16 02:15:32', '2026-10-16 02:15:39', NULL),
(11, 1, 3, 0.00, '2026-10-16 02:16:02', '2026-10-16 02:16:09', 'FINALIZADO', '2026-10-16 02:16:02', '2026-10-16 02:16:09', NULL),
(12, 1, 1, 0.00, '2026-10-16 02:16:32', '2026-10-16 02:16:37', 'FINALIZADO', '2026-10-16 02:16:32', '2026-10-16 02:16:37', NULL),
(13, 2, 5, 50.00, '2026-10-16 02:17:11', '2026-10-16 02:17:16', 'FINALIZADO', '2026-10-16 02:17:11', '2026-10-16 02:17:16', NULL),
(14, 2, 6, 50.00, '2026-10-16 02:17:43', '2026-10-16 02:17:49', 'FINALIZADO', '2026-10-16 02:17:43', '2026-10-16 02:17:49', NULL),
(15, 1, 11, 20.00, '2026-10-16 02:34:56', '2026-10-16 02:35:18', 'FINALIZADO', '2026-10-16 02:34:56', '2026-10-16 02:35:18', NULL),
(16, 1, 4, 100.00, '2026-10-16 02:36:00', '2026-10-16 02:36:04', 'FINALIZADO', '2026-10-16 02:36:00', '2026-10-16 02:36:04', NULL),
(17, 2, 6, 100.00, '2026-10-16 02:37:55', '2026-10-16 02:38:02', 'FINALIZADO', '2026-10-16 02:37:55', '2026-10-16 02:38:02', NULL),
(18, 1, 11, 30.00, '2026-10-16 03:02:32', '2026-10-16 03:02:49', 'FINALIZADO', '2026-10-16 03:02:32', '2026-10-16 03:02:49', 0),
(19, 1, 4, 100.00, '2026-10-16 03:04:11', '2026-10-16 03:04:16', 'FINALIZADO', '2026-10-16 03:04:11', '2026-10-16 03:04:16', 0),
(20, 1, 1, 0.00, '2026-10-16 03:05:56', '2026-10-16 03:06:01', 'FINALIZADO', '2026-10-16 03:05:56', '2026-10-16 03:06:01', 0),
(21, 1, 4, 0.00, '2026-10-16 03:06:13', '2026-10-16 03:06:18', 'FINALIZADO', '2026-10-16 03:06:13', '2026-10-16 03:06:18', 0),
(22, 1, 4, 100.00, '2026-10-16 03:06:57', '2026-10-16 03:07:04', 'FINALIZADO', '2026-10-16 03:06:57', '2026-10-16 03:07:04', 0),
(23, 1, 9, 40.00, '2026-10-16 03:11:57', '2026-10-16 03:12:20', 'FINALIZADO', '2026-10-16 03:11:57', '2026-10-16 03:12:20', 20),
(24, 1, 9, 0.00, '2026-10-16 03:14:34', '2026-10-16 03:14:50', 'FINALIZADO', '2026-10-16 03:14:34', '2026-10-16 03:14:50', 0),
(25, 1, 9, 20.00, '2026-10-16 03:15:03', '2026-10-16 03:15:12', 'FINALIZADO', '2026-10-16 03:15:03', '2026-10-16 03:15:12', 0),
(26, 1, 9, 0.00, '2026-10-16 03:16:19', '2026-10-16 03:16:25', 'FINALIZADO', '2026-10-16 03:16:19', '2026-10-16 03:16:25', 0),
(27, 1, 9, 10.00, '2026-10-16 03:17:40', '2026-10-16 03:17:46', 'FINALIZADO', '2026-10-16 03:17:40', '2026-10-16 03:17:46', 0),
(28, 1, 9, 20.00, '2026-10-16 03:23:28', '2026-10-16 03:23:47', 'FINALIZADO', '2026-10-16 03:23:28', '2026-10-16 03:23:47', 0),
(29, 1, 10, 0.00, '2026-10-16 03:41:22', '2026-10-16 03:41:45', 'FINALIZADO', '2026-10-16 03:41:22', '2026-10-16 03:41:45', 0),
(30, 2, 12, 30.00, '2026-10-16 03:54:35', '2026-10-16 03:55:01', 'FINALIZADO', '2026-10-16 03:54:35', '2026-10-16 03:55:01', 15),
(31, 2, 12, 30.00, '2026-10-16 03:56:02', '2026-10-16 03:56:20', 'FINALIZADO', '2026-10-16 03:56:02', '2026-10-16 03:56:20', 0),
(32, 1, 10, NULL, '2026-10-26 03:25:17', NULL, 'EN_PROCESO', '2026-10-26 03:25:17', '2026-10-26 03:25:17', NULL),
(33, 1, 4, NULL, '2026-10-26 11:47:06', NULL, 'EN_PROCESO', '2026-10-26 11:47:06', '2026-10-26 11:47:06', NULL),
(34, 9, 11, 20.00, '2026-10-26 11:50:48', '2026-10-26 11:51:24', 'FINALIZADO', '2026-10-26 11:50:48', '2026-10-26 11:51:24', 10),
(35, 9, 11, 10.00, '2026-10-26 11:52:27', '2026-10-26 11:52:50', 'FINALIZADO', '2026-10-26 11:52:27', '2026-10-26 11:52:50', 0),
(36, 7, 9, 30.00, '2026-11-01 23:37:37', '2026-11-01 23:37:58', 'FINALIZADO', '2026-11-01 23:37:37', '2026-11-01 23:37:58', 15),
(37, 7, 10, 0.00, '2026-11-01 23:38:30', '2026-11-01 23:38:46', 'FINALIZADO', '2026-11-01 23:38:30', '2026-11-01 23:38:46', 0),
(38, 7, 11, 10.00, '2026-11-01 23:39:27', '2026-11-01 23:39:44', 'FINALIZADO', '2026-11-01 23:39:27', '2026-11-01 23:39:44', 5),
(39, 7, 4, 100.00, '2026-11-02 00:47:55', '2026-11-02 00:48:43', 'FINALIZADO', '2026-11-02 00:47:55', '2026-11-02 00:48:43', 10),
(40, 7, 1, 100.00, '2026-11-02 01:01:59', '2026-11-02 01:02:15', 'FINALIZADO', '2026-11-02 01:01:59', '2026-11-02 01:02:15', 5),
(41, 7, 3, 100.00, '2026-11-02 01:02:27', '2026-11-02 01:02:57', 'FINALIZADO', '2026-11-02 01:02:27', '2026-11-02 01:02:57', 10),
(42, 7, 12, 30.00, '2026-11-02 01:16:25', '2026-11-02 01:16:59', 'FINALIZADO', '2026-11-02 01:16:25', '2026-11-02 01:16:59', 15),
(43, 7, 13, 20.00, '2026-11-02 01:17:59', '2026-11-02 01:18:17', 'FINALIZADO', '2026-11-02 01:17:59', '2026-11-02 01:18:17', 10),
(44, 7, 14, 30.00, '2026-11-02 01:18:53', '2026-11-02 01:19:13', 'FINALIZADO', '2026-11-02 01:18:53', '2026-11-02 01:19:13', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adaptacion_nivel`
--

CREATE TABLE `adaptacion_nivel` (
  `id_adaptacion` bigint(20) UNSIGNED NOT NULL,
  `estudiante_id` bigint(20) UNSIGNED NOT NULL,
  `nivel_anterior_id` bigint(20) UNSIGNED NOT NULL,
  `nivel_resultante_id` bigint(20) UNSIGNED NOT NULL,
  `decision` enum('SUBIR','MANTENER','BAJAR') NOT NULL,
  `promedio` decimal(5,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `adaptacion_nivel`
--

INSERT INTO `adaptacion_nivel` (`id_adaptacion`, `estudiante_id`, `nivel_anterior_id`, `nivel_resultante_id`, `decision`, `promedio`, `fecha`, `created_at`, `updated_at`) VALUES
(2, 7, 1, 1, 'MANTENER', 13.33, '2026-11-02 00:48:43', '2026-11-02 00:48:43', '2026-11-02 00:48:43'),
(3, 7, 1, 2, 'SUBIR', 100.00, '2026-11-02 01:02:57', '2026-11-02 01:02:57', '2026-11-02 01:02:57'),
(4, 7, 2, 1, 'BAJAR', 26.67, '2026-11-02 01:19:13', '2026-11-02 01:19:13', '2026-11-02 01:19:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adaptacion_nivel_actividad`
--

CREATE TABLE `adaptacion_nivel_actividad` (
  `adaptacion_id` bigint(20) UNSIGNED NOT NULL,
  `estudiante_id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` bigint(20) UNSIGNED NOT NULL,
  `intento_id` bigint(20) UNSIGNED NOT NULL,
  `puntaje` decimal(5,2) NOT NULL
) ;

--
-- Volcado de datos para la tabla `adaptacion_nivel_actividad`
--

INSERT INTO `adaptacion_nivel_actividad` (`adaptacion_id`, `estudiante_id`, `actividad_id`, `intento_id`, `puntaje`) VALUES
(2, 7, 9, 36, 30.00),
(2, 7, 10, 37, 0.00),
(2, 7, 11, 38, 10.00),
(3, 7, 1, 40, 100.00),
(3, 7, 3, 41, 100.00),
(3, 7, 4, 39, 100.00),
(4, 7, 12, 42, 30.00),
(4, 7, 13, 43, 20.00),
(4, 7, 14, 44, 30.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_administrador` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_administrador`, `id_usuario`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuestionario_diagnostico`
--

CREATE TABLE `cuestionario_diagnostico` (
  `id_cuestionario` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `estado` enum('BORRADOR','ACTIVO','CERRADO') NOT NULL DEFAULT 'BORRADOR',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuestionario_diagnostico`
--

INSERT INTO `cuestionario_diagnostico` (`id_cuestionario`, `nombre`, `version`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Diagnóstico inicial de comprensión lectora', 1, 'ACTIVO', '2026-10-25 18:24:43', '2026-10-25 20:21:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

CREATE TABLE `docente` (
  `id_docente` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `docente`
--

INSERT INTO `docente` (`id_docente`, `id_usuario`, `nombre`, `apellido`, `created_at`, `updated_at`) VALUES
(1, 2, 'Elena', 'Martínez', '2026-10-11 20:16:13', '2026-10-11 20:16:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `id_estudiante` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `nivel_actual_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiante`
--

INSERT INTO `estudiante` (`id_estudiante`, `id_usuario`, `nombre`, `apellido`, `nivel_actual_id`, `created_at`, `updated_at`) VALUES
(1, 3, 'Carlos', 'Gómez', 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 4, 'María', 'López', 2, '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(7, 11, 'Estudiante', 'Diagnóstico', 1, '2026-10-25 21:17:09', '2026-11-02 01:19:13'),
(8, 12, 'Estudiante', 'Prueba 02', 1, '2026-10-25 22:32:51', '2026-10-26 03:42:57'),
(9, 13, 'Estudiante', 'Prueba 03', 1, '2026-10-26 06:44:01', '2026-10-26 11:49:29'),
(10, 14, 'Estudiante', 'Prueba 03', NULL, '2026-11-01 17:39:28', '2026-11-01 17:39:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante_logro`
--

CREATE TABLE `estudiante_logro` (
  `id_estudiante` bigint(20) UNSIGNED NOT NULL,
  `id_logro` bigint(20) UNSIGNED NOT NULL,
  `fecha_obtencion` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiante_logro`
--

INSERT INTO `estudiante_logro` (`id_estudiante`, `id_logro`, `fecha_obtencion`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 3, '2026-10-11 20:16:13', '2026-10-11 20:16:13', '2026-10-11 20:16:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluacion`
--

CREATE TABLE `evaluacion` (
  `id_evaluacion` bigint(20) UNSIGNED NOT NULL,
  `estudiante_id` bigint(20) UNSIGNED NOT NULL,
  `cuestionario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo` varchar(30) NOT NULL,
  `estado` enum('EN_PROCESO','FINALIZADA') DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `puntaje` decimal(5,2) DEFAULT NULL,
  `nivel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluacion`
--

INSERT INTO `evaluacion` (`id_evaluacion`, `estudiante_id`, `cuestionario_id`, `tipo`, `estado`, `fecha`, `fecha_fin`, `puntaje`, `nivel_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'DIAGNOSTICA', NULL, '2026-10-11 20:16:13', NULL, 45.50, 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(8, 7, 1, 'DIAGNOSTICA', 'FINALIZADA', '2026-10-26 02:39:07', '2026-10-26 03:15:02', 33.33, 1, '2026-10-26 02:39:07', '2026-10-26 03:15:02'),
(9, 8, 1, 'DIAGNOSTICA', 'FINALIZADA', '2026-10-26 03:36:51', '2026-10-26 03:42:57', 20.00, 1, '2026-10-26 03:36:51', '2026-10-26 03:42:57'),
(10, 9, 1, 'DIAGNOSTICA', 'FINALIZADA', '2026-10-26 11:47:42', '2026-10-26 11:49:29', 26.67, 1, '2026-10-26 11:47:42', '2026-10-26 11:49:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gamificacion`
--

CREATE TABLE `gamificacion` (
  `id_gamificacion` bigint(20) UNSIGNED NOT NULL,
  `estudiante_id` bigint(20) UNSIGNED NOT NULL,
  `puntos` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `gamificacion`
--

INSERT INTO `gamificacion` (`id_gamificacion`, `estudiante_id`, `puntos`, `created_at`, `updated_at`) VALUES
(1, 1, 90, '2026-10-11 20:16:13', '2026-10-16 03:12:20'),
(2, 2, 175, '2026-10-11 20:16:13', '2026-10-16 03:55:01'),
(3, 9, 10, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(4, 7, 85, '2026-11-01 23:37:58', '2026-11-02 01:19:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lectura_diagnostica`
--

CREATE TABLE `lectura_diagnostica` (
  `id_lectura` bigint(20) UNSIGNED NOT NULL,
  `cuestionario_id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `contenido` text NOT NULL,
  `orden` tinyint(3) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lectura_diagnostica`
--

INSERT INTO `lectura_diagnostica` (`id_lectura`, `cuestionario_id`, `titulo`, `contenido`, `orden`, `created_at`, `updated_at`) VALUES
(1, 1, 'El misterio de las plantas marchitas', 'Cada mañana, Valeria llegaba temprano a la escuela para cuidar las plantas del pequeño jardín que su salón había construido. Junto con sus compañeros, había sembrado flores, hierbas aromáticas y algunos vegetales.\r\n\r\nUn lunes, Valeria descubrió que varias plantas tenían las hojas caídas y la tierra estaba completamente seca. Le sorprendió porque el viernes anterior las habían regado.\r\n\r\n—Tal vez alguien olvidó cuidarlas durante el fin de semana —comentó Mateo.\r\n\r\nLa profesora Rosa les propuso investigar antes de sacar conclusiones. Los estudiantes observaron el jardín y notaron que las plantas más afectadas estaban cerca de una pared donde recibían sol durante casi todo el día.\r\n\r\nValeria recordó que, durante la semana anterior, el clima había sido especialmente caluroso. Entonces sugirió colocar una pequeña cubierta que proporcionara sombra durante las horas de mayor calor.\r\n\r\nLos estudiantes organizaron turnos para regar las plantas y construyeron una cubierta utilizando materiales reutilizados. Después de varios días, las hojas recuperaron su firmeza y comenzaron a aparecer nuevos brotes.\r\n\r\nAl finalizar la semana, la profesora felicitó al grupo por investigar el problema y trabajar en equipo. Valeria comprendió que observar con atención y buscar explicaciones era mejor que culpar a alguien sin tener pruebas.', 1, '2026-10-25 18:43:16', '2026-10-25 18:43:16'),
(2, 1, 'La biblioteca que volvió a despertar', 'En una escuela de Lima, la biblioteca permanecía casi siempre vacía. Aunque tenía numerosos libros de cuentos, ciencia e historia, pocos estudiantes la visitaban durante los recreos.\r\n\r\nUn día, la profesora Elena preguntó a sus alumnos por qué no utilizaban ese espacio. Algunos respondieron que desconocían los libros disponibles; otros pensaban que la biblioteca solo servía para hacer tareas.\r\n\r\nCamila propuso organizar una semana de lectura con actividades diferentes. Su compañero Joaquín sugirió colocar carteles que recomendaran libros y preparar un rincón donde los estudiantes pudieran compartir sus historias favoritas.\r\n\r\nCon autorización de la dirección, organizaron la actividad. Durante el primer día, asistieron pocos estudiantes. Sin embargo, quienes participaron contaron sus experiencias a otros compañeros y los invitaron a conocer la biblioteca.\r\n\r\nAl tercer día, varias mesas estaban ocupadas. Algunos niños leían cuentos, otros exploraban libros sobre animales y un grupo comentaba las aventuras de sus personajes favoritos.\r\n\r\nAl finalizar la semana, la profesora Elena registró que las visitas habían aumentado. Sin embargo, explicó que el verdadero éxito no consistía únicamente en llenar la biblioteca, sino en conseguir que los estudiantes descubrieran el placer de leer y continuaran haciéndolo.\r\n\r\nCamila comprendió que una buena iniciativa puede comenzar con pocas personas y crecer cuando todos participan.', 2, '2026-10-25 18:43:16', '2026-10-25 18:43:16'),
(3, 1, 'Una decisión para cuidar el agua', 'El agua es indispensable para las personas, los animales y las plantas. La utilizamos para beber, preparar alimentos, mantener la higiene y realizar numerosas actividades cotidianas. Sin embargo, disponer de agua limpia no significa que podamos desperdiciarla.\r\n\r\nEn algunas viviendas, un caño que gotea puede pasar desapercibido durante varios días. Aunque cada gota parezca insignificante, la pérdida continúa mientras el problema no se soluciona. Por eso, revisar las instalaciones y avisar cuando se detecta una fuga son acciones importantes.\r\n\r\nEn la escuela, también es posible utilizar el agua de manera responsable. Cerrar correctamente los caños después de lavarse las manos, emplear solo la cantidad necesaria y comunicar cualquier desperfecto contribuye a evitar desperdicios.\r\n\r\nAlgunas personas creen que las acciones individuales tienen poca importancia porque el consumo de agua depende de muchas familias y empresas. Sin embargo, cuando numerosas personas incorporan hábitos responsables, sus esfuerzos se suman.\r\n\r\nTambién es necesario reconocer que no todos los problemas relacionados con el agua pueden resolverse únicamente con hábitos personales. Las autoridades y las instituciones tienen responsabilidades, como mantener las redes de distribución y promover el acceso seguro al servicio.\r\n\r\nCuidar el agua requiere, por tanto, tanto decisiones individuales como acciones colectivas. Comprender esta responsabilidad compartida nos ayuda a utilizar mejor un recurso esencial para la vida.', 3, '2026-10-25 18:43:16', '2026-10-25 18:43:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logro`
--

CREATE TABLE `logro` (
  `id_logro` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `condicion` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logro`
--

INSERT INTO `logro` (`id_logro`, `nombre`, `descripcion`, `condicion`, `created_at`, `updated_at`) VALUES
(1, 'Primera Evaluación', 'Finalizaste tu evaluación diagnóstica con éxito.', 'Completar evaluación diagnóstica', '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 'Lector Constante', 'Completaste 5 actividades de lectura seguidas.', 'Completar 5 actividades', '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(3, 'Maestro de Comprensión', 'Obtuviste 100% de precisión en una actividad.', 'Sacar 100 puntos en un intento', '2026-10-11 20:16:13', '2026-10-11 20:16:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivel`
--

CREATE TABLE `nivel` (
  `id_nivel` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `rango_min` decimal(5,2) NOT NULL,
  `rango_max` decimal(5,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `nivel`
--

INSERT INTO `nivel` (`id_nivel`, `nombre`, `rango_min`, `rango_max`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Básico', 0.00, 59.99, 'Refuerzo de comprensión literal y habilidades fundamentales', '2026-10-11 20:12:00', '2026-10-11 20:12:00'),
(2, 'Intermedio', 60.00, 79.99, 'Consolidación de comprensión literal e inferencial', '2026-10-11 20:12:00', '2026-10-11 20:12:00'),
(3, 'Avanzado', 80.00, 100.00, 'Profundización y análisis crítico', '2026-10-11 20:12:00', '2026-10-11 20:12:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `id_pregunta` bigint(20) UNSIGNED NOT NULL,
  `evaluacion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `actividad_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cuestionario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lectura_diagnostica_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dimension` enum('LITERAL','INFERENCIAL','CRITICA') DEFAULT NULL,
  `orden` tinyint(3) UNSIGNED DEFAULT NULL,
  `enunciado` text NOT NULL,
  `alternativa_a` text NOT NULL,
  `alternativa_b` text NOT NULL,
  `alternativa_c` text NOT NULL,
  `alternativa_d` text NOT NULL,
  `respuesta_correcta` char(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id_pregunta`, `evaluacion_id`, `actividad_id`, `cuestionario_id`, `lectura_diagnostica_id`, `dimension`, `orden`, `enunciado`, `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`, `respuesta_correcta`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, 'Según el texto diagnóstico, ¿cuál era la misión del personaje principal?', 'Proteger los libros.', 'Destruir la biblioteca.', 'Vender los libros.', 'Ignorar a los visitantes.', 'A', '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, NULL, 1, NULL, NULL, NULL, NULL, '¿Qué fruta deseaba alcanzar el zorro?', 'Manzanas', 'Uvas', 'Naranjas', 'Plátanos', 'B', '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(3, NULL, 3, NULL, NULL, NULL, NULL, '¿Por qué la tortuga ganó la carrera?', 'Porque la liebre no participó.', 'Porque siguió avanzando sin detenerse.', 'Porque corrió más rápido.', 'Porque tomó un atajo.', 'B', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(4, NULL, 3, NULL, NULL, NULL, NULL, '¿Qué hizo la liebre durante la carrera?', 'Se puso a dormir.', 'Regresó a su casa.', 'Ayudó a la tortuga.', 'Abandonó la competencia al inicio.', 'A', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(5, NULL, 4, NULL, NULL, NULL, NULL, '¿Qué provocó que el perro perdiera su hueso?', 'Lo escondió bajo un árbol.', 'Se lo regaló a otro perro.', 'Abrió la boca para intentar obtener el hueso del reflejo.', 'Lo dejó sobre el puente.', 'C', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(6, NULL, 4, NULL, NULL, NULL, NULL, '¿Cuál es la enseñanza principal del relato?', 'Debemos querer siempre lo que tienen otros.', 'La ambición puede hacernos perder lo que tenemos.', 'Es mejor no cruzar puentes.', 'Los perros no deben comer huesos.', 'B', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(7, NULL, 5, NULL, NULL, NULL, NULL, '¿Cuál es una acción que ayuda al medio ambiente?', 'Dejar correr el agua.', 'Mezclar todos los residuos.', 'Desperdiciar electricidad.', 'Reutilizar objetos.', 'D', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(8, NULL, 5, NULL, NULL, NULL, NULL, '¿Qué significa reciclar?', 'Transformar materiales usados para aprovecharlos nuevamente.', 'Comprar siempre productos nuevos.', 'Eliminar todos los árboles.', 'Utilizar más agua potable.', 'A', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(9, NULL, 6, NULL, NULL, NULL, NULL, '¿Qué materiales pueden salir de un volcán?', 'Solo agua fría.', 'Papel y plástico.', 'Lava, gases y cenizas.', 'Únicamente arena de playa.', 'C', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(10, NULL, 6, NULL, NULL, NULL, NULL, '¿Qué pueden producir las erupciones volcánicas?', 'La desaparición de toda la corteza terrestre.', 'Cambios en el paisaje y formación de rocas.', 'La eliminación de la gravedad.', 'La transformación del agua en electricidad.', 'B', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(11, NULL, 7, NULL, NULL, NULL, NULL, '¿Qué caracteriza a la lectura crítica?', 'Memorizar todas las palabras.', 'Leer únicamente el título.', 'Aceptar cualquier afirmación sin analizarla.', 'Evaluar argumentos y evidencias.', 'D', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(12, NULL, 7, NULL, NULL, NULL, NULL, '¿Qué conviene hacer para comprobar una afirmación?', 'Contrastar la información con fuentes confiables.', 'Compartirla inmediatamente.', 'Ignorar las evidencias.', 'Elegir la fuente más breve.', 'A', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(13, NULL, 8, NULL, NULL, NULL, NULL, '¿Qué permite hacer una herramienta digital educativa?', 'Evitar todo tipo de evaluación.', 'Garantizar que cualquier información sea verdadera.', 'Acceder a materiales y colaborar a distancia.', 'Eliminar la necesidad de aprender.', 'C', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(14, NULL, 8, NULL, NULL, NULL, NULL, '¿Qué práctica es recomendable al utilizar recursos digitales?', 'Compartir contraseñas con desconocidos.', 'Verificar las fuentes y proteger los datos personales.', 'Abrir todos los enlaces recibidos.', 'Publicar información privada.', 'B', '2026-10-15 00:58:09', '2026-10-15 00:58:09'),
(15, NULL, 9, NULL, NULL, NULL, NULL, '¿Dónde vivía el ratón?', 'En un árbol del parque.', 'En un pequeño agujero de una casa.', 'En una caja de cartón.', 'Debajo de un puente.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(16, NULL, 9, NULL, NULL, NULL, NULL, '¿Cuándo salía el ratón a buscar comida?', 'Cada mañana.', 'Al mediodía.', 'Cada noche.', 'Todas las tardes.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(17, NULL, 9, NULL, NULL, NULL, NULL, '¿Qué encontró el ratón en la cocina?', 'Un trozo de queso.', 'Una manzana.', 'Un pedazo de pan.', 'Una zanahoria.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(18, NULL, 9, NULL, NULL, NULL, NULL, '¿Qué escuchó el ratón mientras comía?', 'El sonido de la lluvia.', 'La voz de una persona.', 'El ruido de una puerta.', 'Los pasos de un gato.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(19, NULL, 9, NULL, NULL, NULL, NULL, '¿Qué hizo el ratón al escuchar al gato?', 'Se quedó comiendo.', 'Corrió hacia su agujero.', 'Subió a una mesa.', 'Se escondió en la cocina del vecino.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(20, NULL, 9, NULL, NULL, NULL, NULL, '¿Qué quería hacer el gato?', 'Compartir el queso.', 'Jugar con una pelota.', 'Atrapar al ratón.', 'Buscar agua.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(21, NULL, 9, NULL, NULL, NULL, NULL, '¿Qué le permitió al ratón escapar?', 'Esconderse en su agujero.', 'Volar por la ventana.', 'Subir a un árbol.', 'Pedir ayuda a otro gato.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(22, NULL, 9, NULL, NULL, NULL, NULL, '¿Cómo era el ratón después de aquella experiencia?', 'Descuidado.', 'Perezoso.', 'Ruidoso.', 'Más atento.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(23, NULL, 9, NULL, NULL, NULL, NULL, '¿Cuál es el mejor título alternativo para el relato?', 'Un día en la playa.', 'El ratón que aprendió a tener cuidado.', 'La casa del campesino.', 'El gato que buscaba queso.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(24, NULL, 9, NULL, NULL, NULL, NULL, '¿Qué enseñanza se puede obtener del relato?', 'Es mejor salir corriendo sin mirar.', 'Nunca debemos comer queso.', 'Es importante estar atentos ante los peligros.', 'Todos los gatos son amigos de los ratones.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(25, NULL, 10, NULL, NULL, NULL, NULL, '¿Con quién fue Ana al parque?', 'Con su hermano Luis.', 'Con su profesora.', 'Con su abuela.', 'Con una amiga.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(26, NULL, 10, NULL, NULL, NULL, NULL, '¿Cómo estaba el día del paseo?', 'Lluvioso y frío.', 'Nublado y oscuro.', 'Ventoso y frío.', 'Soleado.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(27, NULL, 10, NULL, NULL, NULL, NULL, '¿Qué llevaron Ana y Luis para jugar?', 'Un tablero de ajedrez.', 'Una pelota.', 'Unas bicicletas.', 'Una cometa.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(28, NULL, 10, NULL, NULL, NULL, NULL, '¿Cuántas botellas de agua llevaron?', 'Una.', 'Tres.', 'Dos.', 'Cuatro.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(29, NULL, 10, NULL, NULL, NULL, NULL, '¿Dónde jugaron primero?', 'Cerca de los árboles.', 'Dentro de una tienda.', 'En su casa.', 'Junto a un río.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(30, NULL, 10, NULL, NULL, NULL, NULL, '¿Qué hicieron después de jugar?', 'Fueron a comprar juguetes.', 'Regresaron inmediatamente a casa.', 'Visitaron una biblioteca.', 'Descansaron en una banca y comieron.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(31, NULL, 10, NULL, NULL, NULL, NULL, '¿Qué comieron durante el descanso?', 'Galletas y helados.', 'Sándwiches.', 'Arroz con pollo.', 'Frutas solamente.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(32, NULL, 10, NULL, NULL, NULL, NULL, '¿Qué hicieron con los envoltorios antes de regresar?', 'Los dejaron bajo un árbol.', 'Los guardaron en una banca.', 'Los depositaron en un basurero.', 'Los arrojaron al suelo.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(33, NULL, 10, NULL, NULL, NULL, NULL, '¿Qué demuestra la acción de recoger los envoltorios?', 'Que querían seguir jugando.', 'Que tenían hambre.', 'Que no les gustaba el parque.', 'Que cuidaban la limpieza del lugar.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(34, NULL, 10, NULL, NULL, NULL, NULL, '¿Cuál es la idea principal del relato?', 'Ana y Luis disfrutaron un paseo y cuidaron el parque.', 'Ana perdió su pelota durante una tormenta.', 'Luis no quiso salir de casa.', 'Los niños pasaron el día comprando alimentos.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(35, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué animal tenía el campesino?', 'Una oveja.', 'Una vaca.', 'Una gallina.', 'Un caballo.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(36, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué ponía la gallina cada día?', 'Un huevo de oro.', 'Una moneda de plata.', 'Dos huevos de madera.', 'Una piedra brillante.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(37, NULL, 11, NULL, NULL, NULL, NULL, '¿Para qué utilizaba el campesino la riqueza obtenida?', 'Para viajar todos los días.', 'Para comprar alimentos y mejorar su casa.', 'Para comprar otros animales salvajes.', 'Para abandonar su hogar.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(38, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué quería conseguir el campesino?', 'Dejar de trabajar para siempre.', 'Regalar todos sus alimentos.', 'Vender su casa.', 'Obtener toda la riqueza de una sola vez.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(39, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué pensó el campesino sobre la gallina?', 'Que tenía muchos huevos de oro dentro de su cuerpo.', 'Que podía volar hasta una montaña.', 'Que escondía monedas en el jardín.', 'Que necesitaba vivir en un río.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(40, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué descubrió al abrir la gallina?', 'Un montón de monedas.', 'Muchos huevos de oro.', 'Que no había ningún huevo.', 'Una piedra preciosa.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(41, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué perdió el campesino por su decisión?', 'Solamente una bolsa de alimentos.', 'La gallina y los huevos que recibía diariamente.', 'Su casa y todas sus herramientas.', 'Un caballo y una vaca.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(42, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué defecto mostró el campesino?', 'Generosidad.', 'Paciencia.', 'Responsabilidad.', 'Avaricia e impaciencia.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(43, NULL, 11, NULL, NULL, NULL, NULL, '¿Qué habría sido más prudente hacer?', 'Conservar la gallina y seguir recibiendo sus huevos.', 'Abrir la gallina inmediatamente.', 'Regalar la gallina sin pensarlo.', 'Dejar de alimentar a la gallina.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(44, NULL, 11, NULL, NULL, NULL, NULL, '¿Cuál es la enseñanza principal de la fábula?', 'La riqueza aparece sin ningún esfuerzo.', 'Debemos abandonar todo lo que tenemos.', 'La ambición excesiva puede hacernos perder lo que ya poseemos.', 'Siempre es mejor conseguir todo rápidamente.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(45, NULL, 12, NULL, NULL, NULL, NULL, '¿Qué describe el ciclo del agua?', 'La desaparición definitiva de los océanos.', 'La circulación continua del agua por la Tierra.', 'La formación exclusiva de agua subterránea.', 'El movimiento de los animales acuáticos.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(46, NULL, 12, NULL, NULL, NULL, NULL, '¿Qué provoca la evaporación del agua?', 'El enfriamiento de las nubes.', 'La congelación de los ríos.', 'La fuerza de los árboles.', 'El calor del Sol.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(47, NULL, 12, NULL, NULL, NULL, NULL, '¿En qué consiste la condensación?', 'El vapor se enfría y forma pequeñas gotas.', 'El agua desaparece para siempre.', 'La lluvia se convierte inmediatamente en roca.', 'Los ríos se transforman en nubes sin evaporación.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(48, NULL, 12, NULL, NULL, NULL, NULL, '¿Cómo se forman las nubes según el texto?', 'Por la acumulación de arena en el aire.', 'Por el calentamiento de las rocas.', 'Por la agrupación de pequeñas gotas de agua.', 'Por la transformación de las plantas en vapor.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(49, NULL, 12, NULL, NULL, NULL, NULL, '¿Qué sucede cuando las gotas de las nubes crecen?', 'El agua deja de circular por la Tierra.', 'El agua puede caer en forma de precipitación.', 'Las nubes desaparecen sin dejar agua.', 'Los océanos se congelan por completo.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(50, NULL, 12, NULL, NULL, NULL, NULL, '¿Cuál de los siguientes fenómenos es una forma de precipitación?', 'La evaporación.', 'La condensación.', 'La fotosíntesis.', 'La lluvia.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(51, NULL, 12, NULL, NULL, NULL, NULL, '¿Qué puede ocurrir con el agua después de precipitarse?', 'Puede llegar al suelo, los ríos y los océanos.', 'Se convierte siempre en vapor de inmediato.', 'Desaparece sin llegar a ningún lugar.', 'Se transforma necesariamente en hielo permanente.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(52, NULL, 12, NULL, NULL, NULL, NULL, '¿Por qué el ciclo del agua se considera continuo?', 'Porque el agua solo existe en los océanos.', 'Porque la lluvia ocurre una sola vez.', 'Porque el agua circula y vuelve a pasar por distintas etapas.', 'Porque el Sol impide que el agua cambie.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(53, NULL, 12, NULL, NULL, NULL, NULL, 'Si disminuyera considerablemente el calor solar, ¿qué proceso se vería directamente reducido?', 'La formación de rocas volcánicas.', 'La evaporación del agua.', 'La erosión causada por las raíces.', 'La absorción de oxígeno por los árboles.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(54, NULL, 12, NULL, NULL, NULL, NULL, '¿Cuál es la mejor conclusión sobre el ciclo del agua?', 'El agua permanece inmóvil durante toda su existencia.', 'La precipitación es la única etapa importante.', 'Las nubes producen agua sin relación con otros procesos.', 'Las distintas etapas permiten que el agua circule y se renueve en la naturaleza.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(55, NULL, 13, NULL, NULL, NULL, NULL, '¿Qué es el reciclaje?', 'Transformar materiales usados para elaborar nuevos productos.', 'Comprar productos nuevos y desechar los antiguos.', 'Quemar toda la basura doméstica.', 'Enterrar todos los materiales sin clasificarlos.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(56, NULL, 13, NULL, NULL, NULL, NULL, '¿Cuál de estos materiales puede recuperarse mediante el reciclaje?', 'La luz solar.', 'El viento.', 'El vidrio.', 'El sonido.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(57, NULL, 13, NULL, NULL, NULL, NULL, '¿Qué se debe hacer con los materiales reciclables?', 'Mezclarlos siempre con residuos orgánicos.', 'Separarlos correctamente.', 'Arrojarlos al suelo.', 'Destruirlos antes de clasificarlos.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(58, NULL, 13, NULL, NULL, NULL, NULL, '¿Qué beneficio ofrece el reciclaje?', 'Aumenta necesariamente la contaminación.', 'Elimina la necesidad de cuidar el agua.', 'Hace que todos los recursos naturales sean ilimitados.', 'Reduce la cantidad de basura y aprovecha los recursos.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(59, NULL, 13, NULL, NULL, NULL, NULL, 'Según el texto, ¿qué acción complementa el reciclaje?', 'Comprar objetos innecesarios todos los días.', 'Desechar los objetos que todavía funcionan.', 'Reducir el consumo innecesario.', 'Mezclar todos los residuos.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(60, NULL, 13, NULL, NULL, NULL, NULL, '¿Qué significa reutilizar un objeto?', 'Volver a utilizarlo mientras todavía sirve.', 'Destruirlo inmediatamente después de usarlo.', 'Convertirlo siempre en un material líquido.', 'Desecharlo aunque esté en buen estado.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(61, NULL, 13, NULL, NULL, NULL, NULL, '¿Quiénes pueden contribuir a obtener mejores resultados en el reciclaje?', 'Únicamente los trabajadores de las fábricas.', 'Las familias, las escuelas y las empresas.', 'Solamente las autoridades nacionales.', 'Exclusivamente los estudiantes.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(62, NULL, 13, NULL, NULL, NULL, NULL, 'Una familia utiliza repetidamente una bolsa que todavía está en buen estado. ¿Qué práctica realiza?', 'Contaminación del agua.', 'Consumo innecesario.', 'Desperdicio de materiales.', 'Reutilización.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(63, NULL, 13, NULL, NULL, NULL, NULL, '¿Por qué reciclar no es la única solución para cuidar el ambiente?', 'Porque ningún material puede recuperarse.', 'Porque reciclar siempre aumenta la basura.', 'Porque también es necesario reducir el consumo y reutilizar objetos.', 'Porque solamente las empresas producen residuos.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(64, NULL, 13, NULL, NULL, NULL, NULL, '¿Cuál es la idea principal del texto?', 'Reciclar, reducir y reutilizar ayudan a aprovechar los recursos y disminuir los residuos.', 'Todos los residuos deben quemarse.', 'El reciclaje es responsabilidad exclusiva de los colegios.', 'Los materiales usados nunca pueden aprovecharse nuevamente.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(65, NULL, 14, NULL, NULL, NULL, NULL, '¿Qué caracteriza a los bosques según el texto?', 'Son lugares donde solo viven árboles.', 'Son ecosistemas donde conviven diversos seres vivos.', 'Son espacios sin suelo ni agua.', 'Son ambientes habitados únicamente por animales.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(66, NULL, 14, NULL, NULL, NULL, NULL, '¿Qué función cumplen los árboles durante la fotosíntesis?', 'Absorben oxígeno y producen dióxido de carbono como resultado principal.', 'Eliminan todos los nutrientes del suelo.', 'Absorben dióxido de carbono y liberan oxígeno.', 'Transforman las rocas en animales.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(67, NULL, 14, NULL, NULL, NULL, NULL, '¿Cómo ayudan las raíces de los árboles al suelo?', 'Contribuyen a mantenerlo y disminuir la erosión.', 'Impiden que exista cualquier forma de vida.', 'Aumentan siempre los incendios forestales.', 'Transforman toda la tierra en arena.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(68, NULL, 14, NULL, NULL, NULL, NULL, '¿Qué encuentran muchos animales en los bosques?', 'Únicamente lugares para construir carreteras.', 'Espacios sin vegetación.', 'Solamente agua contaminada.', 'Alimento y refugio.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(69, NULL, 14, NULL, NULL, NULL, NULL, '¿Qué consecuencias puede tener la tala excesiva?', 'La recuperación inmediata de todos los hábitats.', 'La destrucción de hábitats y la alteración del equilibrio natural.', 'La desaparición de todos los fenómenos meteorológicos.', 'La eliminación de la necesidad de cuidar el suelo.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(70, NULL, 14, NULL, NULL, NULL, NULL, '¿Qué medida ayuda a proteger los bosques?', 'Provocar incendios para limpiar grandes áreas.', 'Talar todos los árboles de una zona.', 'Prevenir los incendios y controlar la tala.', 'Eliminar los animales que viven entre los árboles.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(71, NULL, 14, NULL, NULL, NULL, NULL, '¿Por qué los bosques son importantes para los animales?', 'Porque ofrecen alimento y refugio.', 'Porque impiden que los animales encuentren agua.', 'Porque eliminan todas las plantas pequeñas.', 'Porque no tienen cambios en su entorno.', 'A', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(72, NULL, 14, NULL, NULL, NULL, NULL, 'Si se destruye la vegetación de una ladera, ¿qué problema podría aumentar?', 'La producción de alimentos en todos los casos.', 'La cantidad de refugios disponibles para los animales.', 'La protección natural del suelo.', 'La erosión del suelo.', 'D', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(73, NULL, 14, NULL, NULL, NULL, NULL, '¿Qué se puede inferir sobre la recuperación de las áreas forestales dañadas?', 'Es innecesaria si se han talado árboles.', 'Puede contribuir a recuperar parte de los ecosistemas afectados.', 'Solo beneficia a las ciudades y nunca a los animales.', 'Impide que crezcan nuevas plantas.', 'B', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(74, NULL, 14, NULL, NULL, NULL, NULL, '¿Cuál es la conclusión principal del texto?', 'Los bosques son importantes únicamente por su madera.', 'Los incendios y la tala no afectan a los ecosistemas.', 'Proteger los bosques ayuda a conservar los seres vivos y el equilibrio natural.', 'Los animales pueden vivir en cualquier ambiente sin recursos.', 'C', '2026-10-15 21:34:32', '2026-10-15 21:34:32'),
(75, NULL, NULL, 1, 1, 'LITERAL', 1, '¿Qué encontraron Valeria y sus compañeros el lunes por la mañana?', 'Las plantas habían crecido demasiado.', 'Varias plantas tenían las hojas caídas y la tierra seca.', 'El jardín estaba lleno de insectos.', 'Habían desaparecido las herramientas.', 'B', '2026-10-25 19:29:32', '2026-10-25 19:29:32'),
(76, NULL, NULL, 1, 1, 'LITERAL', 2, '¿Qué hicieron los estudiantes para proteger las plantas del calor?', 'Trasladaron todas las plantas al salón.', 'Dejaron de regarlas durante una semana.', 'Construyeron una cubierta para proporcionar sombra.', 'Cambiaron las plantas por otras nuevas.', 'C', '2026-10-25 19:29:32', '2026-10-25 19:29:32'),
(77, NULL, NULL, 1, 1, 'INFERENCIAL', 3, '¿Por qué las plantas cercanas a la pared estaban más afectadas?', 'Porque recibían demasiado sol y perdían humedad.', 'Porque la pared impedía que recibieran aire.', 'Porque sus semillas eran diferentes.', 'Porque los estudiantes nunca las habían regado.', 'A', '2026-10-25 19:29:32', '2026-10-25 19:29:32'),
(78, NULL, NULL, 1, 1, 'INFERENCIAL', 4, '¿Qué podemos deducir de la actitud de la profesora Rosa?', 'Prefería resolver todos los problemas sin ayuda.', 'Consideraba que el jardín no era importante.', 'Quería que los estudiantes dejaran de sembrar.', 'Buscaba que los estudiantes investigaran antes de concluir.', 'D', '2026-10-25 19:29:32', '2026-10-25 19:29:32'),
(79, NULL, NULL, 1, 1, 'CRITICA', 5, '¿Cuál es la mejor valoración de la decisión de Valeria y sus compañeros?', 'Fue incorrecta porque debieron culpar al encargado del jardín.', 'Fue adecuada porque observaron el problema y aplicaron una solución basada en lo encontrado.', 'Fue innecesaria porque las plantas siempre se recuperan solas.', 'Fue equivocada porque debieron retirar todas las plantas.', 'B', '2026-10-25 19:29:32', '2026-10-25 19:29:32'),
(80, NULL, NULL, 1, 2, 'LITERAL', 1, '¿Cómo se encontraba la biblioteca antes de que Camila propusiera la semana de lectura?', 'Permanecía casi siempre vacía, aunque tenía numerosos libros.', 'Estaba cerrada porque no tenía libros disponibles.', 'Recibía diariamente a todos los estudiantes de la escuela.', 'Había sido transformada en un salón de computación.', 'A', '2026-10-25 19:31:12', '2026-10-25 20:10:24'),
(81, NULL, NULL, 1, 2, 'LITERAL', 2, '¿Qué actividad realizaron los estudiantes para promover la lectura?', 'Vendieron los libros antiguos.', 'Cerraron la biblioteca durante un mes.', 'Guardaron todos los libros en cajas.', 'Organizaron actividades para invitar a otros estudiantes a leer.', 'D', '2026-10-25 19:31:12', '2026-10-25 19:31:12'),
(82, NULL, NULL, 1, 2, 'INFERENCIAL', 3, '¿Por qué la biblioteca comenzó a recibir más visitantes?', 'Porque los estudiantes tenían más horas libres.', 'Porque se prohibió utilizar otros espacios.', 'Porque las actividades despertaron el interés por los libros.', 'Porque todos debían asistir obligatoriamente.', 'C', '2026-10-25 19:31:12', '2026-10-25 19:31:12'),
(83, NULL, NULL, 1, 2, 'INFERENCIAL', 4, '¿Qué podemos deducir del trabajo realizado por los estudiantes?', 'Que una biblioteca funciona mejor cuando permanece cerrada.', 'Que la colaboración y la iniciativa pueden recuperar espacios de aprendizaje.', 'Que únicamente los docentes pueden organizar actividades.', 'Que los libros antiguos no tienen utilidad.', 'B', '2026-10-25 19:31:12', '2026-10-25 19:31:12'),
(84, NULL, NULL, 1, 2, 'CRITICA', 5, '¿Cuál es la mejor valoración de la iniciativa organizada por Camila y sus compañeros?', 'Fue innecesaria porque la biblioteca ya tenía suficientes libros.', 'Fue poco útil porque al principio participaron pocos estudiantes.', 'Fue exitosa únicamente porque aumentó la cantidad de visitantes.', 'Fue valiosa porque despertó el interés por leer y buscó mantener ese hábito en los estudiantes.', 'D', '2026-10-25 19:31:12', '2026-10-25 20:11:18'),
(85, NULL, NULL, 1, 3, 'LITERAL', 1, 'Según el texto, ¿qué acción ayuda a evitar el desperdicio de agua en la escuela?', 'Dejar los caños abiertos después de utilizarlos.', 'Utilizar más agua de la necesaria para lavarse las manos.', 'Cerrar correctamente los caños y comunicar los desperfectos.', 'Esperar a que las fugas de agua desaparezcan por sí solas.', 'C', '2026-10-25 19:32:29', '2026-10-25 20:13:17'),
(86, NULL, NULL, 1, 3, 'INFERENCIAL', 2, '¿Por qué podemos deducir que una fuga pequeña de agua debe repararse cuanto antes?', 'Porque, aunque se pierda poca agua cada vez, el desperdicio se acumula mientras continúa la fuga.', 'Porque una fuga pequeña siempre provoca la interrupción inmediata del servicio.', 'Porque el agua que se pierde puede recuperarse automáticamente.', 'Porque todas las fugas pequeñas se convierten en problemas graves el mismo día.', 'A', '2026-10-25 19:32:29', '2026-10-25 20:14:00'),
(87, NULL, NULL, 1, 3, 'INFERENCIAL', 3, '¿Qué podemos inferir sobre la responsabilidad de las personas y las instituciones en el cuidado del agua?', 'Que las personas deben esperar a que las autoridades solucionen todos los problemas relacionados con el agua.', 'Que únicamente las acciones individuales permiten garantizar el uso responsable del agua.', 'Que las instituciones pueden evitar el desperdicio sin necesidad de la colaboración de las personas.', 'Que el cuidado del agua es más efectivo cuando los hábitos individuales se complementan con acciones institucionales.', 'D', '2026-10-25 19:32:29', '2026-10-25 20:14:39'),
(88, NULL, NULL, 1, 3, 'CRITICA', 4, 'Una escuela propone colocar carteles para recordar que se deben cerrar los caños, pero decide no reparar las fugas existentes. Según el texto, ¿cuál es la valoración más adecuada de esta propuesta?', 'Es completamente adecuada porque los carteles solucionan todos los problemas de desperdicio de agua.', 'Es insuficiente porque promover buenos hábitos debe complementarse con la reparación de las instalaciones.', 'Es innecesaria porque únicamente las autoridades nacionales son responsables de cuidar el agua.', 'Es adecuada porque las fugas pequeñas no producen desperdicios importantes.', 'B', '2026-10-25 19:32:29', '2026-10-25 20:15:15'),
(89, NULL, NULL, 1, 3, 'CRITICA', 5, 'Si una comunidad desea cuidar el agua de manera sostenible, ¿cuál de las siguientes propuestas sería la más adecuada según los argumentos del texto?', 'Realizar una campaña de ahorro durante un solo día y después continuar con los hábitos habituales.', 'Dejar toda la responsabilidad del cuidado del agua a las autoridades, sin modificar los hábitos personales.', 'Promover hábitos permanentes de ahorro, reparar las fugas y coordinar acciones entre las personas y las instituciones.', 'Concentrarse únicamente en reducir el consumo doméstico, sin prestar atención al mantenimiento de las redes de agua.', 'C', '2026-10-25 19:32:29', '2026-10-25 20:16:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso`
--

CREATE TABLE `progreso` (
  `id_progreso` bigint(20) UNSIGNED NOT NULL,
  `estudiante_id` bigint(20) UNSIGNED NOT NULL,
  `actividades_completadas` int(11) NOT NULL DEFAULT 0,
  `puntaje_acumulado` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `progreso`
--

INSERT INTO `progreso` (`id_progreso`, `estudiante_id`, `actividades_completadas`, `puntaje_acumulado`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 0.00, '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 2, 1, 100.00, '2026-10-11 20:16:13', '2026-10-11 20:16:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

CREATE TABLE `respuesta` (
  `id_respuesta` bigint(20) UNSIGNED NOT NULL,
  `evaluacion_id` bigint(20) UNSIGNED NOT NULL,
  `pregunta_id` bigint(20) UNSIGNED NOT NULL,
  `alternativa` char(1) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `respuesta`
--

INSERT INTO `respuesta` (`id_respuesta`, `evaluacion_id`, `pregunta_id`, `alternativa`, `es_correcta`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'A', 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 8, 75, 'A', 0, '2026-10-26 02:52:04', '2026-10-26 02:56:06'),
(3, 8, 76, 'D', 0, '2026-10-26 02:52:04', '2026-10-26 02:52:04'),
(4, 8, 77, 'A', 1, '2026-10-26 02:52:04', '2026-10-26 02:52:04'),
(5, 8, 78, 'A', 0, '2026-10-26 02:52:04', '2026-10-26 02:52:04'),
(6, 8, 79, 'B', 1, '2026-10-26 02:52:04', '2026-10-26 02:52:04'),
(7, 8, 80, 'B', 0, '2026-10-26 03:02:40', '2026-10-26 03:02:40'),
(8, 8, 81, 'C', 0, '2026-10-26 03:02:40', '2026-10-26 03:02:40'),
(9, 8, 82, 'C', 1, '2026-10-26 03:02:40', '2026-10-26 03:02:40'),
(10, 8, 83, 'B', 1, '2026-10-26 03:02:40', '2026-10-26 03:02:40'),
(11, 8, 84, 'B', 0, '2026-10-26 03:02:40', '2026-10-26 03:02:40'),
(12, 8, 85, 'D', 0, '2026-10-26 03:04:36', '2026-10-26 03:04:36'),
(13, 8, 86, 'D', 0, '2026-10-26 03:04:36', '2026-10-26 03:04:36'),
(14, 8, 87, 'B', 0, '2026-10-26 03:04:36', '2026-10-26 03:04:36'),
(15, 8, 88, 'B', 1, '2026-10-26 03:04:36', '2026-10-26 03:04:36'),
(16, 8, 89, 'B', 0, '2026-10-26 03:04:36', '2026-10-26 03:04:36'),
(17, 9, 75, 'A', 0, '2026-10-26 03:38:22', '2026-10-26 03:38:22'),
(18, 9, 76, 'C', 1, '2026-10-26 03:38:22', '2026-10-26 03:38:22'),
(19, 9, 77, 'A', 1, '2026-10-26 03:38:22', '2026-10-26 03:38:22'),
(20, 9, 78, 'A', 0, '2026-10-26 03:38:22', '2026-10-26 03:38:22'),
(21, 9, 79, 'A', 0, '2026-10-26 03:38:22', '2026-10-26 03:38:22'),
(22, 9, 80, 'B', 0, '2026-10-26 03:42:32', '2026-10-26 03:42:32'),
(23, 9, 81, 'A', 0, '2026-10-26 03:42:32', '2026-10-26 03:42:32'),
(24, 9, 82, 'D', 0, '2026-10-26 03:42:32', '2026-10-26 03:42:32'),
(25, 9, 83, 'C', 0, '2026-10-26 03:42:32', '2026-10-26 03:42:32'),
(26, 9, 84, 'C', 0, '2026-10-26 03:42:32', '2026-10-26 03:42:32'),
(27, 9, 85, 'C', 1, '2026-10-26 03:42:43', '2026-10-26 03:42:43'),
(28, 9, 86, 'B', 0, '2026-10-26 03:42:43', '2026-10-26 03:42:43'),
(29, 9, 87, 'B', 0, '2026-10-26 03:42:43', '2026-10-26 03:42:43'),
(30, 9, 88, 'A', 0, '2026-10-26 03:42:43', '2026-10-26 03:42:43'),
(31, 9, 89, 'A', 0, '2026-10-26 03:42:43', '2026-10-26 03:42:43'),
(32, 10, 75, 'B', 1, '2026-10-26 11:48:12', '2026-10-26 11:48:12'),
(33, 10, 76, 'B', 0, '2026-10-26 11:48:12', '2026-10-26 11:48:12'),
(34, 10, 77, 'B', 0, '2026-10-26 11:48:12', '2026-10-26 11:48:12'),
(35, 10, 78, 'D', 1, '2026-10-26 11:48:12', '2026-10-26 11:48:12'),
(36, 10, 79, 'C', 0, '2026-10-26 11:48:12', '2026-10-26 11:48:12'),
(37, 10, 80, 'B', 0, '2026-10-26 11:48:59', '2026-10-26 11:48:59'),
(38, 10, 81, 'B', 0, '2026-10-26 11:48:59', '2026-10-26 11:48:59'),
(39, 10, 82, 'B', 0, '2026-10-26 11:48:59', '2026-10-26 11:48:59'),
(40, 10, 83, 'C', 0, '2026-10-26 11:48:59', '2026-10-26 11:48:59'),
(41, 10, 84, 'B', 0, '2026-10-26 11:48:59', '2026-10-26 11:48:59'),
(42, 10, 85, 'A', 0, '2026-10-26 11:49:14', '2026-10-26 11:49:14'),
(43, 10, 86, 'A', 1, '2026-10-26 11:49:14', '2026-10-26 11:49:14'),
(44, 10, 87, 'B', 0, '2026-10-26 11:49:14', '2026-10-26 11:49:14'),
(45, 10, 88, 'B', 1, '2026-10-26 11:49:14', '2026-10-26 11:49:14'),
(46, 10, 89, 'B', 0, '2026-10-26 11:49:14', '2026-10-26 11:49:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_actividad`
--

CREATE TABLE `respuesta_actividad` (
  `id_respuesta_actividad` bigint(20) UNSIGNED NOT NULL,
  `intento_id` bigint(20) UNSIGNED NOT NULL,
  `pregunta_id` bigint(20) UNSIGNED NOT NULL,
  `alternativa` char(1) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Volcado de datos para la tabla `respuesta_actividad`
--

INSERT INTO `respuesta_actividad` (`id_respuesta_actividad`, `intento_id`, `pregunta_id`, `alternativa`, `es_correcta`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'B', 1, '2026-10-11 20:16:13', '2026-10-11 20:16:13'),
(2, 2, 2, 'A', 0, '2026-10-15 05:50:01', '2026-10-15 05:50:23'),
(3, 3, 2, 'A', 0, '2026-10-15 05:50:47', '2026-10-15 05:50:47'),
(4, 4, 3, 'B', 1, '2026-10-15 06:07:23', '2026-10-15 06:07:23'),
(5, 4, 4, 'C', 0, '2026-10-15 06:07:23', '2026-10-15 06:07:30'),
(6, 8, 5, 'C', 1, '2026-10-16 02:09:02', '2026-10-16 02:09:02'),
(7, 8, 6, 'B', 1, '2026-10-16 02:09:02', '2026-10-16 02:09:02'),
(8, 10, 3, 'B', 1, '2026-10-16 02:15:39', '2026-10-16 02:15:39'),
(9, 10, 4, 'A', 1, '2026-10-16 02:15:39', '2026-10-16 02:15:39'),
(10, 11, 3, 'D', 0, '2026-10-16 02:16:09', '2026-10-16 02:16:09'),
(11, 11, 4, 'B', 0, '2026-10-16 02:16:09', '2026-10-16 02:16:09'),
(12, 12, 2, 'C', 0, '2026-10-16 02:16:37', '2026-10-16 02:16:37'),
(13, 13, 7, 'C', 0, '2026-10-16 02:17:16', '2026-10-16 02:17:16'),
(14, 13, 8, 'A', 1, '2026-10-16 02:17:16', '2026-10-16 02:17:16'),
(15, 14, 9, 'C', 1, '2026-10-16 02:17:49', '2026-10-16 02:17:49'),
(16, 14, 10, 'A', 0, '2026-10-16 02:17:49', '2026-10-16 02:17:49'),
(17, 15, 35, 'B', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(18, 15, 36, 'C', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(19, 15, 37, 'A', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(20, 15, 38, 'A', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(21, 15, 39, 'A', 1, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(22, 15, 40, 'B', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(23, 15, 41, 'C', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(24, 15, 42, 'B', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(25, 15, 43, 'C', 0, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(26, 15, 44, 'C', 1, '2026-10-16 02:35:18', '2026-10-16 02:35:18'),
(27, 16, 5, 'C', 1, '2026-10-16 02:36:04', '2026-10-16 02:36:04'),
(28, 16, 6, 'B', 1, '2026-10-16 02:36:04', '2026-10-16 02:36:04'),
(29, 17, 9, 'C', 1, '2026-10-16 02:38:02', '2026-10-16 02:38:02'),
(30, 17, 10, 'B', 1, '2026-10-16 02:38:02', '2026-10-16 02:38:02'),
(31, 18, 35, 'C', 1, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(32, 18, 36, 'A', 1, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(33, 18, 37, 'C', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(34, 18, 38, 'B', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(35, 18, 39, 'B', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(36, 18, 40, 'B', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(37, 18, 41, 'B', 1, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(38, 18, 42, 'B', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(39, 18, 43, 'B', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(40, 18, 44, 'A', 0, '2026-10-16 03:02:49', '2026-10-16 03:02:49'),
(41, 19, 5, 'C', 1, '2026-10-16 03:04:16', '2026-10-16 03:04:16'),
(42, 19, 6, 'B', 1, '2026-10-16 03:04:16', '2026-10-16 03:04:16'),
(43, 7, 3, 'C', 0, '2026-10-16 03:04:33', '2026-10-16 03:04:33'),
(44, 7, 4, 'A', 1, '2026-10-16 03:04:33', '2026-10-16 03:04:33'),
(45, 20, 2, 'C', 0, '2026-10-16 03:06:01', '2026-10-16 03:06:01'),
(46, 21, 5, 'D', 0, '2026-10-16 03:06:18', '2026-10-16 03:06:18'),
(47, 21, 6, 'A', 0, '2026-10-16 03:06:18', '2026-10-16 03:06:18'),
(48, 22, 5, 'C', 1, '2026-10-16 03:07:04', '2026-10-16 03:07:04'),
(49, 22, 6, 'B', 1, '2026-10-16 03:07:04', '2026-10-16 03:07:04'),
(50, 23, 15, 'B', 1, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(51, 23, 16, 'C', 1, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(52, 23, 17, 'D', 0, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(53, 23, 18, 'B', 0, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(54, 23, 19, 'B', 1, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(55, 23, 20, 'C', 1, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(56, 23, 21, 'C', 0, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(57, 23, 22, 'B', 0, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(58, 23, 23, 'C', 0, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(59, 23, 24, 'B', 0, '2026-10-16 03:12:20', '2026-10-16 03:12:20'),
(60, 24, 15, 'C', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(61, 24, 16, 'B', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(62, 24, 17, 'C', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(63, 24, 18, 'A', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(64, 24, 19, 'C', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(65, 24, 20, 'A', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(66, 24, 21, 'B', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(67, 24, 22, 'A', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(68, 24, 24, 'A', 0, '2026-10-16 03:14:50', '2026-10-16 03:14:50'),
(69, 25, 15, 'B', 1, '2026-10-16 03:15:12', '2026-10-16 03:15:12'),
(70, 25, 16, 'A', 0, '2026-10-16 03:15:12', '2026-10-16 03:15:12'),
(71, 25, 17, 'A', 1, '2026-10-16 03:15:12', '2026-10-16 03:15:12'),
(72, 26, 15, 'C', 0, '2026-10-16 03:16:25', '2026-10-16 03:16:25'),
(73, 26, 16, 'A', 0, '2026-10-16 03:16:25', '2026-10-16 03:16:25'),
(74, 27, 15, 'B', 1, '2026-10-16 03:17:46', '2026-10-16 03:17:46'),
(75, 28, 15, 'B', 1, '2026-10-16 03:23:46', '2026-10-16 03:23:46'),
(76, 28, 16, 'D', 0, '2026-10-16 03:23:46', '2026-10-16 03:23:46'),
(77, 28, 17, 'D', 0, '2026-10-16 03:23:46', '2026-10-16 03:23:46'),
(78, 28, 18, 'C', 0, '2026-10-16 03:23:46', '2026-10-16 03:23:46'),
(79, 28, 19, 'B', 1, '2026-10-16 03:23:46', '2026-10-16 03:23:46'),
(80, 28, 20, 'A', 0, '2026-10-16 03:23:46', '2026-10-16 03:23:46'),
(81, 28, 21, 'B', 0, '2026-10-16 03:23:47', '2026-10-16 03:23:47'),
(82, 28, 22, 'A', 0, '2026-10-16 03:23:47', '2026-10-16 03:23:47'),
(83, 28, 23, 'A', 0, '2026-10-16 03:23:47', '2026-10-16 03:23:47'),
(84, 28, 24, 'B', 0, '2026-10-16 03:23:47', '2026-10-16 03:23:47'),
(85, 29, 25, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(86, 29, 26, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(87, 29, 27, 'A', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(88, 29, 28, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(89, 29, 29, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(90, 29, 30, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(91, 29, 31, 'C', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(92, 29, 32, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(93, 29, 33, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(94, 29, 34, 'B', 0, '2026-10-16 03:41:45', '2026-10-16 03:41:45'),
(95, 30, 45, 'B', 1, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(96, 30, 46, 'B', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(97, 30, 47, 'A', 1, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(98, 30, 48, 'B', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(99, 30, 49, 'A', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(100, 30, 50, 'A', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(101, 30, 51, 'A', 1, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(102, 30, 52, 'A', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(103, 30, 53, 'A', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(104, 30, 54, 'B', 0, '2026-10-16 03:55:01', '2026-10-16 03:55:01'),
(105, 31, 45, 'B', 1, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(106, 31, 46, 'A', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(107, 31, 47, 'A', 1, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(108, 31, 48, 'A', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(109, 31, 49, 'B', 1, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(110, 31, 50, 'B', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(111, 31, 51, 'B', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(112, 31, 52, 'B', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(113, 31, 53, 'A', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(114, 31, 54, 'A', 0, '2026-10-16 03:56:20', '2026-10-16 03:56:20'),
(115, 34, 35, 'B', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(116, 34, 36, 'B', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(117, 34, 37, 'D', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(118, 34, 38, 'A', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(119, 34, 39, 'A', 1, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(120, 34, 40, 'B', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(121, 34, 41, 'B', 1, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(122, 34, 42, 'A', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(123, 34, 43, 'B', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(124, 34, 44, 'B', 0, '2026-10-26 11:51:24', '2026-10-26 11:51:24'),
(125, 35, 35, 'B', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(126, 35, 36, 'C', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(127, 35, 37, 'D', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(128, 35, 38, 'C', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(129, 35, 39, 'A', 1, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(130, 35, 40, 'A', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(131, 35, 41, 'A', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(132, 35, 42, 'C', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(133, 35, 43, 'B', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(134, 35, 44, 'A', 0, '2026-10-26 11:52:50', '2026-10-26 11:52:50'),
(135, 36, 15, 'B', 1, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(136, 36, 16, 'D', 0, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(137, 36, 17, 'B', 0, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(138, 36, 18, 'A', 0, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(139, 36, 19, 'A', 0, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(140, 36, 20, 'C', 1, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(141, 36, 21, 'D', 0, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(142, 36, 22, 'D', 1, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(143, 36, 23, 'C', 0, '2026-11-01 23:37:58', '2026-11-01 23:37:58'),
(144, 37, 25, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(145, 37, 26, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(146, 37, 27, 'A', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(147, 37, 28, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(148, 37, 29, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(149, 37, 30, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(150, 37, 31, 'C', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(151, 37, 32, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(152, 37, 33, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(153, 37, 34, 'B', 0, '2026-11-01 23:38:46', '2026-11-01 23:38:46'),
(154, 38, 35, 'B', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(155, 38, 36, 'A', 1, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(156, 38, 37, 'C', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(157, 38, 38, 'B', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(158, 38, 39, 'C', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(159, 38, 40, 'B', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(160, 38, 41, 'A', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(161, 38, 42, 'B', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(162, 38, 43, 'D', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(163, 38, 44, 'A', 0, '2026-11-01 23:39:44', '2026-11-01 23:39:44'),
(164, 39, 5, 'C', 1, '2026-11-02 00:48:43', '2026-11-02 00:48:43'),
(165, 39, 6, 'B', 1, '2026-11-02 00:48:43', '2026-11-02 00:48:43'),
(166, 40, 2, 'B', 1, '2026-11-02 01:02:15', '2026-11-02 01:02:15'),
(167, 41, 3, 'B', 1, '2026-11-02 01:02:57', '2026-11-02 01:02:57'),
(168, 41, 4, 'A', 1, '2026-11-02 01:02:57', '2026-11-02 01:02:57'),
(169, 42, 45, 'B', 1, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(170, 42, 46, 'C', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(171, 42, 47, 'C', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(172, 42, 48, 'C', 1, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(173, 42, 49, 'B', 1, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(174, 42, 50, 'B', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(175, 42, 51, 'B', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(176, 42, 52, 'B', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(177, 42, 53, 'C', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(178, 42, 54, 'B', 0, '2026-11-02 01:16:59', '2026-11-02 01:16:59'),
(179, 43, 55, 'C', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(180, 43, 56, 'B', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(181, 43, 57, 'A', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(182, 43, 58, 'B', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(183, 43, 59, 'D', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(184, 43, 60, 'B', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(185, 43, 61, 'B', 1, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(186, 43, 62, 'B', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(187, 43, 63, 'C', 1, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(188, 43, 64, 'B', 0, '2026-11-02 01:18:17', '2026-11-02 01:18:17'),
(189, 44, 65, 'B', 1, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(190, 44, 66, 'A', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(191, 44, 67, 'C', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(192, 44, 68, 'B', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(193, 44, 69, 'A', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(194, 44, 70, 'C', 1, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(195, 44, 71, 'B', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(196, 44, 72, 'C', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(197, 44, 73, 'D', 0, '2026-11-02 01:19:13', '2026-11-02 01:19:13'),
(198, 44, 74, 'C', 1, '2026-11-02 01:19:13', '2026-11-02 01:19:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('ESTUDIANTE','DOCENTE','ADMINISTRADOR') NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `usuario`, `password_hash`, `rol`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin@lectoplay.edu.pe', '$2y$12$abuOS/14O335U6wTVk0Idegdrc1AqaAHrqmY18Mo/Zl6afWUjHNMe', 'ADMINISTRADOR', 1, '2026-10-11 20:16:13', '2026-10-14 22:53:25', NULL),
(2, 'elena.docente@colegio.edu.pe', '$2y$12$H4zJRnoQZC2zNwo5YZIFMuwsulFvTQlYNh.6b4qBQcjsOTvcl9VKq', 'DOCENTE', 1, '2026-10-11 20:16:13', '2026-10-14 22:53:26', NULL),
(3, 'carlos.gomez@estudiante.edu.pe', '$2y$12$bXspFKU3XjmCFoeWM7PI4utzAh7h7YMLB.TwiBGFFuVaVdys.cu1S', 'ESTUDIANTE', 1, '2026-10-11 20:16:13', '2026-10-14 22:53:26', NULL),
(4, 'maria.lopez@estudiante.edu.pe', '$2y$12$VAuxaNNuzQnsIOdeM4RD3.14tqU09h7K7Ogjjm5PRSlGcq0PBBX1.', 'ESTUDIANTE', 1, '2026-10-11 20:16:13', '2026-10-14 22:53:26', NULL),
(11, 'prueba_diagnostico_01', '$2y$12$t1pEdnWFbGdPjtb8X.uv7u4/hOW.39LCCwJLtQWXfhT5VyqcfssPW', 'ESTUDIANTE', 1, '2026-10-25 21:17:08', '2026-10-25 21:17:08', NULL),
(12, 'prueba_diagnostico_02', '$2y$12$t1pEdnWFbGdPjtb8X.uv7u4/hOW.39LCCwJLtQWXfhT5VyqcfssPW', 'ESTUDIANTE', 1, '2026-10-25 22:32:51', '2026-10-25 22:32:51', NULL),
(13, 'prueba_diagnostico_03', '$2y$12$t1pEdnWFbGdPjtb8X.uv7u4/hOW.39LCCwJLtQWXfhT5VyqcfssPW', 'ESTUDIANTE', 1, '2026-10-26 06:44:01', '2026-10-26 06:44:01', NULL),
(14, 'prueba_diagnostico_04', '$2y$12$t1pEdnWFbGdPjtb8X.uv7u4/hOW.39LCCwJLtQWXfhT5VyqcfssPW', 'ESTUDIANTE', 1, '2026-11-01 17:39:28', '2026-11-01 17:39:28', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `idx_actividad_nivel_estado` (`nivel_id`,`estado`);

--
-- Indices de la tabla `actividad_intento`
--
ALTER TABLE `actividad_intento`
  ADD PRIMARY KEY (`id_intento`),
  ADD KEY `fk_intento_actividad` (`actividad_id`),
  ADD KEY `idx_intento_est_act` (`estudiante_id`,`actividad_id`);

--
-- Indices de la tabla `adaptacion_nivel`
--
ALTER TABLE `adaptacion_nivel`
  ADD PRIMARY KEY (`id_adaptacion`),
  ADD KEY `idx_adaptacion_estudiante_fecha` (`estudiante_id`,`fecha`),
  ADD KEY `fk_adaptacion_nivel_anterior` (`nivel_anterior_id`),
  ADD KEY `fk_adaptacion_nivel_resultante` (`nivel_resultante_id`);

--
-- Indices de la tabla `adaptacion_nivel_actividad`
--
ALTER TABLE `adaptacion_nivel_actividad`
  ADD PRIMARY KEY (`adaptacion_id`,`actividad_id`),
  ADD UNIQUE KEY `uq_adaptacion_estudiante_actividad` (`estudiante_id`,`actividad_id`),
  ADD UNIQUE KEY `uq_adaptacion_intento` (`intento_id`),
  ADD KEY `idx_adaptacion_detalle_estudiante` (`estudiante_id`),
  ADD KEY `idx_adaptacion_detalle_actividad` (`actividad_id`);

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_administrador`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `cuestionario_diagnostico`
--
ALTER TABLE `cuestionario_diagnostico`
  ADD PRIMARY KEY (`id_cuestionario`),
  ADD UNIQUE KEY `uq_cuestionario_nombre_version` (`nombre`,`version`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`id_docente`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`),
  ADD KEY `fk_est_nivel` (`nivel_actual_id`);

--
-- Indices de la tabla `estudiante_logro`
--
ALTER TABLE `estudiante_logro`
  ADD PRIMARY KEY (`id_estudiante`,`id_logro`),
  ADD KEY `fk_el_logro` (`id_logro`);

--
-- Indices de la tabla `evaluacion`
--
ALTER TABLE `evaluacion`
  ADD PRIMARY KEY (`id_evaluacion`),
  ADD UNIQUE KEY `uq_evaluacion_estudiante_cuestionario` (`estudiante_id`,`cuestionario_id`),
  ADD KEY `fk_eval_nivel` (`nivel_id`),
  ADD KEY `idx_eval_est_fecha` (`estudiante_id`,`fecha`),
  ADD KEY `idx_evaluacion_cuestionario` (`cuestionario_id`);

--
-- Indices de la tabla `gamificacion`
--
ALTER TABLE `gamificacion`
  ADD PRIMARY KEY (`id_gamificacion`),
  ADD UNIQUE KEY `estudiante_id` (`estudiante_id`);

--
-- Indices de la tabla `lectura_diagnostica`
--
ALTER TABLE `lectura_diagnostica`
  ADD PRIMARY KEY (`id_lectura`),
  ADD UNIQUE KEY `uq_lectura_cuestionario_orden` (`cuestionario_id`,`orden`);

--
-- Indices de la tabla `logro`
--
ALTER TABLE `logro`
  ADD PRIMARY KEY (`id_logro`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `nivel`
--
ALTER TABLE `nivel`
  ADD PRIMARY KEY (`id_nivel`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD PRIMARY KEY (`id_pregunta`),
  ADD UNIQUE KEY `uq_pregunta_lectura_orden` (`lectura_diagnostica_id`,`orden`),
  ADD KEY `fk_preg_eval` (`evaluacion_id`),
  ADD KEY `fk_preg_actividad` (`actividad_id`),
  ADD KEY `idx_pregunta_cuestionario` (`cuestionario_id`),
  ADD KEY `idx_pregunta_lectura` (`lectura_diagnostica_id`);

--
-- Indices de la tabla `progreso`
--
ALTER TABLE `progreso`
  ADD PRIMARY KEY (`id_progreso`),
  ADD UNIQUE KEY `estudiante_id` (`estudiante_id`);

--
-- Indices de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD UNIQUE KEY `uq_resp_eval_preg` (`evaluacion_id`,`pregunta_id`),
  ADD KEY `fk_resp_preg` (`pregunta_id`);

--
-- Indices de la tabla `respuesta_actividad`
--
ALTER TABLE `respuesta_actividad`
  ADD PRIMARY KEY (`id_respuesta_actividad`),
  ADD UNIQUE KEY `uq_respact_intento_preg` (`intento_id`,`pregunta_id`),
  ADD KEY `fk_respact_preg` (`pregunta_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad`
--
ALTER TABLE `actividad`
  MODIFY `id_actividad` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `actividad_intento`
--
ALTER TABLE `actividad_intento`
  MODIFY `id_intento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `adaptacion_nivel`
--
ALTER TABLE `adaptacion_nivel`
  MODIFY `id_adaptacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_administrador` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cuestionario_diagnostico`
--
ALTER TABLE `cuestionario_diagnostico`
  MODIFY `id_cuestionario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `docente`
--
ALTER TABLE `docente`
  MODIFY `id_docente` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `id_estudiante` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `evaluacion`
--
ALTER TABLE `evaluacion`
  MODIFY `id_evaluacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `gamificacion`
--
ALTER TABLE `gamificacion`
  MODIFY `id_gamificacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lectura_diagnostica`
--
ALTER TABLE `lectura_diagnostica`
  MODIFY `id_lectura` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `logro`
--
ALTER TABLE `logro`
  MODIFY `id_logro` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `nivel`
--
ALTER TABLE `nivel`
  MODIFY `id_nivel` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `id_pregunta` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `progreso`
--
ALTER TABLE `progreso`
  MODIFY `id_progreso` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `id_respuesta` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuesta_actividad`
--
ALTER TABLE `respuesta_actividad`
  MODIFY `id_respuesta_actividad` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD CONSTRAINT `fk_actividad_nivel` FOREIGN KEY (`nivel_id`) REFERENCES `nivel` (`id_nivel`);

--
-- Filtros para la tabla `actividad_intento`
--
ALTER TABLE `actividad_intento`
  ADD CONSTRAINT `fk_intento_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividad` (`id_actividad`),
  ADD CONSTRAINT `fk_intento_est` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `adaptacion_nivel`
--
ALTER TABLE `adaptacion_nivel`
  ADD CONSTRAINT `fk_adaptacion_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_adaptacion_nivel_anterior` FOREIGN KEY (`nivel_anterior_id`) REFERENCES `nivel` (`id_nivel`),
  ADD CONSTRAINT `fk_adaptacion_nivel_resultante` FOREIGN KEY (`nivel_resultante_id`) REFERENCES `nivel` (`id_nivel`);

--
-- Filtros para la tabla `adaptacion_nivel_actividad`
--
ALTER TABLE `adaptacion_nivel_actividad`
  ADD CONSTRAINT `fk_adaptacion_detalle` FOREIGN KEY (`adaptacion_id`) REFERENCES `adaptacion_nivel` (`id_adaptacion`),
  ADD CONSTRAINT `fk_adaptacion_detalle_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividad` (`id_actividad`),
  ADD CONSTRAINT `fk_adaptacion_detalle_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_adaptacion_detalle_intento` FOREIGN KEY (`intento_id`) REFERENCES `actividad_intento` (`id_intento`);

--
-- Filtros para la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `fk_admin_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `docente`
--
ALTER TABLE `docente`
  ADD CONSTRAINT `fk_doc_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD CONSTRAINT `fk_est_nivel` FOREIGN KEY (`nivel_actual_id`) REFERENCES `nivel` (`id_nivel`),
  ADD CONSTRAINT `fk_est_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estudiante_logro`
--
ALTER TABLE `estudiante_logro`
  ADD CONSTRAINT `fk_el_est` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_el_logro` FOREIGN KEY (`id_logro`) REFERENCES `logro` (`id_logro`);

--
-- Filtros para la tabla `evaluacion`
--
ALTER TABLE `evaluacion`
  ADD CONSTRAINT `fk_eval_est` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_eval_nivel` FOREIGN KEY (`nivel_id`) REFERENCES `nivel` (`id_nivel`),
  ADD CONSTRAINT `fk_evaluacion_cuestionario` FOREIGN KEY (`cuestionario_id`) REFERENCES `cuestionario_diagnostico` (`id_cuestionario`);

--
-- Filtros para la tabla `gamificacion`
--
ALTER TABLE `gamificacion`
  ADD CONSTRAINT `fk_gam_est` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `lectura_diagnostica`
--
ALTER TABLE `lectura_diagnostica`
  ADD CONSTRAINT `fk_lectura_cuestionario` FOREIGN KEY (`cuestionario_id`) REFERENCES `cuestionario_diagnostico` (`id_cuestionario`);

--
-- Filtros para la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD CONSTRAINT `fk_preg_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividad` (`id_actividad`),
  ADD CONSTRAINT `fk_preg_eval` FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluacion` (`id_evaluacion`),
  ADD CONSTRAINT `fk_pregunta_cuestionario` FOREIGN KEY (`cuestionario_id`) REFERENCES `cuestionario_diagnostico` (`id_cuestionario`),
  ADD CONSTRAINT `fk_pregunta_lectura` FOREIGN KEY (`lectura_diagnostica_id`) REFERENCES `lectura_diagnostica` (`id_lectura`);

--
-- Filtros para la tabla `progreso`
--
ALTER TABLE `progreso`
  ADD CONSTRAINT `fk_prog_est` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD CONSTRAINT `fk_resp_eval` FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluacion` (`id_evaluacion`),
  ADD CONSTRAINT `fk_resp_preg` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id_pregunta`);

--
-- Filtros para la tabla `respuesta_actividad`
--
ALTER TABLE `respuesta_actividad`
  ADD CONSTRAINT `fk_respact_intento` FOREIGN KEY (`intento_id`) REFERENCES `actividad_intento` (`id_intento`),
  ADD CONSTRAINT `fk_respact_preg` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id_pregunta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
