<?php

$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

if (!$installer->columnExists('moogento_slackcommerce/queue', 'additional_data')) {
    $installer->run("ALTER TABLE `{$installer->getTable('moogento_slackcommerce/queue')}` ADD COLUMN additional_data TEXT NULL DEFAULT NULL;");
}
$installer->endSetup();