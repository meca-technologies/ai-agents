<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Currency;
use App\Models\CustomSettings;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
use App\Models\PaymentPlans;
use App\Models\Setting;
use App\Models\Subscriptions as SubscriptionsModel;
use App\Models\SubscriptionItems;
use App\Models\HowitWorks;
use App\Models\User;
use App\Models\UserAffiliate;
use App\Models\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

// a class that makes subscription and payments with iyzipay
class iyzipayActions
{

    // api keys
    private $config;
    // locale
    private $locale;
    // currency
    private $currency;

    public function __construct($apiKey, $apiSecretKey, $baseUrl, $locale = \Iyzipay\Model\Locale::TR, $currency = \Iyzipay\Model\Currency::TL)
    {
        $this->config = new \Iyzipay\Options();
        $this->config->setApiKey($apiKey);
        $this->config->setSecretKey($apiSecretKey);
        $this->config->setBaseUrl($baseUrl);
        $this->locale = $locale;
        $this->currency = $currency;
    }


    // generate random string with given length
    public function generateRandomString($length = 12)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    // generate random number with given length
    public function generateRandomNumber($length = 12)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
    }



    //************************************************
    
    //************ CUSTOMER ACTIONS ******************

    //************************************************


    // create a customer
    public function createCustomer($request)
    {
        $customer = new \Iyzipay\Model\Customer();
        $customer->setName($request->name);
        $customer->setSurname($request->surname);
        $customer->setGsmNumber($request->phone);
        $customer->setEmail($request->email);
        $customer->setIdentityNumber($request->identityNumber);
        $customer->setRegistrationAddress($request->address);
        $customer->setIp($request->ip);
        $customer->setCity($request->city);
        $customer->setCountry($request->country);
        $customer->setZipCode($request->zipCode);

        $requestCustomer = new \Iyzipay\Request\CreateCustomerRequest();
        $requestCustomer->setLocale($this->locale);
        $requestCustomer->setConversationId(generateRandomNumber());
        $requestCustomer->setCustomer($customer);

        $customer = \Iyzipay\Model\Customer::create($requestCustomer, $this->config);

        return $customer;
    }

    // list all customers
    public function listCustomers($request)
    {
        $requestCustomer = new \Iyzipay\Request\RetrieveCustomerListRequest();
        $requestCustomer->setLocale($this->locale);
        $requestCustomer->setConversationId(generateRandomNumber());
        $requestCustomer->setPage(1);
        $requestCustomer->setCount(1);

        $customerList = \Iyzipay\Model\Customer::retrieveAll($requestCustomer, $this->config);

        return $customerList;
    }

    // retrieve a customer
    public function retrieveCustomer($request)
    {
        $requestCustomer = new \Iyzipay\Request\RetrieveCustomerRequest();
        $requestCustomer->setLocale($this->locale);
        $requestCustomer->setConversationId(generateRandomNumber());
        $requestCustomer->setCustomerReferenceCode($request->customerReferenceCode);

        $customer = \Iyzipay\Model\Customer::retrieve($requestCustomer, $this->config);

        return $customer;
    }

    // update a customer
    public function updateCustomer($request)
    {
        $customer = new \Iyzipay\Model\Customer();
        $customer->setName($request->name);
        $customer->setSurname($request->surname);
        $customer->setGsmNumber($request->phone);
        $customer->setEmail($request->email);
        $customer->setIdentityNumber($request->identityNumber);
        $customer->setRegistrationAddress($request->address);
        $customer->setIp($request->ip);
        $customer->setCity($request->city);
        $customer->setCountry($request->country);
        $customer->setZipCode($request->zipCode);

        $requestCustomer = new \Iyzipay\Request\UpdateCustomerRequest();
        $requestCustomer->setLocale($this->locale);
        $requestCustomer->setConversationId(generateRandomNumber());
        $requestCustomer->setCustomerReferenceCode($request->customerReferenceCode);
        $requestCustomer->setCustomer($customer);

        $customer = \Iyzipay\Model\Customer::update($requestCustomer, $this->config);

        return $customer;
    }

    // delete a customer
    public function deleteCustomer($request)
    {
        $requestCustomer = new \Iyzipay\Request\DeleteCustomerRequest();
        $requestCustomer->setLocale($this->locale);
        $requestCustomer->setConversationId(generateRandomNumber());
        $requestCustomer->setCustomerReferenceCode($request->customerReferenceCode);

        $customer = \Iyzipay\Model\Customer::delete($requestCustomer, $this->config);

        return $customer;
    }

    // check if customer exists
    public function checkCustomer($request)
    {
        $requestCustomer = new \Iyzipay\Request\RetrieveCustomerRequest();
        $requestCustomer->setLocale($this->locale);
        $requestCustomer->setConversationId(generateRandomNumber());
        $requestCustomer->setCustomerReferenceCode($request->customerReferenceCode);

        $customer = \Iyzipay\Model\Customer::retrieve($requestCustomer, $this->config);

        return $customer;
    }


    //************************************************
    
    //************ ADDRESS ACTIONS ******************

    //************************************************


    // create an address
    public function createAddress($request)
    {
        $requestAddress = new \Iyzipay\Request\CreateAddressRequest();
        $requestAddress->setLocale($this->locale);
        $requestAddress->setConversationId(generateRandomNumber());
        $requestAddress->setContactName($request->contactName);
        $requestAddress->setCity($request->city);
        $requestAddress->setCountry($request->country);
        $requestAddress->setAddress($request->address);
        $requestAddress->setZipCode($request->zipCode);

        $address = \Iyzipay\Model\Address::create($requestAddress, $this->config);

        return $address;
    }

    // retrieve an address
    public function retrieveAddress($request)
    {
        $requestAddress = new \Iyzipay\Request\RetrieveAddressRequest();
        $requestAddress->setLocale($this->locale);
        $requestAddress->setConversationId(generateRandomNumber());
        $requestAddress->setAddressReferenceCode($request->addressReferenceCode);

        $address = \Iyzipay\Model\Address::retrieve($requestAddress, $this->config);

        return $address;
    }

    // update an address
    public function updateAddress($request)
    {
        $requestAddress = new \Iyzipay\Request\UpdateAddressRequest();
        $requestAddress->setLocale($this->locale);
        $requestAddress->setConversationId(generateRandomNumber());
        $requestAddress->setAddressReferenceCode($request->addressReferenceCode);
        $requestAddress->setContactName($request->contactName);
        $requestAddress->setCity($request->city);
        $requestAddress->setCountry($request->country);
        $requestAddress->setAddress($request->address);
        $requestAddress->setZipCode($request->zipCode);

        $address = \Iyzipay\Model\Address::update($requestAddress, $this->config);

        return $address;
    }

    // delete an address
    public function deleteAddress($request)
    {
        $requestAddress = new \Iyzipay\Request\DeleteAddressRequest();
        $requestAddress->setLocale($this->locale);
        $requestAddress->setConversationId(generateRandomNumber());
        $requestAddress->setAddressReferenceCode($request->addressReferenceCode);

        $address = \Iyzipay\Model\Address::delete($requestAddress, $this->config);

        return $address;
    }



    //************************************************
    
    //************ PRODUCT ACTIONS ******************

    //************************************************


    // create a product
    public function createProduct($request)
    {
        $requestProduct = new \Iyzipay\Request\CreateProductRequest();
        $requestProduct->setLocale($this->locale);
        $requestProduct->setName($request->name);
        $requestProduct->setItemType(\Iyzipay\Model\ItemType::VIRTUAL);
        $requestProduct->setPrice($request->price);
        $requestProduct->setCurrency($this->currency);
        $requestProduct->setConversationId(generateRandomNumber());

        $product = \Iyzipay\Model\Product::create($requestProduct, $this->config);

        return $product;
    }

    // retrieve a product
    public function retrieveProduct($request)
    {
        $requestProduct = new \Iyzipay\Request\RetrieveProductRequest();
        $requestProduct->setLocale($this->locale);
        $requestProduct->setProductReferenceCode($request->productReferenceCode);
        $requestProduct->setConversationId(generateRandomNumber());

        $product = \Iyzipay\Model\Product::retrieve($requestProduct, $this->config);

        return $product;
    }

    // update a product
    public function updateProduct($request)
    {
        $requestProduct = new \Iyzipay\Request\UpdateProductRequest();
        $requestProduct->setLocale($this->locale);
        $requestProduct->setProductReferenceCode($request->productReferenceCode);
        $requestProduct->setName($request->name);
        $requestProduct->setItemType(\Iyzipay\Model\ItemType::VIRTUAL);
        $requestProduct->setPrice($request->price);
        $requestProduct->setCurrency($this->currency);
        $requestProduct->setConversationId(generateRandomNumber());

        $product = \Iyzipay\Model\Product::update($requestProduct, $this->config);

        return $product;
    }

    // delete a product
    public function deleteProduct($request)
    {
        $requestProduct = new \Iyzipay\Request\DeleteProductRequest();
        $requestProduct->setLocale($this->locale);
        $requestProduct->setProductReferenceCode($request->productReferenceCode);
        $requestProduct->setConversationId(generateRandomNumber());

        $product = \Iyzipay\Model\Product::delete($requestProduct, $this->config);

        return $product;
    }




    //************************************************
    
    //************ PRICE ACTIONS ******************

    //************************************************




    // create a price
    public function createPrice($request)
    {
        $requestPrice = new \Iyzipay\Request\CreatePriceRequest();
        $requestPrice->setLocale($this->locale);
        $requestPrice->setProductReferenceCode($request->productReferenceCode);
        $requestPrice->setPrice($request->price);
        $requestPrice->setCurrency($this->currency);
        $requestPrice->setConversationId(generateRandomNumber());

        $price = \Iyzipay\Model\Price::create($requestPrice, $this->config);

        return $price;
    }

    // retrieve a price
    public function retrievePrice($request)
    {
        $requestPrice = new \Iyzipay\Request\RetrievePriceRequest();
        $requestPrice->setLocale($this->locale);
        $requestPrice->setProductReferenceCode($request->productReferenceCode);
        $requestPrice->setConversationId(generateRandomNumber());

        $price = \Iyzipay\Model\Price::retrieve($requestPrice, $this->config);

        return $price;
    }

    // update a price
    public function updatePrice($request)
    {
        $requestPrice = new \Iyzipay\Request\UpdatePriceRequest();
        $requestPrice->setLocale($this->locale);
        $requestPrice->setProductReferenceCode($request->productReferenceCode);
        $requestPrice->setPrice($request->price);
        $requestPrice->setCurrency($this->currency);
        $requestPrice->setConversationId(generateRandomNumber());

        $price = \Iyzipay\Model\Price::update($requestPrice, $this->config);

        return $price;
    }

    // delete a price
    public function deletePrice($request)
    {
        $requestPrice = new \Iyzipay\Request\DeletePriceRequest();
        $requestPrice->setLocale($this->locale);
        $requestPrice->setProductReferenceCode($request->productReferenceCode);
        $requestPrice->setConversationId(generateRandomNumber());

        $price = \Iyzipay\Model\Price::delete($requestPrice, $this->config);

        return $price;
    }



    //************************************************
    
    //************ PRICINGPLAN ACTIONS ******************

    //************************************************

    // create a pricing plan
    public function createPricingPlan($request)
    {
        $requestPricingPlan = new \Iyzipay\Request\CreatePricingPlanRequest();
        $requestPricingPlan->setLocale($this->locale);
        $requestPricingPlan->setName($request->name);
        $requestPricingPlan->setProductReferenceCode($request->productReferenceCode);
        $requestPricingPlan->setConversationId(generateRandomNumber());

        $pricingPlan = \Iyzipay\Model\PricingPlan::create($requestPricingPlan, $this->config);

        return $pricingPlan;
    }

    // retrieve a pricing plan
    public function retrievePricingPlan($request)
    {
        $requestPricingPlan = new \Iyzipay\Request\RetrievePricingPlanRequest();
        $requestPricingPlan->setLocale($this->locale);
        $requestPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestPricingPlan->setConversationId(generateRandomNumber());

        $pricingPlan = \Iyzipay\Model\PricingPlan::retrieve($requestPricingPlan, $this->config);

        return $pricingPlan;
    }

    // update a pricing plan
    public function updatePricingPlan($request)
    {
        $requestPricingPlan = new \Iyzipay\Request\UpdatePricingPlanRequest();
        $requestPricingPlan->setLocale($this->locale);
        $requestPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestPricingPlan->setName($request->name);
        $requestPricingPlan->setConversationId(generateRandomNumber());

        $pricingPlan = \Iyzipay\Model\PricingPlan::update($requestPricingPlan, $this->config);

        return $pricingPlan;
    }

    // delete a pricing plan
    public function deletePricingPlan($request)
    {
        $requestPricingPlan = new \Iyzipay\Request\DeletePricingPlanRequest();
        $requestPricingPlan->setLocale($this->locale);
        $requestPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestPricingPlan->setConversationId(generateRandomNumber());

        $pricingPlan = \Iyzipay\Model\PricingPlan::delete($requestPricingPlan, $this->config);

        return $pricingPlan;
    }



    //*****************************************************************
    
    //************ SUBSCRIPTION PRICING PLAN ACTIONS ******************

    //*****************************************************************

    // create a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionPricingPlanRequest
    public function createSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\CreateSubscriptionPricingPlanRequest();
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(generateRandomNumber());
        if($request->subscriptionInitialStatus) $requestSubscriptionPricingPlan->setSubscriptionInitialStatus($request->subscriptionInitialStatus);
        $requestSubscriptionPricingPlan->setCurrency($this->currency);
        $requestSubscriptionPricingPlan->setPaymentInterval($request->paymentInterval);
        if($request->pricingPlanPaymentType) $requestSubscriptionPricingPlan->setPricingPlanPaymentType($request->pricingPlanPaymentType);
        if($request->pricingPlanPeriod) $requestSubscriptionPricingPlan->setPricingPlanPeriod($request->pricingPlanPeriod);
        if($request->pricingPlanPeriodCount) $requestSubscriptionPricingPlan->setPricingPlanPeriodCount($request->pricingPlanPeriodCount);
        if($request->trialPeriodDays) $requestSubscriptionPricingPlan->setTrialPeriodDays($request->trialPeriodDays);
        if($request->trialAmount) $requestSubscriptionPricingPlan->setTrialAmount($request->trialAmount);
        $requestSubscriptionPricingPlan->setTrialCurrency($this->currency);
        if($request->trialPaymentInterval) $requestSubscriptionPricingPlan->setTrialPaymentInterval($request->trialPaymentInterval);
        if($request->trialPricingPlanPaymentType) $requestSubscriptionPricingPlan->setTrialPricingPlanPaymentType($request->trialPricingPlanPaymentType);
        if($request->trialPricingPlanPeriod) $requestSubscriptionPricingPlan->setTrialPricingPlanPeriod($request->trialPricingPlanPeriod);
        if($request->trialPricingPlanPeriodCount) $requestSubscriptionPricingPlan->setTrialPricingPlanPeriodCount($request->trialPricingPlanPeriodCount);
        if($request->callbackUrl) $requestSubscriptionPricingPlan->setCallbackUrl($request->callbackUrl);
        if($request->productReferenceCode) $requestSubscriptionPricingPlan->setProductReferenceCode($request->productReferenceCode);

        $subscriptionPricingPlan = \Iyzipay\Model\SubscriptionPricingPlan::create($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // retrieve a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionPricingPlanRequest
    public function retrieveSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\RetrieveSubscriptionPricingPlanRequest();
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(generateRandomNumber());
        $requestSubscriptionPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);

        $subscriptionPricingPlan = \Iyzipay\Model\SubscriptionPricingPlan::retrieve($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // update a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionPricingPlanRequest
    public function updateSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\UpdateSubscriptionPricingPlanRequest();
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(generateRandomNumber());
        $requestSubscriptionPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestSubscriptionPricingPlan->setPricingPlanStatus($request->pricingPlanStatus);

        $subscriptionPricingPlan = \Iyzipay\Model\SubscriptionPricingPlan::update($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }

    // delete a subscription pricing plan for \Iyzipay\Request\Subscription\SubscriptionPricingPlanRequest
    public function deleteSubscriptionPricingPlan($request)
    {
        $requestSubscriptionPricingPlan = new \Iyzipay\Request\DeleteSubscriptionPricingPlanRequest();
        $requestSubscriptionPricingPlan->setLocale($this->locale);
        $requestSubscriptionPricingPlan->setConversationId(generateRandomNumber());
        $requestSubscriptionPricingPlan->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);

        $subscriptionPricingPlan = \Iyzipay\Model\SubscriptionPricingPlan::delete($requestSubscriptionPricingPlan, $this->config);

        return $subscriptionPricingPlan;
    }





    //************************************************
    
    //************ BUYER ACTIONS ******************

    //************************************************

    // create a buyer
    public function createBuyer($request)
    {
        $requestBuyer = new \Iyzipay\Request\CreateBuyerRequest();
        $requestBuyer->setLocale($this->locale);
        $requestBuyer->setConversationId(generateRandomNumber());
        $requestBuyer->setName($request->name);
        $requestBuyer->setSurname($request->surname);
        $requestBuyer->setGsmNumber($request->gsmNumber);
        $requestBuyer->setEmail($request->email);
        $requestBuyer->setIdentityNumber($request->identityNumber);
        $requestBuyer->setLastLoginDate($request->lastLoginDate);
        $requestBuyer->setRegistrationDate($request->registrationDate);
        $requestBuyer->setRegistrationAddress($request->registrationAddress);
        $requestBuyer->setIp($request->ip);
        $requestBuyer->setCity($request->city);
        $requestBuyer->setCountry($request->country);
        $requestBuyer->setZipCode($request->zipCode);

        $buyer = \Iyzipay\Model\Buyer::create($requestBuyer, $this->config);

        return $buyer;
    }

    // retrieve a buyer
    public function retrieveBuyer($request)
    {
        $requestBuyer = new \Iyzipay\Request\RetrieveBuyerRequest();
        $requestBuyer->setLocale($this->locale);
        $requestBuyer->setConversationId(generateRandomNumber());
        $requestBuyer->setCustomerReferenceCode($request->customerReferenceCode);

        $buyer = \Iyzipay\Model\Buyer::retrieve($requestBuyer, $this->config);

        return $buyer;
    }

    // update a buyer
    public function updateBuyer($request)
    {
        $requestBuyer = new \Iyzipay\Request\UpdateBuyerRequest();
        $requestBuyer->setLocale($this->locale);
        $requestBuyer->setConversationId(generateRandomNumber());
        $requestBuyer->setCustomerReferenceCode($request->customerReferenceCode);
        $requestBuyer->setName($request->name);
        $requestBuyer->setSurname($request->surname);
        $requestBuyer->setGsmNumber($request->gsmNumber);
        $requestBuyer->setEmail($request->email);
        $requestBuyer->setIdentityNumber($request->identityNumber);
        $requestBuyer->setLastLoginDate($request->lastLoginDate);
        $requestBuyer->setRegistrationDate($request->registrationDate);
        $requestBuyer->setRegistrationAddress($request->registrationAddress);
        $requestBuyer->setIp($request->ip);
        $requestBuyer->setCity($request->city);
        $requestBuyer->setCountry($request->country);
        $requestBuyer->setZipCode($request->zipCode);

        $buyer = \Iyzipay\Model\Buyer::update($requestBuyer, $this->config);

        return $buyer;
    }

    // delete a buyer
    public function deleteBuyer($request)
    {
        $requestBuyer = new \Iyzipay\Request\DeleteBuyerRequest();
        $requestBuyer->setLocale($this->locale);
        $requestBuyer->setConversationId(generateRandomNumber());
        $requestBuyer->setCustomerReferenceCode($request->customerReferenceCode);

        $buyer = \Iyzipay\Model\Buyer::delete($requestBuyer, $this->config);

        return $buyer;
    }




    //************************************************
    
    //************ BASKET ACTIONS ******************

    //************************************************

    // create a basket
    public function createBasket($request)
    {
        $requestBasket = new \Iyzipay\Request\CreateBasketRequest();
        $requestBasket->setLocale($this->locale);
        $requestBasket->setConversationId(generateRandomNumber());
        $requestBasket->setPrice($request->price);
        $requestBasket->setBasketId($request->basketId);
        $requestBasket->setPaymentGroup($request->paymentGroup);

        $basket = \Iyzipay\Model\Basket::create($requestBasket, $this->config);

        return $basket;
    }

    // retrieve a basket
    public function retrieveBasket($request)
    {
        $requestBasket = new \Iyzipay\Request\RetrieveBasketRequest();
        $requestBasket->setLocale($this->locale);
        $requestBasket->setConversationId(generateRandomNumber());
        $requestBasket->setBasketId($request->basketId);

        $basket = \Iyzipay\Model\Basket::retrieve($requestBasket, $this->config);

        return $basket;
    }

    // update a basket
    public function updateBasket($request)
    {
        $requestBasket = new \Iyzipay\Request\UpdateBasketRequest();
        $requestBasket->setLocale($this->locale);
        $requestBasket->setConversationId(generateRandomNumber());
        $requestBasket->setPrice($request->price);
        $requestBasket->setBasketId($request->basketId);
        $requestBasket->setPaymentGroup($request->paymentGroup);

        $basket = \Iyzipay\Model\Basket::update($requestBasket, $this->config);

        return $basket;
    }

    // delete a basket
    public function deleteBasket($request)
    {
        $requestBasket = new \Iyzipay\Request\DeleteBasketRequest();
        $requestBasket->setLocale($this->locale);
        $requestBasket->setConversationId(generateRandomNumber());
        $requestBasket->setBasketId($request->basketId);

        $basket = \Iyzipay\Model\Basket::delete($requestBasket, $this->config);

        return $basket;
    }






    //************************************************
    
    //************ BASKETITEM ACTIONS ******************

    //************************************************

    // create a basket item
    public function createBasketItem($request)
    {
        $requestBasketItem = new \Iyzipay\Request\CreateBasketItemRequest();
        $requestBasketItem->setLocale($this->locale);
        $requestBasketItem->setConversationId(generateRandomNumber());
        $requestBasketItem->setPrice($request->price);
        $requestBasketItem->setBasketId($request->basketId);
        $requestBasketItem->setCategory1($request->category1);
        $requestBasketItem->setCategory2($request->category2);
        $requestBasketItem->setItemType($request->itemType);
        $requestBasketItem->setSubMerchantKey($request->subMerchantKey);
        $requestBasketItem->setSubMerchantPrice($request->subMerchantPrice);
        $requestBasketItem->setSubMerchantPayoutRate($request->subMerchantPayoutRate);
        $requestBasketItem->setSubMerchantPayoutAmount($request->subMerchantPayoutAmount);

        $basketItem = \Iyzipay\Model\BasketItem::create($requestBasketItem, $this->config);

        return $basketItem;
    }

    // retrieve a basket item
    public function retrieveBasketItem($request)
    {
        $requestBasketItem = new \Iyzipay\Request\RetrieveBasketItemRequest();
        $requestBasketItem->setLocale($this->locale);
        $requestBasketItem->setConversationId(generateRandomNumber());
        $requestBasketItem->setBasketId($request->basketId);
        $requestBasketItem->setId($request->id);

        $basketItem = \Iyzipay\Model\BasketItem::retrieve($requestBasketItem, $this->config);

        return $basketItem;
    }

    // update a basket item
    public function updateBasketItem($request)
    {
        $requestBasketItem = new \Iyzipay\Request\UpdateBasketItemRequest();
        $requestBasketItem->setLocale($this->locale);
        $requestBasketItem->setConversationId(generateRandomNumber());
        $requestBasketItem->setBasketId($request->basketId);
        $requestBasketItem->setId($request->id);
        $requestBasketItem->setPrice($request->price);
        $requestBasketItem->setCategory1($request->category1);
        $requestBasketItem->setCategory2($request->category2);
        $requestBasketItem->setItemType($request->itemType);
        $requestBasketItem->setSubMerchantKey($request->subMerchantKey);
        $requestBasketItem->setSubMerchantPrice($request->subMerchantPrice);
        $requestBasketItem->setSubMerchantPayoutRate($request->subMerchantPayoutRate);
        $requestBasketItem->setSubMerchantPayoutAmount($request->subMerchantPayoutAmount);

        $basketItem = \Iyzipay\Model\BasketItem::update($requestBasketItem, $this->config);

        return $basketItem;
    }

    // delete a basket item
    public function deleteBasketItem($request)
    {
        $requestBasketItem = new \Iyzipay\Request\DeleteBasketItemRequest();
        $requestBasketItem->setLocale($this->locale);
        $requestBasketItem->setConversationId(generateRandomNumber());
        $requestBasketItem->setBasketId($request->basketId);
        $requestBasketItem->setId($request->id);

        $basketItem = \Iyzipay\Model\BasketItem::delete($requestBasketItem, $this->config);

        return $basketItem;
    }






    //************************************************
    
    //************ PAYMENT ACTIONS ******************

    //************************************************

    // create a payment
    public function createPayment($request)
    {
        $requestPayment = new \Iyzipay\Request\CreatePaymentRequest();
        $requestPayment->setLocale($this->locale);
        $requestPayment->setConversationId(generateRandomNumber());
        $requestPayment->setPrice($request->price);
        $requestPayment->setPaidPrice($request->paidPrice);
        $requestPayment->setInstallment($request->installment);
        $requestPayment->setPaymentChannel($request->paymentChannel);
        $requestPayment->setBasketId($request->basketId);
        $requestPayment->setPaymentGroup($request->paymentGroup);
        $requestPayment->setPaymentCard($request->paymentCard);
        $requestPayment->setCurrency($this->currency);
        $requestPayment->setCallbackUrl($request->callbackUrl);
        $requestPayment->setEnabledInstallments($request->enabledInstallments);
        $requestPayment->setBuyer($request->buyer);
        $requestPayment->setShippingAddress($request->shippingAddress);
        $requestPayment->setBillingAddress($request->billingAddress);
        $requestPayment->setBasketItems($request->basketItems);

        $payment = \Iyzipay\Model\Payment::create($requestPayment, $this->config);

        return $payment;
    }

    // retrieve a payment
    public function retrievePayment($request)
    {
        $requestPayment = new \Iyzipay\Request\RetrievePaymentRequest();
        $requestPayment->setLocale($this->locale);
        $requestPayment->setConversationId(generateRandomNumber());
        $requestPayment->setPaymentId($request->paymentId);
        $requestPayment->setPaymentConversationId($request->paymentConversationId);

        $payment = \Iyzipay\Model\Payment::retrieve($requestPayment, $this->config);

        return $payment;
    }

    // get payment with form
    public function getPaymentWithForm($request)
    {
        $requestPayment = new \Iyzipay\Request\CreatePaymentRequest();
        $requestPayment->setLocale($this->locale);
        $requestPayment->setConversationId(generateRandomNumber());
        $requestPayment->setPrice($request->price);
        $requestPayment->setPaidPrice($request->paidPrice);
        $requestPayment->setInstallment($request->installment);
        $requestPayment->setPaymentChannel($request->paymentChannel);
        $requestPayment->setBasketId($request->basketId);
        $requestPayment->setPaymentGroup($request->paymentGroup);
        $requestPayment->setPaymentCard($request->paymentCard);
        $requestPayment->setCurrency($this->currency);
        $requestPayment->setCallbackUrl($request->callbackUrl);
        $requestPayment->setEnabledInstallments($request->enabledInstallments);
        $requestPayment->setBuyer($request->buyer);
        $requestPayment->setShippingAddress($request->shippingAddress);
        $requestPayment->setBillingAddress($request->billingAddress);
        $requestPayment->setBasketItems($request->basketItems);

        $paymentFormInitialize = \Iyzipay\Model\PaymentFormInitialize::create($requestPayment, $this->config);

        return $paymentFormInitialize;
    }

    // get payment with checkout form
    public function getPaymentWithCheckoutForm($request)
    {
        $requestPayment = new \Iyzipay\Request\CreatePaymentRequest();
        $requestPayment->setLocale($this->locale);
        $requestPayment->setConversationId(generateRandomNumber());
        $requestPayment->setPrice($request->price);
        $requestPayment->setPaidPrice($request->paidPrice);
        $requestPayment->setInstallment($request->installment);
        $requestPayment->setPaymentChannel($request->paymentChannel);
        $requestPayment->setBasketId($request->basketId);
        $requestPayment->setPaymentGroup($request->paymentGroup);
        $requestPayment->setPaymentCard($request->paymentCard);
        $requestPayment->setCurrency($this->currency);
        $requestPayment->setCallbackUrl($request->callbackUrl);
        $requestPayment->setEnabledInstallments($request->enabledInstallments);
        $requestPayment->setBuyer($request->buyer);
        $requestPayment->setShippingAddress($request->shippingAddress);
        $requestPayment->setBillingAddress($request->billingAddress);
        $requestPayment->setBasketItems($request->basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($requestPayment, $this->config);

        return $checkoutFormInitialize;
    }






    //************************************************
    
    //************ SUBSCRIPTION ACTIONS ******************

    //************************************************

    // create a subscription
    public function createSubscription($request)
    {
        $requestSubscription = new \Iyzipay\Request\CreateSubscriptionRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestSubscription->setSubscriptionInitialStatus($request->subscriptionInitialStatus);
        $requestSubscription->setCustomer($request->customer);
        $requestSubscription->setBillingAddress($request->billingAddress);
        $requestSubscription->setShippingAddress($request->shippingAddress);
        $requestSubscription->setPaymentCard($request->paymentCard);
        $requestSubscription->setSubscriptionItems($request->subscriptionItems);

        $subscription = \Iyzipay\Model\Subscription::create($requestSubscription, $this->config);

        return $subscription;
    }

    // create a subscription with checkout form
    public function createSubscriptionWithCheckoutForm($request)
    {
        $requestSubscription = new \Iyzipay\Request\CreateSubscriptionRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestSubscription->setSubscriptionInitialStatus($request->subscriptionInitialStatus);
        $requestSubscription->setCustomer($request->customer);
        $requestSubscription->setBillingAddress($request->billingAddress);
        $requestSubscription->setShippingAddress($request->shippingAddress);
        $requestSubscription->setPaymentCard($request->paymentCard);
        $requestSubscription->setSubscriptionItems($request->subscriptionItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($requestSubscription, $this->config);

        return $checkoutFormInitialize;
    }

    // update a subscription
    public function updateSubscription($request)
    {
        $requestSubscription = new \Iyzipay\Request\UpdateSubscriptionRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);
        $requestSubscription->setPricingPlanReferenceCode($request->pricingPlanReferenceCode);
        $requestSubscription->setCustomer($request->customer);
        $requestSubscription->setBillingAddress($request->billingAddress);
        $requestSubscription->setShippingAddress($request->shippingAddress);
        $requestSubscription->setPaymentCard($request->paymentCard);
        $requestSubscription->setSubscriptionItems($request->subscriptionItems);

        $subscription = \Iyzipay\Model\Subscription::update($requestSubscription, $this->config);

        return $subscription;
    }

    // retrieve a subscription
    public function retrieveSubscription($request)
    {
        $requestSubscription = new \Iyzipay\Request\RetrieveSubscriptionRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscription = \Iyzipay\Model\Subscription::retrieve($requestSubscription, $this->config);

        return $subscription;
    }

    // cancel a subscription
    public function cancelSubscription($request)
    {
        $requestSubscription = new \Iyzipay\Request\CancelSubscriptionRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscription = \Iyzipay\Model\Subscription::cancel($requestSubscription, $this->config);

        return $subscription;
    }

    // search a subscription
    public function searchSubscription($request)
    {
        $requestSubscription = new \Iyzipay\Request\SearchSubscriptionRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);
        $requestSubscription->setPage($request->page);
        $requestSubscription->setCount($request->count);

        $subscription = \Iyzipay\Model\Subscription::search($requestSubscription, $this->config);

        return $subscription;
    }

    // retrieve all subscriptions
    public function retrieveAllSubscriptions($request)
    {
        $requestSubscription = new \Iyzipay\Request\RetrieveAllSubscriptionsRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setPage($request->page);
        $requestSubscription->setCount($request->count);

        $subscription = \Iyzipay\Model\Subscription::retrieveAll($requestSubscription, $this->config);

        return $subscription;
    }

    // retrieve subscription details
    public function retrieveSubscriptionDetails($request)
    {
        $requestSubscription = new \Iyzipay\Request\RetrieveSubscriptionDetailsRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscription = \Iyzipay\Model\Subscription::retrieveDetails($requestSubscription, $this->config);

        return $subscription;
    }

    // retrieve subscription transactions
    public function retrieveSubscriptionTransactions($request)
    {
        $requestSubscription = new \Iyzipay\Request\RetrieveSubscriptionTransactionsRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);
        $requestSubscription->setPage($request->page);
        $requestSubscription->setCount($request->count);

        $subscription = \Iyzipay\Model\Subscription::retrieveTransactions($requestSubscription, $this->config);

        return $subscription;
    }

    // retrieve subscription customer
    public function retrieveSubscriptionCustomer($request)
    {
        $requestSubscription = new \Iyzipay\Request\RetrieveSubscriptionCustomerRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscription = \Iyzipay\Model\Subscription::retrieveCustomer($requestSubscription, $this->config);

        return $subscription;
    }

    // retrieve subscription card
    public function retrieveSubscriptionCard($request)
    {
        $requestSubscription = new \Iyzipay\Request\RetrieveSubscriptionCardRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);

        $subscription = \Iyzipay\Model\Subscription::retrieveCard($requestSubscription, $this->config);

        return $subscription;
    }

    // update subscription card
    public function updateSubscriptionCard($request)
    {
        $requestSubscription = new \Iyzipay\Request\UpdateSubscriptionCardRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);
        $requestSubscription->setPaymentCard($request->paymentCard);

        $subscription = \Iyzipay\Model\Subscription::updateCard($requestSubscription, $this->config);

        return $subscription;
    }

    // update subscription customer
    public function updateSubscriptionCustomer($request)
    {
        $requestSubscription = new \Iyzipay\Request\UpdateSubscriptionCustomerRequest();
        $requestSubscription->setLocale($this->locale);
        $requestSubscription->setConversationId(generateRandomNumber());
        $requestSubscription->setSubscriptionReferenceCode($request->subscriptionReferenceCode);
        $requestSubscription->setCustomer($request->customer);

        $subscription = \Iyzipay\Model\Subscription::updateCustomer($requestSubscription, $this->config);

        return $subscription;
    }













}







