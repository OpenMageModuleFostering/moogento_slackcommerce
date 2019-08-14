<?php


class Moogento_SlackCommerce_Model_Resource_Queue extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_serializableFields   = array(
        'additional_data' => array(
            array(),
            array(),
            false
        ),
    );

    protected function _construct()
    {
        $this->_init('moogento_slackcommerce/queue', 'id');
    }
} 