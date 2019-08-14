<?php


class Moogento_SlackCommerce_Model_Notification_New_Backend_Account extends Moogento_SlackCommerce_Model_Notification_Abstract
{
    protected $_referenceModel = 'admin/user';

    protected function _prepareText()
    {
        return $this->helper()->__('New backend account');
    }

    protected function _getAttachments()
    {
        return array(
            'fields' => array(
                array(
                    'title' => $this->helper()->__('User'),
                    'value' => $this->_getReferenceObject()->getFirstname() . ' ' . $this->_getReferenceObject()->getLatsname(),
                    'short' => true,
                ),
                array(
                    'title' => $this->helper()->__('Email'),
                    'value' => $this->_getReferenceObject()->getEmail(),
                    'short' => true,
                ),
                array(
                    'title' => $this->helper()->__('Role'),
                    'value' => $this->_getReferenceObject()->getRole()->getRoleName(),
                    'short' => true,
                ),
            ),
        );
    }
} 