## Setup Instructions
- clone the repo
  `git clone this.repo`,
- install dependencies
  `composer install`
- Create new database and set the database details at `.env` file at project root.
Notes: For reference we already have `.env.example` file, you can rename it to `.env` and setup database details in it.
- perform migrations
  `php artisan migrate`
- perform seeders
  `php artisan db::seed`
- generate key
  `php artisan key:generate`
- serve
  `php artisan serve`
- server hosted @ `localhost:8000`
