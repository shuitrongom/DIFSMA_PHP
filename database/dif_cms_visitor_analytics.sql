CREATE TABLE IF NOT EXISTS `visitor_analytics` (
  `id`           BIGINT NOT NULL AUTO_INCREMENT,
  `session_id`   VARCHAR(64)  NOT NULL COMMENT 'Hash de sesión anónima',
  `pagina`       VARCHAR(500) NOT NULL COMMENT 'URL relativa visitada',
  `titulo`       VARCHAR(300) DEFAULT NULL COMMENT 'Título de la página',
  `referrer`     VARCHAR(500) DEFAULT NULL COMMENT 'Página de origen',
  `ip_hash`      VARCHAR(64)  NOT NULL COMMENT 'SHA-256 de IP (privacidad)',
  `dispositivo`  VARCHAR(10)  NOT NULL DEFAULT 'pc' COMMENT 'pc, celular, tablet',
  `os`           VARCHAR(100) DEFAULT NULL COMMENT 'Sistema operativo detectado',
  `navegador`    VARCHAR(100) DEFAULT NULL COMMENT 'Navegador detectado',
  `pais`         VARCHAR(100) DEFAULT NULL,
  `es_bot`       TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created_at`  (`created_at`),
  KEY `idx_session`     (`session_id`),
  KEY `idx_pagina`      (`pagina`(100)),
  KEY `idx_dispositivo` (`dispositivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
