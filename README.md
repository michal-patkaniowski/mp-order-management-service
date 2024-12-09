# MP Order Management Service

## Overview
This service enables users to manage orders by integrating with an external product system (FakeStoreAPI).

---

## Features
1. **Order Creation**: Users can create orders based on products fetched from the FakeStoreAPI.
2. **Order Cancellation**: Users can cancel (delete) existing orders.
3. **Order Re-creation**: Users can re-submit a previously created order.

---

## Technology Stack
- **PHP**: Version 8.1
- **Framework**: Symfony 5.4
- **Dependency Manager**: Composer
- **Version Control**: Git
- **Database**: SQLite

---

## Functional Requirements
1. **Order Management**:
   - Fetch products from the FakeStoreAPI for user selection.
   - Store orders in a local SQLite database.
   - Allow users to cancel orders.
   - Enable re-creation of previously created orders.
2. **Documentation**:
   - Provide clear instructions for running the project locally.
3. **Unit Testing**:
   - Include tests for key functionalities like order creation, cancellation, and re-creation.

---


## Setting Up the Project Locally
### Prerequisites
Ensure the following extensions are enabled in your `php.ini` configuration file:
```ini
extension=pdo_sqlite
extension=sqlite3
sqlite3.extension_dir=ext
```

### 1. Clone the Repository
```bash
git clone https://github.com/michal-patkaniowski/mp-order-management-service.git
cd <project_folder>
```

### 2. Install Dependencies
Run in the project folder:
```bash
composer install
```

### 3. Run Migrations
Run in the project folder:
```bash
php bin/console doctrine:migrations:migrate
```

### 4. Run Tests
Run in the project folder:
```bash
vendor/bin/phpunit
```

### 5. Start the Server
Run in the project folder:
```bash
symfony server:start
```

## Making API Requests

- Use Postman or any other API client to interact with the API.
- Refer to the API documentation located at `public/openapi.json`.

