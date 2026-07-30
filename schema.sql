-- ============================================================
-- Habitationz CIC — full database schema
-- Import via phpMyAdmin into an empty database, then copy
-- includes/config.local.example.php → includes/config.local.php
-- and fill in your credentials.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Users: admin & editor accounts ───────────────────────────
CREATE TABLE users (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    full_name     VARCHAR(120)     NOT NULL,
    email         VARCHAR(190)     NOT NULL,
    password_hash VARCHAR(255)     NOT NULL,
    role          ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    status        ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Enquiries: contact form submissions ──────────────────────
CREATE TABLE enquiries (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    full_name    VARCHAR(120)  NOT NULL,
    email        VARCHAR(190)  NOT NULL,
    phone        VARCHAR(30)   NULL,
    subject      VARCHAR(150)  NULL,
    message      TEXT          NOT NULL,
    status       ENUM('new','read','responded') NOT NULL DEFAULT 'new',
    admin_note   TEXT          NULL,
    submitted_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_enquiries_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Services: the "How We Serve You" pillar cards ────────────
CREATE TABLE services (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title       VARCHAR(150)  NOT NULL,
    description TEXT          NULL,
    icon        VARCHAR(60)   NOT NULL DEFAULT 'bi-house-heart',
    sort_order  INT           NOT NULL DEFAULT 0,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── FAQs: homepage accordion ──────────────────────────────────
CREATE TABLE faqs (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    question    VARCHAR(255)  NOT NULL,
    answer      TEXT          NOT NULL,
    sort_order  INT           NOT NULL DEFAULT 0,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Page content: editable copy blocks (admin/content.php) ───
CREATE TABLE page_content (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    page_slug   VARCHAR(60)   NOT NULL,
    section_key VARCHAR(60)   NOT NULL,
    content     TEXT          NULL,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_page_section (page_slug, section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Password resets: token-based reset flow ──────────────────
CREATE TABLE password_resets (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED  NOT NULL,
    token_hash VARCHAR(255)  NOT NULL,
    expires_at DATETIME      NOT NULL,
    used       TINYINT(1)    NOT NULL DEFAULT 0,
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_resets_user (user_id, used),
    CONSTRAINT fk_resets_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Admin activity log ───────────────────────────────────────
CREATE TABLE admin_log (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id      INT UNSIGNED  NULL,
    action       VARCHAR(60)   NOT NULL,
    target_table VARCHAR(60)   NULL,
    target_id    INT UNSIGNED  NULL,
    detail       TEXT          NULL,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_log_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ── Seed data: the five service pillars ──────────────────────
INSERT INTO services (title, description, icon, sort_order) VALUES
('Accommodation', 'We provide safe, high-quality accommodation that meets housing and safety standards, including the Housing Act 2004 and REACH standards.', 'bi-house-door', 1),
('Tenancy Support', 'Guidance, budgeting assistance, and advocacy to help residents sustain their tenancies and build independence.', 'bi-clipboard-check', 2),
('Care Partnerships', 'We partner with CQC-registered care providers who deliver integrated, person-centred support alongside our housing.', 'bi-people', 3),
('Community Inclusion', 'Connecting residents to local opportunities and networks to foster social integration and lasting wellbeing.', 'bi-diagram-3', 4),
('Social Value', 'As a Community Interest Company, we reinvest the majority of our surplus into property improvements and expanding access to supported housing.', 'bi-graph-up-arrow', 5);

-- ── Seed data: homepage FAQs ──────────────────────────────────
INSERT INTO faqs (question, answer, sort_order) VALUES
('What does Habitationz CIC do?', 'Habitationz CIC provides safe, supported housing. We partner with regulated care providers to deliver holistic support alongside stable, high-quality accommodation.', 1),
('Who can access Habitationz CIC\'s housing?', 'We support young people and adults, including older persons, who have an assessed housing need and a package of care or support already in place, referred via a local authority.', 2),
('Does Habitationz CIC provide care or support directly?', 'No. Habitationz CIC provides accommodation only. All regulated care and support are delivered by regulated, registered partner organisations.', 3),
('How does Habitationz CIC ensure housing quality and standards?', 'All of our properties comply with the Housing Act 2004 and REACH standards, and are regularly reviewed to ensure they remain safe and fit for purpose.', 4),
('How are Habitationz CIC\'s profits used?', 'As a Community Interest Company, we reinvest the majority of our surplus into property improvements, tenancy sustainment, and expanding access to supported housing.', 5);

-- ── First admin account ──────────────────────────────────────
-- Generate a hash with:  php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
-- then run (replacing name, email, and hash):
--
-- INSERT INTO users (full_name, email, password_hash, role)
-- VALUES ('Your Name', 'you@example.com', '$2y$10$REPLACE_WITH_REAL_HASH', 'admin');
