<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Ads',
    'version_from' => '10.0.0',
    'version_to' => '11.0.0',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '11.0.0-B1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/ads/updates/update_10.0.0_11.0.0/',
    'home_uri' => 'ads_update_1000_1100',

    'module_dir' => 'boonex/ads/',
    'module_uri' => 'ads',

    'db_prefix' => 'bx_ads_',
    'class_prefix' => 'BxAds',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 1,
        'update_relations' => 1,
        'clear_db_cache' => 1,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Ads',

    /**
     * Files Section
     */
    'delete_files' => array(),

    /**
     * Relations Section
     */
    'relations' => array(
        'bx_notifications'
    )
);
