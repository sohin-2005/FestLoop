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
- A personal dashboard of upcoming registrations and followed clubs

**For clubs (coordinators)**
- Self-serve club registration (goes live after a quick admin approval)
- A club panel to manage events, registrations (approve/waitlist/reject), achievements,
  roadmap items, and announcements
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
npm install && npm run build
php artisan serve
```

Seeded logins (password: `password` for all):
- Student: `test@example.com`
- Admin: `admin@festloop.test`
- Club coordinator: `bfc@festloop.test` (ByteForge Coding Club)

## Tests

```bash
php artisan test
```

## Ideas for what's next

See the brainstorm shared alongside this build for a longer feature roadmap
(QR check-in, calendar sync, club analytics, a merch/ticketing layer, etc).
