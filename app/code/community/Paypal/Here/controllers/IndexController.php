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
class Paypal_Here_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_quote    = null;
    /**
     * Instantiate quote and checkout
     * @throws Mage_Core_Exception
     */
    private function _initCheckout() {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Checkin.'));
        }
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    private function _getCustomerSession(){
        return Mage::getSingleton('core/session');
    }

    private function _getCoreSession(){
        return Mage::getSingleton('core/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    private function _getQuote() {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    public function setsessionAction()
    {
        $sendOption=$this->getRequest()->getParam('sendoption');
        $ppheresession=$this->getRequest()->getParam('ppheresession');

        $firstName=(string)$this->getRequest()->getParam('name');
        $lastName=(string)$this->getRequest()->getParam('lname');

        $email=(string)$this->getRequest()->getParam('email');
        $phone=(string)$this->getRequest()->getParam('phone');

        $street=(string)$this->getRequest()->getParam('street');
        $city=(string)$this->getRequest()->getParam('city');
        $postcode=(string)$this->getRequest()->getParam('postcode');
        $country=(string)$this->getRequest()->getParam('country');

        //preset if empty
        if(!isset($country) || $country == '') {
            $country = Mage::helper('here')->getConfigValue('general/store_information/merchant_country');
        }
        if(!isset($postcode) || $postcode == '') {
            $postcode = 'Unknown';
        }
        if(!isset($city) || $city == '') {
            $city = 'Unknown';
        }
        if(!isset($street) || $street == '') {
            $street = 'Unknown';
        }
        if(!isset($firstName) || $firstName == '') {
            $firstName = Mage::helper('here')->getConfigValue('payment/here/name');
        }
        if(!isset($lastName) || $lastName == '') {
            $lastName = Mage::helper('here')->getConfigValue('payment/here/lname');
        }
        if(!isset($email) || $email == '') {
            $email = Mage::helper('here')->getConfigValue('payment/here/email');
        }
        if(!isset($phone) || $phone == '') {
            $phone = Mage::helper('here')->getConfigValue('payment/here/phone');
        }

        Mage::getSingleton('customer/session')->setPPCustFirstName($firstName);
        Mage::getSingleton('customer/session')->setPPCustLastName($lastName);
        Mage::getSingleton('customer/session')->setPPCustEmail($email);
        Mage::getSingleton('customer/session')->setPPCustStreet($street);
        Mage::getSingleton('customer/session')->setPPCustCity($city);
        Mage::getSingleton('customer/session')->setPPCustPostcode($postcode);
        Mage::getSingleton('customer/session')->setPPCustCountry($country);
        Mage::getSingleton('customer/session')->setPPCustPhone($phone);
        Mage::getSingleton('customer/session')->setPPSendOption($sendOption);
        Mage::getSingleton('core/session')->setPPSession($ppheresession);
        return $ppheresession;
    }

    public function placeOrder(){
        $checkout = Mage::getSingleton('checkout/session');
        $quote    = $checkout->getQuote();
        $quote->setIsMultiShipping(false);

        $firstName=Mage::getSingleton('customer/session')->getPPCustFirstName();
        $lastName=Mage::getSingleton('customer/session')->getPPCustLastName();
        $email=Mage::getSingleton('customer/session')->getPPCustEmail();
        $street= Mage::getSingleton('customer/session')->getPPCustStreet();
        $city=Mage::getSingleton('customer/session')->getPPCustCity();
        $postcode=Mage::getSingleton('customer/session')->getPPCustPostcode();
        $country=Mage::getSingleton('customer/session')->getPPCustCountry();
        $phone=Mage::getSingleton('customer/session')->getPPCustPhone();
        $quote->setCustomerEmail($email);

        $addressData = array(
                'firstname' => "$firstName",
                'lastname' => "$lastName",
                'street' => "$street",
                'city' => "$city",
                'postcode' => "$postcode",
                'telephone' => "$phone",
                'country_id' => "$country",
                'region_id' => 0
        );

        $billingAddress = $quote->getBillingAddress()->addData($addressData);
        $billingAddress->setShouldIgnoreValidation(true);
        $shippingAddress = $quote->getShippingAddress()->addData($addressData);
        $quote->getShippingAddress()->setShouldIgnoreValidation(true);
        $shippingAddress->setCollectShippingRates(true)->setShippingMethod('freeshipping_freeshipping')
        ->setPaymentMethod('here');

        $quote->getPayment()->importData(array('method' => 'here'));

        $quote->collectTotals()->save();

        $service = Mage::getModel('sales/service_quote', $quote);

        $service->submitAll();
        $order = $service->getOrder();

        switch ($order->getState()) {
            case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:
            case Mage_Sales_Model_Order::STATE_NEW:
                // TODO
                break;
                // regular placement, when everything is ok
            case Mage_Sales_Model_Order::STATE_PROCESSING:
            case Mage_Sales_Model_Order::STATE_COMPLETE:
            case Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW:
                $order->sendNewOrderEmail();
                break;
        }
        return $order;
    }

    public function returnAction()
    {
        $sessuniqId = Mage::getSingleton('core/session')->getPPSession();
        $uniqId = $this->getRequest()->getParam('key');
        $returnType = $this->getRequest()->getParam('Type');
        if(isset($sessuniqId) && isset($uniqId) && $sessuniqId == $uniqId) {
            $returnType = $this->getRequest()->getParam('Type');
            if(isset($returnType)) {

                if( "Unknown" == $returnType) {
                    $this->_redirect('checkout/cart');
                    return;
                }

                //success here
                //get Invoice ID returned
                $invoiceId = $this->getRequest()->getParam('InvoiceId');
                $this->_initCheckout();
                $order = $this->placeOrder();
                //Create invoice with pp invoice id and transaction id in comments
                Mage::helper('here')->createInvoice($order, $invoiceId, $uniqId );
                // prepare session to success or cancellation page
                $session = $this->_getCheckoutSession();
                $session->clearHelperData();

                // "last successful quote"
                $quoteId = $this->_getQuote()->getId();
                $session->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

                // an order may be created

                if ($order) {
                    $session->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId());
                }
                $this->_redirect('checkout/onepage/success');
                return;
            }
        } else {
            Mage::getSingleton('core/session')->setPPSession('');
        }
        $this->_redirect('checkout/cart');
        return;
    }

}
