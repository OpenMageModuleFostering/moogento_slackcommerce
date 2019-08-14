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
    
    public function sendSecurityNotifications()
    {
        if (!Mage::getStoreConfig('moogento_slackcommerce/general/webhook_url')) {
            return;
        }
        if (Mage::getStoreConfig('moogento_slackcommerce/security/send_type')) {
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            if ( (date('G', $timestamp) != Mage::getStoreConfig('moogento_slackcommerce/security/hour')) ) {
                return;
            }
            $this->_sendDailyFailStatistic();
        }
    }
    
    public function test()
    {
        $this->_sendDailyFailStatistic();
    }

    public function sendMotivationMessage()
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter = $resource->getConnection('core_read');
        $orderItemTable = $resource->getTableName('sales/order_item');
        $config = new Mage_Core_Model_Config();

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)));
        $date->modify('-7days');
        $date_start = new DateTime();
        $date_start->setTimestamp($date->getTimestamp());
        $date_start->modify('-7days');

        /** @var Varien_Db_Select $select */
        $select = $adapter->select();
        $qtyCanceledExpr = $this->getIfNullSql('qty_canceled', 0);
        $qtyRefundedExpr = $this->getIfNullSql('qty_refunded', 0);
        $select_data = new Zend_Db_Expr("sum((qty_ordered - {$qtyCanceledExpr} - {$qtyRefundedExpr})*price)");
        $select->from(array('o' => $resource->getTableName('sales/order_item')), array('value' => $select_data));
        $select->where('o.created_at >= ?', $date->format('Y-m-d H:00:00'));
        $during_week_value = (double) $adapter->fetchOne($select);

        $select = $adapter->select();
        $select->from(array('o' => $resource->getTableName('sales/order_item')), array('value' => $select_data));
        $select->where('o.created_at >= ?', $date_start->format('Y-m-d H:00:00'));
        $select->where('o.created_at < ?', $date->format('Y-m-d H:00:00'));

        $pre_week_value = (double) $adapter->fetchOne($select);

        $difference = $during_week_value - $pre_week_value;
        $file_path = Mage::getModuleDir('etc','Moogento_SlackCommerce').DIRECTORY_SEPARATOR;

        if(!($weeks_of_change = Mage::getStoreConfig('moogento_slackcommerce/security/weeks_of_change'))){
            $weeks_of_change = 0;
        }
        if ($difference > 0) {
            $file_path .= 'positive.txt';
            if($weeks_of_change > 0) {
                $config->saveConfig('moogento_slackcommerce/security/weeks_of_change', ++$weeks_of_change, 'default', 0);
            } elseif ($weeks_of_change <= 0){
                $config->saveConfig('moogento_slackcommerce/security/weeks_of_change', '1', 'default', 0);
            }
        } elseif ($difference < 0) {
            $file_path .= 'negative.txt';
            if($weeks_of_change < 0) {
                $config->saveConfig('moogento_slackcommerce/security/weeks_of_change', --$weeks_of_change, 'default', 0);
            } elseif ($weeks_of_change >= 0){
                $config->saveConfig('moogento_slackcommerce/security/weeks_of_change', '-1', 'default', 0);
            }
        } else {
            $file_path .= 'neutral.txt';
            $config->saveConfig('moogento_slackcommerce/security/weeks_of_change', '0', 'default', 0);
        }
        if(is_file($file_path)){
            $file = file($file_path);
            $result_line = $file[rand(0, count($file)-1)];
            
            $weeks_of_change = Mage::getStoreConfig('moogento_slackcommerce/security/weeks_of_change');
            if($weeks_of_change < 0) $weeks_of_change = -1*$weeks_of_change;
            if($difference < 0) $difference = -1*$difference;
            $result_line = str_replace("$[x]", Mage::app()->getStore()->getCurrentCurrency()->formatPrecision((float)$difference, 0, array(), false), $result_line);
            $result_line = str_replace("[x]", $weeks_of_change, $result_line);

            $data = $this->_getGeneralData();
            $data['text'] = $result_line."\n"."<https://moogento.com|Powered by Moogento>";

            try {
                Mage::helper('moogento_slackcommerce/api')->send($data);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    protected function _sendDailyFailStatistic()
    {
        $data = $this->_getGeneralData();
        
        $send_type = Mage::getStoreConfig('moogento_slackcommerce/security/send_type');
        if($send_type == "default"){
            if(Mage::getStoreConfigFlag('moogento_slackcommerce/general/default_channel')){
                $data['channel'] = Mage::getStoreConfig('moogento_slackcommerce/general/default_channel');
            }        
        } elseif ($send_type == "custom"){
            if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/custom_channel')){
                $data['channel'] = Mage::getStoreConfig('moogento_slackcommerce/security/custom_channel');
            }        
        }
        $data['text'] = Mage::helper('moogento_slackcommerce')->__('Daily Security Summary') . " " . $this->_getMagentoDate();
        $data['attachments'] = array();
        $color = Mage::getStoreConfigFlag('moogento_slackcommerce/security/colorize');
        
        /* using tables */
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');
        $ipFailTable = $resource->getTableName('moogento_slackcommerce/ipfail');
        $targetFailTable = $resource->getTableName('moogento_slackcommerce/targetfail');

        $value = $this->_prepareTotalNumberOfFails();
        
        if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/total_number_fails')){
            $data['attachments']['total_number_fails']['fields'] = array();
            $data['attachments']['total_number_fails']['fields'][] = array(
                'title' => Mage::helper('moogento_slackcommerce')->__("# Fails (all IPs)"),
                'value' => $value,
                'short' => true,
            );
            if ($color) {
                $data['attachments']['total_number_fails']['color'] = Mage::getStoreConfig('moogento_slackcommerce/security/color');
            }
        }
        /********************/
        if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/not_sent_if_no_fails') && $value == 0){
            return false;
        }
        /** count of fails-per-IP */
        if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/count_ip_fails')){
            $data['attachments']['count_ip_fails'] = $this->_prepareCountOfFailsPerIP($color);
        }
        /********************/
        /** Count of fails-per-target */
        if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/count_target_fails')){
            $data['attachments']['count_of_fails-per-target'] = $this->_prepareCountOfFailsPerTarget($color);
        }
        /********************/
        /** Have a line */
        if(Mage::getStoreConfigFlag('moogento_slackcommerce/security/have_line_fails') && Mage::getStoreConfigFlag('moogento_slackcommerce/security/line_fails')){
            $data['attachments']['line_fails']['title'] = Mage::getStoreConfig('moogento_slackcommerce/security/line_fails');
        }
        /********************/


        $query = "UPDATE {$ipFailTable} SET count_of_fails_per_day = '0'";
        $writeConnection->query($query);
        $query = "UPDATE {$targetFailTable} SET count_of_fails_per_day = '0'";
        $writeConnection->query($query);
        /********************/

        $helper = Mage::helper('moogento_slackcommerce/api');
        try {
            $helper->send($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    protected function _prepareTotalNumberOfFails()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $ipFailTable = $resource->getTableName('moogento_slackcommerce/ipfail');

        $select = $readConnection->select();
        $select->from(array('o' => $ipFailTable), array(
            'value' => new Zend_Db_Expr('sum(count_of_fails_per_day)'),
        ));
        
        return $readConnection->fetchOne($select);
    }

    protected function _prepareCountOfFailsPerIP($color)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $ipFailTable = $resource->getTableName('moogento_slackcommerce/ipfail');

        $data = array();
        $select = $readConnection->select();
        $select->from(array('o' => $ipFailTable), array(
            'value' => new Zend_Db_Expr('count_of_fails_per_day'),
            'ip' => new Zend_Db_Expr('ip')
        ));
        $select->where('o.count_of_fails_per_day != ?', 0);
        $value = $readConnection->fetchAll($select);
        $data['title'] = Mage::helper('moogento_slackcommerce')->__("# Fails (by IP)");
        $data['fields'] = array();
        foreach ($value as $val){
            $data['fields'][] = array(
                'title' => long2ip($val['ip']),
                'value' => $val['value'],
                'short' => true,
            );
        }
        if ($color) {
            $data['color'] = Mage::getStoreConfig('moogento_slackcommerce/security/color');
        }
        return $data;
    }

    protected function _prepareCountOfFailsPerTarget($color)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $targetFailTable = $resource->getTableName('moogento_slackcommerce/targetfail');

        $data = array();
        $select = $readConnection->select();
        $select->from(array('o' => $targetFailTable), array(
            'value' => new Zend_Db_Expr('count_of_fails_per_day'),
            'target' => new Zend_Db_Expr('target')
        ));
        $select->where('o.count_of_fails_per_day != ?', 0);
        $value = $readConnection->fetchAll($select);
        $data['title'] = Mage::helper('moogento_slackcommerce')->__("# Fails (by target)");
        $data['fields'] = array();
        foreach ($value as $val){
            $data['fields'][] = array(
                'title' => $val['target'],
                'value' => $val['value'],
                'short' => true,
            );
        }
        if ($color) {
            $data['color'] = Mage::getStoreConfig('moogento_slackcommerce/security/color');
        }
        return $data;
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
        $icon = Mage::getStoreConfig('moogento_slackcommerce/general/icon');
        $icon_url = Mage::getBaseUrl('media') . 'moogento/slack/' . $icon;
        if($icon && file_exists($icon)){
            $data['icon_url'] = $icon;
        } else {
            $data['icon_url'] = Mage::getBaseUrl('media') . 'moogento/slack/moogento_logo_small.png';
        }
        
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

		$base_currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol();

        $fields = array();
//        echo '<pre>';
        foreach ($this->_statFields as $field => $label) {
			// change the title currency code to the base currency code
			$label = str_replace('$ ', $base_currency_symbol . ' ', $label);
            if (Mage::getStoreConfigFlag('moogento_slackcommerce/stats/' . $field)) {
                $fields[] = array(
                    'title' => Mage::helper('moogento_slackcommerce')->__($label),
                    'value' => $this->_getFieldValue($field, $period),
                    'short' => true,
                );
            }
        }
        
        $data['fields'] = $fields;

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
//        $date->setTimezone(new DateTimeZone(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)));
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
        
        $value = $adapter->fetchOne($select);
        if ($formatPrice) {
            $value = Mage::app()->getStore()->getCurrentCurrency()->formatPrecision((float)$value, 0, array(), false);
            
        } else {
            $value = round($value);
        }

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