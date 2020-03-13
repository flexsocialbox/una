<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

/**
 * @see BxDolVote, BxDolVoteLikes
 */
class BxBaseVoteReactions extends BxDolVoteReactions
{
    protected $_sTmplNameBySummary;
    protected $_sTmplContentCounterWrapper = '';

    public function __construct($sSystem, $iId, $iInit = true, $oTemplate = false)
    {
        parent::__construct($sSystem, $iId, $iInit, $oTemplate);

        $this->_sJsClsName .= 'Reactions';

        $sHtmlId = str_replace(array('_' , ' '), array('-', '-'), $sSystem) . '-' . $iId;
        $this->_aHtmlIds = array_merge($this->_aHtmlIds, array(
            'main' => 'bx-vr-' . $sHtmlId,
            'do_menu' => 'bx-vr-do-menu-' . $sHtmlId,
            'do_popup' => 'bx-vr-do-popup-' . $sHtmlId
        ));

        $this->_aElementDefaults = array(
            'show_do_vote_as_button' => false,
            'show_do_vote_as_button_small' => false,
            'show_do_vote_icon' => true,
            'show_do_vote_label' => false,
            'show_counter' => false,
            'show_counter_empty' => true,
            'show_counter_style' => self::$_sCounterStyleDivided, //--- Alloved styles are 'divided' and 'compound'
            'show_legend' => false,
            'show_script' => true
        );

        $this->_sTmplNameBySummary = 'vote_by_summary_reactions.html';
        $this->_sTmplNameByList = 'vote_by_list_reactions.html';

        $this->_sTmplContentDoActionLabel = $this->_oTemplate->getHtml('vote_do_vote_label_reactions.html');
        $this->_sTmplContentCounterLabel = $this->_oTemplate->getHtml('vote_counter_label_reactions.html');
        $this->_sTmplContentCounterWrapper = $this->_oTemplate->getHtml('vote_counter_wrapper_reactions.html');
    }

    public function getJsClick($iValue = 0)
    {
        if(empty($iValue))
            $iValue = $this->getValue();

        return $this->getJsObjectName() . '.toggleDoPopup(this, ' . $iValue . ')';
    }

    public function getJsClickDo($sReaction, $iValue = 0)
    {
        if(empty($iValue))
            $iValue = $this->getValue();

        return $this->getJsObjectName() . '.vote(this, ' . $iValue . ', \'' . $sReaction . '\')';
    }

    public function getJsClickCounter($aParams = array())
    {
        $sJsObject = $this->getJsObjectName();
        $sJsMethod = 'toggleByPopup';

        if(isset($aParams['show_counter_style']) && $aParams['show_counter_style'] == self::$_sCounterStyleCompound)
            return $sJsObject . '.' . $sJsMethod . '(this)';

        $sReaction = !empty($aParams['reaction']) ? $aParams['reaction'] : $this->_sDefault;
        return $sJsObject . '.' . $sJsMethod . '(this, \'' . $sReaction . '\')';
    }

    public function getCounter($aParams = array())
    {
        $sDefault = $this->_aElementDefaults['show_counter_style'];
        $sCounterStyle = !empty($aParams['show_counter_style']) ? $aParams['show_counter_style'] : $sDefault;

        $sMethodPrefix = '_getCounter';
        $sMethod = $sMethodPrefix . bx_gen_method_name($sCounterStyle);
        if(!method_exists($this, $sMethod))
            $sMethod = $sMethodPrefix . bx_gen_method_name($sDefault);

        return $this->$sMethod($aParams);
    }

