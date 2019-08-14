<?php
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('moogento_slackcommerce/ipfail')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('moogento_slackcommerce/ipfail')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` int(11) signed NOT NULL COMMENT 'IP',
  `even_attempts_number` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$installer->endSetup();