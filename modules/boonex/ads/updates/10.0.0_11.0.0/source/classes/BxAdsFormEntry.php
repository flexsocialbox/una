<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Ads Ads
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Create/Edit entry form
 */
class BxAdsFormEntry extends BxBaseModTextFormEntry
{
    protected $_iCategory;
    protected $_sGhostTemplateCover = 'form_ghost_template_cover.html';
	
    public function __construct($aInfo, $oTemplate = false)
    {
        $this->MODULE = 'bx_ads';
        parent::__construct($aInfo, $oTemplate);

        $CNF = &$this->_oModule->_oConfig->CNF;

        $this->_initCategoryFields();

    	if(isset($CNF['FIELD_COVER']) && isset($this->aInputs[$CNF['FIELD_COVER']])) {
            if($this->_oModule->checkAllowedSetThumb() === CHECK_ACTION_RESULT_ALLOWED) {
                $this->aInputs[$CNF['FIELD_COVER']]['storage_object'] = $CNF['OBJECT_STORAGE'];
                $this->aInputs[$CNF['FIELD_COVER']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_COVER']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_COVER']]['value']) : $CNF['OBJECT_UPLOADERS'];
                $this->aInputs[$CNF['FIELD_COVER']]['upload_buttons_titles'] = array(
                    'Simple' => _t('_bx_ads_form_entry_input_covers_uploader_simple_title'), 
                    'HTML5' => _t('_bx_ads_form_entry_input_covers_uploader_html5_title')
                );
                $this->aInputs[$CNF['FIELD_COVER']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW'];
                $this->aInputs[$CNF['FIELD_COVER']]['storage_private'] = 0;
                $this->aInputs[$CNF['FIELD_COVER']]['multiple'] = false;
                $this->aInputs[$CNF['FIELD_COVER']]['content_id'] = 0;
                $this->aInputs[$CNF['FIELD_COVER']]['ghost_template'] = '';
            }
            else
                unset($this->aInputs[$CNF['FIELD_COVER']]);
        }

