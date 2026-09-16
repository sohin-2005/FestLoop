# FestLoop 🎉

**Discover. Celebrate. Repeat.**

FestLoop is a centralized events & clubs platform for a college campus. Instead of every
club running its own page to post notices, FestLoop gives every club:

- a public **club page** (about, mission, achievements, roadmap, announcements)
- a way to **post events** with a unified registration flow (or link out to an external form)
- **followers** who get notified the moment they post something new

...and gives every student **one place** to browse everything happening on campus.

Built with Laravel 12, Blade, Tailwind CSS, and Alpine.js.

## Features

**For students**
- Browse/search all events with live AJAX filtering (category, club, time)
- One-click registration, with automatic **waitlisting** when an event is full and
  automatic promotion + notification when a spot opens up
- Follow clubs to get a personalized "For You" feed and email/in-app notifications
- Automatic reminders ~24h before an event you're registered for
- A personal dashboard of upcoming registrations and followed clubs

**For clubs (coordinators)**
- Self-serve club registration (goes live after a quick admin approval)
- A club panel to manage events, registrations (approve/waitlist/reject), achievements,
  roadmap items, and announcements
- Export any event's sign-up list to CSV for attendance sheets or certificates
- Optional external registration links (e.g. a Google Form) instead of using FestLoop's
  built-in registration
- Post-event recaps that show up on the public club page

**For admins**
- A simple approval queue for newly registered clubs

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # uses sqlite by default
php artisan migrate --seed
php artisan storage:link         # so uploaded logos/banners are servable
npm install && npm run build
php artisan serve
```

The default seed loads the 22 real student clubs of Govt. Model Engineering College
(from mec.ac.in/students/clubs and each club's own site). Each gets a placeholder
coordinator login, meant to be handed over to the club.

Seeded logins (password: `password` for all):
- Student: `test@example.com`
- Admin: `admin@festloop.test`
- Club coordinator: `<short name>@festloop.test`, e.g. `iedc@festloop.test`, `foss@festloop.test`, `ilu@festloop.test` (Illuminati)

Want fictional clubs and a busy event calendar for demos instead?

```bash
php artisan db:seed --class=DemoSeeder
```

## Scheduled tasks

Attendees get a reminder (in-app + email) roughly 24h before an event starts.
That runs on Laravel's scheduler, so in production point cron at:

```
* * * * * cd /path/to/festloop && php artisan schedule:run >> /dev/null 2>&1
```

To fire it by hand:

```bash
php artisan events:send-reminders          # default: events within the next 24h
php artisan events:send-reminders --hours=48
```

## Email

`MAIL_MAILER=log` by default, so mail is written to `storage/logs/laravel.log`
instead of being sent — handy in development. For real delivery, set
`MAIL_MAILER=smtp` in `.env` and fill in the `MAIL_HOST` / `MAIL_USERNAME` /
`MAIL_PASSWORD` credentials from your provider.

## Tests

```bash
php artisan test
```

## Ideas for what's next

See the brainstorm shared alongside this build for a longer feature roadmap
(QR check-in, calendar sync, club analytics, a merch/ticketing layer, etc).
