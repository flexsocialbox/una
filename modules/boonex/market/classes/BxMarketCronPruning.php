<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Market Market
 * @ingroup     UnaModules
 *
 * @{
 */

require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

class BxMarketCronPruning extends BxDolCron
{
    protected $_sModule;
    protected $_oModule;

    public function __construct()
    {
        $this->_sModule = 'bx_market';
        $this->_oModule = BxDolModule::getInstance($this->_sModule);

        parent::__construct();
    }

    function processing()
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $oPayments = BxDolPayments::getInstance();

        $aLicenses = $this->_oModule->_oDb->getLicense(array('type' => 'expired'));
        foreach($aLicenses as $aLicense) {
            $aSubscription = $oPayments->getSubscriptionsInfo(array('subscription_id' => $aLicense['order']), true);
            if(!empty($aSubscription) && is_array($aSubscription)) {
                $aSubscription = array_shift($aSubscription);

                if($aSubscription['data']['status'] == 'active') {
                    $this->_oModule->_oDb->updateLicense(array('expired' => $aSubscription['data']['cperiod_end'] + 86400 * (int)getParam($CNF['OPTION_RECURRING_RESERVE'])), array('id' => $aLicense['id']));
                    continue;
                }
                else {
                    $oPayments->sendSubscriptionExpirationLetters($aSubscription['pending_id'], $aLicense['order']);

                    $this->_oModule->_oDb->updateLicense(array('expired' => $aLicense['expired'] + 86400 * (int)getParam($CNF['OPTION_RECURRING_RESERVE'])), array('id' => $aLicense['id']));
                    continue;
                }
            }

            bx_alert($this->_oModule->getName(), 'license_expire', 0, false, $aLicense);
            
            $this->_oModule->_oDb->processExpiredLicense($aLicense);
        }
    }
}

/** @} */
