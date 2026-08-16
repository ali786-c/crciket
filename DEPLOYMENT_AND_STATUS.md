# Cricket Draft System: Deployment and Current Status

## Current implementation

The project is a Laravel 12 application using Blade Templates, Alpine.js, Bootstrap 5, Font Awesome Free, MySQL, Laravel authentication, Spatie permissions, and AJAX polling through `fetch()` and `setInterval`.

The current completed modules are the Laravel foundation, MySQL schema, authentication, Spatie roles, premium responsive UI design system, admin command center, tournament creation, tournament lifecycle controls, team management with captain assignment and revocation, admin user management, player profile onboarding, tournament registration, admin player approval, configurable rounds and pick-number/team assignments, transactional draft control, timer expiry, admin extensions, pause/resume, skip, latest-pick undo, audit logs, captain and player workspaces, public live viewing, revision-aware polling, CSV pick-history export, and live pick-status metrics.

## Local development

```bash
cd /home/ubuntu/cricket-draft
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve --host=0.0.0.0 --port=8000
```

Configure `.env` with the MySQL database credentials before running migrations. For local development, the seeded administrator account is `admin@cricketdraft.test` with the development password configured in `DatabaseSeeder`. Change this password immediately outside the local sandbox.

The optional demo preview can be seeded with:

```bash
php artisan db:seed --class=DemoTournamentSeeder
```

The public preview URL then follows the slug format:

```text
/tournaments/preview-cup/draft/live
```

## cPanel deployment

Create the MySQL database and database user in cPanel, grant the user all privileges on that database, and upload the repository outside the public web root when possible. The web root should point only to the Laravel `public` directory. If the hosting plan requires `public_html`, place the Laravel application one directory above it and copy or point the public entry files into `public_html` according to the host’s supported document-root configuration.

On the server, run Composer with production flags and build the frontend assets before enabling the application:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set the production environment values before caching configuration:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_cpanel_database
DB_USERNAME=your_cpanel_user
DB_PASSWORD=your_cpanel_password
```

Enable HTTPS through cPanel SSL or Let’s Encrypt. Confirm that the `storage` and `bootstrap/cache` directories are writable by the web process, keep `.env` outside version control, and do not use the demo seeder in production.

## Validation completed

The local MySQL test database is configured in `phpunit.xml`. The full Laravel suite is re-run after each feature change; the latest count is recorded in the final task report. The project is committed to the local Git repository on the `main` branch. The most recent milestones are:

| Commit | Milestone |
|---|---|
| `4770631` | Laravel foundation and draft domain schema |
| `588cb9d` | Laravel scaffold and test configuration |
| `f07e3f9` | Configurable draft controls and live polling boards |
| `a74244e` | Admin and captain authentication interface polish |
| `042316a` | Player onboarding and admin approvals |

## Production hardening checklist

The application now fails fast if a production environment enables `APP_DEBUG`, provides safe 404, 419, and 500 pages, includes the `php artisan storage:link --force` deployment step, and uses a safe `APP_DEBUG=false` default in `.env.example`. Before real tournament use, set the production environment values, run the deployment commands above, verify the MySQL connection, confirm the storage symlink, and perform a smoke test for admin login, captain assignment, player registration, draft start, pick, timer expiry, CSV history export, and public viewing.

Email or in-app notifications and a cPanel smoke test using the real domain and MySQL credentials remain optional follow-up work. UI visual verification notes are stored in `notes/ui_preview_findings.md`.
