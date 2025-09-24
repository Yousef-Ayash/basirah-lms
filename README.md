<p align="center">
  <a href="./README.md">English</a> | 
  <a href="./README.ar.md">العربية</a>
</p>

# Basirah LMS

This is a Learning Management System (LMS) web application built with Laravel, Inertia, and Vue.js. It was created to replace an older WordPress-based LMS, providing a more robust, modern, and maintainable solution for managing educational content and users.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running the Application](#running-the-application)
    - [Development](#development)
    - [Production](#production)
- [Environment Configuration](#environment-configuration)

---

## Prerequisites

Before you begin, ensure you have the following software installed on your machine:

- **PHP** (\>= 8.1)
- **Composer** (PHP dependency manager)
- **Node.js** (\>= 18.x) & **npm** (or yarn)
- **A database server** (e.g., MySQL, PostgreSQL, SQL Server)

---

## Installation

Follow these steps to get your development environment set up.

1.  **Clone the repository:**
    Open your terminal and run the following command to clone the project:

    ```bash
    git clone https://github.com/Yousef-Ayash/basirah-lms.git
    cd basirah-lms
    ```

2.  **Install PHP Dependencies:**
    Use Composer to install the required PHP packages.

    ```bash
    composer install
    ```

3.  **Install JavaScript Dependencies:**
    Use npm (or your preferred package manager like yarn) to install the frontend packages.

    ```bash
    npm install
    ```

4.  **Set Up Environment File:**
    Copy the example environment file. This file is where you'll store your application's secrets and configuration.

    ```bash
    cp .env.example .env
    ```

5.  **Generate Application Key:**
    Laravel requires an application key for securing user sessions and other encrypted data.

    ```bash
    php artisan key:generate
    ```

6.  **Configure `.env` file:**
    Open the `.env` file in your code editor and update the following variables to match your local setup:
    - `APP_NAME`: Your application's name.
    - `APP_URL`: The full URL for your local development server (e.g., `http://localhost:8000`).
    - `DB_CONNECTION`: The type of database you're using (e.g., `mysql`, `pgsql`, `sqlsrv`).
    - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Your database credentials.

7.  **Run Database Migrations:**
    This command will create all the necessary tables in your database. You can add the `--seed` flag to also populate the database with initial data if seeders have been created.

    ```bash
    php artisan migrate --seed
    ```

---

## Running the Application

### Development

For development, you need to run two processes simultaneously in separate terminal windows: the Vite development server for asset compilation and hot-reloading, and the Laravel development server.

1.  **Terminal 1: Start the Vite Dev Server**
    This server will watch your Vue files for changes and automatically refresh the browser.

    ```bash
    npm run dev
    ```

2.  **Terminal 2: Start the Laravel Server**
    This server will handle the application's backend logic.

    ```bash
    php artisan serve
    ```

🚀 Your application should now be running and accessible at the URL specified by `APP_URL` in your `.env` file (typically `http://localhost:8000`).

---

### Production

When deploying to a production server, the process is different. You do not run the Vite dev server. Instead, you build optimized, versioned assets.

1.  **Build Frontend Assets:**
    Run the following command to compile and minify your Vue components and other assets.

    ```bash
    npm run build
    ```

    This will create a `public/build` directory containing the compiled assets.

2.  **Optimize Laravel:**
    For better performance in production, cache your configuration, routes, and views.

    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

3.  **Web Server Configuration:**
    Configure your web server (e.g., Nginx or Apache) to serve your application. The server's document root should point to the `[your-project-path]/public` directory.

---

## Environment Configuration

Your `.env` file controls the application's behavior based on the environment. **Never commit your `.env` file to version control.**

### Development `.env` Example

For local development, you want debugging features enabled.

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_dev_db
DB_USERNAME=root
DB_PASSWORD=your_password

# ... other settings
```

### Production `.env` Example

For a live production server, debugging should be disabled for security and performance.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_prod_db
DB_USERNAME=your_prod_user
DB_PASSWORD=your_strong_secret_password

# Use production-ready drivers
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# ... other settings
```

**⚠️ Important:** Always set `APP_DEBUG=false` in a production environment. Leaving it as `true` can expose sensitive configuration data to end-users.
