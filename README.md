# Habitationz CIC

A custom PHP website for **Habitationz CIC** — a UK-based Community Interest Company
providing safe, supported housing in partnership with regulated care providers.
Built from scratch in native PHP 8.x and Bootstrap 5, following the same architecture as
the Simply My Care codebase.

---

## Project Structure

```
habitationzcic/
├── admin/                  # Admin panel (admin / editor roles)
│   ├── dashboard.php
│   ├── enquiries.php       # Contact form enquiries
│   ├── services.php        # "How We Serve You" pillar CRUD
│   ├── faqs.php            # Homepage FAQ accordion CRUD
│   ├── content.php         # Editable page copy blocks (home / about / working-together)
│   ├── users.php           # Admin & editor account management (admin only)
│   ├── activity-log.php    # Admin activity log (admin only)
│   ├── login.php / logout.php / forgot-password.php
│   ├── _layout_top.php     # Admin shell (sidebar + topbar)
│   └── _layout_bottom.php  # Admin shell (closing tags + JS)
│
├── includes/                # Shared PHP includes
│   ├── config.php           # Loader that includes config.local.php when present
│   ├── config.local.example.php # Safe template — copy to config.local.php on the server
│   ├── database.php         # PDO database wrapper
│   ├── session.php          # Auth class (login, CSRF, role checks)
│   ├── header.php           # Public site header + nav
│   ├── footer.php           # Public site footer
│   ├── mailer.php           # PHPMailer SMTP wrapper
│   └── PHPMailer/           # Vendored PHPMailer library
│
├── assets/
│   ├── css/
│   │   └── style.css        # Single stylesheet for entire site
│   └── img/                 # Logo, favicon, hero photos
│
├── uploads/                 # Reserved for future admin-managed media (gitignored)
│
├── index.php                # Home
├── about.php
├── services.php
├── working-together.php
├── contact.php
├── privacy.php
├── terms.php
├── cookies.php
├── 404.php
├── schema.sql                # Full database schema
├── .htaccess                 # Apache config (HTTPS redirect, 404)
└── .gitignore
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.x (native, no framework) |
| Frontend | Bootstrap 5.3, Bootstrap Icons 1.11 |
| Font | Plus Jakarta Sans (Google Fonts) |
| Database | MySQL |
| Email | PHPMailer with SMTP |
| Version control | Git |

---

## Database Tables

| Table | Purpose |
|---|---|
| `users` | Admin & editor accounts |
| `enquiries` | Contact form submissions |
| `services` | "How We Serve You" pillar cards (homepage + services page) |
| `faqs` | Homepage FAQ accordion |
| `page_content` | Editable copy blocks via admin (home / about / working-together) |
| `password_resets` | Token-based password reset |
| `admin_log` | Admin activity log |

---

## Local Setup

1. **Create your config file**
   ```bash
   cp includes/config.local.example.php includes/config.local.php
   ```
   Then open `includes/config.local.php` and fill in your database credentials
   and real site contact details (email, phone, address).

2. **Import the database**
   Create a database and import `schema.sql`. This seeds the five service
   pillars and homepage FAQs.

3. **Create your first admin account**
   ```bash
   php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
   ```
   Then run in your database:
   ```sql
   INSERT INTO users (full_name, email, password_hash, role)
   VALUES ('Your Name', 'you@example.com', '<paste hash here>', 'admin');
   ```

4. **Set BASE_URL**
   In `includes/config.local.php`, set `BASE_URL` to `''` if running at root,
   or `'/subfolder'` if in a subfolder.

5. **Run a local PHP server**
   ```bash
   php -S localhost:8000
   ```
   Then visit `http://localhost:8000` and `http://localhost:8000/admin/login.php`.

---

## Key Conventions

- All asset paths use the `BASE_URL` constant — never hardcoded paths.
- CSS lives in a single file: `assets/css/style.css`.
- All DB queries go through the `Database::` static wrapper (PDO).
- Auth is handled by the `Auth::` class in `includes/session.php` (roles: `admin`, `editor`).
- CSRF tokens protect every POST form.
- `includes/config.local.php` is gitignored — never commit real credentials.
- The logo (`assets/img/logo.svg`) and hero photos in `assets/img/` are placeholders —
  replace them with your own brand assets (a media upload flow can be added to
  `admin/content.php` when needed).

---

## Branches

| Branch | Purpose |
|---|---|
| `main` | Production |
| `dev` | Development — work in progress |

---

## Contact

**Habitationz CIC**
📧 Update `SITE_EMAIL` in `includes/config.local.php`
📞 Update `SITE_PHONE` in `includes/config.local.php`
