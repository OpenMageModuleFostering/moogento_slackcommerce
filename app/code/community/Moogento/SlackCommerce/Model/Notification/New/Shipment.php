<?php


class Moogento_SlackCommerce_Model_Notification_New_Shipment extends Moogento_SlackCommerce_Model_Notification_New_Order
{
    protected $_referenceModel = 'sales/order_shipment';

    protected function _getOrder()
    {
        return $this->_getReferenceObject()->getOrder();
    }

    protected function _prepareText()
    {
        return $this->helper()->__('New shipment #%s for order #%s', $this->_getReferenceObject()->getIncrementId(), $this->_getOrder()->getIncrementId());
    }

    protected function _getAttachments() {
        return array(
            'fields' => array_merge(array(
                array(
                    'title' => $this->helper()->__('Total Qty Shipped'),
                    'value' => $this->_getReferenceObject()->getTotalQty(),
                    'short' => true,
                ),
            ), $this->_prepareOrderFields()),
        );
    }

    protected function _getProductsData()
    {
        $data = array();
        $limit = 3;
        $i = 0;
        foreach ($this->_getReferenceObject()->getAllItems() as $item) {
            if ($i >= $limit) break;
            $data[] = (int)$item->getQty() . ' x ' . $item->getSku();
            $i++;
        }

        return implode("\n", $data);
    }
}