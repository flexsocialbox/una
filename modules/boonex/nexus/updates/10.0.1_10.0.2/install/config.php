<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Nexus',
    'version_from' => '10.0.1',
    'version_to' => '10.0.2',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '10.0.0-RC1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/nexus/updates/update_10.0.1_10.0.2/',
    'home_uri' => 'nexus_update_1001_1002',

    'module_dir' => 'boonex/nexus/',
    'module_uri' => 'nexus',

    'db_prefix' => 'bx_nexus_',
    'class_prefix' => 'BxNexus',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 0,
        'update_files' => 1,
        'update_languages' => 0,
        'clear_db_cache' => 0,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Nexus',

    /**
     * Files Section
     */
    'delete_files' => array(),
);
