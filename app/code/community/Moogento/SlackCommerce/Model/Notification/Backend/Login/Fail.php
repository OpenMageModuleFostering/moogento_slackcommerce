<?php


class Moogento_SlackCommerce_Model_Notification_Backend_Login_Fail extends Moogento_SlackCommerce_Model_Notification_Abstract
{
    protected function _prepareText()
    {
        return $this->helper()->__('Backend login fail');
    }

    protected function _getAttachments()
    {
        $additionalData = $this->getAdditionalData();
        if (!is_array($additionalData)) {
            $additionalData = @unserialize($additionalData);
        }
        return array(
            'fields' => array(
                array(
                    'title' => $this->helper()->__('User'),
                    'value' => $additionalData['username'],
                    'short' => true,
                ),
                array(
                    'title' => $this->helper()->__('IP'),
                    'value' => $additionalData['IP']. ($additionalData['Country'] ? ' ('.$additionalData['Country'].')':''),
                    'short' => false,
                ),
                array(
                    'title' => $this->helper()->__('Source'),
                    'value' => $additionalData['URL'],
                    'short' => false,
                ),
                array(
                    'title' => $this->helper()->__('# Fails (this IP)'),
                    'value' => (1+$additionalData['Even_number_of_failed_open']),
                    'short' => false,
                ),
                array(
                    'title' => $this->helper()->__('Error'),
                    'value' => $additionalData['message'],
                    'short' => false,
                ),
            ),
        );
    }
} 