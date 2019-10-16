<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Decorous',
    'version_from' => '10.0.1',
    'version_to' => '10.0.2',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '10.0.0-RC1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/decorous/updates/update_10.0.1_10.0.2/',
    'home_uri' => 'decorous_update_1001_1002',

    'module_dir' => 'boonex/decorous/',
    'module_uri' => 'decorous',

    'db_prefix' => 'bx_decorous_',
    'class_prefix' => 'BxDecorous',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 1,
        'clear_db_cache' => 1,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Boonex Decorous Template',

    /**
     * Files Section
     */
    'delete_files' => array(),
);
