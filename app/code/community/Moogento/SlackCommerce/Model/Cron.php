<?php


class Moogento_SlackCommerce_Model_Cron
{
    protected $_statFields = array(
        'qty_orders' => '# Orders',
        'total_revenue' => '$ Revenue',
        'qty_products' => '# Products',
        'avg_products_order' => '# Prod\'s /Order',
        'avg_revenue_order' => '$ Rev /Order',
    );






    public function send()
    {
        if (!Mage::getStoreConfig('moogento_slackcommerce/general/webhook_url')) {
            return;
        }
        $limit = 50;
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table   = Mage::getSingleton('core/resource')->getTableName('moogento_slackcommerce/queue');

        $query = "UPDATE {$table} SET cron_id = NULL WHERE status = 0 AND cron_id IS NOT NULL AND DATEDIFF(`date`, NOW()) > 0";

        $cronId = md5(time());
        $query = "UPDATE {$table} SET cron_id = '{$cronId}' WHERE status = 0 AND cron_id IS NULL LIMIT $limit";

        $write->query($query);

        $collection = Mage::getResourceModel('moogento_slackcommerce/queue_collection');
        $collection->addFieldToFilter('cron_id', $cronId);

        foreach ($collection as $notification) {
            $notification->send();
        }
    }

    public function sendStats()
    {
        if (!Mage::getStoreConfig('moogento_slackcommerce/general/webhook_url')) {
            return;
        }
        if (Mage::getStoreConfig('moogento_slackcommerce/stats/send_type')) {
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            if (date('G', $timestamp) != Mage::getStoreConfig('moogento_slackcommerce/stats/hour')) {
                return;
            }

            if (Mage::getStoreConfigFlag('moogento_slackcommerce/stats/daily_stats')) {
                $this->_sendDailyStats();
            }

            if (Mage::getStoreConfigFlag('moogento_slackcommerce/stats/weekly_stats')) {
               if (date('w', $timestamp) == Mage::getStoreConfig('moogento_slackcommerce/stats/day')) {
                    $this->_sendWeeklyStats();
               }
            }
        }
    }

    protected function _getGeneralData()
    {
        $data = array(
            'channel' => null,
            'attachments' => array(),
        );

        if (Mage::getStoreConfig('moogento_slackcommerce/stats/send_type') == 'custom') {
            $data['channel'] = Mage::getStoreConfig('moogento_slackcommerce/stats/custom_channel');
        }

        $store = Mage::app()->getDefaultStoreView();
        $appEmulation = Mage::getSingleton('core/app_emulation');

        //Start environment emulation of the specified store
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store->getId());

