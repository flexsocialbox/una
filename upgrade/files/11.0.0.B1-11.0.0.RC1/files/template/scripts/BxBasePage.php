<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');

/**
 * Page representation.
 * @see BxDolPage
 */
class BxBasePage extends BxDolPage
{
    protected $_oTemplate;

    protected $_sStorage; //--- Storage object for page's images like custom cover, HTML block attachments, etc.
    protected $_oPageCacheObject = null;

    public function __construct ($aObject, $oTemplate)
    {
        parent::__construct ($aObject);

        if ($oTemplate)
            $this->_oTemplate = $oTemplate;
        else
            $this->_oTemplate = BxDolTemplate::getInstance();

		$this->_sStorage = 'sys_images';
    }

    /**
     * Very similar to BxBasePage::getCode
     * but adds css and js files which are needed for the corect page display
     */ 
    public function getCodeDynamic ()
    {
        $oTemplate = BxDolTemplate::getInstance();

        // get js&css before the page code is generated
        $aCssBefore = $oTemplate->getCss();
        $aJsBefore = $oTemplate->getJs();

        // generate page code
        $sContent = $this->getCode();

        // get js&css after the page code is generated
        $aCssAfter = $oTemplate->getCss();
        $aJsAfter = $oTemplate->getJs();

        // compare files which were added before and after page code is generated
        $f = function ($a1, $a2) {
            return strcasecmp($a1['url'], $a2['url']);
        };
        $aCssNew = array_udiff($aCssAfter, $aCssBefore, $f);
        $aJsNew = array_udiff($aJsAfter, $aJsBefore, $f);

        // add newly added js&css files in static mode
        $sCss = $sJs = '';
        foreach ($aCssNew as $a)
            $sCss .= $oTemplate->addCss($a['url'], true);
        foreach ($aJsNew as $a)
            $sJs .= $oTemplate->addJs($a['url'], true);

        return $sJs . $sCss . $sContent;
    }
    
    /**
     * Get page code with automatic caching, adding necessary css/js files and system template vars.
     * @return string.
     */
    public function getCode ()
    {
        if (bx_get('dynamic') && ($iBlockId = (int)bx_get('pageBlock'))) {

            if (!$this->_isVisiblePage($this->_aObject)) {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }

            bx_alert('system', 'page_output_block', 0, false, array(
                'page_name' => $this->_sObject,
                'page_object' => $this,
                'page_query' => $this->_oQuery,
                'block_id' => (int)$iBlockId,
            ));

            header( 'Content-type:text/html;charset=utf-8' );
            echo $this->_getBlockOnlyCode($iBlockId);
            exit;
        }

        if (!$this->_isVisiblePage($this->_aObject))
            return $this->_getPageAccessDeniedMsg ();

        $this->_addJsCss();

        $this->_addSysTemplateVars();

        $this->_selectMenu();

        $this->_setSubmenu(array());

        if (!getParam('sys_page_cache_enable') || !$this->_aObject['cache_lifetime']) {
            $sPageCode = $this->_getPageCode();
        }
        else {
            $oCache = $this->_getPageCacheObject();
            $sKey = $this->_getPageCacheKey();

            $mixedRet = $oCache->getData($sKey, $this->_aObject['cache_lifetime']);

            if ($mixedRet !== null) {
                $sPageCode = $mixedRet;
            } else {
                $sPageCode = $this->_getPageCode();
                $oCache->setData($sKey, $sPageCode, $this->_aObject['cache_lifetime']);
            }
        }
        
        bx_alert('system', 'page_output', 0, false, array(
            'page_name' => $this->_sObject,
            'page_object' => $this,
            'page_query' => $this->_oQuery,
            'page_code' => &$sPageCode,
        ));

        return $sPageCode;
    }

    /**
     * Is page cover enabled.
     * @return string
     */
    public function isPageCover()
    {
        $bResult = false;
        switch((int)$this->_aObject['cover']) {
            case 1: //--- Enabled for all
                $bResult = true;
                break;

            case 2: //--- Enabled for visitors only
                $bResult = !isLogged();
                break;

            case 3: //--- Enabled for members only
                $bResult = isLogged();
                break;
        }

    	return $bResult;
    }

