<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Persons',
    'version_from' => '10.0.0',
    'version_to' => '10.0.1',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '10.0.0-B2'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/persons/updates/update_10.0.0_10.0.1/',
    'home_uri' => 'persons_update_1000_1001',

    'module_dir' => 'boonex/persons/',
    'module_uri' => 'persons',

    'db_prefix' => 'bx_persons_',
    'class_prefix' => 'BxPersons',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 0,
        'clear_db_cache' => 1,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Persons',

    /**
     * Files Section
     */
    'delete_files' => array(),
);
