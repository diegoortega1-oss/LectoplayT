# LectoPlay

**Sistema web gamificado adaptativo para reforzar la comprensión lectora en estudiantes de primaria de la I.E. 1221 María Parado de Bellido.**

## 1. Descripción del proyecto

LectoPlay es un sistema web desarrollado como parte de un proyecto de tesis de Ingeniería de Sistemas. Su propósito es reforzar la comprensión lectora de estudiantes de educación primaria mediante actividades de lectura, evaluación del desempeño y elementos de gamificación.

La propuesta contempla el seguimiento del progreso de los estudiantes y la adaptación de las actividades según su nivel de aprendizaje, con el objetivo de ofrecer una experiencia educativa más motivadora y adecuada a sus necesidades.

## 2. Objetivos

- Reforzar la comprensión lectora en las dimensiones literal, inferencial y crítica.
- Ofrecer actividades de lectura de acuerdo con el nivel asignado al estudiante.
- Incorporar elementos de gamificación para motivar la participación.
- Registrar los resultados de las actividades y el progreso de los estudiantes.
- Facilitar a docentes y administradores el acceso a funcionalidades según su rol.
- Incorporar mecanismos de adaptación que permitan recomendar actividades según el desempeño.

## 3. Tecnologías utilizadas

- **Backend:** PHP y Laravel.
- **Frontend:** Blade, HTML y CSS.
- **Base de datos:** MySQL.
- **Entorno local:** XAMPP.
- **Editor de código:** Visual Studio Code.
- **Control de versiones:** Git y GitHub.

## 4. Funcionalidades

### Implementadas

- Autenticación de usuarios.
- Inicio de sesión con validación de credenciales y estado del usuario.
- Control de acceso según los roles de estudiante, docente y administrador.
- Panel principal del estudiante.
- Visualización del nivel asignado, puntos, logros y actividades.
- Listado de actividades activas correspondientes al nivel del estudiante.
- Desarrollo y resolución de actividades de lectura.
- Guardado y edición de respuestas durante un intento en curso.
- Finalización de actividades y visualización de resultados.
- Historial de intentos realizados por el estudiante.
- Registro de puntos de gamificación según las reglas implementadas.
- Pruebas automatizadas para validar el flujo de actividades.

### En desarrollo

- Diagnóstico inicial de comprensión lectora.
- Asignación automática del nivel inicial a partir del resultado del diagnóstico.
- Recomendaciones adaptativas de actividades.
- Ampliación de las funcionalidades de seguimiento para docentes y administradores.

El estado de las funcionalidades se actualizará conforme avance el desarrollo.

## 5. Requisitos previos

Para ejecutar el proyecto localmente, se necesita:

- PHP 8.2 o una versión compatible con las dependencias del proyecto.
- Composer.
- MySQL.
- XAMPP u otro entorno que proporcione PHP y MySQL.
- Git.
- Node.js y npm, si se requiere instalar o compilar los recursos frontend.

Las versiones compatibles pueden consultarse en `composer.json` y `package.json`, si este último está presente.

## 6. Instalación y ejecución

### 6.1. Obtener el código fuente

Clona el repositorio:

```bash
git clone URL_DEL_REPOSITORIO
cd Lectoplay
```

Reemplaza `URL_DEL_REPOSITORIO` por la dirección real del repositorio.

Si ya tienes el proyecto descargado, puedes omitir este paso.

### 6.2. Instalar dependencias de PHP

Desde la raíz del proyecto, ejecuta:

```bash
composer install
```

### 6.3. Configurar las variables de entorno

Copia el archivo de ejemplo:

```powershell
Copy-Item .env.example .env
```

Si ya tienes un archivo `.env`, no lo sobrescribas.

Configura las variables necesarias para la aplicación y la conexión a MySQL. Por ejemplo:

```env
APP_NAME=LectoPlay
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bdlectoplay
DB_USERNAME=root
DB_PASSWORD=
```

Los valores anteriores son ilustrativos para un entorno local. Deben ajustarse a la configuración de MySQL de cada equipo. No se deben publicar credenciales reales.

### 6.4. Generar la clave de Laravel

Si se trata de una instalación nueva y el archivo `.env` no contiene una clave válida, ejecuta:

```bash
php artisan key:generate
```

### 6.5. Preparar la base de datos

