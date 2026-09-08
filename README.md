# Adesh & Co. — Dynamic Event Management System

ICT726 Assignment 4 — PHP + MySQL conversion of the Assessment 3 static site
("Adesh & Co.", a Hurstville event-management studio).

## Requirements

- PHP 8.0+ with the `pdo_mysql` extension
- MySQL 5.7+ / MariaDB 10.3+
- Apache (with `mod_rewrite`/`.htaccess` support) — e.g. XAMPP, WAMP, MAMP, or `php -S`


## Project structure

```
config/db.php          Single PDO connection, shared by every page
includes/auth.php      Session handling, login/logout, RBAC, CSRF helpers
includes/functions.php Server-side form validation + small view helpers
includes/header.php    Shared <head> + site header/nav (session-aware)
includes/footer.php    Shared site footer + script includes
sql/schema.sql         Database schema + seed data
sql/seed_users.php     One-time script to create demo accounts
index.php … contact.php  Public pages (data-driven where relevant)
register.php / login.php / logout.php   Authentication
privacy.php            Privacy notice
admin/                 Admin dashboard: enquiries, packages, gallery,
                        testimonials, users (all full CRUD)
planner/index.php      Planner dashboard: update assigned enquiries
client/index.php       Client dashboard: view own enquiries/testimonials
css/, js/, images/     Static assets (shared with the Assessment 3 site)
robots.txt, sitemap.xml   Basic SEO
```

## Roles & access control

- **Guest** (not logged in): browse all public pages, submit an enquiry,
  register, log in.
- **Client**: everything a guest can do, plus a dashboard showing their
  own enquiries and testimonials, and the ability to submit a testimonial.
- **Planner**: dashboard listing enquiries assigned to them, with a status
  dropdown (new → in progress → confirmed → closed).
- **Admin**: full dashboard — manage all enquiries (assign to planners,
  change status, delete), manage packages (CRUD), manage the gallery
  (upload/delete images), moderate testimonials (approve/reject/delete),
  and manage user roles/status.

Every restricted page starts with `require_role([...])`, which redirects
anonymous visitors to `/login.php` and shows a 403 page to logged-in users
of the wrong role.

## Security notes

- Passwords are hashed with `password_hash()` (bcrypt) — never stored or
  logged in plain text.
- All SQL uses PDO prepared statements (`PDO::ATTR_EMULATE_PREPARES` is
  disabled so real server-side prepares are used).
- Every state-changing form includes a CSRF token, verified with a
  timing-safe comparison (`hash_equals`).
- Output is escaped with `htmlspecialchars()` via the `e()` helper to
  prevent stored/reflected XSS.
- Session IDs are regenerated on login to prevent session fixation.
- Uploaded gallery images are checked for a valid image signature
  (`getimagesize`), restricted to a small extension allow-list, capped at
  4MB, and renamed to a random filename before being stored.
- `config/`, `includes/` and `sql/` are blocked from direct HTTP access
  via `.htaccess`.
