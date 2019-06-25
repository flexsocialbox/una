<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

class BxFilesUpdater extends BxDolStudioUpdater
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }
    
    public function actionExecuteSql($sOperation)
    {
        if($sOperation == 'install') {
            if(!$this->oDb->isFieldExists('bx_files_main', 'rrate'))
        	        $this->oDb->query("ALTER TABLE `bx_files_main` ADD `rrate` float NOT NULL default '0' AFTER `votes`");
            if(!$this->oDb->isFieldExists('bx_files_main', 'rvotes'))
        	        $this->oDb->query("ALTER TABLE `bx_files_main` ADD `rvotes` int(11) NOT NULL default '0' AFTER `rrate`");
        }

        return parent::actionExecuteSql($sOperation);
    }
}