/**
 * Controls ALL Payment actions of Iyzico
 */
class IyzicoController extends Controller
{

    // iyzipay actions class
    private $iyzipayActions;

    // constructor
    public function __construct()
    {
        $settings = $this->retrieveGatewaySettings();
        $this->iyzipayActions = new IyzicoActions($settings->apiKey, $settings->apiSecretKey, $settings->baseUrl, $settings->currency);
    }

    private function retrieveGatewaySettings()
    {
        $gateway = Gateways::where("code", "iyzico")->first();
        if($gateway == null) { abort(404); } 
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $settings = Setting::first();

        return [
            'apiKey' => $gateway->mode == 'live' ? $gateway->live_client_id : $gateway->sandbox_client_id,
            'apiSecretKey' => $gateway->mode == 'live' ? $gateway->live_client_secret : $gateway->sandbox_client_secret,
            'baseUrl' =>  $gateway->mode == 'live' ? $gateway->base_url : $gateway->sandbox_url,
            'currency' => $currency,
        ];
    }


    /**
     * Reads GatewayProducts table and returns price id of the given plan
     */
    public static function getIyzicoPriceId($planId)
    {

        //check if plan exists
        $plan = PaymentPlans::where('id', $planId)->first();
        if ($plan != null) {
            $product = GatewayProducts::where(["plan_id" => $planId, "gateway_code" => "iyzico"])->first();
            if ($product != null) {
                return $product->price_id;
            } else {
                return null;
            }
        }
        return null;
    }


