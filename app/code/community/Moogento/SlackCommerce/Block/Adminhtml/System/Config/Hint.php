<?php

class Moogento_SlackCommerce_Block_Adminhtml_System_Config_Hint
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    protected $_template = 'moogento/slackcommerce/system/config/hint.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    public function getLogo()
    {
        return Mage::getStoreConfig('moogento/general/url') . 'media/moo_logo/' . Mage::helper('moogento_slackcommerce/moo')->l() . '/moogento_logo_slackcommerce.png';
    }

    public function getInfo()
    {
        return Mage::getStoreConfig('moogento/general/url') . 'media/moo_info/' . Mage::helper('moogento_slackcommerce/moo')->i() . '/moogento_slackcommerce.js';
    }
}