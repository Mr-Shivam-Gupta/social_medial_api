# Project Name

## Description

This project is built on Laravel 10, a PHP framework. It requires PHP version 8 or above.

## Installation

1. Make sure you have PHP 8 installed on your system.

2. Clone the repository:
    ```bash
    git clone <repository-url>
    ```

3. Navigate to the project directory:
    ```bash
    cd project-directory
    ```

4. Install dependencies using Composer:
    ```bash
    composer install
    ```

5. Set up your `.env` file by copying `.env.example` and configuring it with your environment-specific settings:
    ```bash
    cp .env.example .env
    ```

6. Generate an application key:
    ```bash
    php artisan key:generate
    ```

7. Set up your database configuration in the `.env` file.

8. Run database migrations:
    ```bash
    php artisan migrate
    ```

## Usage
To start the development server, run:
```bash
php artisan serve
```
9.You will get the file `api_routes.xlsx` in this directory use it to configure.

