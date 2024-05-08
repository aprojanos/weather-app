# weather-app
Demo application for presenting real-time weather data

## Features

- Get weather data by current location or selected city
- 2 day hourly forecast
- Air quality data
- Show location on map
- Update data according to server-side schedule

## Installation

### Prerequisites

- PHP >= 8.2 with extensions: php8.2-mysql, php8.2-xml, php8.2-dom
- Composer
- Node.js and npm

### Setup

1. Clone the repository to your local machine:
   ```bash
   git clone git@github.com:aprojanos/weather-app.git
   ```
   
2. Install PHP dependencies:
    ```
    composer install
    ```
3. Install JavaScript dependencies:
    ```
    npm install
    ```
4. Copy the example environment file and make the required configuration adjustments:
    ```
    cp .env.example .env
    ```
5. Generate an application key:
    ```
    php artisan key:generate
    ```
6. Start the Laravel development server:
    ```
    php artisan serve
    ```
7. Start queue:
    ```
    php artisan queue:listen
    ```
8. Start reverb:
    ```
    php artisan reverb:start
    ```
9. In a new terminal, start the frontend assets compilation process:
    ```
    npm run dev
    ```

## Usage
After installation, access the application via http://localhost:8000 (or the port provided by php artisan serve). 



## License
This project is licensed under Unlicense - see the LICENSE.md file for details.

## Acknowledgments
Laravel, for the incredible PHP framework.
Laravel Echo and Pusher, for making real-time broadcasting a breeze.


Thank you for checking out our project.
