<?php


class Moogento_SlackCommerce_Model_Notification_New_Credit extends Moogento_SlackCommerce_Model_Notification_New_Order
{
    protected $_referenceModel = 'sales/order_creditmemo';

    protected function _getOrder()
    {
        return $this->_getReferenceObject()->getOrder();
    }

    protected function _prepareText()
    {
        // return $this->helper()->__('New creditmemo #%s for order #%s', $this->_getReferenceObject()->getIncrementId(), $this->_getOrder()->getIncrementId());
        return $this->helper()->__('Creditmemo (Order #%s)', $this->_getOrder()->getIncrementId());
    }
	
    protected function _trimZeros($amount) {
        return preg_replace('~\.00$~','',$amount);
    }

    protected function _getAttachments() {
        return array(
            'fields' => array_merge(array(
                array(
                    'title' => $this->helper()->__('Credit Amount'),
                    'value' => $this->_trimZeros(strip_tags($this->_getOrder()->formatPrice($this->_getReferenceObject()->getGrandTotal()))),
                    'short' => true,
                ),
            ), $this->_prepareOrderFields()),
        );
    }

    protected function _getProductsData()
    {
        $data = array();
        $limit = 2;
		$count = count($this->_getReferenceObject()->getAllItems());
        $i = 0;
        foreach ($this->_getReferenceObject()->getAllItems() as $item) {
            if ($i >= ($limit + 1)) break; // ie. need 2 over to show summary line (may as well show item if 1 over, instead of summary of 1)
            $data[] = $this->_trimZeros($item->getQty()) . ' x ' . $item->getSku();
            $i++;
        }
		if($count > $i) $data[] = '(+ ' . ($count - $i) . ' more)';

        return implode("\n", $data);
    }
} 