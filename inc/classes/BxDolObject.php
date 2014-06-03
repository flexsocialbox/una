<?php defined('BX_DOL') or defined('BX_DOL_INSTALL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    DolphinCore Dolphin Core
 * @{
 */


/** 
 * @page objects Objects
 * Classes which represents high level programming constructions to generate ready functionality, like Comments, Votings, Forms. 
 */


/**
 * Base class for all Dolphin Object classes
 */
class BxDolObject extends BxDol {
	protected $_iId = 0;    ///< item id the action to be performed with
    protected $_sSystem = ''; ///< current system name
    protected $_aSystem = array(); ///< current system array

    protected $_oQuery = null;

    public function __construct($sSystem, $iId, $iInit = 1) {
    	parent::BxDol();

    	$this->_aSystems = $this->getSystems();
        if(!isset($this->_aSystems[$sSystem]))
			return;

        $this->_sSystem = $sSystem;
		$this->_aSystem = $this->_aSystems[$sSystem];

		if(!$this->isEnabled()) 
			return;

		if($iInit)
			$this->init($iId);
    }

	public function init($iId)
    {
    	if(!$this->isEnabled()) 
        	return false;

        if(empty($this->_iId) && $iId)
			$this->setId($iId);

		return true;
    }

	public function getSystemId()
    {
        return $this->_aSystem['id'];
    }

    public function getSystemName()
    {
        return $this->_sSystem;
    }

	public function getSystemInfo()
    {
        return $this->_aSystem;
    }

	public function getId()
    {
        return $this->_iId;
    }

	public function setId($iId)
    {
        if($iId == $this->getId())
        	return;

        $this->_iId = $iId;
    }

	public function isEnabled ()
    {
        return $this->_aSystem && (int)$this->_aSystem['is_on'] == 1;
    }
}