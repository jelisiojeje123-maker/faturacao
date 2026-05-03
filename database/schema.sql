-- =============================================================
-- SISTEMA DE FATURAÇÃO - PRESTADORES DE SERVIÇOS - MOÇAMBIQUE
-- Schema SQL v1.0
-- Compatível com MySQL 8.0+
-- =============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Criar base de dados
CREATE DATABASE IF NOT EXISTS `faturacao_mz`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `faturacao_mz`;

-- =============================================================
-- TABELA: users (Utilizadores do sistema)
-- =============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(191) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','operador') NOT NULL DEFAULT 'operador',
  `avatar`     VARCHAR(255) NULL,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: company_settings (Configurações da empresa)
-- =============================================================
CREATE TABLE IF NOT EXISTS `company_settings` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(200) NOT NULL,
  `nuit`          VARCHAR(20) NULL COMMENT 'NUIT - Número Único de Identificação Tributária',
  `email`         VARCHAR(191) NULL,
  `phone`         VARCHAR(50) NULL,
  `address`       VARCHAR(300) NULL,
  `city`          VARCHAR(100) NULL,
  `logo`          VARCHAR(255) NULL,
  `iva_rate`      DECIMAL(5,2) NOT NULL DEFAULT 16.00 COMMENT 'Taxa IVA padrão (%)',
  `currency`      VARCHAR(10) NOT NULL DEFAULT 'MT',
  `invoice_prefix`VARCHAR(20) NOT NULL DEFAULT 'FAT',
  `next_invoice_number` INT UNSIGNED NOT NULL DEFAULT 1,
  `payment_terms` TEXT NULL,
  `bank_name`     VARCHAR(150) NULL,
  `bank_account`  VARCHAR(50) NULL,
  `bank_iban`     VARCHAR(50) NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: clients (Clientes)
-- =============================================================
CREATE TABLE IF NOT EXISTS `clients` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(200) NOT NULL,
  `nuit`       VARCHAR(20) NULL COMMENT 'NUIT do cliente',
  `email`      VARCHAR(191) NULL,
  `phone`      VARCHAR(50) NULL,
  `address`    VARCHAR(300) NULL,
  `city`       VARCHAR(100) NULL,
  `country`    VARCHAR(100) NOT NULL DEFAULT 'Moçambique',
  `status`     ENUM('ativo','inativo','pendente') NOT NULL DEFAULT 'ativo',
  `notes`      TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_clients_name` (`name`),
  INDEX `idx_clients_nuit` (`nuit`),
  INDEX `idx_clients_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: services (Catálogo de serviços)
-- =============================================================
CREATE TABLE IF NOT EXISTS `services` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `price`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `unit`        VARCHAR(50) NOT NULL DEFAULT 'un' COMMENT 'un, hora, mês, etc.',
  `type`        ENUM('pontual','recorrente') NOT NULL DEFAULT 'pontual',
  `iva_exempt`  TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = isento de IVA',
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_services_name` (`name`),
  INDEX `idx_services_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: quotes (Orçamentos)
-- =============================================================
CREATE TABLE IF NOT EXISTS `quotes` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`     INT UNSIGNED NOT NULL,
  `quote_number`  VARCHAR(50) NOT NULL UNIQUE,
  `issue_date`    DATE NOT NULL,
  `expiry_date`   DATE NULL,
  `status`        ENUM('rascunho','enviado','aceite','recusado','expirado','convertido') NOT NULL DEFAULT 'rascunho',
  `subtotal`      DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `iva_amount`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `discount`      DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total`         DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes`         TEXT NULL,
  `terms`         TEXT NULL,
  `converted_to_invoice_id` INT UNSIGNED NULL,
  `created_by`    INT UNSIGNED NOT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_quotes_client` (`client_id`),
  INDEX `idx_quotes_status` (`status`),
  CONSTRAINT `fk_quotes_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_quotes_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: quote_items (Itens do orçamento)
-- =============================================================
CREATE TABLE IF NOT EXISTS `quote_items` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`    INT UNSIGNED NOT NULL,
  `service_id`  INT UNSIGNED NULL,
  `description` VARCHAR(500) NOT NULL,
  `quantity`    DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `unit_price`  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `sort_order`  INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_quote_items_quote` (`quote_id`),
  CONSTRAINT `fk_quote_items_quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_quote_items_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: invoices (Faturas)
-- =============================================================
CREATE TABLE IF NOT EXISTS `invoices` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`      INT UNSIGNED NOT NULL,
  `quote_id`       INT UNSIGNED NULL COMMENT 'Se convertida de orçamento',
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `issue_date`     DATE NOT NULL,
  `due_date`       DATE NOT NULL,
  `status`         ENUM('rascunho','emitida','paga','vencida','cancelada') NOT NULL DEFAULT 'rascunho',
  `subtotal`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `iva_rate`       DECIMAL(5,2) NOT NULL DEFAULT 16.00,
  `iva_amount`     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `retencao_rate`  DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Taxa de retenção (%)',
  `retencao_amount`DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `discount`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total`          DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `amount_paid`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `amount_due`     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes`          TEXT NULL,
  `terms`          TEXT NULL,
  `created_by`     INT UNSIGNED NOT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_invoices_client` (`client_id`),
  INDEX `idx_invoices_status` (`status`),
  INDEX `idx_invoices_due_date` (`due_date`),
  INDEX `idx_invoices_issue_date` (`issue_date`),
  CONSTRAINT `fk_invoices_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_invoices_quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_invoices_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: invoice_items (Itens da fatura)
-- =============================================================
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`  INT UNSIGNED NOT NULL,
  `service_id`  INT UNSIGNED NULL,
  `description` VARCHAR(500) NOT NULL,
  `quantity`    DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `unit_price`  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `sort_order`  INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_invoice_items_invoice` (`invoice_id`),
  CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_invoice_items_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- TABELA: payments (Pagamentos)
-- =============================================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`     INT UNSIGNED NOT NULL,
  `amount`         DECIMAL(15,2) NOT NULL,
  `payment_date`   DATE NOT NULL,
  `method`         ENUM('dinheiro','transferencia','mobile_money','cheque','outro') NOT NULL DEFAULT 'transferencia',
  `reference`      VARCHAR(200) NULL COMMENT 'Referência da transação',
  `notes`          TEXT NULL,
  `receipt_number` VARCHAR(50) NULL,
  `created_by`     INT UNSIGNED NOT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_payments_invoice` (`invoice_id`),
  INDEX `idx_payments_date` (`payment_date`),
  INDEX `idx_payments_method` (`method`),
  CONSTRAINT `fk_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
