<?php


class Moogento_SlackCommerce_Model_Notification_New_Status extends Moogento_SlackCommerce_Model_Notification_New_Order
{
    protected $_referenceModel = 'sales/order';

    protected function _prepareText()
    {
        $status = str_replace(Moogento_SlackCommerce_Model_Queue::KEY_NEW_STATUS . '_', '', $this->getEventKey());
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        if (isset($statuses[$status])) {
            $status = $statuses[$status];
        }
        return $this->helper()->__('Order #%s assigned new status: %s', $this->_getOrder()->getIncrementId(), $status);
    }
}