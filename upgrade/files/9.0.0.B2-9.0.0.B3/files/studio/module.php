<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaEndAdmin UNA Studio End Admin Pages
 * @ingroup     UnaStudio
 * @{
 */

require_once('./../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
require_once(BX_DOL_DIR_STUDIO_INC . 'utils.inc.php');

bx_import('BxDolLanguages');

bx_require_authentication(true);

$sName = bx_get('name');
if($sName === false)
    $sName = bx_get('mod_value');
$sName = $sName !== false ? bx_process_input($sName) : '';

$sPage = bx_get('page');
$sPage = $sPage !== false ? bx_process_input($sPage) : '';

$oPage = BxDolStudioModule::getObjectInstance($sName, $sPage);

$oTemplate = BxDolStudioTemplate::getInstance();
$oTemplate->setPageNameIndex($oPage->getPageIndex());
$oTemplate->setPageHeader($oPage->getPageHeader());
$oTemplate->setPageContent('page_caption_code', $oPage->getPageCaption());
$oTemplate->setPageContent('page_attributes', $oPage->getPageAttributes());
$oTemplate->setPageContent('page_menu_code', $oPage->getPageMenu());
$oTemplate->setPageContent('page_main_code', $oPage->getPageCode());
$oTemplate->addCss($oPage->getPageCss());
$oTemplate->addJs($oPage->getPageJs());
$oTemplate->getPageCode();

/** @} */
