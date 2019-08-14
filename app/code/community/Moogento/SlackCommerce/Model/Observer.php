<?php


class Moogento_SlackCommerce_Model_Observer
{
    protected $_newInvoices = array();

    public function sales_order_save_after($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getOrigData('status')
            && $order->getOrigData('status') != $order->getData('status')
            && Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_NEW_STATUS . '_' . $order->getData('status'))
            ) {

            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_NEW_STATUS . '_' . $order->getData('status'),
                'reference_id' => $order->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function sales_order_place_after($observer)
    {
        if (Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_NEW_ORDER)) {
            $order = $observer->getEvent()->getOrder();

            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_NEW_ORDER,
                'reference_id' => $order->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function sales_order_invoice_save_before($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->isObjectNew()) {
            $invoice->setData('process_notifications', true);
        }
    }

    public function sales_order_invoice_save_after($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getData('process_notifications') && Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_NEW_INVOICE)) {
            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_NEW_INVOICE,
                'reference_id' => $invoice->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function sales_order_shipment_save_before($observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        if ($shipment->isObjectNew()) {
            $shipment->setData('process_notifications', true);
        }
    }

    public function sales_order_shipment_save_after($observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        if ($shipment->getData('process_notifications') && Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_NEW_SHIPMENT)) {
            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_NEW_SHIPMENT,
                'reference_id' => $shipment->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function sales_order_creditmemo_save_before($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo->isObjectNew()) {
            $creditmemo->setData('process_notifications', true);
        }
    }

    public function sales_order_creditmemo_save_after($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo->getData('process_notifications') && Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_NEW_CREDIT)) {
            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_NEW_CREDIT,
                'reference_id' => $creditmemo->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function admin_user_save_before($observer)
    {
        $user = $observer->getEvent()->getDataObject();
        if ($user->isObjectNew()) {
            $user->setData('process_notifications', true);
        }
    }

    public function admin_user_save_after($observer)
    {
        $user = $observer->getEvent()->getDataObject();

        if ($user->getData('process_notifications') && Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_NEW_BACKEND_ACCOUNT)) {
            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_NEW_BACKEND_ACCOUNT,
                'reference_id' => $user->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function admin_session_user_login_success($observer)
    {
        $user = $observer->getEvent()->getUser();
        if (Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_BACKEND_LOGIN)) {
            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_BACKEND_LOGIN,
                'reference_id' => $user->getId(),
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function admin_session_user_login_failed($observer)
    {
        $userName = $observer->getEvent()->getUserName();
        $ip = $_SERVER["REMOTE_ADDR"];
        $long = ip2long($ip);
        if ($long == -1 || $long === FALSE) {
            $even_attempts_number = 0;
        } else{
            $ip_insys = Mage::getModel("moogento_slackcommerce/ipfail")->load($long, 'ip');
            if(!$ip_insys->getId()){
                $ip_insys->setIp($long);
                $even_attempts_number = 1;
            } else {
                $even_attempts_number = $ip_insys->getEvenAttemptsNumber();
                $even_attempts_number++;
            }
            $ip_insys->setEvenAttemptsNumber($even_attempts_number);
            $ip_insys->save();
        }
        
        $message = $observer->getEvent()->getException()->getMessage();
        if (Mage::helper('moogento_slackcommerce')->shouldSend(Moogento_SlackCommerce_Model_Queue::KEY_BACKEND_LOGIN_FAIL)) {
            $queue = Mage::getModel('moogento_slackcommerce/queue');
            $queue->setData(array(
                'event_key' => Moogento_SlackCommerce_Model_Queue::KEY_BACKEND_LOGIN_FAIL,
                'reference_id' => 0,
                'date' => date("Y-m-d H:i:s", Mage::getModel('core/date')->gmtTimestamp()),
                'additional_data' => array(
                    'username' => $userName,
                    'message' => $message,
                    'IP' => $ip,
                    'URL' => 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                    'Country' => Mage::helper('moogento_slackcommerce')->ipInfo("Country"),
                    'Even_number_of_failed_open' => $even_attempts_number-1,
                ),
            ));
            try {
                $queue->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
} 