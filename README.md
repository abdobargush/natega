# Natega

Mini Booking platform

### Backend

The backend is built using [Laravel](https://laravel.com) to get started

1. Make sure `php` and `composer` are installed
2. Copy `.env.example` to `.env`
3. Set the database credentials in `.env` file
4. Set up google credintials in `.env` file
5. run the following commands

```
$ composer install
$ php artisan migrate
$ php artisan serve
```

> Note: `valet` can't be used when trying google authentication the application should be served from `127.0.0.1` becasue google doesn't allow `.test` domains for callback urls

### Frontend

The frontend is built using [React](https://reactjs.org/) on [InertiaJS](https://inertiajs.com/) and [TailwindCss](tailwindcss.com)

To build the front end run the following

```
$ npm install
$ npm run build
```

### Notifications

The notifications sent by this application are queued by default for more on queues checkout [Laravel Docs](https://laravel.com/docs/9.x/queues#main-content)

### Reminders

Booking reminders are scheduled using `app/Jobs/BookingReminderJob.php` for more on jobs and task scheduling checkout [Laravel Docs](https://laravel.com/docs/9.x/queues#main-content)

### Screenshots

![Link Google Calendar](/public/images/screenshot1.png)
![Events listing page](/public/images/screenshot2.png)
![Booking page](/public/images/screenshot3.png)
![Bookings listing page](/public/images/screenshot3.png)
