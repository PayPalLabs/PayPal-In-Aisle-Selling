<?php
/**
 * Paypalhere
 *
 * @package      :  Paypalhere
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class Paypal_Here_Block_Link extends Mage_Core_Block_Template
{
    public function getPPHereButton()
    {
        $html = '';
        if (Mage::helper('here')->getConfigValue('payment/here/active')) {
          $html .= '<img id="paypalhere_checkout" src="'.$this->getSkinUrl('images/paypal/paypalhere/paypalhere.png').'" alt="'.Mage::helper('here')->__('PayPalHere').'" class="v-middle" />';
        }
        return $html;
    }
}
