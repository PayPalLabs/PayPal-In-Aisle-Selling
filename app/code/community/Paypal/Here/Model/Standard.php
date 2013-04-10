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
class Paypal_Here_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'here';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    public function getSession()
    {
        return Mage::getSingleton('here/session');
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

   private function _getPPHereUrl($number, $returnUrl)
    {
        $url = 'var pphereUrl = "paypalhere://takePayment?accepted="+encodeURIComponent(\'paypal,cash,card,invoice,check\')+"&returnUrl="+'.$returnUrl;
        $url .= 'pphereUrl+="&InvoiceId="+"'.$number.'"+"&as=b64&payerPhone="+encodeURIComponent(phone)+"&step=choosePayment";';
        return $url;
    }
    private function _getReturnUrl($id)
    {
        $url = 'encodeURIComponent(\''.Mage::getUrl().'here/index/return?key='.$id.'&Return={result}&Type={Type}&InvoiceId={InvoiceId}&Tip={Tip}&Email={Email}&TxId={TxId}\');';
        return $url;
    }

    public function getStandardCheckoutContents($ppheresession)
    {
        $uniqId = uniqid();

        $result = $this->_getPPHereUrl($uniqId, $this->_getReturnUrl($ppheresession));

        $result .= 'var buyerEmail = email;';
        $result .= 'var buyerPhone = phone;';
        $result .= 'var number = "'.$uniqId.'";';
        $result .= 'var itemList = new Array();';
        foreach($this->getQuote()->getAllVisibleItems() as $_item):
            $result .= 'var item = {};';
            $result .= 'item.taxRate = "0.0";';
            $result .= 'item.name = "'.$_item->getName().'";';
            $result .= 'item.description = "'.$_item->getName().'";';
            $result .= 'item.unitPrice = "'. $_item->getPrice().'";';
            $result .= 'item.taxName = "Tax";';
            $result .= 'item.quantity = "'.$_item->getQty().'";';
            $result .= 'itemList.push(item);';
        endforeach;
        if (Mage::getStoreConfigFlag('carriers/freeshipping/active')) {
            //add free shipping for now
            $result .= 'var item = {};';
            $result .= 'item.taxRate = "0.0";';
            $result .= 'item.name = "Shipping";';
            $result .= 'item.description = "Shipping";';
            $result .= 'item.unitPrice = "0.0";';
            $result .= 'item.taxName = "Tax";';
            $result .= 'item.quantity = "1";';
            $result .= 'itemList.push(item);';
        }
        $result .= 'invoice=new Object();';
        $result .= 'invoice.paymentTerms = "DueOnReceipt";';
        $result .= 'invoice.currencyCode = "USD";';
        $result .= 'invoice.number = number;';
        $result .= 'invoice.merchantEmail = "merchant@email.com";';
        $result .= 'invoice.payerEmail = buyerEmail;';
        $result .= 'invoice.itemList = new Object();';
        $result .= 'invoice.itemList.item = itemList;';
        $result .= 'pphereUrl = pphereUrl + "&invoice=" + Base64.encode($.toJSON(invoice));';

        $result .= 'window.location.href=pphereUrl;';
        return $result;
    }

}