    public function _getCounterDivided($aParams = array())
    {
        $bDynamicMode = isset($aParams['dynamic_mode']) && (bool)$aParams['dynamic_mode'] === true;
        $bShowCounterEmpty = isset($aParams['show_counter_empty']) && (bool)$aParams['show_counter_empty'] === true;
        $bShowScript = !isset($aParams['show_script']) || (bool)$aParams['show_script'] === true;

        $bVote = $this->_isVote();
        $aVote = $this->_getVote();
        $aReactions = $this->getReactions();

        $sClass = isset($aParams['class_counter']) ? $aParams['class_counter'] : '';
        if(isset($aParams['show_do_vote_as_button_small']) && (bool)$aParams['show_do_vote_as_button_small'] === true)
            $sClass .= ' bx-btn-small-height';
        else if(isset($aParams['show_do_vote_as_button']) && (bool)$aParams['show_do_vote_as_button'] === true)
            $sClass .= ' bx-btn-height';
        $sClass .= ' bx-def-margin-sec-right';

        $aParams['id_counter'] = '';

        $sResult = '';
        foreach($aReactions as $sName) {
            $iCount = (int)$aVote['count_' . $sName];

            $aParams['class_counter'] = ($iCount == 0 && (!$bShowCounterEmpty || $sName != $this->_sDefault || $bVote) ? ' bx-vc-hidden' : '') . ' ' . $sName . ' ' . $sClass;
            $aParams['reaction'] = $sName;
            $aParams['vote'] = array(
                'count' => $iCount,
                'sum' => $aVote['sum_' . $sName],
                'rate' => $aVote['rate_' . $sName],
            );

            $sResult .= parent::getCounter(array_merge($aParams, array('show_script' => false)));
        }

        return $this->_oTemplate->parseHtmlByContent($this->_getTmplContentCounterWrapper(), array(
            'html_id' => $this->_aHtmlIds['counter'],
            'style_prefix' => $this->_sStylePrefix,
            'class' => !$bVote && !$bShowCounterEmpty ? 'bx-vc-hidden' : '',
            'type' => $this->_sType,
            'style' => self::$_sCounterStyleDivided,
            'bx_if:show_link' => array(
                'condition' => false,
                'content' => array()
            ),
            'bx_if:show_text' => array(
                'condition' => true,
                'content' => array(
                    'counter' => $sResult,
                )
            ),
            'script' => $bShowScript ? $this->getJsScript($aParams) : ''
        ));
    }

    public function _getCounterCompound($aParams = array())
    {
        $bDynamicMode = isset($aParams['dynamic_mode']) && (bool)$aParams['dynamic_mode'] === true;
        $bShowCounterEmpty = isset($aParams['show_counter_empty']) && (bool)$aParams['show_counter_empty'] === true;
        $bShowScript = !isset($aParams['show_script']) || (bool)$aParams['show_script'] === true;

        $bVote = $this->_isVote();
        $aVote = $this->_getVote();
        $aReactions = $this->getReactions();

        $sClass = isset($aParams['class_counter']) ? $aParams['class_counter'] : '';
        if(isset($aParams['show_do_vote_as_button_small']) && (bool)$aParams['show_do_vote_as_button_small'] === true)
            $sClass .= ' bx-btn-small-height';
        else if(isset($aParams['show_do_vote_as_button']) && (bool)$aParams['show_do_vote_as_button'] === true)
            $sClass .= ' bx-btn-height';

        $aParams['id_counter'] = '';

        $sResult = '';
        $iResultC = $iResultS = 0;
        foreach($aReactions as $sName) {
            $iCount = (int)$aVote['count_' . $sName];

            $aParams = array_merge($aParams, array(
                'show_counter_active' => false,
                'show_counter_label_icon' => true,
                'show_counter_label_text' => false,
                'class_counter' => ($iCount == 0 && (!$bShowCounterEmpty || $sName != $this->_sDefault || $bVote) ? ' bx-vc-hidden' : '') . ' ' . $sName . ' ' . $sClass,
                'reaction' => $sName,
                'vote' => array(
                    'count' => $iCount,
                    'sum' => $aVote['sum_' . $sName],
                    'rate' => $aVote['rate_' . $sName],
                )
            ));

            $iResultC += $iCount;
            $iResultS += (int)$aVote['sum_' . $sName];
            $sResult .= trim(parent::getCounter(array_merge($aParams, array('show_script' => false))));
        }

        $aParams = array_merge($aParams, array(
            'show_counter_active' => false,
            'show_counter_label_icon' => false,
            'show_counter_label_text' => true,
            'class_counter' => ' total-count ' . $sClass,
            'reaction' => '',
            'vote' => array(
                'count' => $iResultC,
                'sum' => $iResultS,
                'rate' => $iResultC > 0 ? round($iResultS / $iResultC, 2) : 0,
            )
        ));
        $sResult .= parent::getCounter(array_merge($aParams, array('show_script' => false)));

        return $this->_oTemplate->parseHtmlByContent($this->_getTmplContentCounterWrapper(), array(
            'html_id' => $this->_aHtmlIds['counter'],
            'style_prefix' => $this->_sStylePrefix,
            'class' => !$bVote && !$bShowCounterEmpty ? 'bx-vc-hidden' : '',
            'type' => $this->_sType,
            'style' => self::$_sCounterStyleCompound,
            'bx_if:show_link' => array(
                'condition' => true,
                'content' => array(
                    'href' => 'javascript:void(0)',
                    'onclick' => 'javascript:' . $this->getJsClickCounter($aParams),
                    'title' => bx_html_attribute($this->_getTitleDoBy($aParams)),
                    'counter' => $sResult,
                )
            ),
            'bx_if:show_text' => array(
                'condition' => false,
                'content' => array()
            ),
            'script' => $bShowScript ? $this->getJsScript($aParams) : ''
        ));
    }

