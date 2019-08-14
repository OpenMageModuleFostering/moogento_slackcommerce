<?php


class Moogento_SlackCommerce_Model_Notification_New_Order extends Moogento_SlackCommerce_Model_Notification_Abstract
{
    protected $_referenceModel = 'sales/order';

    protected function _prepareText()
    {
        return $this->helper()->__('Order #%s', $this->_getOrder()->getIncrementId());
    }

    protected function _getOrder()
    {
        return $this->_getReferenceObject();
    }

    protected function _getAttachments() {
        $fields = $this->_prepareOrderFields();
        return array(
            'fields' => $fields
        );
    }
	
    protected function _prepareCustomerName() {
		$cust_name = '';
		$cust_type = '';
		$cust_name = $this->_getOrder()->getCustomerName();
		
		if($this->_getOrder()->getCustomerIsGuest() || ($cust_name == $this->helper()->__('Guest'))){
			$cust_type = $this->helper()->__('Guest');
			$cust_name = $this->_getOrder()->getBillingAddress()->getName();
		}
		else {
			$cust_type = $this->helper()->__('Customer');
			$cust_name = $this->_getOrder()->getCustomerName();
		}
		
        return array(
            'title' => $cust_type,
            'value' => $cust_name,
            'short' => true,
        );
    }
	
    protected function _prepareOrderAmount() {
        return array(
            'title' => $this->helper()->__('Order Amount'),
            'value' => $this->_trimZeros(strip_tags($this->_getOrder()->formatPrice($this->_getOrder()->getGrandTotal()))),
            'short' => true,
        );
    }
	
    protected function _prepareProductsData() {
        return array(
            'title' => $this->helper()->__('Products'),
            'value' => $this->_getProductsData(),
            'short' => true,
        );
    }
	
    protected function _trimZeros($amount) {
        return preg_replace('~\.00$~','',$amount);
    }

    protected function _prepareOrderFields()
    {		
        return array(
            $this->_prepareCustomerName(),
            $this->_prepareOrderAmount(),
            $this->_prepareProductsData(),
        );
    }

    protected function _getProductsData()
    {
        $data = array();
        $limit = 2;
		$count = count($this->_getOrder()->getAllVisibleItems());
        $i = 0;
        foreach ($this->_getOrder()->getAllVisibleItems() as $item) {
            if ($i >= ($limit + 1)) break; // ie. need 2 over to show summary line (may as well show item if 1 over, instead of summary of 1)
            $data[] = $this->_trimZeros($item->getQtyOrdered()) - $this->_trimZeros($item->getQtyCancelled()) . ' x ' . $item->getSku();
            $i++;
        }
		if($count > $i) $data[] = '(+ ' . ($count - $i) . ' more)';

        return implode("\n", $data);
    }
}