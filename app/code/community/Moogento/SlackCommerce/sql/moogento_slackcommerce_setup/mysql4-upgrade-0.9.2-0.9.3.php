<?php
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

if (!$installer->columnExists('moogento_slackcommerce/ipfail', 'count_of_fails_per_day')) {
    $installer->run("ALTER TABLE `{$installer->getTable('moogento_slackcommerce/ipfail')}` ADD COLUMN count_of_fails_per_day int(11) unsigned NOT NULL DEFAULT '0';");
}

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('moogento_slackcommerce/targetfail')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('moogento_slackcommerce/targetfail')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `target` TEXT NULL DEFAULT NULL,
  `count_of_fails_per_day` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$installer->endSetup();