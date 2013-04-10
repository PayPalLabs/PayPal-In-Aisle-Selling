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
class Paypal_Here_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getConfigValue($configValue, $websiteId = null){
        if(is_null($websiteId)){
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }

        $string = "";
        if($websiteId !=0){
            $string = Mage::app()->getWebsite($websiteId)->getConfig($configValue);
        }
        return $string;
    }

    public function getStoreId($storeCode){
        if($storeCode === 0){
            return 0;
        }
        $stores = array_keys(Mage::app()->getStores());
        foreach($stores as $id){
            $store = Mage::app()->getStore($id);
            if($store->getCode()==$storeCode) {
                return $store->getId();
            }
        }
        return false;
    }

    public function getWebsiteId($websiteCode){
        if($websiteCode === 0){
            return 0;
        }
        $websites = array_keys(Mage::app()->getWebsites());
        foreach($websites as $id){
            $website = Mage::app()->getWebsite($id);
            if($website->getCode()==$websiteCode) {
                return $website->getId();
            }
        }
        return false;
    }
    public function createInvoice($order, $ppInvoiceId="NA", $ppTransactionId="NA"){
        $isInvoiceCreated = false;
        try{
            if(!$order->canInvoice()){
                return $isInvoiceCreated;
            }
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
            if (!$invoice->getTotalQty()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $comment = "Paypal Invoice id: ".$ppInvoiceId;
            $comment .= "<br/> Paypal Order ID: ".$ppTransactionId;
            $order->addStatusHistoryComment($comment)
            ->setIsVisibleOnFront(true)
            ->setIsCustomerNotified(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
            $transactionSave->save();
            $isInvoiceCreated = true;
        }catch(Exception $e){
            Mage::log("Invoice created failed !!");
            Mage::logException($e);
            throw $e;
        }
        Mage::log("Invoice created : ".$isInvoiceCreated);
        return $isInvoiceCreated;
    }

}
