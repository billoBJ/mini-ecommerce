# Billo Commerce

API de e-commerce construida con Laravel 13, pensada como backend para una SPA de Vue 3 separada (autenticación por cookie/sesión vía Sanctum, no tokens Bearer). Sigue una arquitectura por capas (hexagonal-ish): dominio puro, casos de uso, adaptadores de persistencia y adaptadores HTTP, todos desacoplados por interfaces.

## Stack

- **Laravel 13** / PHP 8.3+
- **PostgreSQL 16**
- **Laravel Sanctum** — autenticación de SPA por cookie + CSRF (no personal access tokens)
- **Redis** — extensión PHP instalada y disponible; cache/sesión/queue usan el driver `database` por defecto (ver [Variables de entorno](#variables-de-entorno))
- **Colas de Laravel** — worker dedicado en Docker (`queue:work`), driver `database`
- **Vite + Tailwind 4** — únicamente para los assets propios de Laravel (`welcome.blade.php`); la SPA de Vue vive en un repo aparte y consume esta API vía CORS
- **PHPUnit** — configurado (`pestphp/pest-plugin` está permitido en `composer.json` pero Pest no está instalado todavía)
- **Docker** — Dockerfile multi-stage + docker-compose (app, nginx, postgres, queue)

## Contenido / Arquitectura

El dominio de negocio implementado cubre **Auth, Products, Customers y Orders** (con `order_items` y `order_status_history` como parte del agregado `Order`), más el esquema de base de datos completo para **Teams, Roles/Permissions, Payments y Webhooks** (migraciones y seeders listos; sin lógica de aplicación todavía).

Cada feature con lógica de negocio sigue el mismo patrón de capas:

```
app/
├── Domain/<Feature>/          Entidades, value objects, puertos (interfaces) y excepciones de dominio.
│                               No conocen Eloquent ni Laravel. Ej: Order.php calcula sus propios
│                               totales y valida sus propias transiciones de estado.
├── DTOs/<Feature>/             Forma de los datos que cruzan la frontera HTTP -> Aplicación.
├── Services/<Feature>/         Casos de uso (orquestación): hablan con los puertos del dominio,
│                               nunca con Eloquent directamente.
├── Infrastructure/
│   └── Persistence/Eloquent/  Adaptadores que implementan los puertos del dominio usando Eloquent.
│                               Traducen entre el Model (persistencia) y la Entity (dominio).
├── Models/                    Eloquent models puros (persistencia), sin lógica de negocio.
├── Http/
│   ├── Controllers/Api/<Feature>/   Controladores de una sola acción (__invoke).
│   ├── Requests/<Feature>/          Validación de entrada (FormRequest).
│   └── Resources/                   Formato de salida JSON.
└── Providers/AppServiceProvider.php Bindings puerto -> adaptador (ej. ProductRepositoryInterface -> EloquentProductRepository).
```

**Por qué esta separación:** el dominio (`Order`, `OrderItem`, `Product`, `Customer`) nunca depende de Eloquent, así que la lógica de negocio se puede probar sin base de datos. La capa `Services` orquesta (busca datos, valida reglas que requieren I/O, guarda); la capa `Domain` protege invariantes que no requieren I/O (una orden no puede tener cero items, las transiciones de estado siguen una máquina de estados fija, los totales se calculan, nunca se confían).

`Order` es el ejemplo más completo: agrega sus propios `OrderItem`, calcula `subtotal()`/`tax()`/`total()` a partir de los items (nunca los recibe ya calculados), y valida transiciones de estado (`pending -> confirmed -> processing -> shipped -> completed`, o `pending`/`confirmed -> cancelled`) antes de persistir. `EloquentOrderRepository::save()` guarda la orden y sus items en una sola transacción de base de datos.

## Autenticación

Es autenticación de SPA basada en **cookies de sesión + CSRF**, no tokens Bearer:

1. El frontend hace `GET /sanctum/csrf-cookie` primero (setea la cookie `XSRF-TOKEN`).
2. `POST /api/auth/register` o `POST /api/auth/login` con el header `X-XSRF-TOKEN` (el propio cliente HTTP de Axios/Vue suele hacerlo automático). Esto crea una sesión (`Auth::login()`), no un token.
3. Las siguientes requests solo necesitan enviar las cookies (`credentials: 'include'` / `withCredentials: true`); Sanctum valida la sesión vía el middleware `statefulApi()`.
4. `POST /api/auth/logout` invalida la sesión y regenera el token CSRF.

Esto requiere que el dominio del frontend esté en `SANCTUM_STATEFUL_DOMAINS` y `FRONTEND_URL` (para CORS con `supports_credentials: true`) — ver `.env.example`. Por defecto apunta a `localhost:5190` (Vite dev server de la SPA). Una request solo se trata como "de sesión" si su `Origin`/`Referer` coincide con esos dominios — si no coincide, Sanctum la trata como stateless y falla silenciosamente (login "exitoso" pero `/me` da 401 después). Con herramientas que no son un navegador (Postman, curl) hay que agregar ese header a mano; el navegador lo manda solo.

**El token CSRF rota en cada login/logout** (regeneración de sesión, buena práctica). El frontend debe leerlo de la cookie `XSRF-TOKEN` en cada request de escritura, nunca cachearlo una sola vez al arrancar la app — si lo cachea, empieza a fallar con `419` justo después del primer login.

## Endpoints

23 rutas registradas (`php artisan route:list`). El detalle completo con ejemplos de body está en [`Billo-Commerce.postman_collection.json`](./Billo-Commerce.postman_collection.json) (importable directo en Postman).

| Recurso | Rutas | Auth |
|---|---|---|
| Auth | `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/me`, `POST /api/auth/logout` | login/register públicas, resto requiere sesión |
| Products | `GET /api/products`, `GET /api/products/{id}` | públicas |
| | `POST`, `PUT /api/products/{id}`, `DELETE /api/products/{id}` | requieren sesión |
| Customers | `GET/POST/PUT/DELETE /api/customers[/{id}]` | todas requieren sesión (PII) |
| Orders | `GET/POST /api/orders`, `GET /api/orders/{id}`, `PATCH /api/orders/{id}/status` | todas requieren sesión |

## Base de datos

20 migraciones, PostgreSQL. Grupos:

- **Laravel/Sanctum**: `users` (+ `current_team_id`), `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `personal_access_tokens`.
- **Teams & permisos**: `teams`, `team_user`, `team_invitations`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` (esquema listo, sin endpoints todavía).
- **Comercio**: `products`, `customers`, `orders`, `order_items`, `order_status_history`, `payments`, `webhooks`.

Reglas de borrado clave: `orders.customer_id` y `order_items.product_id` son `RESTRICT` (no se puede borrar un customer/product con historial); `order_items`/`order_status_history` son `CASCADE` respecto a su `order_id` (mueren con la orden); `changed_by`/`user_id` en tablas de auditoría son `SET NULL`. `order_items` guarda un snapshot (`name`/`sku`/`price`) del producto al momento de la compra, independiente del registro vivo.

**Seeders** (`php artisan db:seed`): 11 usuarios, 5 equipos, roles/permisos con asignaciones, 30 productos, 15 customers, 25 orders con items/historial/pagos coherentes entre sí, 12 webhooks.

## Instalación

### Opción A — Docker (recomendado)

Requiere Docker y Docker Compose.

```bash
cp .env.example .env
docker compose up -d --build
```

Eso levanta 4 servicios:

| Servicio | Qué hace | Puerto |
|---|---|---|
| `nginx` | Sirve la app | `8000` (host) -> `80` |
| `app` | PHP-FPM (Laravel) | `9000` (interno) |
| `postgres` | Base de datos | `5432` |
| `queue` | `php artisan queue:work` | — |

El `docker-entrypoint.sh` del contenedor `app` hace automáticamente, en cada arranque: copiar `.env` si falta, `composer install` si falta `vendor/`, esperar a que Postgres esté listo, generar `APP_KEY` si falta, y correr `php artisan migrate --force` (controlado por `RUN_MIGRATIONS=true` en `docker-compose.yml`; el servicio `queue` lo tiene en `false` para no migrar dos veces). El contenedor `app` recién se marca *healthy* cuando termina — `queue` espera explícitamente ese healthcheck (`condition: service_healthy`) antes de arrancar su propio `queue:work`; `nginx` solo espera a que el contenedor `app` arranque (no a que termine de migrar), así que la primera petición justo al levantar los servicios puede tardar unos segundos en responder.

```bash
# Sembrar datos de prueba
docker compose exec app php artisan db:seed

# Ver logs
docker compose logs -f app

# Correr cualquier comando artisan/composer
docker compose exec app php artisan tinker
docker compose exec app composer install

# Parar
docker compose down

# Parar y borrar también el volumen de Postgres (reinicio total)
docker compose down -v
```

La app queda en `http://localhost:8000`.

### Opción B — Manual (sin Docker)

Requiere PHP 8.3+, Composer, PostgreSQL 16, Node 22.

```bash
composer install
cp .env.example .env
php artisan key:generate

# Ajusta DB_* en .env a tu Postgres local, luego:
php artisan migrate
php artisan db:seed   # opcional, datos de prueba

npm install
npm run build          # o `npm run dev` en desarrollo

php artisan serve
```

`composer run dev` levanta servidor + queue worker + Vite watcher en paralelo (usa `concurrently`).

## Variables de entorno relevantes

| Variable | Para qué | Default |
|---|---|---|
| `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, ... | Conexión a Postgres | `pgsql` / `127.0.0.1` |
| `SESSION_DRIVER` | Dónde vive la sesión (auth cookie-based) | `database` |
| `SANCTUM_STATEFUL_DOMAINS` | Qué dominios frontend reciben auth por cookie | `localhost:5190,...` |
| `FRONTEND_URL` | Origen(es) permitido(s) en CORS (`supports_credentials: true`) | `http://localhost:5190,...` |
| `QUEUE_CONNECTION` | Driver de colas | `database` |
| `CACHE_STORE` | Driver de cache | `database` |
| `REDIS_*` | Redis está disponible (extensión instalada) pero no es el driver activo por defecto | — |

## Testing

```bash
php artisan test
```

Actualmente solo están los tests de ejemplo que trae Laravel por defecto (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`) — todavía no hay cobertura de Products/Customers/Orders.

## Colección de Postman

[`Billo-Commerce.postman_collection.json`](./Billo-Commerce.postman_collection.json) — los 18 endpoints de negocio con ejemplos de body y variables encadenadas (login guarda el token/sesión, crear un recurso guarda su id para los siguientes requests).