    public function getElement($aParams = array())
    {
        $sClass = $this->_sStylePrefix . '-' . $this->_sType;
        if(isset($aParams['show_do_vote_as_button_small']) && (bool)$aParams['show_do_vote_as_button_small'] === true)
            $sClass .=  '-button-small';
        else if(isset($aParams['show_do_vote_as_button']) && (bool)$aParams['show_do_vote_as_button'] === true)
            $sClass .= '-button';

        $aParams['class_element'] = isset($aParams['class_element']) ? $aParams['class_element'] : '';
        $aParams['class_element'] .= ' ' . $sClass;

        return parent::getElement($aParams);
    }

    /**
     * Internal methods.
     */
    protected function _isShowDoVote($aParams, $isAllowedVote, $bCount)
    {
        $bResult = parent::_isShowDoVote($aParams, $isAllowedVote, $bCount);
        if(!$bResult)
            return $bResult;

        return $isAllowedVote || $bCount;
    }

    protected function _getDoVote($aParams = array(), $isAllowedVote = true)
    {
        $bUndo = $this->isUndo();
    	$bVoted = isset($aParams['is_voted']) && (bool)$aParams['is_voted'] === true;
        $bShowDoVoteAsButtonSmall = isset($aParams['show_do_vote_as_button_small']) && (bool)$aParams['show_do_vote_as_button_small'] === true;
        $bShowDoVoteAsButton = !$bShowDoVoteAsButtonSmall && isset($aParams['show_do_vote_as_button']) && (bool)$aParams['show_do_vote_as_button'] === true;
        $bDisabled = !$isAllowedVote || ($bVoted && !$bUndo);

        $sClass = '';
        if($bShowDoVoteAsButton)
            $sClass = ' bx-btn';
        else if ($bShowDoVoteAsButtonSmall)
            $sClass = ' bx-btn bx-btn-small';

        $sJsClick = '';
        if(!$bDisabled) {
            if($bVoted && $bUndo) {
                $sClass = ' ' . $this->_sStylePrefix . '-voted' . $sClass;
                $sJsClick = $this->getJsClickDo($aParams['track']['reaction']);
            }
            else 
                $sJsClick = $this->getJsClick();
        }
        else
            $sClass .= $bShowDoVoteAsButton || $bShowDoVoteAsButtonSmall ? ' bx-btn-disabled' : ' ' . $this->_sStylePrefix . '-disabled';

        return $this->_oTemplate->parseLink('javascript:void(0)', $this->_getDoVoteLabel($aParams), array(
            'class' => $this->_sStylePrefix . '-do-vote' . $sClass,
            'title' => _t($this->_getTitleDoWithTrack($bVoted, $aParams['track'])),
            'onclick' => $sJsClick,
        ));
    }

    protected function _getDoVoteLabel($aParams = array())
    {
    	$bVoted = isset($aParams['is_voted']) && (bool)$aParams['is_voted'] === true;

        return $this->_oTemplate->parseHtmlByContent($this->_getTmplContentDoActionLabel(), array(
            'style_prefix' => $this->_sStylePrefix,
            'bx_if:show_icon' => array(
                'condition' => isset($aParams['show_do_vote_icon']) && (bool)$aParams['show_do_vote_icon'] === true,
                'content' => array(
                    'style_prefix' => $this->_sStylePrefix,
                    'name' => $this->_getIconDoWithTrack($bVoted, $aParams['track'])
                )
            ),
            'bx_if:show_text' => array(
                'condition' => isset($aParams['show_do_vote_label']) && (bool)$aParams['show_do_vote_label'] === true,
                'content' => array(
                    'style_prefix' => $this->_sStylePrefix,
                    'text' => _t($this->_getTitleDoWithTrack($bVoted, $aParams['track']))
                )
            )
        ));
    }

    public function _getDoVotePopup($iValue = 0)
    {
        if(empty($iValue))
            $iValue = $this->getValue();

        $oMenu = BxTemplMenu::getObjectInstance($this->_sMenuDoVote);
        if(!$oMenu)
            return '';

        $oMenu->setParams(array(
            'object' => &$this,
            'value' => $iValue,
        ));
        return $oMenu->getCode();
    }