        if(isset($CNF['FIELD_PHOTO']) && isset($this->aInputs[$CNF['FIELD_PHOTO']])) {
            $this->aInputs[$CNF['FIELD_PHOTO']]['storage_object'] = $CNF['OBJECT_STORAGE_PHOTOS'];
            $this->aInputs[$CNF['FIELD_PHOTO']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_PHOTO']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_PHOTO']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_PHOTO']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW_PHOTOS'];
            $this->aInputs[$CNF['FIELD_PHOTO']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_PHOTO']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_PHOTO']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_PHOTO']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_PHOTO']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if(isset($this->aInputs[$CNF['FIELD_VIDEO']])) {
            $this->aInputs[$CNF['FIELD_VIDEO']]['storage_object'] = $CNF['OBJECT_STORAGE_VIDEOS'];
            $this->aInputs[$CNF['FIELD_VIDEO']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_VIDEO']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_VIDEO']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_VIDEO']]['images_transcoder'] = $CNF['OBJECT_VIDEOS_TRANSCODERS']['poster_preview'];
            $this->aInputs[$CNF['FIELD_VIDEO']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_VIDEO']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_VIDEO']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_VIDEO']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_VIDEO']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if (isset($CNF['FIELD_FILE']) && isset($this->aInputs[$CNF['FIELD_FILE']])) {
            $this->aInputs[$CNF['FIELD_FILE']]['storage_object'] = $CNF['OBJECT_STORAGE_FILES'];
            $this->aInputs[$CNF['FIELD_FILE']]['uploaders'] = !empty($this->aInputs[$CNF['FIELD_FILE']]['value']) ? unserialize($this->aInputs[$CNF['FIELD_FILE']]['value']) : $CNF['OBJECT_UPLOADERS'];
            $this->aInputs[$CNF['FIELD_FILE']]['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW_FILES'];
            $this->aInputs[$CNF['FIELD_FILE']]['storage_private'] = 0;
            $this->aInputs[$CNF['FIELD_FILE']]['multiple'] = true;
            $this->aInputs[$CNF['FIELD_FILE']]['content_id'] = 0;
            $this->aInputs[$CNF['FIELD_FILE']]['ghost_template'] = '';
            $this->aInputs[$CNF['FIELD_FILE']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }

        if(isset($this->aInputs[$CNF['FIELD_POLL']])) {
            $this->aInputs[$CNF['FIELD_POLL']]['tr_attrs'] = array('class'=> 'bx-base-text-attachment-item');
        }
    }

    function getCode($bDynamicMode = false)
    {
        $sInclude = $this->_oModule->_oTemplate->addJs(array('entry.js'), $bDynamicMode);
        return ($bDynamicMode ? $sInclude : '') . $this->_oModule->_oTemplate->getJsCode('entry') . parent::getCode($bDynamicMode);
    }

    function initChecker ($aValues = array (), $aSpecificValues = array())
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $bValues = $aValues && !empty($aValues['id']);
        $aContentInfo = $bValues ? $this->_oModule->_oDb->getContentInfoById($aValues['id']) : false;

        $this->_initCategoryFields($aContentInfo[$CNF['FIELD_CATEGORY']]);

        if(isset($CNF['FIELD_COVER']) && isset($this->aInputs[$CNF['FIELD_COVER']])) {
            if($bValues)
                $this->aInputs[$CNF['FIELD_COVER']]['content_id'] = $aValues['id'];

            $this->aInputs[$CNF['FIELD_COVER']]['ghost_template'] = $this->_oModule->_oTemplate->parseHtmlByName($this->_sGhostTemplateCover, $this->_getCoverGhostTmplVars($aContentInfo));
        }

        parent::initChecker ($aValues, $aSpecificValues);
    }

    public function insert ($aValsToAdd = array(), $isIgnore = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $iContentId = parent::insert ($aValsToAdd, $isIgnore);
        if(!empty($iContentId))
            $this->processFiles($CNF['FIELD_COVER'], $iContentId, true);

        return $iContentId;
    }

    public function update ($iContentId, $aValsToAdd = array(), &$aTrackTextFieldsChanges = null)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $iResult = parent::update ($iContentId, $aValsToAdd, $aTrackTextFieldsChanges);

        $this->processFiles($CNF['FIELD_COVER'], $iContentId, false);

        return $iResult;
    }

    protected function genCustomInputCategoryView(&$aInput)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if(!empty($this->_iCategory)) {
            $aCategory = $this->_oModule->_oDb->getCategories(array('type' => 'id', 'id' => $this->_iCategory));
            if(!empty($aCategory) && is_array($aCategory))
                $aInput['value'] = bx_process_output(_t($aCategory['title']));
        }

        return $this->genInputStandard($aInput);
    }
 
    protected function genCustomViewRowValueCategoryView(&$aInput)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if(empty($aInput['value']))
            return '';

        $aCategory = $this->_oModule->_oDb->getCategories(array('type' => 'id', 'id' => $aInput['value']));
        if(empty($aCategory) || !is_array($aCategory))
            return '';

        $sLink = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($CNF['URL_CATEGORIES'], array($CNF['GET_PARAM_CATEGORY'] => $aCategory['id']));
        return $this->_oModule->_oTemplate->parseLink($sLink, bx_process_output(_t($aCategory['title'])));
    }

    protected function _getCoverGhostTmplVars($aContentInfo = array())
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	return array (
            'name' => $this->aInputs[$CNF['FIELD_COVER']]['name'],
            'content_id' => $this->aInputs[$CNF['FIELD_COVER']]['content_id'],
            'editor_id' => isset($CNF['FIELD_TEXT_ID']) ? $CNF['FIELD_TEXT_ID'] : '',
            'thumb_id' => isset($CNF['FIELD_THUMB']) && isset($aContentInfo[$CNF['FIELD_THUMB']]) ? $aContentInfo[$CNF['FIELD_THUMB']] : 0,
            'name_thumb' => isset($CNF['FIELD_THUMB']) ? $CNF['FIELD_THUMB'] : ''
        );
    }

    protected function _getPhotoGhostTmplVars($aContentInfo = array())
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	return array (
            'name' => $this->aInputs[$CNF['FIELD_PHOTO']]['name'],
            'content_id' => (int)$this->aInputs[$CNF['FIELD_PHOTO']]['content_id'],
            'editor_id' => isset($CNF['FIELD_TEXT_ID']) ? $CNF['FIELD_TEXT_ID'] : ''
    	);
    }

    protected function _initCategoryFields($iCategory = 0)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $this->_iCategory = (int)$iCategory;
        if(empty($this->_iCategory) && bx_get('category') !== false)
            $this->_iCategory = (int)bx_get('category');

        if(isset($CNF['FIELD_CATEGORY']) && isset($this->aInputs[$CNF['FIELD_CATEGORY']]) && $this->_iCategory != 0)
            $this->aInputs[$CNF['FIELD_CATEGORY']]['value'] = $this->_iCategory;

        if(isset($CNF['FIELD_CATEGORY_VIEW']) && isset($this->aInputs[$CNF['FIELD_CATEGORY_VIEW']]) && $this->_iCategory != 0)
            $this->aInputs[$CNF['FIELD_CATEGORY_VIEW']]['value'] = $this->_iCategory;

        if(isset($CNF['FIELD_CATEGORY_SELECT']) && isset($this->aInputs[$CNF['FIELD_CATEGORY_SELECT']]))
            $this->aInputs[$CNF['FIELD_CATEGORY_SELECT']]['values'] = $this->_oModule->serviceGetCategoryOptions(0, true);
    }
}

/** @} */
