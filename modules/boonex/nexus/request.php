<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Nexus Nexus - Mobile Apps and Desktop apps connector
 * @ingroup     UnaModules
 *
 * @{
 */

check_logged();

if ( empty($aRequest) || empty($aRequest[0]) ) {
    BxDolRequest::processAsFile($aModule, $aRequest);
} else {
    BxDolRequest::processAsAction($aModule, $aRequest);
}

/** @} */
