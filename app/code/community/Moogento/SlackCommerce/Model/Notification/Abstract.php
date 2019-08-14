<?php


abstract class Moogento_SlackCommerce_Model_Notification_Abstract extends Varien_Object
{
    protected $_referenceModel = false;
    protected $_referenceObject = null;

    public function helper()
    {
        return Mage::helper('moogento_slackcommerce');
    }

    protected function _getReferenceObject()
    {
        if (is_null($this->_referenceObject)) {
            $this->_referenceObject = false;
            if ($this->_referenceModel && $this->getReferenceId()) {
                $this->_referenceObject = Mage::getModel($this->_referenceModel)->load($this->getReferenceId());
            }
        }

        return $this->_referenceObject;
    }

    public function prepareData()
    {
        $data = array(
            'channel' => null,
            'attachments' => array(),
        );
        $key = $this->getEventKey();

        $referenceObject = $this->_getReferenceObject();
        $store = Mage::app()->getDefaultStoreView();
        if ($referenceObject && $referenceObject->getStoreId()) {
            $store = Mage::app()->getStore($referenceObject->getStoreId());
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');

        //Start environment emulation of the specified store
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store->getId());

        if (Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_send_type') == 'custom') {
            $data['channel'] = Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_custom_channel');
        }
        $data['username'] = $store->getFrontendName();

        if (Mage::getStoreConfig('moogento_slackcommerce/general/icon')){
            $data['icon_url'] = Mage::getBaseUrl('media') . 'moogento/slack/' . Mage::getStoreConfig('moogento_slackcommerce/general/icon');
        } else {
            $data['icon_url'] = Mage::getBaseUrl('media') . 'moogento/slack/moogento_logo_small.png';
        }

        $data['text'] = $this->_prepareText();

        $data['attachments'] = array($this->_prepareAttachments());

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $data;
    }

    protected function _prepareAttachments()
    {
        $key = $this->getEventKey();
        $attachments = $this->_getAttachments();
        if (Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_colorize')) {
            $attachments['color'] = Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_color');
        }
        $attachments['fallback'] = $this->_prepareFallback($attachments);

        return $attachments;
    }

    protected abstract function _prepareText();

    protected function _getAttachments() {
        return array();
    }

    protected function _prepareFallback($attachments)
    {
        $fallback = array();
        foreach ($attachments['fields'] as $field) {
            $fallback[] = $field['title'] . ': ' . $field['value'];
        }

        return implode("\n", $fallback);
    }
} 