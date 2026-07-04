# Smartphone Product Manager --- PHP Backend Assignment

A **Laravel REST API** for managing a smartphone catalog.

The application imports products from **DummyJSON** and exposes its own
CRUD API backed by a local SQLite database.

------------------------------------------------------------------------

# Tech Stack

-   PHP 8.4
-   Laravel
-   SQLite
-   Eloquent ORM
-   PHPUnit

------------------------------------------------------------------------

# Installation

``` bash
git clone https://github.com/nikvitkaua/appflame-test.git
cd appflame-test
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

### Seed the database

``` bash
curl -X POST http://localhost:8000/api/products/seed
```

### Run tests

``` bash
php artisan test
```

------------------------------------------------------------------------

# API Endpoints

### `GET /api/products`

Returns a paginated list of products.

**Query parameters**

- `page` – page number
- `limit` – items per page (1–100)
- `brand` – case-insensitive brand filter

---

### `GET /api/products/{id}`

Returns a single product by its ID.

---

### `POST /api/products`

Creates a new product.

---

### `PATCH /api/products/{id}`

Partially updates an existing product.

Only the provided fields are modified.

---

### `DELETE /api/products/{id}`

Deletes a product.

---

### `POST /api/products/seed`

Imports or updates all smartphones from DummyJSON.

## Create Product

``` bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{"title":"Pixel 9","brand":"Google","price":799.99,"stock":15}'
```

## Partial Update

``` bash
curl -X PATCH http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{"stock":42}'
```

------------------------------------------------------------------------

# Database Schema

The application uses a single `products` table.

### Main fields

- **id** *(bigint, primary key)* — Local primary key.
- **external_id** *(bigint, nullable, unique)* — DummyJSON product ID used to prevent duplicates during repeated imports.
- **title** *(string)* — Product title.
- **description** *(text, nullable)* — Product description.
- **brand** *(string, nullable, indexed)* — Indexed because filtering by brand is supported.
- **category** *(string, nullable)* — Product category.
- **sku** *(string, nullable)* — Product SKU.
- **price** *(decimal(10,2))* — Product price.
- **discount_percentage** *(decimal(5,2))* — Discount percentage.
- **rating** *(decimal(3,2))* — Product rating.
- **stock** *(unsigned integer)* — Available stock.
- **thumbnail** *(string, nullable)* — Thumbnail URL.
- **images** *(JSON, nullable)* — Array of image URLs.
- **tags** *(JSON, nullable)* — Array of product tags.
- **created_at / updated_at** *(timestamps)* — Laravel timestamps.

------------------------------------------------------------------------

# Design Decisions

-   Validation is handled by **Form Request** classes instead of
    controllers.
-   Communication with DummyJSON is isolated inside `DummyJsonService`.
-   `external_id` together with `updateOrCreate()` makes repeated
    imports idempotent.
-   PATCH validation uses `sometimes` rules to support partial updates.
-   `limit` is clamped to **1-100** to prevent invalid or excessive
    pagination requests.

------------------------------------------------------------------------

# Tests
-   Product list with pagination
-   Case-insensitive brand filtering
-   Retrieve a single product (including 404)
-   Product creation
-   Validation failures
-   Partial updates
-   Product deletion
-   DummyJSON import using `Http::fake()`
-   Repeated imports without duplicates

Run:

``` bash
php artisan test
```

------------------------------------------------------------------------

# AI Usage

Parts of this project including boilerplate code, tests, and the
initial README draft were created with AI assistance and then manually
reviewed, refactored, and improved.

I can explain every implementation detail during the interview.
