-- =====================================================================
-- SKYLEARN : schéma de base de données complet (MySQL 8 / MariaDB 10.5+)
-- Encodage : utf8mb4 | Moteur : InnoDB | Montants en XAF (entiers)
--
-- Import :  mysql -u root -p < skylearn.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS skylearn
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skylearn;

SET NAMES utf8mb4;

-- =====================================================================
-- 1. UTILISATEURS ET AUTHENTIFICATION
-- =====================================================================

CREATE TABLE roles (
  id          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code        VARCHAR(30)  NOT NULL,              -- admin, student
  label       VARCHAR(60)  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_code (code)
) ENGINE=InnoDB;

CREATE TABLE classes (
  id          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(40)  NOT NULL,              -- 6e, 5e, ... Tle
  slug        VARCHAR(40)  NOT NULL,
  cycle       ENUM('college','lycee') NOT NULL,
  exam        ENUM('BEPC','PROBATOIRE','BAC') NULL, -- examen préparé par la classe
  position    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_classes_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE users (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id          TINYINT UNSIGNED NOT NULL,
  first_name       VARCHAR(80)  NOT NULL,
  last_name        VARCHAR(80)  NOT NULL,
  phone            VARCHAR(20)  NOT NULL,         -- format normalisé +237XXXXXXXXX
  email            VARCHAR(190) NULL,             -- facultatif (beaucoup d'élèves n'en ont pas)
  password_hash    VARCHAR(255) NOT NULL,         -- password_hash() PHP
  class_id         SMALLINT UNSIGNED NULL,
  school           VARCHAR(150) NULL,             -- établissement (facultatif)
  city             VARCHAR(100) NULL,
  school_year      VARCHAR(9)   NULL,             -- ex : 2026-2027
  status           ENUM('active','suspended','pending') NOT NULL DEFAULT 'active',
  email_verified_at DATETIME NULL,
  last_login_at    DATETIME NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_phone (phone),
  UNIQUE KEY uq_users_email (email),              -- plusieurs NULL autorisés
  KEY idx_users_role (role_id),
  KEY idx_users_class (class_id),
  KEY idx_users_status (status),
  CONSTRAINT fk_users_role  FOREIGN KEY (role_id)  REFERENCES roles(id),
  CONSTRAINT fk_users_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  token_hash  CHAR(64)     NOT NULL,              -- SHA-256 du jeton (jamais le jeton en clair)
  expires_at  DATETIME     NOT NULL,
  used_at     DATETIME     NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pwreset_token (token_hash),
  KEY idx_pwreset_user (user_id),
  CONSTRAINT fk_pwreset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Limitation des tentatives de connexion
CREATE TABLE login_attempts (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  identifier    VARCHAR(190) NOT NULL,            -- téléphone ou e-mail saisi
  ip_address    VARCHAR(45)  NOT NULL,
  success       TINYINT(1)   NOT NULL DEFAULT 0,
  attempted_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_login_identifier (identifier, attempted_at),
  KEY idx_login_ip (ip_address, attempted_at)
) ENGINE=InnoDB;

-- =====================================================================
-- 2. ORGANISATION PÉDAGOGIQUE : Classe -> Matière -> Chapitre -> Contenu
-- =====================================================================

CREATE TABLE subjects (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  class_id    SMALLINT UNSIGNED NOT NULL,
  name        VARCHAR(100) NOT NULL,
  slug        VARCHAR(120) NOT NULL,
  description TEXT NULL,
  icon        VARCHAR(60)  NULL,
  position    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_subjects_class_slug (class_id, slug),
  KEY idx_subjects_active (is_active),
  CONSTRAINT fk_subjects_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE chapters (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  subject_id  INT UNSIGNED NOT NULL,
  title       VARCHAR(180) NOT NULL,
  slug        VARCHAR(200) NOT NULL,
  description TEXT NULL,
  position    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_chapters_subject_slug (subject_id, slug),
  FULLTEXT KEY ft_chapters_title (title),
  CONSTRAINT fk_chapters_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 3. FORMULES ET TARIFS (prix par classe)
-- =====================================================================

CREATE TABLE subscription_plans (
  id             TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code           VARCHAR(30)  NOT NULL,           -- weekly, monthly, yearly
  name           VARCHAR(60)  NOT NULL,
  duration_days  SMALLINT UNSIGNED NOT NULL,
  position       TINYINT UNSIGNED NOT NULL DEFAULT 0,
  is_active      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_plans_code (code)
) ENGINE=InnoDB;

-- Grille de tarifs : un prix par couple (classe, formule), modifiable par l'admin
CREATE TABLE class_plan_prices (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  class_id    SMALLINT UNSIGNED NOT NULL,
  plan_id     TINYINT UNSIGNED NOT NULL,
  price       INT UNSIGNED NOT NULL,              -- en XAF
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_price_class_plan (class_id, plan_id),
  CONSTRAINT fk_price_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
  CONSTRAINT fk_price_plan  FOREIGN KEY (plan_id)  REFERENCES subscription_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 4. CONTENUS ÉDUCATIFS
-- =====================================================================

CREATE TABLE contents (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  chapter_id       INT UNSIGNED NOT NULL,
  type             ENUM('course','exercise','exam','correction') NOT NULL,
  title            VARCHAR(220) NOT NULL,
  description      TEXT NULL,
  body             MEDIUMTEXT NULL,               -- contenu en ligne (HTML nettoyé), facultatif
  video_url        VARCHAR(500) NULL,
  exam_year        SMALLINT UNSIGNED NULL,        -- pour les épreuves
  correction_of_id INT UNSIGNED NULL,             -- lie un corrigé à son épreuve / exercice
  -- Règle d'accès : free = tous, logged = connecté, subscriber = abonné actif,
  -- plan = formule précise (required_plan_id obligatoire)
  access_level     ENUM('free','logged','subscriber','plan') NOT NULL DEFAULT 'subscriber',
  required_plan_id TINYINT UNSIGNED NULL,
  is_downloadable  TINYINT(1) NOT NULL DEFAULT 0, -- téléchargement autorisé ou lecture seule
  is_published     TINYINT(1) NOT NULL DEFAULT 0,
  published_at     DATETIME NULL,
  views_count      INT UNSIGNED NOT NULL DEFAULT 0,
  created_by       INT UNSIGNED NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contents_chapter (chapter_id, type),
  KEY idx_contents_published (is_published, published_at),
  KEY idx_contents_access (access_level),
  FULLTEXT KEY ft_contents_search (title, description),
  CONSTRAINT fk_contents_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE,
  CONSTRAINT fk_contents_corr    FOREIGN KEY (correction_of_id) REFERENCES contents(id) ON DELETE SET NULL,
  CONSTRAINT fk_contents_plan    FOREIGN KEY (required_plan_id) REFERENCES subscription_plans(id) ON DELETE SET NULL,
  CONSTRAINT fk_contents_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE content_files (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  content_id    INT UNSIGNED NOT NULL,
  file_kind     ENUM('pdf','document','image','video','other') NOT NULL DEFAULT 'pdf',
  original_name VARCHAR(255) NOT NULL,
  stored_name   VARCHAR(255) NOT NULL,            -- nom aléatoire sur le disque
  storage_path  VARCHAR(500) NOT NULL,            -- chemin relatif dans storage/ (hors dossier public)
  mime_type     VARCHAR(100) NOT NULL,
  size_bytes    BIGINT UNSIGNED NOT NULL DEFAULT 0,
  position      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_files_stored (stored_name),
  KEY idx_files_content (content_id),
  CONSTRAINT fk_files_content FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 5. ABONNEMENTS ET PAIEMENTS (MeSomb)
-- =====================================================================

CREATE TABLE subscriptions (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  class_id    SMALLINT UNSIGNED NOT NULL,         -- classe couverte par l'abonnement
  plan_id     TINYINT UNSIGNED NOT NULL,
  price_paid  INT UNSIGNED NOT NULL,              -- prix figé à l'achat (XAF)
  status      ENUM('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
  starts_at   DATETIME NULL,                      -- renseignés à l'activation (paiement confirmé)
  ends_at     DATETIME NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_subs_user_status (user_id, status, ends_at),
  KEY idx_subs_ends (status, ends_at),            -- pour les rappels d'expiration
  CONSTRAINT fk_subs_user  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_subs_class FOREIGN KEY (class_id) REFERENCES classes(id),
  CONSTRAINT fk_subs_plan  FOREIGN KEY (plan_id)  REFERENCES subscription_plans(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id            INT UNSIGNED NOT NULL,
  subscription_id    INT UNSIGNED NULL,
  transaction_ref    VARCHAR(60)  NOT NULL,       -- référence interne unique (ex : SKL-20261005-000123)
  provider           VARCHAR(30)  NOT NULL DEFAULT 'mesomb',
  provider_reference VARCHAR(100) NULL,           -- identifiant de transaction côté MeSomb
  amount             INT UNSIGNED NOT NULL,       -- XAF
  fees               INT UNSIGNED NULL,
  currency           CHAR(3)      NOT NULL DEFAULT 'XAF',
  payer_phone        VARCHAR(20)  NOT NULL,
  operator           ENUM('MTN','ORANGE') NULL,
  status             ENUM('pending','success','failed','cancelled','expired') NOT NULL DEFAULT 'pending',
  failure_reason     VARCHAR(255) NULL,
  receipt_number     VARCHAR(40)  NULL,           -- numéro de reçu généré à la réussite
  paid_at            DATETIME NULL,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pay_ref (transaction_ref),
  UNIQUE KEY uq_pay_provider_ref (provider, provider_reference),
  UNIQUE KEY uq_pay_receipt (receipt_number),
  KEY idx_pay_user (user_id, created_at),
  KEY idx_pay_status (status, created_at),
  CONSTRAINT fk_pay_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_pay_sub  FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Journal brut des notifications serveur du fournisseur (audit, litiges, rejeu)
CREATE TABLE payment_events (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  payment_id    INT UNSIGNED NULL,
  provider      VARCHAR(30) NOT NULL DEFAULT 'mesomb',
  event_type    VARCHAR(60) NULL,
  payload       LONGTEXT NOT NULL,                -- corps JSON reçu tel quel
  signature_ok  TINYINT(1) NOT NULL DEFAULT 0,
  processed     TINYINT(1) NOT NULL DEFAULT 0,
  received_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pevents_payment (payment_id),
  CONSTRAINT fk_pevents_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================
-- 6. QUIZ
-- =====================================================================

CREATE TABLE quizzes (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  subject_id       INT UNSIGNED NOT NULL,         -- la classe se déduit via la matière
  chapter_id       INT UNSIGNED NULL,
  title            VARCHAR(200) NOT NULL,
  description      TEXT NULL,
  duration_minutes SMALLINT UNSIGNED NULL,        -- NULL = sans limite de temps
  access_level     ENUM('free','logged','subscriber','plan') NOT NULL DEFAULT 'subscriber',
  required_plan_id TINYINT UNSIGNED NULL,
  shuffle          TINYINT(1) NOT NULL DEFAULT 1, -- mélange questions et options
  is_published     TINYINT(1) NOT NULL DEFAULT 0,
  created_by       INT UNSIGNED NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_quizzes_subject (subject_id, is_published),
  KEY idx_quizzes_chapter (chapter_id),
  FULLTEXT KEY ft_quizzes_title (title),
  CONSTRAINT fk_quizzes_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  CONSTRAINT fk_quizzes_chapter FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE SET NULL,
  CONSTRAINT fk_quizzes_plan    FOREIGN KEY (required_plan_id) REFERENCES subscription_plans(id) ON DELETE SET NULL,
  CONSTRAINT fk_quizzes_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE quiz_questions (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  quiz_id       INT UNSIGNED NOT NULL,
  question_type ENUM('single','true_false') NOT NULL DEFAULT 'single',
  question_text TEXT NOT NULL,
  image_path    VARCHAR(500) NULL,
  explanation   TEXT NULL,                        -- affichée après correction
  position      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_qq_quiz (quiz_id, position),
  CONSTRAINT fk_qq_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_options (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question_id INT UNSIGNED NOT NULL,
  option_text VARCHAR(500) NOT NULL,
  is_correct  TINYINT(1) NOT NULL DEFAULT 0,      -- ne JAMAIS envoyer au navigateur avant la soumission
  position    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_qo_question (question_id),
  CONSTRAINT fk_qo_question FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_attempts (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  quiz_id     INT UNSIGNED NOT NULL,
  status      ENUM('in_progress','completed','expired') NOT NULL DEFAULT 'in_progress',
  score       SMALLINT UNSIGNED NULL,
  total       SMALLINT UNSIGNED NULL,
  started_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- chronomètre géré côté serveur
  finished_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_qa_user (user_id, quiz_id),
  KEY idx_qa_quiz (quiz_id),
  CONSTRAINT fk_qa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_qa_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_attempt_answers (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  attempt_id  INT UNSIGNED NOT NULL,
  question_id INT UNSIGNED NOT NULL,
  option_id   INT UNSIGNED NULL,                  -- NULL = sans réponse
  is_correct  TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_qaa_attempt_question (attempt_id, question_id),
  CONSTRAINT fk_qaa_attempt  FOREIGN KEY (attempt_id)  REFERENCES quiz_attempts(id) ON DELETE CASCADE,
  CONSTRAINT fk_qaa_question FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
  CONSTRAINT fk_qaa_option   FOREIGN KEY (option_id)   REFERENCES quiz_options(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================
-- 7. NOTIFICATIONS, ANNONCES, TÉMOIGNAGES, CONTACT
-- =====================================================================

CREATE TABLE notifications (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  type        VARCHAR(40)  NOT NULL,              -- registration, payment_success, subscription_active,
                                                  -- expiring_soon, expired, new_content, announcement
  channel     ENUM('site','email','sms') NOT NULL DEFAULT 'site',
  title       VARCHAR(180) NOT NULL,
  message     TEXT NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  read_at     DATETIME NULL,
  sent_at     DATETIME NULL,                      -- NULL = en attente d'envoi (e-mail / SMS)
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notif_user (user_id, is_read, created_at),
  KEY idx_notif_pending (channel, sent_at),
  CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE announcements (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title       VARCHAR(180) NOT NULL,
  body        TEXT NOT NULL,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  starts_at   DATETIME NULL,
  ends_at     DATETIME NULL,
  created_by  INT UNSIGNED NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ann_active (is_active, starts_at, ends_at),
  CONSTRAINT fk_ann_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE testimonials (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  author_name   VARCHAR(120) NOT NULL,
  author_role   VARCHAR(120) NULL,                -- ex : Élève en Tle, parent d'élève
  content       TEXT NOT NULL,
  rating        TINYINT UNSIGNED NULL,            -- 1 à 5
  photo_path    VARCHAR(500) NULL,
  is_published  TINYINT(1) NOT NULL DEFAULT 0,
  position      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(120) NOT NULL,
  phone       VARCHAR(20)  NULL,
  email       VARCHAR(190) NULL,
  subject     VARCHAR(180) NULL,
  message     TEXT NOT NULL,
  status      ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  ip_address  VARCHAR(45) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contact_status (status, created_at)
) ENGINE=InnoDB;

-- =====================================================================
-- 8. SUIVI, JOURNAUX, PARAMÈTRES
-- =====================================================================

CREATE TABLE downloads (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED NULL,
  content_file_id INT UNSIGNED NOT NULL,
  ip_address      VARCHAR(45) NULL,
  downloaded_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_dl_user (user_id, downloaded_at),
  KEY idx_dl_file (content_file_id),
  CONSTRAINT fk_dl_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_dl_file FOREIGN KEY (content_file_id) REFERENCES content_files(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NULL,
  action      VARCHAR(80)  NOT NULL,              -- ex : content.create, user.suspend, price.update
  entity_type VARCHAR(60)  NULL,
  entity_id   BIGINT UNSIGNED NULL,
  details     TEXT NULL,                          -- JSON éventuel
  ip_address  VARCHAR(45) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_log_user (user_id, created_at),
  KEY idx_log_entity (entity_type, entity_id),
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
  setting_key   VARCHAR(80) NOT NULL,
  setting_value TEXT NULL,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB;

-- =====================================================================
-- DONNÉES INITIALES
-- =====================================================================

INSERT INTO roles (code, label) VALUES
  ('admin',   'Administrateur'),
  ('student', 'Élève');

INSERT INTO classes (name, slug, cycle, exam, position) VALUES
  ('6e',       '6e',       'college', NULL,         1),
  ('5e',       '5e',       'college', NULL,         2),
  ('4e',       '4e',       'college', NULL,         3),
  ('3e',       '3e',       'college', 'BEPC',       4),
  ('2nde',     '2nde',     'lycee',   NULL,         5),
  ('1ère',     '1ere',     'lycee',   'PROBATOIRE', 6),
  ('Tle',      'tle',      'lycee',   'BAC',        7);

INSERT INTO subscription_plans (code, name, duration_days, position) VALUES
  ('weekly',  'Hebdomadaire', 7,   1),
  ('monthly', 'Mensuel',      30,  2),
  ('yearly',  'Annuel',       365, 3);

-- Grille de départ par paliers (HYPOTHÈSE à valider avec le client, modifiable en admin)
INSERT INTO class_plan_prices (class_id, plan_id, price)
SELECT c.id, p.id,
  CASE
    WHEN c.slug IN ('6e','5e','4e') THEN
      CASE p.code WHEN 'weekly' THEN 500  WHEN 'monthly' THEN 1500 ELSE 10000 END
    WHEN c.slug IN ('3e','2nde') THEN
      CASE p.code WHEN 'weekly' THEN 750  WHEN 'monthly' THEN 2000 ELSE 14000 END
    ELSE
      CASE p.code WHEN 'weekly' THEN 1000 WHEN 'monthly' THEN 2500 ELSE 18000 END
  END
FROM classes c CROSS JOIN subscription_plans p;

INSERT INTO settings (setting_key, setting_value) VALUES
  ('site_name',            'SKYLEARN'),
  ('site_slogan',          ''),
  ('whatsapp_number',      ''),
  ('contact_phone',        ''),
  ('contact_email',        ''),
  ('downloads_enabled',    '1'),
  ('expiry_reminder_days', '3');

  INSERT INTO settings (setting_key, setting_value) VALUES
  ('site_name',            'SKYLEARN'),
  ('site_slogan',          'Réussir au secondaire, simplement.'),
  ('site_description',     'Cours, exercices, épreuves et corrigés pour les élèves du secondaire au Cameroun.'),
  ('hero_title',           'Réussis ton année scolaire avec SKYLEARN'),
  ('hero_subtitle',        'Cours, exercices, épreuves et corrigés — accessibles partout, sur ton téléphone.'),
  ('cta_primary_label',    'Créer un compte'),
  ('cta_secondary_label',  'Voir les abonnements'),
  ('contact_email',        'contact@skylearn.cm'),
  ('contact_phone',        '+237 6 00 00 00 00'),
  ('whatsapp_number',      '+237600000000'),
  ('address_city',         'Douala, Cameroun'),
  ('facebook_url',         ''),
  ('tiktok_url',           ''),
  ('youtube_url',          ''),
  ('instagram_url',        ''),
  ('linkedin_url',         ''),
  ('primary_color',        '#1d4ed8'),
  ('primary_color_dark',   '#1e3a8a'),
  ('primary_color_light',  '#dbeafe'),
  ('stat_students',        '1500'),
  ('stat_resources',       '800'),
  ('stat_exams',           '250'),
  ('stat_success_rate',    '95')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);




-- ---------------------------------------------------------------------
-- 1. Table des séries
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS series (
  id          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  class_id    SMALLINT UNSIGNED NOT NULL,
  code        VARCHAR(10)  NOT NULL,
  label       VARCHAR(80)  NOT NULL,
  description VARCHAR(255) NULL,
  position    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_series_class_code (class_id, code),
  KEY idx_series_class_active (class_id, is_active),
  CONSTRAINT fk_series_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. Liaison sujets ↔ séries
-- ---------------------------------------------------------------------
ALTER TABLE subjects
  ADD COLUMN IF NOT EXISTS series_id SMALLINT UNSIGNED NULL AFTER class_id,
  ADD KEY IF NOT EXISTS idx_subjects_series (series_id),
  ADD CONSTRAINT fk_subjects_series FOREIGN KEY (series_id)
      REFERENCES series(id) ON DELETE SET NULL;

-- ---------------------------------------------------------------------
-- 3. Liaison users ↔ séries
-- ---------------------------------------------------------------------
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS series_id SMALLINT UNSIGNED NULL AFTER class_id,
  ADD KEY IF NOT EXISTS idx_users_series (series_id),
  ADD CONSTRAINT fk_users_series FOREIGN KEY (series_id)
      REFERENCES series(id) ON DELETE SET NULL;


-- ---------------------------------------------------------------------
-- 5. Séries de 2nde
-- ---------------------------------------------------------------------
INSERT INTO series (class_id, code, label, description, position) VALUES
  ((SELECT id FROM classes WHERE slug = '2nde'), 'A',   '2nde A — Littéraire',          'Orientation vers A4/A5',        1),
  ((SELECT id FROM classes WHERE slug = '2nde'), 'C',   '2nde C — Mathématiques',       'Orientation vers C ou E',       2),
  ((SELECT id FROM classes WHERE slug = '2nde'), 'TI',  '2nde TI — Technologies',       'Orientation vers TI',           3),
  ((SELECT id FROM classes WHERE slug = '2nde'), 'ACC', '2nde ACC — Comptabilité',      'Orientation vers ACC',          4),
  ((SELECT id FROM classes WHERE slug = '2nde'), 'CG',  '2nde CG — Commerce & Gestion', 'Orientation vers CG',           5),
  ((SELECT id FROM classes WHERE slug = '2nde'), 'ESF', '2nde ESF — Économie',          'Orientation vers ESF',          6);

-- ---------------------------------------------------------------------
-- 6. Séries de 1ère
-- ---------------------------------------------------------------------
INSERT INTO series (class_id, code, label, description, position) VALUES
  ((SELECT id FROM classes WHERE slug = '1ere'), 'A4',  'Série A4 — Littéraire',              'Langues vivantes, littérature',    1),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'A5',  'Série A5 — Langues étrangères',      'Anglais, Espagnol, Allemand',      2),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'C',   'Série C — Mathématiques',            'Maths + Sciences physiques',       3),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'D',   'Série D — Sciences Bio',             'SVT + Sciences physiques',         4),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'E',   'Série E — Mathématiques & Technique','Maths + Technique',                5),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'TI',  'Série TI — Technologies',            'Informatique et technologies',     6),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'ACC', 'Série ACC — Comptabilité',           'Comptabilité et gestion',          7),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'CG',  'Série CG — Commerce & Gestion',      'Commerce, économie',               8),
  ((SELECT id FROM classes WHERE slug = '1ere'), 'ESF', 'Série ESF — Économie',               'Économie et sciences financières', 9);

-- ---------------------------------------------------------------------
-- 7. Séries de Tle
-- ---------------------------------------------------------------------
INSERT INTO series (class_id, code, label, description, position) VALUES
  ((SELECT id FROM classes WHERE slug = 'tle'), 'A4',  'Série A4 — Littéraire',              'Langues vivantes, littérature',    1),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'A5',  'Série A5 — Langues étrangères',      'Anglais, Espagnol, Allemand',      2),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'C',   'Série C — Mathématiques',            'Maths + Sciences physiques',       3),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'D',   'Série D — Sciences Bio',             'SVT + Sciences physiques',         4),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'E',   'Série E — Mathématiques & Technique','Maths + Technique',                5),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'TI',  'Série TI — Technologies',            'Informatique et technologies',     6),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'ACC', 'Série ACC — Comptabilité',           'Comptabilité et gestion',          7),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'CG',  'Série CG — Commerce & Gestion',      'Commerce, économie',               8),
  ((SELECT id FROM classes WHERE slug = 'tle'), 'ESF', 'Série ESF — Économie',               'Économie et sciences financières', 9);

-- Compte administrateur : à créer depuis un script PHP, jamais avec un mot de passe en clair.
-- Générer le hash :  php -r "echo password_hash('MotDePasseFort', PASSWORD_DEFAULT);"
-- INSERT INTO users (role_id, first_name, last_name, phone, email, password_hash)
-- VALUES ((SELECT id FROM roles WHERE code='admin'), 'Admin', 'SKYLEARN', '+237600000000',
--         'admin@skylearn.example', '<hash_ici>');

-- =====================================================================
-- REQUÊTES UTILES (référence, en commentaire)
-- =====================================================================

-- Un élève a-t-il accès à un contenu "subscriber" de sa classe ?
-- La classe du contenu se déduit via chapitre -> matière.
--
-- SELECT 1
-- FROM contents c
-- JOIN chapters ch ON ch.id = c.chapter_id
-- JOIN subjects s  ON s.id  = ch.subject_id
-- JOIN subscriptions sub ON sub.class_id = s.class_id
-- WHERE c.id = :content_id
--   AND sub.user_id = :user_id
--   AND sub.status = 'active'
--   AND sub.ends_at > NOW()
--   AND (c.access_level <> 'plan' OR sub.plan_id = c.required_plan_id)
-- LIMIT 1;

-- Marquer les abonnements expirés (à lancer par tâche planifiée, en complément
-- de la vérification ends_at > NOW() faite à chaque accès) :
--
-- UPDATE subscriptions SET status = 'expired'
-- WHERE status = 'active' AND ends_at <= NOW();

-- Recherche rapide de contenus :
--
-- SELECT id, title, type FROM contents
-- WHERE is_published = 1
--   AND MATCH(title, description) AGAINST (:terme IN NATURAL LANGUAGE MODE);
