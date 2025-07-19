# Laravel Ticketing System

## Key Features

- **Complete Ticketing System**: Create, view, reply to, and manage the status of tickets.
- **Authentication**: Secure registration, login, and logout system for users and admins via both API (Sanctum) and web.
- **Roles & Permissions**: Utilizes the `spatie/laravel-permission` package to manage user roles (admin, support, regular user).
- **File Attachments**: Ability to upload files for tickets and replies using `spatie/laravel-medialibrary`.
- **Real-time Admin Panel**: The admin panel is built with **Livewire** and **Laravel Reverb**, allowing the ticket list and replies to update live without needing a page refresh.
- **Notifications**: Sends email notifications to users when they receive a reply from the support team.
- **Bilingual Support**: Provides language files for both Persian (fa) and English (en).
- **Rate Limiting**: Implemented for enhanced security on sensitive routes like login and registration.
- **Docker Support**: The project is fully containerized and can be easily run using **Laravel Sail**.

---

## Installation and Setup Guide (via Docker)


1.  **Prerequisites**:
    -   Docker Desktop

2.  **Clone the Project and Initial Setup**:
    ```bash
    git clone https://github.com/m4ry4md/TicketingSystem.git
    cd <project-directory>
    cp .env.example .env
    ```

3.  **Run Sail**:
    Execute the following command to build and run the Docker containers.
    ```bash
    ./vendor/bin/sail up -d
    ```

4.  **Install Dependencies and Initial Setup**:
    Run the following commands to install packages, run migrations, and seed data inside the container.
    ```bash
    ./vendor/bin/sail composer install
    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail artisan migrate --seed
    ./vendor/bin/sail artisan storage:link
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev
    ```

The project is now accessible at `http://localhost`.

## Key Packages Used

### Backend (Composer)
- **`laravel/framework`**:
- **`livewire/livewire`**:
- **`laravel/sanctum`**:
- **`laravel/reverb`**:
- **`spatie/laravel-permission`**:
- **`spatie/laravel-medialibrary`**:

### Frontend (NPM)
- **`tailwindcss`**:
- **`vite`**:
- **`alpinejs`**:
- **`axios`**:

---

## Important Artisan Commands

-   `php artisan migrate --seed`: Runs database migrations and seeds initial data.
-   `php artisan reverb:start`: Starts the WebSocket server.
-   `php artisan queue:work`: Starts the queue worker.

## Default Credentials

The following users are created in the system by the seeders:

-   **Super Admin:**
    -   **Email**: `super_admin@example.com`
    -   **Password**: `maryam123456`

-   **Support User:**
    -   **Email**: `support@example.com`
    -   **Password**: `maryam123456`

-   **Regular Users:**
    -   **Email**: `user1@example.com`, `user2@example.com`, `user3@example.com`
    -   **Password**: `password`

---
