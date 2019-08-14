<?php


class Moogento_SlackCommerce_Model_Queue extends Mage_Core_Model_Abstract
{
    const KEY_NEW_ORDER = 'new_order';
    const KEY_NEW_INVOICE = 'new_invoice';
    const KEY_NEW_SHIPMENT = 'new_shipment';
    const KEY_NEW_CREDIT = 'new_credit';
    const KEY_NEW_STATUS = 'new_status';
    const KEY_NEW_BACKEND_ACCOUNT = 'new_backend_account';
    const KEY_BACKEND_LOGIN = 'backend_login';
    const KEY_BACKEND_LOGIN_FAIL = 'backend_login_fail';

    const STATUS_QUEUED = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    protected function _construct()
    {
        $this->_init('moogento_slackcommerce/queue');
    }

    //  {Store} {Order ID} {Amount - base currency} {customer name} {first 3 ordered [qty x sku]}
    public function send()
    {
        $notification = $this->_getNotification();

        $data = $notification->prepareData();
        if($notification->getEventKey() == 'backend_login_fail'){
            if(Mage::getStoreConfig('moogento_slackcommerce/security/send_type_immediate') == "custom"){
                if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/custom_channel')){
                    $data['channel'] = Mage::getStoreConfig('moogento_slackcommerce/security/immediate_custom_channel');
                }        
            }
            if(Mage::getStoreConfig('moogento_slackcommerce/security/colorize_immediate')){
                foreach($data["attachments"] as $index => $value){
                    $data["attachments"][$index]['color'] = Mage::getStoreConfig('moogento_slackcommerce/security/color_immediate');
                }
            }
        }

        $helper = Mage::helper('moogento_slackcommerce/api');
        try {
            $result = $helper->send($data);
            if ($result === true) {
                $this->setStatus(self::STATUS_SUCCESS);
            } else {
                $this->setStatus(self::STATUS_ERROR);
                $this->setStatusMessage($result);
            }
        } catch (Exception $e) {
            $this->setStatus(self::STATUS_ERROR);
            $this->setStatusMessage($e->getMessage());
        }
        $this->save();
    }

    /**
     * @return Moogento_SlackCommerce_Model_Notification_Abstract
     */
    protected function _getNotification()
    {
        if (strpos($this->getEventKey(), self::KEY_NEW_STATUS) === 0) {
            $alias = 'moogento_slackcommerce/notification_' . self::KEY_NEW_STATUS;
        } else {
            $alias = 'moogento_slackcommerce/notification_' . $this->getEventKey();
        }

        $model = Mage::getModel($alias);
        $model->setData($this->getData());

        return $model;
    }


} 