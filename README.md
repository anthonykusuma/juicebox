<p align="center">
    <a href="https://juicebox.com.au" target="_blank">
        <img src="public/juicebox.png" alt="Juicebox Logo">
    </a>
</p>

# Juicebox | Laravel Developer Code Test
This Laravel application developed for Juicebox implements a RESTful API for a simple blog system.

## Technologies Used
- [Laravel 11](https://laravel.com/)
- [PHP 8.3](https://www.php.net/)
- [MySQL 8.4](https://www.mysql.com/)
- [Composer 2.7](https://getcomposer.org/)
- [PHPUnit 11](https://phpunit.de/)
- SMTP Mail Server. I use [Mailpit](https://mailpit.axllent.org/) for local email testing.

## Installation
1. Clone the repository and go to the project directory
```bash
git clone https://github.com/anthonykusuma/juicebox.git
cd juicebox
```
2. Install the dependencies
```bash
composer install
```
3. Create two databases in MySQL, one for the application `juicebox` and one for testing `juicebox_testing`
4. Copy the `.env.example` file to `.env` and copy the `.env.testing.example` file to `.env.testing` then update the database & email configuration.
5. Generate the application key
```bash
php artisan key:generate
```
6. Run the migrations
```bash
php artisan migrate
```
7. Seed the database (Optional)
```bash
php artisan db:seed
```
8. If you use [Valet](https://laravel.com/docs/11.x/valet) you can park the project and access it via `http://juicebox.test`
```bash
valet park
```
Otherwise, you can run the application using the following command and access it via `http://localhost:8000` (or any other port you specify with the `--port` option)
```bash
php artisan serve
```
9. Run the worker to process the queued jobs
```bash
php artisan queue:work
```

## API Documentation
The API documentation is generated using [Scramble](https://scramble.dedoc.co/) and can be accessed via `http://juicebox.test/docs/api` (or `http://localhost:8000/docs/api` if you are not using Valet).

## Welcome Email
The application sends an email to the user when a successful registration is made. This job is queued and processed by the worker. You can dispatch this job manually by running the following command after registering a new user.
```bash
php artisan email:send-welcome {userId}
```