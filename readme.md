# Allocation Platform

1) Clone this
2) Run in the folder
``` bash
$ composer install
$ php artisan key:generate
$ php artisan migrate
```
3) Create an application here: https://www.wrike.com/frontend/apps/index.html#/api, name it anything you like
4) Assign the callback URI as http://example.com/welcome: (e.g. localhost:8000/welcome for local machine)
5) Get the client ID and client secret 
6) Put them in .env file as CLIENT_ID & CLIENT_SECRET (look at the env.example)
7) Register a new user at http://localhost:8000/admin/register
8) Your admin panel will be available at http://localhost:8000/public/admin
9) Create teams and tasks
10) Browse to http://localhost:8000/welcome
11) Click on "Start" to login
12) Click on "Report" to get a report (you must be logged in first)