        $data['username'] = $store->getFrontendName();
        $data['icon_url'] = Mage::getBaseUrl('media') . 'moogento/slack/' . Mage::getStoreConfig('moogento_slackcommerce/general/icon');

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $data;
    }
	
    protected function _getMagentoDate()
    {
        return Mage::getModel('core/date')->date('l jS M');
    }
	
    protected function _trimZeros($amount) {
        return preg_replace('~\.00$~','',$amount);
    }

    protected function _sendDailyStats()
    {
        $data = $this->_getGeneralData();
		
        $data['text'] = Mage::helper('moogento_slackcommerce')->__('Daily Metrics') . ' for ' . $this->_getMagentoDate();
        $data['attachments'] = array($this->_prepareAttachments('24hours'));

        $helper = Mage::helper('moogento_slackcommerce/api');
        try {
            $helper->send($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    protected function _sendWeeklyStats()
    {
        $data = $this->_getGeneralData();
        $data['text'] = Mage::helper('moogento_slackcommerce')->__('Weekly Metrics') . ' for week ending ' . $this->_getMagentoDate();
        $data['attachments'] = array($this->_prepareAttachments('7days'));

        $helper = Mage::helper('moogento_slackcommerce/api');
        try {
            $helper->send($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    protected function _prepareAttachments($period)
    {
        $data = array();
        if (Mage::getStoreConfigFlag('moogento_slackcommerce/stats/colorize')) {
            $data['color'] = Mage::getStoreConfig('moogento_slackcommerce/stats/color');
        }

        $fields = array();
//        echo '<pre>';
        foreach ($this->_statFields as $field => $label) {
            if (Mage::getStoreConfigFlag('moogento_slackcommerce/stats/' . $field)) {
                $fields[] = array(
                    'title' => Mage::helper('moogento_slackcommerce')->__($label),
                    'value' => $this->_getFieldValue($field, $period),
                    'short' => true,
                );
            }
        }
//        echo '</pre>';
        
        $data['fields'] = $fields;
//        var_dump($fields); 
//        die();

        return $data;
    }

    protected function _getFieldValue($field, $period)
    {
        $fieldExpression = null;
        $formatPrice = false;
        $joinItems = false;
        switch ($field) {
            case 'qty_orders':
                $fieldExpression = 'count(DISTINCT entity_id)';
                break;
            case 'total_revenue':
                $formatPrice = true;
                $fieldExpression =
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)) * %s)',
                        $this->getIfNullSql('o.base_total_invoiced', 0),
                        $this->getIfNullSql('o.base_tax_invoiced', 0),
                        $this->getIfNullSql('o.base_shipping_invoiced', 0),
                        $this->getIfNullSql('o.base_total_refunded', 0),
                        $this->getIfNullSql('o.base_tax_refunded', 0),
                        $this->getIfNullSql('o.base_shipping_refunded', 0),
                        $this->getIfNullSql('o.base_to_global_rate', 0)
                    );
                break;
            case 'qty_products':
                $joinItems = true;
                $fieldExpression = 'SUM(oi.total_qty_ordered)';
                break;
            case 'avg_products_order':
                $joinItems = true;
                $fieldExpression = 'SUM(oi.total_qty_ordered)/COUNT(DISTINCT o.entity_id)';
                break;
            case 'avg_revenue_order':
                $formatPrice = true;
                $fieldExpression = sprintf('SUM((%s - %s - %s - (%s - %s - %s)) * %s) / count(DISTINCT entity_id)',
                    $this->getIfNullSql('o.base_total_invoiced', 0),
                    $this->getIfNullSql('o.base_tax_invoiced', 0),
                    $this->getIfNullSql('o.base_shipping_invoiced', 0),
                    $this->getIfNullSql('o.base_total_refunded', 0),
                    $this->getIfNullSql('o.base_tax_refunded', 0),
                    $this->getIfNullSql('o.base_shipping_refunded', 0),
                    $this->getIfNullSql('o.base_to_global_rate', 0)
                );
                break;
        }

        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $orderItemTable = Mage::getSingleton('core/resource')->getTableName('sales/order_item');

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)));
        $date->modify('-' . $period);

        /** @var Varien_Db_Select $select */
        $select = $adapter->select();
        $select->from(array('o' => $orderTable), array(
            'value' => new Zend_Db_Expr($fieldExpression),
        ));
        if ($joinItems) {
            $qtyCanceledExpr = $this->getIfNullSql('qty_canceled', 0);
            $cols            = array(
                'order_id'           => 'order_id',
                'total_qty_ordered'  => new Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
                'total_product_price'  => new Zend_Db_Expr("SUM(base_price)"),
                'items_count'  => new Zend_Db_Expr("COUNT(1)"),
            );
            $selectOrderItem = $adapter->select();
            $selectOrderItem->from($orderItemTable, $cols)
                            ->where('parent_item_id IS NULL')
                            ->group('order_id');

            $select->joinLeft(array('oi' => $selectOrderItem), 'o.entity_id = oi.order_id');
        }

        $select->where('o.created_at >= ?', $date->format('Y-m-d H:00:00'));
//        echo $select; 
        $value = $adapter->fetchOne($select);
        if ($formatPrice) {
//            $value = $this->_trimZeros(Mage::app()->getDefaultStoreView()->formatPrice((float)$value, false));
            $value = $this->_trimZeros(Mage::helper('core')->currency((float)$value, true, false));
            
        } else {
            $value = round($value);
        }
//        var_dump($value);

        return $value;
    }

    public function getIfNullSql($expression, $value = 0)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IFNULL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("IFNULL(%s, %s)", $expression, $value);
        }

        return new Zend_Db_Expr($expression);
    }
} 