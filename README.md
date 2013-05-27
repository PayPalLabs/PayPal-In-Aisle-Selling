#PayPal In Aisle selling with Magento
#Getting Started Guide, version 1.0
##Table of Contents

1. Overview
2. System Requirement
3. Prerequisites
4. Configuring PayPal Here module
5. Troubleshooting
 
##1. Overview

This setup guide is to enable PayPal Order Ahead to be used with Magento. PayPal Order Ahead will let consumers check in to stores and then directly pay for goods and services with their PayPal accounts. Merchant is able to set locations, create invoices, and take PayPal payments on the mobile devices.

##2. System Requirement

Your system must meet the following requirements:

* Magento Community 1.7 or Magento Enterprise 1.12 (If you are not sure what version you are running, log in to the Magento Admin Panel. The version displays at the bottom of the page).
* The latest version of this plugin on [magento connect](http://www.magentocommerce.com/magento-connect/catalog/product/view/id/17221/s/paypal-in-aisle-selling-6567/) or simply clone this repository.


##3. Prerequisites

Before you register your application and enable the PayPal Check-in into the Magento, please make sure all of the following are true:

* You have a PayPal account (Premier or Merchant Account), which is needed to register your application with PayPal.
* The web server on which Magento runs uses Secure Sockets Layer (SSL), which is also referred to as Secure HTTP or HTTPS to retrieve all the user attributes that Log In with PayPal supports.
* Magento Admin should be on full valid HTTPS URL throughout.

##4. Configuring PayPal Here module

1. Go to Magento, system > Configuration, general tab and make sure your store informations are correct
![ScreenShot](readmeimages/InAisleSelling_conf_01.png)

2. Activate free shipping
![ScreenShot](readmeimages/InAisleSelling_config_02.png)

3. Activate PayPal Here
![ScreenShot](readmeimages/InAisleSelling_conf_03.png)

4. You should see a PayPal here button in the shopping cart
![ScreenShot](readmeimages/InAisleSelling_conf_04.png)

##5. Troubleshooting

No issue yet