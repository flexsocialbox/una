-- SETTINGS
SET @iCategId = (SELECT `id` FROM `sys_options_categories` WHERE `name`='bx_froala_general' LIMIT 1);
DELETE FROM `sys_options` WHERE `name`='bx_froala_icons_template';
INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `extra`, `check`, `check_params`, `check_error`, `order`) VALUES
('bx_froala_icons_template', 'svg', @iCategId, '_bx_froala_option_icons_template', 'select', 'svg,font_awesome_5,text', '', '', '', 5);

-- ALERTS
SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='bx_froala' LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `name`='bx_froala';
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;

-- INJECTIONS
DELETE FROM `sys_injections` WHERE `name`='bx_froala';
INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
('bx_froala', 0, 'injection_footer', 'service', 'a:2:{s:6:"module";s:9:"bx_froala";s:6:"method";s:9:"injection";}', 0, 1);

-- PRELOADER
DELETE FROM `sys_preloader` WHERE `module`='bx_froala';
SET @iMaxOrder = (SELECT `order` FROM `sys_preloader` WHERE `type` = 'css_system' ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_preloader`(`module`, `type`, `content`, `active`, `order`) VALUES
('bx_froala', 'css_system', '{dir_plugins_modules}boonex/froala/plugins/froala/css/|froala_style.min.css',  '1',  @iMaxOrder + 1);
