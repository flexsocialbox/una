<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Credits Credits
 * @ingroup     UnaModules
 *
 * @{
 */

define('BX_CREDITS_DIRECTION_IN', 'in');
define('BX_CREDITS_DIRECTION_OUT', 'out');

define('BX_CREDITS_ORDER_TYPE_SINGLE', 'single'); //--- one-time payment
define('BX_CREDITS_ORDER_TYPE_RECURRING', 'recurring'); //--- recurring payment (subscription)


class BxCreditsModule extends BxBaseModGeneralModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    public function actionGetProfiles()
    {
        $sTerm = bx_get('term');

        $a = BxDolService::call('system', 'profiles_search', array($sTerm), 'TemplServiceProfiles');

        echoJson($a);
    }

    public function actionCheckout()
    {
        $iBuyerId = bx_get_logged_profile_id();
        if(!$iBuyerId)
            return echoJson(array('code' => 1, 'msg' => _t('_bx_credits_err_unknown_buyer')));

        $aData = $this->_oConfig->getCheckoutData();
        if(empty($aData) || !is_array($aData))
            return echoJson(array('code' => 2, 'msg' => _t('_bx_credits_err_incorrect_data')));

        $iSellerId = (int)$aData['seller'];
        $fAmount = (float)$aData['amount'];

        $fBalance = (float)$this->_oDb->getProfile(array('type' => 'balance', 'id' => $iBuyerId));
        if($fAmount > $fBalance)
            return echoJson(array('code' => 3, 'msg' => _t('_bx_credits_err_low_balance')));

        $sOrder = $this->_oConfig->getOrder();
        $sInfo = '_bx_credits_txt_history_info_checkout';
        $sData = serialize(array(
            'conversion' => $this->_oConfig->getConversionRateUse(),
            'precision' => $this->_oConfig->getPrecision()
        ));

        $this->updateProfileBalance($iBuyerId, $iSellerId, -$fAmount, $sOrder, $sInfo, $sData);
        $this->updateProfileBalance($iSellerId, $iBuyerId, $fAmount, $sOrder, $sInfo, $sData);

        bx_alert($this->getName(), 'checkout', 0, false, array(
            'seller' => $iSellerId,
            'buyer' => $iBuyerId,
            'amount' => $fAmount, 
            'order' => $sOrder
        ));

        return echoJson(array(
            'code' => 0,
            'redirect' => bx_append_url_params($aData['return_data_url'], array('o' => $sOrder, 'c' => $aData['custom']))
        ));
    }

    public function serviceGetSafeServices()
    {
        return array (
            'GetBlockBundles' => '',
            'GetBlockOrders' => '',
            'GetBlockHistory' => '',
        );
    }

    public function serviceGetCheckoutUrl()
    {
        return $this->_oConfig->getCheckoutUrl();
    }

    public function serviceValidateCheckout($iSeller, $iBuyer, $fAmount, $sOrder)
    {
        $fAmount = (float)$fAmount;

        $aOut = $this->_oDb->getHistory(array('type' => 'row_by', 'by' => array('first_pid' => $iBuyer, 'direction' => BX_CREDITS_DIRECTION_OUT, 'order' => $sOrder)));
        if(empty($aOut) || !is_array($aOut) || (int)$aOut['second_pid'] != (int)$iSeller)
            return false;
        
        $aDataOut = unserialize($aOut['data']);
        if($this->_oConfig->convertM2C($fAmount, $aDataOut['conversion'], $aDataOut['precision']) != (float)$aOut['amount'])
            return false;

        $aIn = $this->_oDb->getHistory(array('type' => 'row_by', 'by' => array('first_pid' => $iSeller, 'direction' => BX_CREDITS_DIRECTION_IN, 'order' => $sOrder)));
        if(empty($aIn) || !is_array($aIn) || (int)$aIn['second_pid'] != (int)$iBuyer)
            return false;

        $aDataIn = unserialize($aIn['data']);
        if($this->_oConfig->convertM2C($fAmount, $aDataIn['conversion'], $aDataIn['precision']) != (float)$aIn['amount'])
            return false;

        return true;
    }

    public function serviceGetBlockCheckout()
    {
        $oBuyer = BxDolProfile::getInstance();
        if(!$oBuyer)
            return MsgBox(_t('_bx_credits_err_unknown_buyer'));
        
        $iSeller = (int)bx_get('seller');
        $oSeller = BxDolProfile::getInstance($iSeller);
        if(!$oSeller)
            return MsgBox(_t('_bx_credits_err_unknown_seller'));

        $fAmountM = (float)bx_get('amount');
        $fAmountC = $this->_oConfig->convertM2C($fAmountM);

        $aData = array(
            'seller' => $iSeller,
            'currency' => array(
                'code' => bx_process_input(bx_get('currency_code')),
                'sign' => bx_process_input(bx_get('currency_sign')),
            ),
            'amountm' => $fAmountM,
            'amountc' => $fAmountC
        );

        $iItemsCount = (int)bx_get('items_count');
        for($i = 0; $i < $iItemsCount; $i++) {
            $aData['items'][] = array(
                'title' => bx_process_input(bx_get('item_title_' . $i)),
                'quantity' => (int)bx_get('item_quantity_' . $i)
            );
        }

        $this->_oConfig->setCheckoutData(array(
            'seller' => $iSeller,
            'amount' => $fAmountC,
            'custom' => bx_process_input(bx_get('custom')),
            'return_data_url' => bx_process_input(bx_get('return_data_url')),
        ));
        return $this->_oTemplate->getBlockCheckout($oBuyer, $oSeller, $aData);
    }

    public function serviceGetMenuItemAddonAmount()
    {
        $iProfileId = bx_get_logged_profile_id();
        if(!$iProfileId)
            return '';

        return array(
            'unit' => $this->_oTemplate->getUnit(), 
            'value' => $this->convertC2S($this->_oDb->getProfile(array('type' => 'balance', 'id' => $iProfileId)), false)
        );
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-page_blocks Page Blocks
     * @subsubsection bx_credits-get_block_bundles get_block_bundles
     * 
     * @code bx_srv('bx_credits', 'get_block_bundles'); @endcode
     * 
     * Get page block with bundles
     * 
     * @see BxCreditsModule::serviceGetBlockBundles
     */
    /** 
     * @ref bx_credits-get_block_bundles "get_block_bundles"
     */
    public function serviceGetBlockBundles()
    {
        return $this->_oTemplate->getBlockBundles();
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-page_blocks Page Blocks
     * @subsubsection bx_credits-get_block_orders_note get_block_orders_note
     * 
     * @code bx_srv('bx_credits', 'get_block_orders_note', [...]); @endcode
     * 
     * Get page block with a notice for orders usage.
     *
     * @return HTML string with block content to display on the site.
     * 
     * @see BxCreditsModule::serviceBlockLicensesNote
     */
    /** 
     * @ref bx_credits-get_block_orders_note "get_block_orders_note"
     */
    public function serviceGetBlockOrdersNote()
    {
        return MsgBox(_t('_bx_credits_page_block_content_orders_common_note'));
    }
    
    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-page_blocks Page Blocks
     * @subsubsection bx_credits-block_orders get_block_orders
     * 
     * @code bx_srv('bx_credits', 'get_block_orders', [...]); @endcode
     * 
     * Get page block with a list of orders purchased by currently logged member.
     *
     * @return an array describing a block to display on the site or false if there is no enough input data. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxCreditsModule::serviceBlockLicenses
     */
    /** 
     * @ref bx_credits-get_block_orders "get_block_orders"
     */
    public function serviceGetBlockOrders($sType = 'common') 
    {
        return $this->_getBlockOrders($sType);
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-page_blocks Page Blocks
     * @subsubsection bx_credits-get_block_history get_block_history
     * 
     * @code bx_srv('bx_credits', 'get_block_history', [...]); @endcode
     * 
     * Get page block with a list of all changes with credits.
     *
     * @return an array describing a block to display on the site or false if there is no enough input data. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxCreditsModule::serviceGetBlockHistory
     */
    /** 
     * @ref bx_credits-get_block_history "get_block_history"
     */
    public function serviceGetBlockHistory($sType = 'common') 
    {
        return $this->_getBlockHistory($sType);
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-get_payment_data get_payment_data
     * 
     * @code bx_srv('bx_credits', 'get_payment_data', [...]); @endcode
     * 
     * Get an array with module's description. Is needed for payments processing module.
     * 
     * @return an array with module's description.
     * 
     * @see BxCreditsModule::serviceGetPaymentData
     */
    /** 
     * @ref bx_credits-get_payment_data "get_payment_data"
     */
    public function serviceGetPaymentData()
    {
        $CNF = &$this->_oConfig->CNF;

        $oPermalink = BxDolPermalinks::getInstance();

        $aResult = $this->_aModule;
        $aResult['url_browse_order_common'] = BX_DOL_URL_ROOT . $oPermalink->permalink($CNF['URL_ORDERS_COMMON'], array('filter' => '{order}'));
        $aResult['url_browse_order_administration'] = BX_DOL_URL_ROOT . $oPermalink->permalink($CNF['URL_ORDERS_ADMINISTRATION'], array('filter' => '{order}'));

        return $aResult;
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-get_cart_item get_cart_item
     * 
     * @code bx_srv('bx_credits', 'get_cart_item', [...]); @endcode
     * 
     * Get an array with prodict's description. Is used in Shopping Cart in payments processing module.
     * 
     * @param $iItemId product's ID.
     * @return an array with prodict's description. Empty array is returned if something is wrong.
     * 
     * @see BxCreditsModule::serviceGetCartItem
     */
    /** 
     * @ref bx_credits-get_cart_item "get_cart_item"
     */
    public function serviceGetCartItem($iItemId)
    {
    	$CNF = &$this->_oConfig->CNF;

        if(!$iItemId)
            return array();

        $aItem = $this->_oDb->getBundle(array('type' => 'id', 'id' => $iItemId));
        if(empty($aItem) || !is_array($aItem))
            return array();

        return array (
            'id' => $aItem[$CNF['FIELD_ID']],
            'author_id' => $this->_oConfig->getAuthor(),
            'name' => $aItem[$CNF['FIELD_NAME']],
            'title' => _t($aItem[$CNF['FIELD_TITLE']]),
            'description' => $this->_oConfig->getBundleDescription($aItem),
            'url' => $this->_oConfig->getBundleUrl($aItem),
            'price_single' => $aItem[$CNF['FIELD_PRICE']],
            'price_recurring' => '',
            'period_recurring' => 1,
            'period_unit_recurring' => '',
            'trial_recurring' => ''
        );
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-get_cart_items get_cart_items
     * 
     * @code bx_srv('bx_credits', 'get_cart_items', [...]); @endcode
     * 
     * Get an array with prodicts' descriptions by seller. Is used in Manual Order Processing in payments processing module.
     * 
     * @param $iSellerId seller ID.
     * @return an array with prodicts' descriptions. Empty array is returned if something is wrong or seller doesn't have any products.
     * 
     * @see BxCreditsModule::serviceGetCartItems
     */
    /** 
     * @ref bx_credits-get_cart_items "get_cart_items"
     */
    public function serviceGetCartItems($iAuthorId)
    {
    	$CNF = &$this->_oConfig->CNF;

        $iAuthorId = $this->_oConfig->getAuthor();
        $aItems = $this->_oDb->getBundle(array('type' => 'all', 'active' => 1));

        $aResult = array();
        foreach($aItems as $aItem)
            $aResult[] = array(
                'id' => $aItem[$CNF['FIELD_ID']],
                'author_id' => $iAuthorId,
                'name' => $aItem[$CNF['FIELD_NAME']],
                'title' => _t($aItem[$CNF['FIELD_TITLE']]),
                'description' => $this->_oConfig->getBundleDescription($aItem),
                'url' => $this->_oConfig->getBundleUrl($aItem),
                'price_single' => $aItem[$CNF['FIELD_PRICE']],
                'price_recurring' => '',
                'period_recurring' => 1,
                'period_unit_recurring' => '',
                'trial_recurring' => ''
            );

        return $aResult;
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-register_cart_item register_cart_item
     * 
     * @code bx_srv('bx_credits', 'register_cart_item', [...]); @endcode
     * 
     * Register a processed single time payment inside the Credits module. Is called with payment processing module after the payment was registered there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemId item ID.
     * @param $iItemCount the number of purchased items.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @param $sLicense license number genereted with payment processing module for internal usage
     * @return an array with purchased prodict's description. Empty array is returned if something is wrong.
     * 
     * @see BxCreditsModule::serviceRegisterCartItem
     */
    /** 
     * @ref bx_credits-register_cart_item "register_cart_item"
     */
    public function serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense)
    {
        return $this->_serviceRegisterItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense, BX_CREDITS_ORDER_TYPE_SINGLE);
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-register_subscription_item register_subscription_item
     * 
     * @code bx_srv('bx_credits', 'register_subscription_item', [...]); @endcode
     * 
     * Register a processed subscription (recurring payment) inside the Credits module. Is called with payment processing module after the subscription was registered there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemId item ID.
     * @param $iItemCount the number of purchased items.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @param $sLicense license number genereted with payment processing module for internal usage
     * @return an array with subscribed prodict's description. Empty array is returned if something is wrong.
     * 
     * @see BxCreditsModule::serviceRegisterSubscriptionItem
     */
    /** 
     * @ref bx_credits-register_subscription_item "register_subscription_item"
     */
    public function serviceRegisterSubscriptionItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense)
    {
        return $this->_serviceRegisterItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense, BX_CREDITS_ORDER_TYPE_RECURRING);
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-reregister_cart_item reregister_cart_item
     * 
     * @code bx_srv('bx_credits', 'reregister_cart_item', [...]); @endcode
     * 
     * Reregister a single time payment inside the Credits module. Is called with payment processing module after the payment was reregistered there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemIdOld old item ID.
     * @param $iItemIdNew new item ID.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @return an array with purchased prodict's description. Empty array is returned if something is wrong.
     * 
     * @see BxCreditsModule::serviceReregisterCartItem
     */
    /** 
     * @ref bx_credits-reregister_cart_item "reregister_cart_item"
     */
    public function serviceReregisterCartItem($iClientId, $iSellerId, $iItemIdOld, $iItemIdNew, $sOrder)
    {
        return array();
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-reregister_subscription_item reregister_subscription_item
     * 
     * @code bx_srv('bx_credits', 'reregister_subscription_item', [...]); @endcode
     * 
     * Reregister a subscription (recurring payment) inside the Credits module. Is called with payment processing module after the subscription was reregistered there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemIdOld old item ID.
     * @param $iItemIdNew new item ID.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @return an array with subscribed prodict's description. Empty array is returned if something is wrong.
     * 
     * @see BxCreditsModule::serviceReregisterSubscriptionItem
     */
    /** 
     * @ref bx_credits-reregister_subscription_item "reregister_subscription_item"
     */
    public function serviceReregisterSubscriptionItem($iClientId, $iSellerId, $iItemIdOld, $iItemIdNew, $sOrder)
    {
        return array();
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-unregister_cart_item unregister_cart_item
     * 
     * @code bx_srv('bx_credits', 'unregister_cart_item', [...]); @endcode
     * 
     * Unregister an earlier processed single time payment inside the Credits module. Is called with payment processing module after the payment was unregistered there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemId item ID.
     * @param $iItemCount the number of purchased items.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @param $sLicense license number genereted with payment processing module for internal usage
     * @return boolean value determining where the payment was unregistered or not.
     * 
     * @see BxCreditsModule::serviceUnregisterCartItem
     */
    /** 
     * @ref bx_credits-unregister_cart_item "unregister_cart_item"
     */
    public function serviceUnregisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense)
    {
        return $this->_serviceUnregisterItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense, BX_CREDITS_ORDER_TYPE_SINGLE);
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-unregister_subscription_item unregister_subscription_item
     * 
     * @code bx_srv('bx_credits', 'unregister_subscription_item', [...]); @endcode
     * 
     * Unregister an earlier processed subscription (recurring payment) inside the Credits module. Is called with payment processing module after the subscription was unregistered there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemId item ID.
     * @param $iItemCount the number of purchased items.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @param $sLicense license number genereted with payment processing module for internal usage
     * @return boolean value determining where the subscription was unregistered or not.
     * 
     * @see BxCreditsModule::serviceUnregisterSubscriptionItem
     */
    /** 
     * @ref bx_credits-unregister_subscription_item "unregister_subscription_item"
     */
    public function serviceUnregisterSubscriptionItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense)
    {
    	return $this->_serviceUnregisterItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense, BX_CREDITS_ORDER_TYPE_RECURRING); 
    }

    /**
     * @page service Service Calls
     * @section bx_credits Credits
     * @subsection bx_credits-payments Payments
     * @subsubsection bx_credits-cancel_subscription_item cancel_subscription_item
     * 
     * @code bx_srv('bx_credits', 'cancel_subscription_item', [...]); @endcode
     * 
     * Cancel an earlier processed subscription (recurring payment) inside the Credits module. Is called with payment processing module after the subscription was canceled there.
     * 
     * @param $iClientId client ID.
     * @param $iSellerId seller ID
     * @param $iItemId item ID.
     * @param $iItemCount the number of purchased items.
     * @param $sOrder order number received from payment provider (PayPal, Stripe, etc)
     * @return boolean value determining where the subscription was canceled or not.
     * 
     * @see BxCreditsModule::serviceCancelSubscriptionItem
     */
    /** 
     * @ref bx_credits-cancel_subscription_item "cancel_subscription_item"
     */
    public function serviceCancelSubscriptionItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder)
    {
    	return true;
    }

    public function serviceGetProfileBalance($iProfileId = 0)
    {
        return $this->getProfileBalance($iProfileId);
    }

    public function serviceUpdateProfileBalance($iFirstPid, $fAmount, $iSecondPid = 0, $sOrder = '', $sInfo = '')
    {
        if(empty($sInfo))
            $sInfo = '_bx_credits_txt_history_info_service';

        return $this->updateProfileBalance($iFirstPid, $iSecondPid, $fAmount, $sOrder, $sInfo);
    }

    /**
     * Delete all content by profile 
     * @param $iProfileId profile id 
     * @return number of deleted items
     */
    public function serviceDeleteEntitiesByAuthor ($iProfileId)
    {
        $this->_oDb->deleteProfile(array('id' => $iProfileId));
        $this->_oDb->deleteHistory(array('first_pid' => $iProfileId));
    }


    /*
     * Common methods
     */
    public function convertC2S($fCredits, $bWithUnit = true)
    {
        $sResult = '';
        if($bWithUnit)
            $sResult .= $this->_oTemplate->getUnit();

        $iPrecision = $this->_oConfig->getPrecision();
        $sResult .= sprintf("%01." . $iPrecision . "f", round((float)$fCredits, $iPrecision));

        return $sResult;
    }

    public function getProfileBalance($iProfileId = 0)
    {
        if(empty($iProfileId))
            $iProfileId = bx_get_logged_profile_id();

        if(empty($iProfileId))
            return 0;

        return (float)$this->_oDb->getProfile(array('type' => 'balance', 'id' => $iProfileId));
    }

    public function updateProfileBalance($iFirstPid, $iSecondPid, $fAmount, $sOrder = '', $sInfo = '', $sData = '')
    {
        $CNF = &$this->_oConfig->CNF;

        if(empty($sOrder))
            $sOrder = $this->_oConfig->getOrder();

        if(empty($sData))
            $sData = serialize(array(
                'conversion' => $this->_oConfig->getConversionRateUse(),
                'precision' => $this->_oConfig->getPrecision()
            ));

        if(!$this->_oDb->updateProfileBalance($iFirstPid, $fAmount))
            return false;

        $fAmountAbs = abs($fAmount);
        $sDirection = $fAmount > 0 ? BX_CREDITS_DIRECTION_IN : BX_CREDITS_DIRECTION_OUT;

        $this->_oDb->insertHistory(array(
            'first_pid' => $iFirstPid,
            'second_pid' => $iSecondPid,
            'amount' => $fAmountAbs,
            'direction' => $sDirection,
            'order' => $sOrder,
            'data' => $sData,
            'info' => $sInfo,
            'date' => time()
        ));

        bx_alert($this->getName(), 'update_balance', 0, false, array(
            'first_pid' => $iFirstPid,
            'second_pid' => $iSecondPid,
            'amount' => $fAmountAbs,
            'direction' => $sDirection,
            'order' => $sOrder,
        ));

        $oSecondProfile = BxDolProfile::getInstanceMagic($iSecondPid);
        sendMailTemplate($CNF['ETEMPLATE_' . strtoupper($sDirection)], 0, $iFirstPid, array(
            'second_profile_name' => $oSecondProfile->getDisplayName(),
            'amount' => $fAmountAbs,
        ));

        return true;
    }

    public function processGrant($iProfileId, $fAmount, $sMessage = '')
    {
        $CNF = &$this->_oConfig->CNF;
        
        $oProfile = BxDolProfile::getInstance($iProfileId);
        if(!$oProfile)
            return array('code' => 1, 'msg' => '_bx_credits_err_profile_not_found');

        $sInfo = '_bx_credits_txt_history_info_grant';
        if(!$this->updateProfileBalance($iProfileId, 0, $fAmount, '', $sInfo))
            return array('code' => 2, 'msg' => '_bx_credits_err_cannot_update_balance');

        sendMailTemplate($CNF['ETEMPLATE_GRANTED'], 0, $iProfileId, array(
            'amount' => $fAmount,
            'message' => $sMessage
        ));

        bx_alert($this->getName(), 'granted', 0, false, array(
            'profile' => $iProfileId,
            'amount' => $fAmount,
        ));

        return array('code' => 0);
    }

    public function processWithdrawRequest($iProfileId, $fAmount, $sMessage = '')
    {
        $CNF = &$this->_oConfig->CNF;

        if(empty($iProfileId))
            $iProfileId = bx_get_logged_profile_id ();

        if(empty($iProfileId))
            return array('code' => 1, 'msg' => '_bx_credits_err_login_required');

        $oProfile = BxDolProfile::getInstance($iProfileId);
        if(!$oProfile)
            return array('code' => 1, 'msg' => '_bx_credits_err_login_required');

        $fBalance = $this->getProfileBalance($iProfileId);
        if($fAmount > $fBalance)
            return array('code' => 2, 'msg' => '_bx_credits_err_low_balance');

        $aResult = array('code' => 3, 'msg' => '_bx_credits_err_cannot_send');

        $oEmailTemplates = BxDolEmailTemplates::getInstance();
        if(!$oEmailTemplates)
            return $aResult;

        $fRate = $this->_oConfig->getConversionRateWithdraw();

        $aTemplateVars = array(
            'profile_link' => $oProfile->getUrl(),
            'profile_name' => $oProfile->getDisplayName(),
            'amount' => $fAmount,
            'rate' => $fRate,
            'message' => $sMessage,
        );
        $aTemplate = $oEmailTemplates->parseTemplate($CNF['ETEMPLATE_WITHDRAW_REQUESTED'], $aTemplateVars);
        if(!$aTemplate)
            return $aResult;

        $sEmail = $this->_oConfig->getWithdrawEmail();
        if(!sendMail($sEmail, $aTemplate['Subject'], $aTemplate['Body'], 0, array(), BX_EMAIL_SYSTEM, 'html', false, array(), false))
            return $aResult;

        bx_alert($this->getName(), 'withdraw_requested', 0, $iProfileId, array(
            'profile' => $iProfileId,
            'amount' => $fAmount,
            'rate' => $fRate
        ));

        return array('code' => 0);
    }

    public function processWithdrawConfirm($iProfileId, $fAmount, $sMessage = '')
    {
        $CNF = &$this->_oConfig->CNF;
        
        $oProfile = BxDolProfile::getInstance($iProfileId);
        if(!$oProfile)
            return array('code' => 1, 'msg' => '_bx_credits_err_profile_not_found');

        $fBalance = $this->getProfileBalance($iProfileId);
        if($fAmount > $fBalance)
            return array('code' => 2, 'msg' => '_bx_credits_err_low_balance');

        $sInfo = '_bx_credits_txt_history_info_withdraw';
        if(!$this->updateProfileBalance($iProfileId, 0, -$fAmount, '', $sInfo))
            return array('code' => 3, 'msg' => '_bx_credits_err_cannot_update_balance');

        sendMailTemplate($CNF['ETEMPLATE_WITHDRAW_SENT'], 0, $iProfileId, array(
            'amount' => $fAmount,
            'message' => $sMessage
        ));

        bx_alert($this->getName(), 'withdraw_sent', 0, false, array(
            'profile' => $iProfileId,
            'amount' => $fAmount,
        ));

        return array('code' => 0);
    }

    /*
     * Internal methods
     */
    protected function _serviceRegisterItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense, $sType)
    {
        $CNF = &$this->_oConfig->CNF;

    	$aItem = $this->serviceGetCartItem($iItemId);
        if(empty($aItem) || !is_array($aItem))
            return array();

        $aBundle = $this->_oDb->getBundle(array('type' => 'id', 'id' => $iItemId));
        if(empty($aBundle) || !is_array($aBundle))
            return array();

        $iTrial = 0;
        $sDuration = '';
        $sAction = 'register';
        if($sType == BX_CREDITS_ORDER_TYPE_RECURRING && isset($CNF['FIELD_DURATION_RECURRING'], $CNF['FIELD_TRIAL_RECURRING'])) {
            $iTrial = $aBundle[$CNF['FIELD_TRIAL_RECURRING']];
            $sDuration = $aBundle[$CNF['FIELD_DURATION_RECURRING']];

            if($this->_oDb->isOrderByPbo($iClientId, $iItemId, $sOrder))
                $sAction = 'prolong';
        }

        if(!$this->_oDb->{$sAction . 'Order'}($iClientId, $iItemId, $iItemCount, $sOrder, $sLicense, $sType, $sDuration, $iTrial))
            return array();

        $sInfo = '_bx_credits_txt_history_info_purchase';
        $fAmount = ((float)$aBundle[$CNF['FIELD_AMOUNT']] + (float)$aBundle[$CNF['FIELD_BONUS']]) * $iItemCount;
        if(!$this->updateProfileBalance($iClientId, 0, $fAmount, '', $sInfo))
            return array();

        bx_alert($this->getName(), 'order_' . $sAction, 0, false, array(
            'profile_id' => $iClientId,
            'bundle_id' => $iItemId,
            'product_id' => $iItemId,   //--- Alias for 'bundle_id'
            'count' => $iItemCount,
            'order' => $sOrder,
            'license' => $sLicense,
            'type' => $sType,
            'duration' => $sDuration,
            'trial' => $iTrial
        ));

        $oClient = BxDolProfile::getInstanceMagic($iClientId);
        $oSeller = BxDolProfile::getInstanceMagic($iSellerId);
        $sSellerUrl = $oSeller->getUrl();
        $sSellerName = $oSeller->getDisplayName();

        sendMailTemplate($CNF['ETEMPLATE_PURCHASED'], 0, $iClientId, array(
            'client_name' => $oClient->getDisplayName(),
            'bundle_name' => $aBundle[$CNF['FIELD_NAME']],
            'bundle_title' => _t($aBundle[$CNF['FIELD_TITLE']]),
            'bundle_url' => $this->_oConfig->getBundleUrl($aBundle),
            'vendor_url' => $sSellerUrl,
            'vendor_name' => $sSellerName,
            'order' => $sOrder,
            'license' => $sLicense,
            'notes' => _t('_bx_credits_txt_purchased_note', $sSellerUrl, $sSellerName),
        ));

        return $aItem;
    }

    protected function _serviceUnregisterItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrder, $sLicense, $sType)
    {
        $CNF = &$this->_oConfig->CNF;

        $aBundle = $this->_oDb->getBundle(array('type' => 'id', 'id' => $iItemId));
        if(empty($aBundle) || !is_array($aBundle))
            return false;

        if(!$this->_oDb->unregisterOrder($iClientId, $iItemId, $sOrder, $sLicense, $sType))
            return false;

        $fAmount = (float)$aBundle[$CNF['FIELD_AMOUNT']] + (float)$aBundle[$CNF['FIELD_BONUS']];
        $aProfile = $this->_oDb->getProfile(array('type' => 'id', 'id' => $iClientId));
        if(!empty($aProfile) && is_array($aProfile))
            $this->_oDb->updateProfile(array('balance' => (float)$aProfile['balance'] - $fAmount), array('id' => $aProfile['id']));

        bx_alert($this->getName(), 'order_unregister', 0, false, array(
            'profile_id' => $iClientId,
            'bundle_id' => $iItemId,
            'product_id' => $iItemId,   //--- Alias for 'bundle_id'
            'count' => $iItemCount,
            'order' => $sOrder,
            'license' => $sLicense,
            'type' => $sType,
        ));

        return true;
    }

    protected function _getBlockOrders($sType) 
    {
        $CNF = &$this->_oConfig->CNF;

        $sGrid = $CNF['OBJECT_GRID_ORDERS_' . strtoupper($sType)];
        $oGrid = BxDolGrid::getObjectInstance($sGrid);
        if(!$oGrid)
            return '';

        return array(
            'content' => $oGrid->getCode(),
            'menu' => $CNF['OBJECT_MENU_MANAGE_SUBMENU']
        );
    }

    protected function _getBlockHistory($sType) 
    {
        $CNF = &$this->_oConfig->CNF;

        $sGrid = $CNF['OBJECT_GRID_HISTORY_' . strtoupper($sType)];
        $oGrid = BxDolGrid::getObjectInstance($sGrid);
        if(!$oGrid)
            return '';

        return array(
            'content' => $oGrid->getCode(),
            'menu' => $CNF['OBJECT_MENU_MANAGE_SUBMENU']
        );
    }
}

/** @} */
