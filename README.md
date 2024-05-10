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

- PHP >= 8.2 with extensions: mysql, xml, dom, gmp
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
    
    database connection
    ```
5. Generate an application and vapid keys:
    ```
    php artisan key:generate
    php artisan webpush:vapid
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
10. set up a crontab:
    ```
    * * * * * cd {path_to_project} && php artisan schedule:run >> /dev/null 2>&1
    ```


## Usage
After installation, access the application via http://localhost:8000 (or the port provided by php artisan serve). 



## License
This project is licensed under Unlicense - see the LICENSE.md file for details.