    protected function _getCounterLabel($iCount, $aParams = array())
    {
        $sReaction = !empty($aParams['reaction']) ? $aParams['reaction'] : $this->_sDefault;

        return $this->_oTemplate->parseHtmlByContent($this->_getTmplContentCounterLabel(), array(
            'style_prefix' => $this->_sStylePrefix,
            'bx_if:show_icon' => array(
                'condition' => !isset($aParams['show_counter_label_icon']) || (bool)$aParams['show_counter_label_icon'] === true,
                'content' => array(
                    'style_prefix' => $this->_sStylePrefix,
                    'name' => $this->getIcon($sReaction)
                )
            ),
            'bx_if:show_text' => array(
                'condition' => !isset($aParams['show_counter_label_text']) || (bool)$aParams['show_counter_label_text'] === true,
                'content' => array(
                    'style_prefix' => $this->_sStylePrefix,
                    'text' => parent::_getCounterLabel($iCount)
                )
            )
        ));
    }

    protected function _getVotedBy($aParams = array())
    {
        if(!isset($aParams['reaction']))
            return $this->_getVotedBySummary($aParams);

        $bSummary = $aParams['reaction'] == 'summary';

        $aBrowseParams = array('type' => 'by', 'object_id' => $this->getId());
        if(!$bSummary)
            $aBrowseParams['reaction'] = $aParams['reaction'];

        $aValues = $this->_oQuery->getPerformed($aBrowseParams);

        $aTmplUsers = array();
        foreach($aValues as $mValue) {
            $mValue = is_array($mValue) ? $mValue : array('author_id' => (int)$mValue, 'reaction' => '');

            list($sUserName, $sUserUrl, $sUserIcon, $sUserUnit) = $this->_getAuthorInfo($mValue['author_id']);
            $aTmplUsers[] = array(
                'style_prefix' => $this->_sStylePrefix,
                'user_unit' => $sUserUnit,
                'bx_if:show_reaction' => array(
                    'condition' => $bSummary,
                    'content' => array(
                        'style_prefix' => $this->_sStylePrefix,
                        'icon' => $bSummary ? $this->getIcon($mValue['reaction']) : ''
                    )
                )
            );
        }

        if(empty($aTmplUsers))
            $aTmplUsers = MsgBox(_t('_Empty'));

        return $this->_oTemplate->parseHtmlByName($this->_sTmplNameByList, array(
            'style_prefix' => $this->_sStylePrefix,
            'class' => ' ' . $this->_sStylePrefix . '-bl-' . $aParams['reaction'],
            'bx_repeat:list' => $aTmplUsers
        ));
    }

    protected function _getVotedBySummary($aParams = array())
    {
        $sJsObject = $this->getJsObjectName();

        $sTxtSummary = _t('_vote_do_by_summary');
        $aReactions = array_merge(array('summary'), $this->getReactions());

        $aMenuItems = array();
        $aTmplVarsLists = array();
        foreach ($aReactions as $sReaction) {
            if($sReaction == $this->_sDefault)
                continue;

            $bSummary = $sReaction == 'summary';

            $sName = $this->_sStylePrefix . '-' . $sReaction;
            $sTitle = !$bSummary ? $this->_oTemplate->parseIcon($this->getIcon($sReaction)) : $sTxtSummary;
            $sTitleAttr = !$bSummary ? _t('_vote_do_by_x_reaction', _t($this->_aDataList[$sReaction]['title'])) : $sTxtSummary;

            $aMenuItems[] = array('id' => $sName, 'name' => $sName, 'class' => '', 'link' => 'javascript:void(0)', 'onclick' => 'javascript:' . $sJsObject . '.changeVotedBy(this, \'' . $sReaction . '\')', 'target' => '_self', 'title' => $sTitle, 'title_attr' => $sTitleAttr, 'active' => 1);

            $aTmplVarsLists[] = array(
                'content' => $this->_getVotedBy(array('reaction' => $sReaction))
            );
        }

        $oMenu = new BxTemplMenuInteractive(array('template' => 'menu_interactive_vertical.html', 'menu_id'=> $this->_sStylePrefix . '-voted-by', 'menu_items' => $aMenuItems));
        $oMenu->setSelected('', $this->_sStylePrefix . '-summary');

        return $this->_oTemplate->parseHtmlByName($this->_sTmplNameBySummary, array(
            'style_prefix' => $this->_sStylePrefix,
            'title' => '',
            'menu' => $oMenu->getCode(),
            'bx_repeat:lists' => $aTmplVarsLists
        ));
    }

    protected function _getTmplContentCounterWrapper()
    {
        return $this->_sTmplContentCounterWrapper;
    }
}

/** @} */
