-- ============================================================
-- Adesh & Co. — Event Management System
-- ICT726 Assignment 4 — Database Schema
-- Import with: mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS adesh_and_co CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE adesh_and_co;

-- ------------------------------------------------------------
-- 1. USERS  (authentication + role-based access control)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id         INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL UNIQUE,
    password_hash   VARCHAR(255)        NOT NULL,
    role            ENUM('admin','planner','client') NOT NULL DEFAULT 'client',
    phone           VARCHAR(30)         DEFAULT NULL,
    status          ENUM('active','disabled') NOT NULL DEFAULT 'active',
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. PACKAGES  (services & pricing — admin-managed, CRUD #1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS packages (
    package_id      INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    tagline         VARCHAR(150)        DEFAULT NULL,
    price_from      DECIMAL(10,2)       NOT NULL,
    description     TEXT                NOT NULL,
    is_featured     TINYINT(1)          NOT NULL DEFAULT 0,
    display_order   INT                 NOT NULL DEFAULT 0,
    created_by      INT                 DEFAULT NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. GALLERY  (event photos — admin-managed, CRUD #2)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery (
    image_id        INT AUTO_INCREMENT PRIMARY KEY,
    image_url       VARCHAR(255)        NOT NULL,
    caption         VARCHAR(150)        NOT NULL,
    category        ENUM('wedding','corporate','celebration') NOT NULL,
    alt_text        VARCHAR(255)        NOT NULL,
    uploaded_by     INT                 DEFAULT NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. ENQUIRIES  (contact/booking form submissions — form #1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enquiries (
    enquiry_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT                 DEFAULT NULL,
    full_name       VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL,
    phone           VARCHAR(30)         DEFAULT NULL,
    event_type      ENUM('wedding','corporate','celebration','other') NOT NULL,
    event_date      DATE                NOT NULL,
    message         TEXT                NOT NULL,
    status          ENUM('new','in_progress','confirmed','closed') NOT NULL DEFAULT 'new',
    assigned_to     INT                 DEFAULT NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. TESTIMONIALS  (client reviews — form #2, admin-moderated)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS testimonials (
    testimonial_id  INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT                 DEFAULT NULL,
    client_name     VARCHAR(100)        NOT NULL,
    event_type      VARCHAR(100)        NOT NULL,
    rating          TINYINT             NOT NULL DEFAULT 5,
    quote           TEXT                NOT NULL,
    status          ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Seed data
-- ------------------------------------------------------------

-- NOTE: seed user accounts (admin / planner / client) are NOT inserted here
-- because passwords must be hashed with PHP's password_hash() at insert time,
-- not typed in as plain SQL. Run sql/seed_users.php once (from a browser or
-- the command line, after this schema has been imported) to create them.
-- That script prints the login credentials it created.

-- created_by / uploaded_by / user_id / assigned_to are left NULL here because
-- the users table is populated afterwards by seed_users.php. Once an admin
-- account exists you can freely re-attribute rows via the admin dashboard.

INSERT INTO packages (name, tagline, price_from, description, is_featured, display_order) VALUES
('Day-of Coordination', 'Light touch', 850.00, 'Two planning check-ins, final vendor confirmations & run sheet, 8-hour on-site coordination block, setup and pack-down supervision, one dedicated coordinator on the day.', 0, 1),
('Full Planning', 'Most comprehensive', 3200.00, 'Venue sourcing & contract negotiation, full vendor booking & management, styling & design direction included, monthly then weekly planning meetings, full day-of coordination team.', 1, 2),
('Styling & Design', 'Design-led', 1100.00, 'Concept board & colour palette, florals, tablescapes & signage design, hire coordination for styling items, on-site styling setup on the day.', 0, 3);

INSERT INTO gallery (image_url, caption, category, alt_text) VALUES
('images/101-500x500.jpg', 'Centrepiece detail', 'wedding', 'Close-up of a floral table centrepiece with candles at a wedding reception'),
('images/109-900x450.jpg', 'End-of-year function', 'corporate', 'Guests networking during a corporate end-of-year function'),
('images/1045-500x503.jpg', 'Baby shower dessert table', 'celebration', 'Styled dessert table set up for a baby shower celebration'),
('images/1083-400x501.jpg', 'Engagement styling', 'celebration', 'Styled table setting at an intimate engagement party'),
('images/177-1400x1050.jpg', "Priya & Anthony's reception", 'wedding', 'Wide shot of a styled wedding reception venue with long tables and floral centrepieces'),
('images/237-500x505.jpg', 'Ceremony setup', 'wedding', 'Floral ceremony arch with rows of white chairs set up for an outdoor wedding'),
('images/257-500x504.jpg', 'Meridian product launch', 'corporate', 'Stage and branded backdrop set up for a corporate product launch event'),
('images/297-500x700.jpg', "Grace's 50th", 'celebration', 'Balloon and floral decoration setup for a milestone birthday celebration'),
('images/352-500x502.jpg', 'Conference check-in', 'corporate', 'Branded registration desk set up at the entrance of a corporate conference'),
('images/669-700x560.jpg', 'Before guests arrive', 'wedding', 'Bridal party photographed outside the reception venue before guests arrive'),
('images/705-500x506.jpg', 'Evening lighting', 'wedding', 'Fairy light and lantern lighting design at an evening wedding reception'),
('images/779-900x451.jpg', 'Awards night', 'corporate', 'Presenter on stage at a corporate awards night event');

INSERT INTO testimonials (client_name, event_type, rating, quote, status) VALUES
('Priya & Anthony', 'Wedding · Full Planning', 5, 'Adesh planned our entire wedding from Melbourne over video calls and it went off without a single hiccup. Every vendor showed up exactly when they were supposed to.', 'approved'),
('Sarina K.', 'Wedding · Day-of Coordination', 5, 'We booked day-of coordination thinking we had everything under control. We did not. Having someone else run the timeline saved the whole event.', 'approved'),
('Meridian Co.', 'Corporate Launch · Full Planning', 5, 'Our product launch had a very tight brand guideline and Adesh & Co. nailed it without a single revision needed on the styling.', 'approved'),
('Ravi N.', 'Milestone Birthday · Styling & Design', 4, 'I called Adesh directly the week before my mum''s 50th when our original caterer cancelled. He had a replacement locked in within two days.', 'approved');

INSERT INTO enquiries (full_name, email, phone, event_type, event_date, message, status) VALUES
('Tara Williams', 'tara.w@example.com', '0400555666', 'celebration', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Planning an engagement party for around 40 guests, need styling and design help.', 'new');
