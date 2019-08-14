<?php


class Moogento_SlackCommerce_Block_Adminhtml_System_Config_Failsstatistic extends Mage_Adminhtml_Block_Widget implements
    Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'moogento/slackcommerce/system/config/failsstatistic.phtml';

    public function initForm()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        $head->addJs('moogento/slackcommerce/failsstatistic.js');

        return parent::_prepareLayout();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    protected function _getValues()
    {
        $fails_statistic = array(
            'send_type' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/send_type'),
            'custom_channel' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/custom_channel'),
            'colorize' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/colorize'),
            'color' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/color'),
            'hour' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/hour'),
            
            'total_number_fails' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/total_number_fails'),
            'count_ip_fails' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/count_ip_fails'),
            'count_target_fails' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/count_target_fails'),
            'not_sent_if_no_fails' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/not_sent_if_no_fails'),
            'have_line_fails' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/have_line_fails'),
            'line_fails' => Mage::getStoreConfig('moogento_slackcommerce/fails_statistic/line_fails'),
        );

        return Mage::helper('core')->jsonEncode($fails_statistic);
    }


}