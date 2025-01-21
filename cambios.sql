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
