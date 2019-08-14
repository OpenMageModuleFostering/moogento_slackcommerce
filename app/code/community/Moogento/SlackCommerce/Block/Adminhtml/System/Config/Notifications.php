<?php


class Moogento_SlackCommerce_Block_Adminhtml_System_Config_Notifications extends Mage_Adminhtml_Block_Widget implements
    Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'moogento/slackcommerce/system/config/notifications.phtml';

    protected $_list = array(
        array(
            'key' => 'new_order',
            'name' => 'New Order',
        ),
        array(
            'key' => 'new_invoice',
            'name' => 'New Invoice',
        ),
        array(
            'key' => 'new_shipment',
            'name' => 'New Shipment',
        ),
        array(
            'key' => 'new_credit',
            'name' => 'New Credit',
        ),
    );

    public function initForm()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        $head->addJs('moogento/general/jquery.min.js');
        $head->addJs('moogento/general/jquery-ui.min.js');
        $head->addJs('moogento/general/chosen.jquery.min.js');
        $head->addJs('moogento/general/knockout.js');
        $head->addJs('moogento/general/jquery.switchButton.js');
        $head->addJs('moogento/jscolor/jscolor.js');

        $head->addJs('moogento/slackcommerce/notifications.js');

        $head->addCss('moogento/general/config.css');
        $head->addCss('moogento/general/chosen.min.css');
        $head->addCss('moogento/general/jqueryui/jquery-ui-1.10.4.custom.min.css');

        return parent::_prepareLayout();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    protected function _getNotificationsJson()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();

        foreach($statuses as $status => $label){
            $this->_list[] = array(
                'key' => 'new_status_' . $status,
                'name' => 'New ' . $label . ' Order',
            );
        }

        $this->_list[] = array(
            'key' => 'new_backend_account',
            'name' => 'New Backend Account',
        );

        $this->_list[] = array(
            'key' => 'backend_login',
            'name' => 'Backend Login',
        );
        $this->_list[] = array(
            'key' => 'backend_login_fail',
            'name' => 'Backend Login Fail',
        );

        $notificationsData = array();

        foreach ($this->_list as $data) {
            $key = $data['key'];

            $data['send_type'] = Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_send_type');
            $data['custom_channel'] = Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_custom_channel');
            $data['colorize'] = Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_colorize');
            $data['color'] = Mage::getStoreConfig('moogento_slackcommerce/notifications/' . $key . '_color');

            $notificationsData[] = $data;
        }

        return Mage::helper('core')->jsonEncode($notificationsData);
    }


}