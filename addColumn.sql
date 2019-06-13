ALTER TABLE commandes ADD COLUMN depart_atelier  DATE DEFAULT NULL AFTER delivery_at;
ALTER TABLE commandes ADD COLUMN date_lancement  DATE DEFAULT NULL AFTER depart_atelier;


CREATE TABLE CAS_Value (
`id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
`Num_CAS` int(6) UNSIGNED NOT NULL COMMENT 'Identifiant Série sur lequel commencer à compter'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `CAS_Value` (`Num_CAS`) VALUES
(5),
(8),
(11),
(12),
(13),
(14),
(15),
(18),
(22),
(24),
(25),
(31);


INSERT INTO `depots` (`id`,`name`, `open`) VALUES
(003, 'Lyon', 1),
(810, 'DCM',1 ),
(820, 'Vulcain', 1),
(830, 'SST', 1 ),
(999, 'divers',  1);
COMMIT;


ALTER TABLE CAS_Value
ADD CONSTRAINT constraint_CAS_Value UNIQUE KEY(`Num_CAS`);


UPDATE `depots` SET `open` = 0 where `id` = 010;
UPDATE `depots` SET `open` = 0 where `id` = 023;
UPDATE `depots` SET `open` = 0 where `id` = 046;
UPDATE `depots` SET `open` = 0 where `id` = 047;

UPDATE `depots` SET `name` = 'Le Mans' where `id` = 020;
UPDATE `depots` SET `name` = 'Bourges' where `id` = 045;

ALTER TABLE bl_series_starters ADD COLUMN reserved_by  varchar(255) DEFAULT NULL;
ALTER TABLE bl_series_starters ADD COLUMN created tinyint(1) DEFAULT 1;