    public function setPageCover($bCover = true)
    {
        $this->_aObject['cover'] = (bool)$bCover;
    }

	public function getPageCoverImage($bTranscoder = true)
    {
    	$iId = (int)$this->_aObject['cover_image'];
    	if(empty($iId)) {
    		$iId = (int)getParam('sys_site_cover_common');
    		if(empty($iId))
    			return array();
    	}

    	$aResult = array(
    		'id' => $iId
    	);
    	if($bTranscoder)
    		$aResult['transcoder'] = BX_DOL_TRANSCODER_OBJ_COVER;
    	else 
    		$aResult['object'] = $this->_sStorage;

    	return $aResult;
    }

    public function getPageCoverParams()
    {
        $oMenuSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');
        if($oMenuSubmenu) {
            $aParams = $oMenuSubmenu->getPageCoverParams();
            if(!empty($aParams) && is_array($aParams))
                return $aParams;
        }

    	return array (
            'title' => $this->_getPageTitle(),
            'actions' => '',
            'bx_if:image' => array (
                'condition' => false,
                'content' => array(),
            ),
            'bx_if:icon' => array (
                'condition' => false,
                'content' => array(),
            ),
        );
    }

    /**
     * Get block title.
     * @return string
     */
    public function getBlockTitle ($aBlock)
    {
        return $this->_replaceMarkers(_t($aBlock['title']), array('block_id' => $aBlock['id']));
    }

    /**
     * Get page array with all cells and blocks
     */
    public function getPage ()
    {
        return array(
            'id' => $this->_aObject['id'],
            'title' => $this->_getPageTitle(),
            'uri' => $this->_aObject['uri'],
            'author' => $this->_aObject['author'],
            'added' => $this->_aObject['added'],
            'module' => $this->getModule (),
            'type' => $this->getType (),
            'layout' => $this->_aObject['layout_id'],
            'elements' => $this->getPageBlocks (),
        );
    }

    public function getPageBlocks ()
    {
        $aFieldsUnset = array('cell_id', 'active', 'copyable', 'deletable', 'object', 'text', 'text_updated', 'title_system', 'visible_for_levels');
        $aCells = $this->_oQuery->getPageBlocks();
        foreach ($aCells as $sKey => $aCell) {
            foreach ($aCell as $i => $aBlock) {                
                if (!$this->_isVisibleBlock($aBlock))
                    unset($aCells[$sKey][$i]);
                
                $this->processPageBlock($aCells[$sKey][$i], true);
                $aBlock = $aCells[$sKey][$i];

                $sFunc = '_getBlock' . ucfirst($aBlock['type']);
                $aCells[$sKey][$i]['content'] = method_exists($this, $sFunc) ? $this->$sFunc($aBlock) : $aBlock['content'];
                $aCells[$sKey][$i]['title'] = $this->getBlockTitle($aBlock);
                foreach ($aFieldsUnset as $s)
                    unset($aCells[$sKey][$i][$s]);
            }
        }
        return $aCells;
    }

