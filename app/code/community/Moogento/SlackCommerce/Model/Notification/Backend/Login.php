<?php


class Moogento_SlackCommerce_Model_Notification_Backend_Login extends Moogento_SlackCommerce_Model_Notification_Abstract
{
    protected $_referenceModel = 'admin/user';

    protected function _prepareText()
    {
        return $this->helper()->__('Backend login');
    }

    protected function _getAttachments()
    {
        return array(
            'fields' => array(
                array(
                    'title' => $this->helper()->__('User'),
                    'value' => $this->_getReferenceObject()->getUsername(),
                    'short' => true,
                ),
            ),
        );
    }
}