# API DEMO

## Installation

Clone the repo locally:

```sh
git clone https://github.com/jonquintero/jabu.git jabu
cd jabu
```

Install PHP dependencies:

```sh
composer install
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Setup your Database

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Run the dev server (the output will give the address):

```sh
php artisan serve
```

You're ready to go Postman to http://address:8000/api/login, and login with:

- **Username:** johndoe@example.com
- **Password:** secret

Copy the token key and setup Authorization Bearer token.

## Endpoints to tasks

```sh
GET http://address:8000/api/tasks
GET http://address:8000/api/tasks/create
POST http://address:8000/api/tasks
GET http://address:8000/api/tasks/{task}
GET http://address:8000/api/tasks/{task}/edit
PUT http://address:8000/api/tasks/{task}
DELETE http://address:8000/api/tasks/{task}
```

Example body to create/edit a task

```sh
{
    "name": "XXXXXXXXX",
    "frequency": 1, (this value come list frequency of the backend, please check the list)
    "status": true/false
}
```

QueryString to look for tasks by date

```sh
{
  http://address:8000/api/tasks?fromDate=Y-m-d&untilDate=Y-m-d
}
```

QueryString to look for tasks by group

```sh
{
  http://address:8000/api/tasks?groupBy=today
  http://address:8000/api/tasks?groupBy=tomorrow
  http://address:8000/api/tasks?groupBy=next week
  http://address:8000/api/tasks?groupBy=next
}
```