    /**
     * Get page code vars
     * @return string
     */
    protected function _getPageCodeVars ()
    {
    	$aHiddenOn = array(
            pow(2, BX_DB_HIDDEN_PHONE - 1) => 'bx-def-media-phone-hide',
            pow(2, BX_DB_HIDDEN_TABLET - 1) => 'bx-def-media-tablet-hide',
            pow(2, BX_DB_HIDDEN_DESKTOP - 1) => 'bx-def-media-desktop-hide',
            pow(2, BX_DB_HIDDEN_MOBILE - 1) => 'bx-def-mobile-app-hide'
        );

        $aVars = array (
            'page_id' => 'bx-page-' . $this->_aObject['uri'],
        );
        $aBlocks = $this->_oQuery->getPageBlocks();
        foreach ($aBlocks as $sKey => $aCell) {
            $sCell = '';
            foreach ($aCell as $aBlock) {
                $this->processPageBlock($aBlock, false);

                $sContentWithBox = $this->_getBlockCode($aBlock);

            	$sHiddenOn = '';
                if(!empty($aBlock['hidden_on']))
                    foreach($aHiddenOn as $iHiddenOn => $sClass)
                        if((int)$aBlock['hidden_on'] & $iHiddenOn)
                            $sHiddenOn .= ' ' . $sClass;

                if ($sContentWithBox)
                    $sCell .= '<div class="bx-page-block-container bx-def-padding-sec-topbottom' . $sHiddenOn . '" id="bx-page-block-' . $aBlock['id'] . '">' . $sContentWithBox . '</div>';
            }
            $aVars[$sKey] = $sCell;
        }

        return $aVars;
    }

    /**
     * Process block values, especially if someting need to be overrided 
     */
    protected function processPageBlock(&$aBlock, $bApi = false) 
    {

    }

    /**
     * Get page code only.
     * @return string
     */
    protected function _getPageCode ()
    {
        $aVars = $this->_getPageCodeVars ();
        return $this->_oTemplate->parseHtmlByName($this->_aObject['template'], $aVars);
    }

    /**
     * Get one block code only.
     * @return string
     */
    protected function _getBlockOnlyCode ($iBlockId)
    {
        if (!($aBlock = $this->_oQuery->getPageBlock((int)$iBlockId)))
            return '';
        return $this->_getBlockCode($aBlock);
    }

    /**
     * Get block code.
     * @return string
     */
    protected function _getBlockCode($aBlock)
    {
        $sContentWithBox = '';
        $oFunctions = $this->_oTemplate->getTemplateFunctions();

        if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->beginPageBlock(_t($aBlock['title']), $aBlock['id']);

        $sFunc = '_getBlock' . ucfirst($aBlock['type']);
        if ($this->_isVisibleBlock($aBlock) && method_exists($this, $sFunc)) {
            $mixedContent = $this->$sFunc($aBlock);

            $sTitle = $this->getBlockTitle($aBlock);

            if (is_array($mixedContent) && !empty($mixedContent['content'])) {
				$sContentWithBox = $oFunctions->designBoxContent(
                	isset($mixedContent['title']) ? $mixedContent['title'] : $sTitle,
                    $mixedContent['content'],
                    isset($mixedContent['designbox_id']) ? $mixedContent['designbox_id'] : $aBlock['designbox_id'],
                    isset($mixedContent['menu']) ? $mixedContent['menu'] : false
				);
            } 
            elseif (is_string($mixedContent) && !empty($mixedContent)) {                    
                $sContentWithBox = $oFunctions->designBoxContent($sTitle, $mixedContent, $aBlock['designbox_id']);
            }
        }

        if (isset($GLOBALS['bx_profiler'])) $GLOBALS['bx_profiler']->endPageBlock($aBlock['id'], $sContentWithBox ? false : true, false );

        return $sContentWithBox;
    }

    /**
     * Add necessary js and css files.
     */
    protected function _addJsCss()
    {
        $this->_oTemplate->addCss('page_layouts.css');
    }

    /**
     * Set system template variables, like page title, meta description, meta keywords and meta robots.
     */
    protected function _addSysTemplateVars ()
    {
        $oTemplate = BxDolTemplate::getInstance();

        $sPageTitle = $this->_getPageTitle();
        if ($sPageTitle)
            $oTemplate->setPageHeader ($sPageTitle);

        $sMetaDesc = $this->_getPageMetaDesc();
        if ($sMetaDesc)
            $oTemplate->setPageDescription ($sMetaDesc);

        $sMetaRobots = $this->_getPageMetaRobots();
        if ($sMetaRobots)
            $oTemplate->setPageMetaRobots ($sMetaRobots);

        $sMetaImage = $this->_getPageMetaImage();
        if ($sMetaImage)
            $oTemplate->addPageMetaImage($sMetaImage);

        $sMetaKeywords = $this->_getPageMetaKeywords();
        if ($sMetaKeywords)
            $oTemplate->addPageKeywords ($sMetaKeywords);
    }

