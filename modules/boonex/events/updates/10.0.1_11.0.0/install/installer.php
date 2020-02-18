<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

class BxEventsUpdater extends BxDolStudioUpdater
{
    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    public function actionExecuteSql($sOperation)
    {
        if($sOperation == 'install') {
            if(!$this->oDb->isFieldExists('bx_events_data', 'labels'))
                $this->oDb->query("ALTER TABLE `bx_events_data` ADD `labels` text NOT NULL AFTER `timezone`");
            if(!$this->oDb->isFieldExists('bx_events_data', 'location'))
                $this->oDb->query("ALTER TABLE `bx_events_data` ADD `location` text NOT NULL AFTER `labels`");
        }

        return parent::actionExecuteSql($sOperation);
    }
}
