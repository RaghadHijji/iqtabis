# Iqtabis | اقتبس

> لكل لحظة اقتباس يشبهها — A quote for every moment.

Iqtabis is a bilingual (Arabic / English) quotes website built with Laravel.

**Live site:** https://iqtabis-production-macu34.laravel.cloud/ Anyone can read, filter and search quotes; registered users can share, edit and delete their own quotes, like and save quotes, comment, and report bad content. It also has an admin dashboard and a JSON API.

Final project of the Al-Qimmah Bootcamp (Tuwaiq Club × Umm Al-Qura University), Programming & Development track.

## Features

**Required (FR-01 to FR-07)**
- View all quotes without logging in, with who shared each one and the date.
- Sign up (name 2–50 letters, unique email, password of at least 8), log in, log out.
- Share a quote of 3–155 characters.
- Edit your own quotes within 10 minutes of posting, and delete them any time. Nobody else can edit or delete them.

**Added**
- Likes, a private "Saved" list and a "My likes" tab.
- 7 categories with filtering, search by text or by who said it, and sorting by newest or most liked.
- Quote of the Day and the most liked quotes of each category.
- A page for each quote (comments, similar quotes) and for each poster.
- Comments (the writer can edit a comment within 10 minutes), notifications, and reporting bad quotes or comments (hidden after 3 reports).
- Blocked words: quotes and comments about politics or with insults are refused.
- Copy a quote, save it as a story-size picture (1080 × 1920), or open a random quote.
- Email verification, password reset by email, "Remember me", and password confirmation on sign up.
- Account settings: profile picture, email, password, delete account.
- Admin dashboard for site numbers and reported content.
- Arabic / English interface, light / dark mode, Gregorian and Hijri dates.
- JSON API with token authentication (Laravel Sanctum).

The full list is in the SRS (FR-01 to FR-31, NFR-01 to NFR-16).

## Built with

- Laravel 13 (PHP 8.3+), Blade layouts and components
- Tailwind CSS 4 and Vite
- SQLite locally; the live site uses MySQL (change `DB_CONNECTION` in `.env`)
- Laravel Sanctum for API tokens
- Pest for tests

## Run it locally

You need PHP 8.3+, Composer and Node.js.

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# create the SQLite database, the tables and the starting data
touch database/database.sqlite
php artisan migrate --seed

# optional: sample members with likes and comments
php artisan db:seed --class=DemoActivitySeeder

# optional: make an account an admin
php artisan app:make-admin you@example.com

npm run build        # or keep "npm run dev" running while you work
php artisan serve
```

Then open http://localhost:8000.

- The seeder adds the 7 categories, 42 real quotes with who said them and a test account: `test@example.com` / `password`.
- Emails (verification and password reset) go to `storage/logs/laravel.log` because `MAIL_MAILER=log`. In local mode the site also shows the verification link on the page, so you can confirm an account without a real mail server.

## Tests

```bash
php artisan test
```

The tests cover the required functions (sign up, log in, share, edit and delete rules), the API, moderation, comments, admin and more.

## API

All endpoints start with `/api` and return JSON. Endpoints that change data need the header `Authorization: Bearer <token>`, which you get from `/api/register` or `/api/login`.

| Method | Endpoint | Auth |
| --- | --- | --- |
| POST | /api/register, /api/login | Public |
| GET | /api/quotes, /api/quotes/{id} | Public |
| GET | /api/user, /api/favorites | Token |
| POST | /api/logout | Token |
| POST | /api/quotes | Token, confirmed email |
| PUT / DELETE | /api/quotes/{id} | Token, owner only |
| POST | /api/quotes/{id}/like, /favorite, /report | Token |
| POST | /api/email/verification-notification | Token |

Details, status codes and errors are in section 9 of the SRS.

## Where things are

| Part | Location |
| --- | --- |
| Routes | `routes/web.php`, `routes/api.php`, `routes/console.php` |
| Controllers | `app/Http/Controllers` (web) and `app/Http/Controllers/Api` |
| Form rules | `app/Http/Requests`, blocked words in `app/Rules/CleanContent.php` and `config/moderation.php` |
| Who can edit or delete | `app/Policies` |
| Models | `app/Models` (User, Quote, Category, Comment) |
| Tables | `database/migrations`, starting data in `database/seeders` |
| Pages | `resources/views` (layout in `Layout/app.blade.php`, home in `welcome.blade.php`) |
| Reusable parts | `resources/views/components` (navbar, login pop-up, footer, dialogs…) |
| Styles | `resources/css/app.css` |
| Arabic text | `lang/ar.json` |
| Tests | `tests/Feature` |

## Documents

- `Raghad_SRS.pdf`: Software Requirements Specification, version 2.3
- `Raghad_ERD.pdf`: Entity Relationship Diagram, 9 tables

## Author

Raghad Hijji · [GitHub](https://github.com/RaghadHijji) · [LinkedIn](https://www.linkedin.com/in/raghad-ahmad-661540370)
