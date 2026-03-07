# MaziwaHub

MaziwaHub is a comprehensive dairy management and milk supply chain system built with Laravel and Vue.js. It connects farmers, veterinarians, collection centers, and partners to streamline milk production, tracking, and delivery operations.

## Features

- **Farmer Management:** Register farmers, manage their profiles, and track their feeding and cow treatment histories.
- **Cow Management:** Track individual cows, their milk production records, and veterinary treatments.
- **Milk Collection & Delivery:** Manage milk collection centers, process milk deliveries, and track claims.
- **Partner & Agent System:** Powerful role-based management for partners, field agents, and veterinarians.
- **Geographic Organization:** Supports multi-tiered geographic tracking (Countries, Regions, Counties, Districts, Subcounties, Parishes, Villages).
- **Vue Frontend:** A fast, reactive single-page application built with Vue 3, Vite, Pinia, and Tailwind CSS.
- **API First:** Robust RESTful structure internally using Laravel Sanctum for API authentication.

## System Requirements

- PHP >= 8.2
- Node.js & npm (for Vite, Vue 3)
- Supported Database (MySQL, PostgreSQL, SQLite)
- Composer

## Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd maziwahub
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   Copy the `.env.example` to `.env` and generate the application key.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configure your database credentials in the `.env` file.

5. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Serve the Application:**
   Run the Laravel server and building tools in parallel using the `npm run dev` script or the custom composer `dev` command:
   ```bash
   composer dev
   ```
   Alternatively, run:
   ```bash
   php artisan serve
   npm run dev
   ```

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Vue 3, Vite, Tailwind CSS, Pinia
- **Authentication:** Laravel Sanctum
- **Testing:** Pest PHP

## License

This project is proprietary and confidential.
