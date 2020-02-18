<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

/**
 * System services.
 */
class BxBaseServices extends BxDol implements iBxDolProfileService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function serviceIsSafeService($s)
    {
        $sService = bx_gen_method_name($s);
        $aSafeServices = $this->serviceGetSafeServices();
        return isset($aSafeServices[$sService]);
    }

    public function serviceGetSafeServices()
    {
        return array(
            'GetCreatePostForm' => 'BxBaseServices',
            'KeywordSearch' => 'BxBaseServices',
            'Cmts' => 'BxBaseServices',

            'CreateAccountForm' => 'BxBaseServiceAccount',
            'ForgotPassword' => 'BxBaseServiceAccount',

            'CategoriesList' => 'BxBaseServiceCategory',

            'Test' => 'BxBaseServiceLogin',
            'MemberAuthCode' => 'BxBaseServiceLogin',
            'LoginForm' => 'BxBaseServiceLogin',
        
            'KeywordsCloud' => 'BxBaseServiceMetatags',

            'ProfileMembership' => 'BxBaseServiceProfiles',
            'ProfileNotifications' => 'BxBaseServiceProfiles',
            'GetCountOnlineProfiles' => 'BxBaseServiceProfiles',

            'GetChartGrowth' => 'BxBaseChartServices',
            'GetChartStats' => 'BxBaseChartServices',

            'GetCartItemsCount' => 'BxBasePaymentsServices',
            'GetOrdersCount' => 'BxBasePaymentsServices',
        );
    }
    public function serviceProfileUnit ($iContentId, $aParams = array())
    {
        return $this->_serviceProfileFunc('getUnit', $iContentId, $aParams);
    }

    public function serviceHasImage ($iContentId)
    {
        return false;
    }

    public function serviceProfilePicture ($iContentId)
    {
        return $this->_serviceProfileFunc('getPicture', $iContentId);
    }

    public function serviceProfileAvatar ($iContentId)
    {
        return $this->_serviceProfileFunc('getAvatar', $iContentId);
    }
	
	public function serviceProfileCover ($iContentId)
    {
        return $this->_serviceTemplateFunc('urlCover', $iContentId);
    }

    public function serviceProfileEditUrl ($iContentId)
    {
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=account-settings-info');
    }

    public function serviceProfileThumb ($iContentId)
    {
        return $this->_serviceProfileFunc('getThumb', $iContentId);
    }

    public function serviceProfileIcon ($iContentId)
    {
        return $this->_serviceProfileFunc('getIcon', $iContentId);
    }

    public function serviceProfileName ($iContentId)
    {
        return $this->_serviceProfileFunc('getDisplayName', $iContentId);
    }

    public function serviceProfileUrl ($iContentId)
    {
        return $this->_serviceProfileFunc('getUrl', $iContentId);
    }

    /**
     * @see iBxDolProfileService::serviceCheckAllowedProfileView
     */ 
    public function serviceCheckAllowedProfileView($iContentId)
    {
        return _t('_Access denied');
    }

    /**
     * @see iBxDolProfileService::serviceCheckAllowedProfileContact
     */ 
    public function serviceCheckAllowedProfileContact($iContentId)
    {
        return _t('_Access denied');
    }

    /**
     * @see iBxDolProfileService::serviceCheckAllowedPostInProfile
     */ 
    public function serviceCheckAllowedPostInProfile($iContentId)
    {
        return _t('_Access denied');
    }

    /**
     * @see iBxDolProfileService::serviceGetSpaceTitle
     */ 
    public function serviceGetSpaceTitle()
    {
        return '';
    }

    /**
     * @see iBxDolProfileService::serviceGetParticipatingProfiles
     */ 
    public function serviceGetParticipatingProfiles($iProfileId, $aConnectionObject = false)
    {
        return array();
    }
    
    /**
     * @page service Service Calls
     * @section bx_system_general System Services 
     * @subsection bx_system_general-general General
     * @subsubsection bx_system_general-get_create_post_form get_create_post_form
     * 
     * @code bx_srv('system', 'get_create_post_form', [false, "bx_posts"], 'TemplServices'); @endcode
     * @code {{~system:get_create_post_form:TemplServices[false,"bx_posts"]~}} @endcode
     * 
     * Get United Create Post form.
     * @param $mixedContextId - context which the post will be created in:
     *      - false = 'Public' post form;
     *      - 0 = 'Profile' post form, which allows to post in your own profile and connections;
     *      - n = 'Context' post form, which allows to post in context (3d party profile, group, event, etc).
     * @param $sDefault - tab selected by default.
     * @param $aCustom - an array with custom paramaters.
     * @return string with form content
     * 
     * @see BxBaseServices::serviceGetCreatePostForm
     */
    /** 
     * @ref bx_system_general-get_create_post_form "get_create_post_form"
     */
    public function serviceGetCreatePostForm($mixedContextId = false, $sDefault = '', $aCustom = array())
    {
    	if(!isLogged() || ($mixedContextId !== false && !is_numeric($mixedContextId)))
            return '';

        if($mixedContextId !== false)
            $mixedContextId = (int)(!empty($mixedContextId) ? -$mixedContextId : bx_get_logged_profile_id());

        $bContext = $mixedContextId !== false;
        if($bContext && ($oContextProfile = BxDolProfile::getInstance(abs($mixedContextId))) !== false)
            if($oContextProfile->checkAllowedPostInProfile() !== CHECK_ACTION_RESULT_ALLOWED)
                return '';

        $sTitle = _t('_sys_page_block_title_create_post' . (!$bContext ? '_public' : ($mixedContextId < 0 ? '_context' : '')));
        $sPlaceholder = _t('_sys_txt_create_post_placeholder', BxDolProfile::getInstance()->getDisplayName());

    	$oMenu = BxDolMenu::getObjectInstance('sys_create_post');

    	$aMenuItems = $oMenu->getMenuItems();
    	if(empty($aMenuItems) || !is_array($aMenuItems))
            return '';

    	if(empty($sDefault)) {
            $aDefault = array_shift($aMenuItems);
            $sDefault = !empty($aDefault['module']) ? $aDefault['module'] : $aDefault['name'];
    	}
    	$oMenu->setSelected($sDefault, $sDefault);

    	$oTemplate = BxDolTemplate::getInstance();
    	$oTemplate->addJs(array('BxDolCreatePost.js'));

    	$sJsObject = 'oBxDolCreatePost';
        $sJsContent = $oTemplate->_wrapInTagJsCode("var " . $sJsObject . " = new BxDolCreatePost(" . json_encode(array(
            'sObjName' => $sJsObject,
            'sRootUrl' => BX_DOL_URL_ROOT,
            'sDefault' => $sDefault,
            'iContextId' => $bContext ? $mixedContextId : 0,
            'oCustom' => $aCustom
        )) . ");");

    	return array('content' => BxDolTemplate::getInstance()->parseHtmlByName('create_post_form.html', array(
            'default' => $sDefault,
            'title' => $sTitle,
            'placeholder' => $sPlaceholder,
            'user_thumb' => BxDolProfile::getInstance()->getUnit(0, array('template' => 'unit_wo_info')),
            'menu' => $oMenu->getCode(),
            'form' => BxDolService::call($sDefault, 'get_create_post_form', array(array('context_id' => $mixedContextId, 'ajax_mode' => true, 'absolute_action_url' => true, 'custom' => $aCustom))),
            'js_object' => $sJsObject,
            'js_content' => $sJsContent
    	)));
    }

    /**
     * @see iBxDolProfileService::serviceCheckSpacePrivacy
     */ 
    public function serviceCheckSpacePrivacy($iContentId)
    {
        return _t('_Access denied');
    }
    
    public function serviceFormsHelper ()
    {
        return new BxTemplAccountForms();
    }

    public function serviceActAsProfile ()
    {
        return false;
    }

    public function servicePrepareFields ($aFieldsProfile)
    {
        return $aFieldsProfile;
    }

    public function serviceProfilesSearch ($sTerm, $iLimit)
    {
        $oDb = BxDolAccountQuery::getInstance();
        $aRet = array();
        $a = $oDb->searchByTerm($sTerm, $iLimit);
        foreach ($a as $r)
            $aRet[] = array ('label' => $this->serviceProfileName($r['content_id']), 'value' => $r['profile_id']);
        return $aRet;
    }

    /**
     * @page service Service Calls
     * @section bx_system_general System Services 
     * @subsection bx_system_general-general General
     * @subsubsection bx_system_general-keyword_search keyword_search
     * 
     * @code bx_srv('system', 'keyword_search', ["bx_posts", ["keyword" => "test"}], 'TemplServices'); @endcode
     * 
     * @code {{~system:keyword_search:TemplServices["bx_posts", {"keyword":"test"}]~}} @endcode
     * @code {{~system:keyword_search:TemplServices["bx_albums", {"meta_type": "location_country", "keyword": "AU"}, "unit.html"]~}} @endcode
     * @code {{~system:keyword_search:TemplServices["bx_albums", {"meta_type": "location_country_state", "state":"NSW", "keyword": "AU"}, "unit.html"]~}} @endcode
     * @code {{~system:keyword_search:TemplServices["bx_albums", {"meta_type": "location_country_city", "state":"NSW", "city":"Manly", "keyword": "AU"}, "unit.html"]~}} @endcode
     * @code {{~system:keyword_search:TemplServices["bx_posts", {"meta_type": "mention", "keyword": 2}, "unit_gallery.html"]~}} @endcode
     * @code {{~system:keyword_search:TemplServices["bx_posts", {"cat": "bx_posts_cats", "keyword": 3}, "unit_gallery.html"]~}} @endcode
     * 
     * Search by keyword
     * @param $sSection - search object to search in, usually module name, for example: bx_posts
     * @param $aCondition - condition for search, supported conditions: 
     *          - search by keyword: ["keyword" => "test"]
     *          - search by country: ["meta_type" => "location_country", "keyword" => "AU"]
     *          - search by country and state: ["meta_type": "location_country_state", "state":"NSW", "keyword": "AU"]
     *          - search by country, state and city: ["meta_type": "location_country_city", "state":"NSW", "city":"Manly", "keyword": "AU"]
     *          - search for mentions: ["meta_type" => "mention", "keyword" => 2]
     *          - search in category: ["cat": "bx_posts_cats", "keyword": 3]
     * @param $sTemplate - template for displaying search results, for example: unit.html
     * @param $iStart - paginate, display records starting from this number
     * @param $iPerPage - paginate, display this number of records per page
     * @param $bLiveSearch - search results like in live search
     * 
     * @see BxBaseServices::serviceKeywordSearch
     */
    /** 
     * @ref bx_system_general-keyword_search "keyword_search"
     */
    public function serviceKeywordSearch ($sSection, $aCondition, $sTemplate = '', $iStart = 0, $iPerPage = 0, $bLiveSearch = 0)
    {
        if (!$sSection || !isset($aCondition['keyword']))
            return '';

        $sClass = 'BxTemplSearch';

        $sElsName = 'bx_elasticsearch';
        $sElsMethod = 'is_configured';
        if(BxDolRequest::serviceExists($sElsName, $sElsMethod) && BxDolService::call($sElsName, $sElsMethod)) {
             $oModule = BxDolModule::getInstance($sElsName);

             bx_import('Search', $oModule->_aModule);
             $sClass = 'BxElsSearch';
        }

        $oSearch = new $sClass(array($sSection));
        $oSearch->setLiveSearch($bLiveSearch);
        $oSearch->setMetaType(isset($aCondition['meta_type']) ? $aCondition['meta_type'] : '');
        $oSearch->setCategoryObject(isset($aCondition['cat']) ? $aCondition['cat'] : '');
        $oSearch->setCustomSearchCondition($aCondition);
        $oSearch->setRawProcessing(true);
        $oSearch->setCustomCurrentCondition(array(
            'paginate' => array (
                'start' => $iStart,
                'perPage' => $iPerPage ? $iPerPage : BX_DOL_SEARCH_RESULTS_PER_PAGE_DEFAULT,
            )));
        if ($sTemplate)
            $oSearch->setUnitTemplate($sTemplate);
        
        return $oSearch->response();
    }
    
    /**
     * @page service Service Calls
     * @section bx_system_general System Services 
     * @subsection bx_system_general-general General
     * @subsubsection bx_system_general-cmts cmts
     * 
     * @code bx_srv('system', 'cmts', ["sys_blocks", 1], 'TemplServices'); @endcode
     * 
     * @code {{~system:cmts:TemplServices["sys_blocks", 1]~}} @endcode
     * 
     * Comments block
     * @param $sObject - comments object name
     * @param $sId - content id assiciated tith the comments
     * 
     * @see BxBaseServices::serviceCmts
     */
    /** 
     * @ref bx_system_general-cmts "cmts"
     */
    public function serviceCmts ($sObject, $sId)
    {
        $o = BxDolCmts::getObjectInstance($sObject, $sId);
        if (!$o || !$o->isEnabled())
            return '';
        return $o->getCommentsBlock(array(), array('in_designbox' => false, 'show_empty' => true));
    }

    public function _serviceProfileFunc ($sFunc, $iContentId, $aParams = array())
    {
        if (!$iContentId)
            return false;
        if (!($oAccount = BxDolAccount::getInstance($iContentId)))
            return false;

        return $oAccount->$sFunc(false, $aParams);
    }

    public function serviceAlertResponseProcessInstalled()
    {
        BxDolTranscoderImage::registerHandlersSystem();
    }

    public function serviceAlertResponseProcessStorageChange ($oAlert)
    {
        if ('sys_storage_default' != $oAlert->aExtras['option'])
            return;

        $aStorages = BxDolStorageQuery::getStorageObjects();
        foreach ($aStorages as $r) {
            if (0 == $r['current_size'] && 0 == $r['current_number'] && ($oStorage = BxDolStorage::getObjectInstance($r['object'])))
                $oStorage->changeStorageEngine($oAlert->aExtras['value']);
        }

    }

    public function serviceGetOptionsProfileBot()
    {
        $aResult = array(
            array('key' => '', 'value' => _t('_Select_one'))
        );

        $aAccountsIds = BxDolAccountQuery::getInstance()->getOperators();
        foreach($aAccountsIds as $iAccountId) {
            $aProfilesIds = BxDolAccount::getInstance($iAccountId)->getProfilesIds(true, false);
            foreach($aProfilesIds as $iProfileId) {
                $oProfile = BxDolProfile::getInstance($iProfileId);
                $aResult[] = array(
                    'key' => $iProfileId,
                    'value' => _t('_sys_profile_with_type', $oProfile->getDisplayName(), $oProfile->getModule()),
                );
            }
        }

        return $aResult;
    }
    
    public function serviceGetOptionsModuleListForPrivacySelector()
    {
        $aResult = array();
        $BxDolModuleQuery = BxDolModuleQuery::getInstance();
        $aModules = $BxDolModuleQuery->getModulesBy(array('type' => 'modules', 'active' => 1));
        foreach($aModules as $aModule){
            $oModule = BxDolModule::getInstance($aModule['name']);
            if($oModule instanceof iBxDolContentInfoService){
                if (!BxDolRequest::serviceExists($aModule['name'], 'act_as_profile'))
                    continue;
                $aResult[$aModule['name']] = $aModule['title'];
            }
        }
        return $aResult;
    }
    

    public function serviceGetOptionsEmbedDefault()
    {
        $aResults = array(
            '' => _t('_None')
        );

        $aObjects = BxDolEmbedQuery::getObjects();
        foreach($aObjects as $aObject)
            $aResults[$aObject['object']] = $aObject['title'];

        return $aResults;
    }

    public function serviceGetOptionsRelations()
    {
        $aModules = BxDolModuleQuery::getInstance()->getModulesBy(array('type' => 'modules', 'active' => 1));

        $aProfiles = array();
        foreach($aModules as $aModule) {
            $sMethod = 'act_as_profile';
            if(!BxDolRequest::serviceExists($aModule['name'], $sMethod) || !BxDolService::call($aModule['name'], $sMethod))
                continue;

            $aProfiles[$aModule['name']] = _t('_' . $aModule['name']);
        }

        $aResults = array();
        foreach($aProfiles as $sName1 => $sTitle1)
            foreach($aProfiles as $sName2 => $sTitle2)
                $aResults[$sName1 . '_' . $sName2] = $sTitle1 . ' - ' . $sTitle2;

        return $aResults;
    }
    
    public function serviceGetBadge($aBadge, $bIsCompact = false)
    {
        $sClass = '';
        if ($bIsCompact){
            $aBadge['is_icon_only'] = 1;
        }
        if ($aBadge['is_icon_only'] == 1){
            $sClass = 'bx-badge-compact';
        }
        
        return BxDolTemplate::getInstance()->parseHtmlByName('badge.html', array(
            'bx_if:content' => array(
                'condition' => $aBadge['is_icon_only'] != '1',
                'content' => array('content' => _t($aBadge['text'])),
            ),
            'bx_if:icon' => array(
                'condition' => $aBadge['icon'] != '',
                'content' => array('content' => BxDolTemplate::getInstance()->getIcon($aBadge['icon'], array('class' => 'bx-badge-icon'))),
            ),
            'title' => $aBadge['text'],
            'style' => $aBadge['color'] != '' ? 'style = "background-color: ' . $aBadge['color'] . '"' : '',
            'class' => $sClass,
            )
    	);
    }
}

/** @} */