    /**
     * Select menu from page properties.
     */
    protected function _selectMenu ()
    {
        BxDolMenu::setSelectedGlobal ($this->_aObject['module'], $this->_aObject['uri']);
    }

    /**
     * Set page submenu if it's specified
     */
    protected function _setSubmenu ($aParams)
    {
        if(empty($this->_aObject['submenu']))
            return;

        $oMenuSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');
        if(!$oMenuSubmenu)
            return;

        $oMenuSubmenu->setObjectSubmenu($this->_aObject['submenu'], $aParams);
    }

    /**
     * Get content for 'raw' block type.
     * @return string
     */
    protected function _getBlockRaw ($aBlock)
    {
        $s = '<div class="bx-page-raw-container">' . BxDolTemplate::getInstance()->parseHtmlByContent($aBlock['content'], array()) . '</div>';
        $s = $this->_replaceMarkers($s, array('block_id' => $aBlock['id']));
        $s = bx_process_macros($s);
        return $s;
    }

    /**
     * Get content for 'custom' block type.
     * @return string
     */
    protected function _getBlockCustom ($aBlock)
    {
        $s = '<div class="bx-page-custom-container">' . BxDolTemplate::getInstance()->parseHtmlByContent($aBlock['content'], array()) . '</div>';
        $s = $this->_replaceMarkers($s, array('block_id' => $aBlock['id']));
        $s = bx_process_macros($s);
        return $s;
    }

    /**
     * Get content for 'html' block type.
     * @return string
     */
    protected function _getBlockHtml ($aBlock)
    {
        $s = '<div class="bx-page-html-container">' . $aBlock['content'] . '</div>';
        $s = $this->_replaceMarkers($s, array('block_id' => $aBlock['id']));
        $s = bx_process_macros($s);
        return $s;
    }

    /**
     * Get content for 'wiki' block type.
     * @return string
     */
    protected function _getBlockWiki ($aBlock)
    {
        $oWiki = BxDolWiki::getObjectInstance($this->_aObject['module']);
        if (!$oWiki) {
            $sContent = _t('_sys_wiki_error_missing_wiki_object', $this->_aObject['module']);
        } 
        else {
            $sContent = $oWiki->getBlockContent($aBlock['id'], false, (int)bx_get($aBlock['id'].'rev') ? (int)bx_get($aBlock['id'].'rev') : false);
        }

        $s = '<div id="bx-page-wiki-container-' . $aBlock['id'] . '" class="bx-page-wiki-container">' . $sContent . '</div>';
        $s = $this->_replaceMarkers($s, array('block_id' => $aBlock['id']));
        $s = bx_process_macros($s);
        return $s;
    }

    /**
     * Get content for 'lang' block type.
     * @return string
     */
    protected function _getBlockLang ($aBlock)
    {
        $s = '<div class="bx-page-lang-container">' . _t(trim($aBlock['content'])) . '</div>';
        $s = $this->_replaceMarkers($s, array('block_id' => $aBlock['id']));
        $s = bx_process_macros($s);
        return $s;
    }

    /**
     * Get content for 'image' block type.
     * @return string
     */
    protected function _getBlockImage ($aBlock)
    {
        if (empty($aBlock['content']))
            return false;

        list($iFileId, $sAlign ) = explode('#', $aBlock['content']);
        $iFileId = (int)$iFileId;
        if (!$iFileId)
            return false;

        $oStorage = BxDolStorage::getObjectInstance(BX_DOL_STORAGE_OBJ_IMAGES);
        if (!$oStorage)
            return false;

        $sUrl = $oStorage->getFileUrlById($iFileId);
        if (!$sUrl)
            return false;

        $sStyleAdd = '';
        if ('center' == $sAlign || 'left' == $sAlign || 'right' == $sAlign)
            $sStyleAdd = 'style="text-align:' . $sAlign . '"';

        return '<div class="bx-page-image-container" ' . $sStyleAdd . '><img src="' . $sUrl . '" /></div>';
    }

