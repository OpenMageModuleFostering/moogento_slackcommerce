<?php

$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('moogento_slackcommerce/queue')}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `event_key` VARCHAR(255) NOT NULL COMMENT 'Key',
  `reference_id` int(10) unsigned NOT NULL COMMENT 'Reference ID',
  `status` tinyint(1) DEFAULT 0,
  `status_message` text NULL DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation date',
  `cron_id` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_MAGE_MOOGENTO_SLACKCOMMERCE_QUEUE_STATUS` (`status`),
  KEY `IDX_MAGE_MOOGENTO_SLACKCOMMERCE_QUEUE_CRON_ID` (`cron_id`),
  KEY `IDX_MAGE_MOOGENTO_SLACKCOMMERCE_QUEUE_DATE` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$installer->endSetup();