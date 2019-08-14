<?php

class Moogento_SlackCommerce_Block_Adminhtml_System_Config_Form_Button_Test extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
    * Set template
    */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getButtonHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getClickUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/slackcommerce/test/');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                       ->setData(array(
                           'id'        => 'test_button',
                           'label'     => $this->helper('adminhtml')->__('Send test message'),
                           'onclick'   => "javascript:setLocation('" . $this->getClickUrl() .  "'); return false;",
                       ));

        return $button->toHtml();
    }
} 