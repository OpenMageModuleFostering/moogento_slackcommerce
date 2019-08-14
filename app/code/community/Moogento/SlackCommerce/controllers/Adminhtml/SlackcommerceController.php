<?php


class Moogento_SlackCommerce_Adminhtml_SlackcommerceController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return true;
    }

    public function testAction()
    {
        $helper = Mage::helper('moogento_slackcommerce/api');
        try {
            $result = $helper->send(array('text' => $helper->__('Moooo! Testing... Daisy 1.. Daisy 2.. Daisy 3..'), 'icon_url' => Mage::getBaseUrl('media') . 'moogento/slack/moogento_logo_small.png'));
            if ($result === true) {
                $this->_getSession()->addSuccess($helper->__('Check the %s channel for our test moo, we enlisted help from Daisy 1-3',
                        Mage::getStoreConfig('moogento_slackcommerce/general/default_channel')));
            } else {
                $this->_getSession()->addError($helper->__('Oops! I couldn\'t send the test message: %s', $result));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }
} 