    /**
     * Saves Membership plan product in iyzico gateway.
     * @param planId ID of plan in PaymentPlans model.
     * @param productName Name of the product, plain text
     * @param price Price of product
     * @param frequency Time interval of subscription, month / annual
     * @param type Type of product subscription/one-time
     */
    public static function saveProduct($planId, $productName, $price, $frequency, $type){

        $plan = PaymentPlans::where('id', $planId)->first();

        $product = null;
        $oldProductId = null;

        //////// PRODUCT ////////

        //check if product exists
        $productData = GatewayProducts::where(["plan_id" => $planId, "gateway_code" => "iyzico"])->first();
        if($productData != null){

            // Create product in every situation. maybe user updated iyzico credentials.

            if($productData->product_id != null){
                //Product has been created before
                $oldProductId = $productData->product_id;
            }else{
                //Product has NOT been created before but record exists. Create new product and update record.
            }


            $newProduct = $this->iyzipayActions->createProduct([
                "name"          => $productName,
                "price"         => $price,
            ]);

            Log::info("iyzico product refreshed : " . $newProduct);

            $productData->product_id = $newProduct['data']['referenceCode'];
            $productData->plan_name = $productName;
            $productData->save();


            $product = $productData;
        }else{

            
            $newProduct = $this->iyzipayActions->createProduct([
                "name"          => $productName,
                "price"         => $price,
            ]);

            Log::info("iyzico product created : " . $newProduct);

            $product = new GatewayProducts();
            $product->plan_id = $planId;
            $product->plan_name = $productName;
            $product->gateway_code = "iyzico";
            $product->gateway_title = "iyzico";
            $product->product_id = $newProduct['data']['referenceCode'];
            $product->save();
        }


        ////////// PRICING PLAN //////////


        //check if price exists
        if($product->price_id != null){
            //Price exists

            // One-Time price
            if($type == "o"){
                
                // iyzico handles one time prices with payments, so we do not need to set anything for one-time payments.
                $product->price_id = __('Not Needed');
                $product->save();
                
            }else{
                // Subscription


                $oldPricingPlanId = $product->price_id;

                // create new plan with new values
                $interval = $frequency == "m" ? 'MONTHLY' : 'YEARLY';

                if($plan->trial_days != "undefined"){
                    $trials = $plan->trial_days ?? 0;
                }else{
                    $trials = 0;
                }

                // $this->iyzipayActions->deleteSubscriptionPricingPlan($oldBillingPlanId); -> Moved to updateUserData() function

                $subscriptionPricingPlan = $this->iyzipayActions->createSubscriptionPricingPlan([
                    'subscriptionInitialStatus' => 'ACTIVE',
                    'paymentInterval' => $interval,
                    'pricingPlanPaymentType' => 'RECURRING',
                    'pricingPlanPeriod' => 'MONTH',
                    'pricingPlanPeriodCount' => 1,
                    'trialPeriodDays' => $trials,
                    'trialAmount' => 0,
                    'productReferenceCode' => $product->product_id,
                    'price' => $price,
                ]);

                Log::info("iyzico price refreshed : " . $subscriptionPricingPlan);

                $product->price_id = $subscriptionPricingPlan['data']['referenceCode'];
                $product->save();

                $history = new OldGatewayProducts();
                $history->plan_id = $planId;
                $history->plan_name = $productName;
                $history->gateway_code = 'iyzico';
                $history->product_id = $product->product_id;
                $history->old_product_id = $oldProductId;
                $history->old_price_id = $oldPricingPlanId;
                $history->new_price_id = $subscriptionPricingPlan['data']['referenceCode'];
                $history->status = 'check';
                $history->save();

                $tmp = self::updateUserData();

                ///////////// To support old entries and prevent update issues on trial and non-trial areas
                ///////////// update system is cancelled. instead we are going to create new ones, deactivate old ones and replace them.

            }

        }else{
            // price_id is null so we need to create plans

            // One-Time price
            if($type == "o"){
                
                // iyzico handles one time prices with orders, so we do not need to set anything for one-time payments.
                $product->price_id = __('Not Needed');
                $product->save();
                
            }else{
                // Subscription


                $interval = $frequency == "m" ? 'MONTHLY' : 'YEARLY';

                $trials = $plan->trial_days ?? 0;

                $subscriptionPricingPlan = $this->iyzipayActions->createSubscriptionPricingPlan([
                    'subscriptionInitialStatus' => 'ACTIVE',
                    'paymentInterval' => $interval,
                    'pricingPlanPaymentType' => 'RECURRING',
                    'pricingPlanPeriod' => 'MONTH',
                    'pricingPlanPeriodCount' => 1,
                    'trialPeriodDays' => $trials,
                    'trialAmount' => 0,
                    'productReferenceCode' => $product->product_id,
                    'price' => $price,
                ]);

                Log::info("iyzico price created : " . $subscriptionPricingPlan);

                $product->price_id = $subscriptionPricingPlan['data']['referenceCode'];
                $product->save();
            }
        }













    }


}