    /**
     * Get content for 'rss' block type.
     * @return string
     */
    protected function _getBlockRss ($aBlock)
    {
        if (empty($aBlock['content']))
            return false;

        list( $sUrl, $iNum ) = explode('#', $aBlock['content']);
        $iNum = (int)$iNum;

        return BxDolRss::getObjectInstance('sys_page_block')->getHolder($aBlock['id'], $iNum);
    }

    /**
     * Get content for 'menu' block type.
     * @return string
     */
    protected function _getBlockMenu ($aBlock)
    {
        $oMenu = BxTemplMenu::getObjectInstance($aBlock['content']);
        return $oMenu ? $oMenu->getCode () : '';
    }

    /**
     * Get content for 'service' block type.
     * @return string
     */
    protected function _getBlockService ($aBlock)
    {
        return BxDolService::callSerialized($aBlock['content'], $this->_aMarkers);
    }

    /**
     * Get page title.
     * @return string
     */
    protected function _getPageTitle()
    {
        return $this->_replaceMarkers(_t($this->_aObject['title']));
    }

    /**
     * Get page meta description.
     * @return string
     */
    protected function _getPageMetaDesc()
    {
        return $this->_replaceMarkers(_t($this->_aObject['meta_description']));
    }

    /**
     * Get page meta image.
     * @return string
     */
    protected function _getPageMetaImage()
    {
        return '';
    }
    
    /**
     * Get page meta keywords.
     * @return string
     */
    protected function _getPageMetaKeywords()
    {
        return $this->_replaceMarkers(_t($this->_aObject['meta_keywords']));
    }

    /**
     * Get page meta robots.
     * @return string
     */
    protected function _getPageMetaRobots()
    {
        return _t($this->_aObject['meta_robots']);
    }

    /**
     * Get access denied message HTML.
     * @return string
     */
    protected function _getPageAccessDeniedMsg ()
    {
        return MsgBox(_t('_Access denied'));
    }

    /**
     * Get page cache object.
     * @return cache object instance
     */
    protected function _getPageCacheObject ()
    {
        if ($this->_oPageCacheObject != null) {
            return $this->_oPageCacheObject;
        } else {
            $sEngine = getParam('sys_page_cache_engine');
            $this->_oPageCacheObject = bx_instance ('BxDolCache' . $sEngine);
            if (!$this->_oPageCacheObject->isAvailable())
                $this->_oPageCacheObject = bx_instance ('BxDolCacheFile');
            return $this->_oPageCacheObject;
        }
    }

    /**
     * Get page cache key.
     * @param $isPrefixOnly return key prefix only.
     * @return string
     */
    protected function _getPageCacheKey ($isPrefixOnly = false)
    {
        $s = 'page_' . $this->_aObject['object'] . '_';
        if ($isPrefixOnly)
            return $s;
        $s .= $this->_getPageCacheParams ();
        $s .= bx_site_hash() . '.php';
        return $s;
    }

    /**
     * Additional cache key. In the case of dynamic page.
     * For example - profile page, where each profile has own page.
     * @return string
     */
    protected function _getPageCacheParams ()
    {
        return '';
    }

    /**
     * Clean page cache.
     * @param $isDelAllWithPagePrefix delete cache by prefix, it can be used for dynamic pages, like profile view, where for each profile separate cache is generated.
     * @return string
     */
    protected function cleanCache ($isDelAllWithPagePrefix = false)
    {
        $oCache = $this->_getPageCacheObject ();
        $sKey = $this->_getPageCacheKey($isDelAllWithPagePrefix);

        if ($isDelAllWithPagePrefix)
            return $oCache->removeAllByPrefix($sKey);
        else
            return $oCache->delData($sKey);
    }
}

/** @} */
