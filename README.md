# Divine Hospital – Divine ENT Centre

Website for Divine Hospital / Divine ENT Centre, Farrukhabad (Dr. Rajat Goel, MS ENT).
Built with **Laravel 13 + Blade**, hand-written CSS and vanilla JS. **No npm or build step is needed.**

## Run locally

```bash
composer install
cp .env.example .env    # first time only
php artisan key:generate
php artisan migrate
php artisan serve
```

## Admin panel

All website content — every page and section, specialities, timings, phone numbers, address, doctor profile, photos, logo, SEO, header and footer — is edited at **`/admin`**. Appointment requests are listed there too.

Create (or reset) an admin login:

```bash
php artisan admin:create
```

Each editor screen has a **Restore original content** button that brings back the default text.
Uploaded photos are converted to WebP automatically and saved in `public/images/uploads` (this folder must be writable on the server).

## Where to edit things (for developers)

| What | File |
|---|---|
| Admin fields and the original/default text | `config/cms.php` |
| Page layouts | `resources/views/pages/*` and `resources/views/sections/*` |
| Design (colours, fonts, spacing) | `public/css/app.css` (tokens at the top) |
| Behaviour (menu, "Open now", gallery) | `public/js/app.js` |
| Default photos | `public/images/*.webp` (640/1024/1600px widths) |

In templates, read content with `site('screen.section.field')`, for example `site('clinic.numbers.phone')`.
The `CLINIC_*` and `APPOINTMENT_NOTIFY_EMAIL` values in `.env` are only used as defaults until they are changed in the admin panel.

After editing `.env` or config on a live server, run `php artisan config:cache`.

## Appointment requests

The form saves each request to the `appointments` table and redirects back with a confirmation, plus a prefilled WhatsApp message.
If a notification email is set (admin → Contact & timings) and the `MAIL_*` settings are configured, the reception also gets an email.
There is spam protection (a honeypot field and a limit of 5 submissions per minute per IP).

## Animations

All motion lives in section **23. Motion** of `public/css/app.css` plus `public/js/app.js`:

- **Headlines:** split into words by a tiny inline script in `components/layout.blade.php`.
- **Scroll reveals:** add `data-reveal` to any element. Values: `blur`, `scale`, `left`, `right`, `pop` or `clip`. Stagger with `style="--i: 2"`.
- **Card effects:** add the `spotlight` class for the cursor glow, `data-tilt` for 3D tilt, `data-magnetic` for the button pull.
- **Reduced motion:** when a visitor turns on "reduce motion" on their device, all animation is disabled automatically.

## Tests

```bash
php artisan test
```

## Deploying to shared hosting (cPanel / Hostinger)

1. Upload the project and point the domain's document root to `public/`.
2. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain` in `.env`.
3. Run `php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache`.
4. Run `php artisan admin:create` to set up the admin login, and make sure `public/images/uploads` is writable.
5. Add your domain to the `Sitemap:` line in `public/robots.txt`.
