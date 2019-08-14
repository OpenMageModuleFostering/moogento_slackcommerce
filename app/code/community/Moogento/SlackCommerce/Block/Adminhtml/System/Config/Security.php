<?php


class Moogento_SlackCommerce_Block_Adminhtml_System_Config_Security extends Mage_Adminhtml_Block_Widget implements
    Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'moogento/slackcommerce/system/config/security.phtml';

    public function initForm()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        $head->addJs('moogento/slackcommerce/security.js');

        return parent::_prepareLayout();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    protected function _getValues()
    {
        $security = array(
            'security_stats' => Mage::getStoreConfig('moogento_slackcommerce/security/security_stats'),
			
            'hour' => Mage::getStoreConfig('moogento_slackcommerce/security/hour'),
            'send_type' => Mage::getStoreConfig('moogento_slackcommerce/security/send_type'),
            'custom_channel' => Mage::getStoreConfig('moogento_slackcommerce/security/custom_channel'),
            'colorize' => Mage::getStoreConfig('moogento_slackcommerce/security/colorize'),
            'color' => Mage::getStoreConfig('moogento_slackcommerce/security/color'),

            'send_type_immediate' => Mage::getStoreConfig('moogento_slackcommerce/security/send_type_immediate'),
            'immediate_custom_channel' => Mage::getStoreConfig('moogento_slackcommerce/security/immediate_custom_channel'),
            'colorize_immediate' => Mage::getStoreConfig('moogento_slackcommerce/security/colorize_immediate'),
            'color_immediate' => Mage::getStoreConfig('moogento_slackcommerce/security/color_immediate'),

            'total_number_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/total_number_fails'),
            'count_ip_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/count_ip_fails'),
            'count_target_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/count_target_fails'),
            'not_sent_if_no_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/not_sent_if_no_fails'),
            'have_line_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/have_line_fails'),
            'line_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/line_fails'),
        );

        return Mage::helper('core')->jsonEncode($security);
    }


}