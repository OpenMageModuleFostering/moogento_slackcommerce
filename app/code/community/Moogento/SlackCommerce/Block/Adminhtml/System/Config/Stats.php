<?php


class Moogento_SlackCommerce_Block_Adminhtml_System_Config_Stats extends Mage_Adminhtml_Block_Widget implements
    Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'moogento/slackcommerce/system/config/stats.phtml';

    public function initForm()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        $head->addJs('moogento/slackcommerce/stats.js');

        return parent::_prepareLayout();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    protected function _getValues()
    {
        $stats = array(
            'send_type' => Mage::getStoreConfig('moogento_slackcommerce/stats/send_type'),
            'custom_channel' => Mage::getStoreConfig('moogento_slackcommerce/stats/custom_channel'),
            'colorize' => Mage::getStoreConfig('moogento_slackcommerce/stats/colorize'),
            'color' => Mage::getStoreConfig('moogento_slackcommerce/stats/color'),

            'qty_orders' => Mage::getStoreConfig('moogento_slackcommerce/stats/qty_orders'),
            'total_revenue' => Mage::getStoreConfig('moogento_slackcommerce/stats/total_revenue'),
            'qty_products' => Mage::getStoreConfig('moogento_slackcommerce/stats/qty_products'),
            'avg_products_order' => Mage::getStoreConfig('moogento_slackcommerce/stats/avg_products_order'),
            'avg_revenue_order' => Mage::getStoreConfig('moogento_slackcommerce/stats/avg_revenue_order'),
            'hour' => Mage::getStoreConfig('moogento_slackcommerce/stats/hour'),
            'daily_stats' => Mage::getStoreConfig('moogento_slackcommerce/stats/daily_stats'),
            'weekly_stats' => Mage::getStoreConfig('moogento_slackcommerce/stats/weekly_stats'),
            'day' => Mage::getStoreConfig('moogento_slackcommerce/stats/day'),
            'total_number_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/total_number_fails'),
            'count_ip_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/count_ip_fails'),
            'count_target_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/count_target_fails'),
            'not_sent_if_no_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/not_sent_if_no_fails'),
            'have_line_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/have_line_fails'),
            'line_fails' => Mage::getStoreConfig('moogento_slackcommerce/security/line_fails'),
        );

        return Mage::helper('core')->jsonEncode($stats);
    }


}