<?php


class Moogento_SlackCommerce_Model_Notification_New_Invoice extends Moogento_SlackCommerce_Model_Notification_New_Order
{
    protected $_referenceModel = 'sales/order_invoice';

    protected function _getOrder()
    {
        return $this->_getReferenceObject()->getOrder();
    }

    protected function _prepareText()
    {
        return $this->helper()->__('New invoice #%s for order #%s', $this->_getReferenceObject()->getIncrementId(), $this->_getOrder()->getIncrementId());
    }
	
    protected function _trimZeros($amount) {
        return preg_replace('~\.00$~','',$amount);
    }

    protected function _getAttachments() {
        return array(
            'fields' => array_merge(array(
                array(
                    'title' => $this->helper()->__('Invoice Amount'),
                    'value' => $this->_trimZeros(strip_tags($this->_getOrder()->formatPrice($this->_getReferenceObject()->getGrandTotal()))),
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
            $data[] = $item->getQty() . ' x ' . $item->getSku();
            $i++;
        }

        return implode("\n", $data);
    }
}