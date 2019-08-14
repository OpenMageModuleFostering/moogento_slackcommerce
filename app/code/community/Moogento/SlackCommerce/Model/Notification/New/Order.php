<?php


class Moogento_SlackCommerce_Model_Notification_New_Order extends Moogento_SlackCommerce_Model_Notification_Abstract
{
    protected $_referenceModel = 'sales/order';

    protected function _prepareText()
    {
        return $this->helper()->__('New order #%s', $this->_getOrder()->getIncrementId());
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
	
    protected function _trimZeros($amount) {
        return preg_replace('~\.00$~','',$amount);
    }

    protected function _prepareOrderFields()
    {
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
            array(
                'title' => $cust_type,//$this->helper()->__('Customer'),
                'value' => $cust_name,//$this->_getOrder()->getCustomerFirstname() . ' ' . $this->_getOrder()->getCustomerLastname(),
                'short' => true,
            ),
            array(
                'title' => $this->helper()->__('Order Amount'),
                'value' => $this->_trimZeros(strip_tags($this->_getOrder()->formatPrice($this->_getOrder()->getGrandTotal()))),
                'short' => true,
            ),
            array(
                'title' => $this->helper()->__('Products'),
                'value' => $this->_getProductsData(),
                'short' => true,
            ),
        );
    }

    protected function _getProductsData()
    {
        $data = array();
        $limit = 3;
        $i = 0;
        foreach ($this->_getOrder()->getAllVisibleItems() as $item) {
            if ($i >= $limit) break;
            $data[] = $item->getQtyOrdered() - $item->getQtyCancelled() . ' x ' . $item->getSku();
            $i++;
        }

        return implode("\n", $data);
    }
}