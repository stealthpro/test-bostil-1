## Installation

- create project folder - `mkdir test_project`
- go to folder - `cd test_project`
- clone project - `git clone git@github.com:stealthpro/test-bostil-1.git .`
- install composer dependencies - `composer install`
- create .env file - `cp .env.example .env`
- generate application key - `php artisan key:generate`
- configure database in .env file
- cache config - `php artisan config:cache`
- apply migrations - `php artisan migrate`
- create storage symlink - `php artisan storage:link`
- run application - `php artisan serve`


## Seeding test data

First way:
- create 20 test folders - `php artisan db:seed --class=FolderSeeder`
- create 40 test pages - `php artisan db:seed --class=PageSeeder`

Second way:
- go to tinker - `php artisan tinker`
- create test folders - `factory(\App\Models\Folder::class, 20)->create();`
- create test pages - `factory(\App\Models\Page::class, 20)->create();`


## Swagger API documentation

API documentation link - `http://127.0.0.1:8000/api/documentation`


## Testing

- before testing - `php artisan config:clear`
- run tests - `php artisan test`
