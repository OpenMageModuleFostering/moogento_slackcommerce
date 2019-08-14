<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DELETE FROM `{$installer->getTable('core_config_data')}` WHERE path='moogento_slackcommerce/security/line_fails';
");

$installer->endSetup();