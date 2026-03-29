# aboutlink.me — Backend

REST API para la plataforma **aboutlink.me**, un servicio de bio-links personalizable donde los usuarios pueden crear su perfil público con links, proyectos, temas visuales y wallpapers.

Construido con **Laravel 12** y autenticación JWT.

---

## Stack

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 / PHP 8.2+ |
| Base de datos | MySQL |
| Autenticación | JWT (`tymon/jwt-auth`) |
| Procesamiento de imágenes | Intervention Image 3 |
| Login social | Google API Client, Facebook Graph SDK |

---

## Requisitos

- PHP >= 8.2
- Composer
- MySQL
- Extensiones PHP: `gd` o `imagick`, `pdo_mysql`, `mbstring`, `openssl`

---

## Instalación

```bash
git clone <repo-url> aboutlink-me-backend
cd aboutlink-me-backend

composer install

cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Configurar el archivo `.env`:

```env
APP_NAME=aboutlink.me
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aboutlink_me_backend
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
```

Ejecutar migraciones y seeders:

```bash
php artisan migrate --seed
php artisan storage:link
```

---

## Reset de base de datos

La base de datos se reinicia completamente con:

```bash
php artisan migrate:fresh --seed
```

Esto recrea todas las tablas y siembra los datos iniciales:
- **ThemeSeeder** — 12 temas visuales (air, lake, bloom, etc.)
- **CategorySeeder** — 12 categorías de links (GitHub, LinkedIn, Instagram, etc.)
- **JohnDoeSeeder** — Usuario demo `john.doe@example.com` con proyectos, links y diseño de perfil configurado

### Usuario demo

| Campo | Valor |
|---|---|
| Email | `john.doe@example.com` |
| Password | `123456` |
| Nickname | `johndoe` |
| Tema | Lake |
| Wallpaper | `original_1774761315.webp` |

---

## Endpoints principales

Base URL: `/api`

### Autenticación (`/api/auth`)

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/auth/login` | Login con email y password |
| POST | `/auth/register` | Registro de nuevo usuario |
| POST | `/auth/logout` | Cerrar sesión |
| POST | `/auth/refresh` | Refrescar token JWT |
| GET | `/auth/me` | Datos del usuario autenticado |
| PUT | `/auth/update-profile` | Actualizar perfil completo |
| POST | `/auth/validate-nickname` | Verificar disponibilidad de nickname |
| DELETE | `/auth/delete-account` | Eliminar cuenta |
| POST | `/auth/google` | Login con Google |
| POST | `/auth/facebook` | Login con Facebook |
| POST | `/auth/validate-token` | Validar token JWT |

### Público

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/landing` | Obtener perfil público por nickname |

### Protegidos (requieren Bearer token)

| Método | Ruta | Descripción |
|---|---|---|
| GET/POST/PUT/DELETE | `/links` | CRUD de links |
| PUT | `/link/updateState/{id}` | Activar/desactivar link |
| PUT | `/updateOrderslinks` | Reordenar links |
| GET/POST/PUT/DELETE | `/projects` | CRUD de proyectos |
| POST | `/projects/update/{id}` | Actualizar proyecto (con imágenes) |
| PUT | `/project/updateState/{id}` | Activar/desactivar proyecto |
| PUT | `/updateOrdersprojects` | Reordenar proyectos |
| GET/POST | `/user-profile-design` | Obtener/guardar diseño de perfil |
| DELETE | `/profile/wallpaper` | Eliminar wallpaper |
| POST | `/upload/profile` | Subir foto de perfil |
| DELETE | `/profile/photo` | Eliminar foto de perfil |
| DELETE | `/galery/image/{id}` | Eliminar imagen de galería |
| GET | `/themes` | Listar temas disponibles |
| GET | `/categories` | Listar categorías de links |

---

## Almacenamiento de imágenes

Las imágenes se guardan en `storage/app/public/` con acceso público vía `storage/`:

```
storage/app/public/
├── profile/          # Fotos de perfil
│   ├── {timestamp}.webp
│   ├── thumb_{timestamp}.webp
│   └── medium_{timestamp}.webp
├── wallpaper/        # Wallpapers de perfil
│   └── original_{timestamp}.webp
└── galery/           # Imágenes de proyectos
    ├── {timestamp}{random}.webp
    ├── thumb_{timestamp}{random}.webp
    └── medium_{timestamp}{random}.webp
```

---

## Diseño de perfil

Cada usuario puede personalizar su perfil con:

- **Tema** — 12 opciones (3 gratuitas, 9 premium): `air`, `blocks`, `lake`, `mineral`, `rise`, `bloom`, `breeze`, `astrid`, `groove`, `agate`, `twilight`, `grid`
- **Wallpaper tipo imagen** — archivo subido por el usuario
- **Wallpaper tipo color** — color sólido predefinido o personalizado (hex)
- **Wallpaper tipo patrón** — patrones predefinidos

---

## Licencia

Proyecto privado — todos los derechos reservados.