El proyecto incluye un archivo SQL de referencia ubicado en:

```text
database/backup/bdlectoplay.sql
```

Para preparar la base de datos en un entorno local:

1. Inicia MySQL desde XAMPP.
2. Abre phpMyAdmin en `http://localhost/phpmyadmin`.
3. Crea una base de datos llamada `bdlectoplay`, si todavía no existe.
4. Selecciona la base de datos creada.
5. Accede a la pestaña **Importar**.
6. Selecciona `database/backup/bdlectoplay.sql` e inicia la importación.
7. Comprueba que las tablas se hayan creado correctamente.
8. Verifica las credenciales de conexión del archivo `.env`.

**Importante:** el archivo SQL debe corresponder a la estructura y los datos necesarios para la versión del proyecto que se está ejecutando. Debe probarse en una base de datos vacía antes de utilizarse como procedimiento de instalación.

No ejecutes migraciones sobre una base de datos existente sin verificar previamente su estado y compatibilidad.

### 6.6. Ejecutar la aplicación

Desde la raíz del proyecto, ejecuta:

```bash
php artisan serve
```

Abre en el navegador la dirección indicada por Laravel, normalmente:

http://127.0.0.1:8000

## 7. Pruebas automatizadas

Para ejecutar las pruebas automatizadas disponibles, utiliza:

```bash
php artisan test
```

Las pruebas deben ejecutarse en el entorno configurado para testing. Si utilizan SQLite en memoria, sus resultados no sustituyen las pruebas de integración sobre MySQL.

## 8. Estructura principal del proyecto

```text
Lectoplay/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   ├── Controller.php
│   │   │   ├── StudentActivityController.php
│   │   │   └── StudentDashboardController.php
│   │   ├── Middleware/
│   │   │   └── EnsureRole.php
│   │   ├── Models/
│   │   │   ├── Actividad.php
│   │   │   ├── ActividadIntento.php
│   │   │   ├── Administrador.php
│   │   │   ├── Docente.php
│   │   │   ├── Estudiante.php
│   │   │   ├── Evaluacion.php
│   │   │   ├── Gamificacion.php
│   │   │   ├── Logro.php
│   │   │   ├── Nivel.php
│   │   │   ├── Pregunta.php
│   │   │   ├── Progreso.php
│   │   │   ├── Respuesta.php
│   │   │   ├── RespuestaActividad.php
│   │   │   ├── User.php
│   │   │   └── Usuario.php
│   │   └── Providers/
│   │       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── gamification.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
├── database/
│   ├── backups/
│   │   └── bdlectoplay.sql
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   └── 2026_10_15_000001_add_puntos_obtenidos_to_actividad_intento_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── panel.blade.php
│       ├── student/
│       │   ├── activities/
│       │   │   ├── _header.blade.php
│       │   │   ├── attempt.blade.php
│       │   │   ├── history.blade.php
│       │   │   ├── result.blade.php
│       │   │   └── show.blade.php
│       │   └── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── console.php
│   └── web.php
├── storage/
│   └── logs/
│       └── .gitignore
├── tests/
│   ├── Feature/
│   │   ├── ExampleTest.php
│   │   └── StudentActivityFlowTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   ├── Pest.php
│   └── TestCase.php
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── phpunit.xml
├── README.md
└── vite.config.js
```

La estructura mostrada resume los directorios principales y puede variar conforme avance el desarrollo.

## 9. Base de datos y seguridad

- El archivo `.env` contiene configuración local y no debe publicarse.
- No deben incluirse contraseñas reales ni credenciales de acceso.
- Los archivos SQL deben revisarse antes de publicarse para evitar exponer información personal o sensible.
- Los datos de demostración, si se incluyen, deben ser ficticios o estar preparados para su distribución segura.
- La base de datos utilizada para pruebas debe estar separada de la base de datos de desarrollo.

## 10. Control de versiones

El código fuente se administra mediante Git y se publica en un repositorio remoto para documentar la evolución del proyecto.

Los commits deben representar cambios reales, como la implementación de funcionalidades, correcciones, pruebas y actualizaciones de documentación.

## 11. Contexto académico

- **Proyecto:** LectoPlay.
- **Área:** Ingeniería de Sistemas.
- **Tipo:** Proyecto de tesis.
- **Institución educativa de aplicación:** I.E. N.° 1221 María Parado de Bellido.

