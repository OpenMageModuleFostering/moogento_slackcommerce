<?php

class Moogento_SlackCommerce_Helper_Moo extends Mage_Core_Helper_Abstract
{
    protected $_module = 'Moogento_SlackCommerce';

    public function x()
    {
        //email|server IP|php-sourced domain|magento-sourced domain|extension name|version|key|magento platform|magento version
        $d = array(
            '',
            '',
            '',
            '',
            'slackcommerce',
            $this->v(),
            '',
            '',
            '',
        );
        return implode('||', $d);
    }

    public function d()
    {
        return str_replace('www.', '', parse_url(Mage::getStoreConfig('web/unsecure/base_url'), PHP_URL_HOST));
    }

    public function pd()
    {
        return str_replace('www.', '', $_SERVER['HTTP_HOST']);
    }

    //info
    public function i()
    {
        return base64_encode(base64_encode($this->x()));
    }
    //logo
    public function l()
    {
        return base64_encode(base64_encode(base64_encode(base64_encode($this->x()))));
    }
    //mark
    public function m()
    {
        return base64_encode($this->x());
    }
    //feed
    public function f()
    {
        return base64_encode(base64_encode(base64_encode($this->x())));
    }

    public function v()
    {
        $lf = Mage::getModuleDir('etc', $this->_module) . DS . 'version.txt';
        if (file_exists($lf)) {
            return file_get_contents($lf);
        } else {
            return Mage::getConfig()->getModuleConfig($this->_module)->version;
        }
    }

    public function p()
    {
        if (function_exists('Mage::getEdition')) {
            $e = Mage::getEdition();
            $translate = array(
                'Community' => 'CE',
                'Enterprise' => 'EE',
                'Professional' => 'PE',
                'Go' => 'GO',
            );
            return $translate[$e];
        } else {
            if ($this->isMageEnterprise()) {
                return 'EE';
            } else if ($this->isMageProfessional()) {
                return 'PE';
            }
        }
        return 'CE';
    }

    public function isMageEnterprise() {
        return Mage::getConfig ()->getModuleConfig ( 'Enterprise_Enterprise' ) && Mage::getConfig ()->getModuleConfig ( 'Enterprise_AdminGws' ) && Mage::getConfig ()->getModuleConfig ( 'Enterprise_Checkout' ) && Mage::getConfig ()->getModuleConfig ( 'Enterprise_Customer' );
    }


    /**
     * True if the version of Magento currently being rune is Enterprise Edition
     */
    public function isMageProfessional() {
        return Mage::getConfig ()->getModuleConfig ( 'Enterprise_Enterprise' ) && !Mage::getConfig ()->getModuleConfig ( 'Enterprise_AdminGws' ) && !Mage::getConfig ()->getModuleConfig ( 'Enterprise_Checkout' ) && !Mage::getConfig ()->getModuleConfig ( 'Enterprise_Customer' );
    }

    public function e($h)
    {
		return false;
    }
}