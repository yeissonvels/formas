-- TABLA PDFS

ALTER TABLE `ge_pdfs` CHANGE `dni` `dni` VARCHAR(14) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `total_checked_by` `total_checked_by` INT(11) NULL, CHANGE `total_checked_on` `total_checked_on` DATETIME NULL, CHANGE `total_checked_note` `total_checked_note` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `total_checked_system_date` `total_checked_system_date` DATETIME NULL, CHANGE `salecomment` `salecomment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `pdfname` `pdfname` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `pdf_uploaded_on` `pdf_uploaded_on` DATETIME NULL, CHANGE `commissionpayed` `commissionpayed` INT(11) NULL, CHANGE `commission_payed_on` `commission_payed_on` DATETIME NULL, CHANGE `commission_validated_by` `commission_validated_by` INT(11) NULL, CHANGE `image` `image` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `status` `status` INT(11) NULL, CHANGE `reuploaded` `reuploaded` INT(11) NULL, CHANGE `returned_on` `returned_on` DATETIME NULL, CHANGE `comment` `comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `orderexist` `orderexist` INT(11) NULL, CHANGE `accounting_checked_by` `accounting_checked_by` INT(11) NULL, CHANGE `accounting_checked_on` `accounting_checked_on` DATETIME NULL, CHANGE `accounting_checked_note` `accounting_checked_note` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `accounting_checked_system_date` `accounting_checked_system_date` DATETIME NULL, CHANGE `cancelled_by` `cancelled_by` INT(11) NULL, CHANGE `cancelled_on` `cancelled_on` DATETIME NULL, CHANGE `cancell_reason` `cancell_reason` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE `ge_pdfs` CHANGE `cancelled` `cancelled` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `ge_pdfs` CHANGE `pdf_yet_printed` `pdf_yet_printed` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nos permite controlar si la propuesta de pedido ya se imprimio', CHANGE `pending_payed_on` `pending_payed_on` DATETIME NULL DEFAULT NULL;
ALTER TABLE `ge_pdfs` ADD `tel` VARCHAR(60) NULL DEFAULT NULL AFTER `customer`;

-- TABLA USUARIOS 
ALTER TABLE `ge_users` CHANGE `last_login` `last_login` DATETIME NULL DEFAULT NULL;
ALTER TABLE `ge_users` CHANGE `deleted_on` `deleted_on` DATETIME NULL DEFAULT NULL;
ALTER TABLE `ge_users` CHANGE `deleted_by` `deleted_by` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ge_users` CHANGE `deleted_reason` `deleted_reason` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- TABLA MENU

ALTER TABLE `ge_menu_items` CHANGE `icon` `icon` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- ESTIMATES

CREATE TABLE `ge_estimates` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `storeid` int(11) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `tel` varchar(60) NOT NULL,
  `saledate` datetime NOT NULL COMMENT 'aunque se llama venta, realmente es la fecha del presupuesto',
  `total` decimal(10,2) NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` int(11) NULL,
  `cancelled` int(11) NULL,
  `cancelled_by` int(11) NULL,
  `cancelled_on` datetime NULL,
  `cancell_reason` varchar(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `ge_estimates`
  ADD PRIMARY KEY (`id`);

  ALTER TABLE `ge_estimates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

  ALTER TABLE `ge_estimates` ADD `estimateorigin` INT NOT NULL AFTER `tel`;

  -- ESTIMATE COMMENTS

  CREATE TABLE `ge_estimate_comments` (
  `id` int(11) NOT NULL,
  `estimateid` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `ge_estimate_comments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ge_estimate_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


  --- INCIDENCES

  ALTER TABLE `ge_incidences` CHANGE `observations` `observations` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Observaciones para la entrega', CHANGE `customer` `customer` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Sólo cuando la incidencia no pertenece a un pedido', CHANGE `dni` `dni` VARCHAR(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `address` `address` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `cp` `cp` INT(11) NULL, CHANGE `city` `city` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `provinceid` `provinceid` INT(11) NULL, CHANGE `telephone` `telephone` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `telephone2` `telephone2` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `email` `email` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `deliverydate` `deliverydate` DATETIME NULL, CHANGE `seller` `seller` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `assembler` `assembler` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
