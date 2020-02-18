-- TABLE: bundles
CREATE TABLE IF NOT EXISTS `bx_credits_bundles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `added` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `bonus` int(11) NOT NULL DEFAULT '0',
  `price` float NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

-- TABLE: orders
CREATE TABLE IF NOT EXISTS `bx_credits_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) unsigned NOT NULL default '0',
  `bundle_id` int(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  `order` varchar(32) NOT NULL default '',
  `license` varchar(32) NOT NULL default '',
  `type` varchar(16) NOT NULL default '',
  `added` int(11) unsigned NOT NULL default '0',
  `expired` int(11) unsigned NOT NULL default '0',
  `new` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`id`),
  KEY `order_id` (`bundle_id`, `profile_id`),
  KEY `license` (`license`)
);

CREATE TABLE IF NOT EXISTS `bx_credits_orders_deleted` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) unsigned NOT NULL default '0',
  `bundle_id` int(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  `order` varchar(32) NOT NULL default '',
  `license` varchar(32) NOT NULL default '',
  `type` varchar(16) NOT NULL default '',
  `added` int(11) unsigned NOT NULL default '0',
  `expired` int(11) unsigned NOT NULL default '0',
  `new` tinyint(1) NOT NULL default '1',
  `reason` varchar(16) NOT NULL default '',
  `deleted` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `order_id` (`bundle_id`, `profile_id`),
  KEY `license` (`license`)
);

-- TABLE: profiles
CREATE TABLE IF NOT EXISTS `bx_credits_profiles` (
  `id` int(11) NOT NULL DEFAULT '0',
  `balance` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

-- TABLE: history
CREATE TABLE IF NOT EXISTS `bx_credits_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_pid` int(11) NOT NULL DEFAULT '0',
  `second_pid` int(11) NOT NULL DEFAULT '0',
  `amount` float NOT NULL DEFAULT '0',
  `direction` enum('in', 'out') NOT NULL DEFAULT 'in',
  `order` varchar(32) NOT NULL default '',
  `data` text NOT NULL default '',
  `info` varchar(255) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);


-- FORMS: credit
INSERT INTO `sys_objects_form`(`object`, `module`, `title`, `action`, `form_attrs`, `table`, `key`, `uri`, `uri_title`, `submit_name`, `params`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_credits_credit', 'bx_credits', '_bx_credits_form_credit', '', 'a:1:{s:7:"enctype";s:19:"multipart/form-data";}', '', '', '', '', 'a:1:{i:0;s:9:"do_submit";}', '', 0, 1, 'BxCreditsFormWithdraw', 'modules/boonex/credits/classes/BxCreditsFormWithdraw.php');

INSERT INTO `sys_form_displays`(`object`, `display_name`, `module`, `view_mode`, `title`) VALUES 
('bx_credits_credit', 'bx_credits_credit_grant', 'bx_credits', 0, '_bx_credits_form_credit_display_grant'),
('bx_credits_credit', 'bx_credits_credit_withdraw_request', 'bx_credits', 0, '_bx_credits_form_credit_display_withdraw_request'),
('bx_credits_credit', 'bx_credits_credit_withdraw_confirm', 'bx_credits', 0, '_bx_credits_form_credit_display_withdraw_confirm');

INSERT INTO `sys_form_inputs`(`object`, `module`, `name`, `value`, `values`, `checked`, `type`, `caption_system`, `caption`, `info`, `required`, `collapsed`, `html`, `attrs`, `attrs_tr`, `attrs_wrapper`, `checker_func`, `checker_params`, `checker_error`, `db_pass`, `db_params`, `editable`, `deletable`) VALUES 
('bx_credits_credit', 'bx_credits', 'balance', '', '', 0, 'text', '_bx_credits_form_credit_input_sys_balance', '_bx_credits_form_credit_input_balance', '', 0, 0, 0, 'a:1:{s:8:"disabled";s:8:"disabled";}', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'rate', '', '', 0, 'text', '_bx_credits_form_credit_input_sys_rate', '_bx_credits_form_credit_input_rate', '', 0, 0, 0, 'a:1:{s:8:"disabled";s:8:"disabled";}', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'result', '', '', 0, 'text', '_bx_credits_form_credit_input_sys_result', '_bx_credits_form_credit_input_result', '', 0, 0, 0, 'a:1:{s:8:"disabled";s:8:"disabled";}', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'amount', '', '', 0, 'text', '_bx_credits_form_credit_input_sys_amount', '_bx_credits_form_credit_input_amount', '', 1, 0, 0, '', '', '', 'Avail', '', '_bx_credits_form_credit_input_amount_err', 'Xss', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'message', '', '', 0, 'textarea', '_bx_credits_form_credit_input_sys_message', '_bx_credits_form_credit_input_message', '', 0, 0, 0, '', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'profile', '', '', 0, 'custom', '_bx_credits_form_credit_input_sys_profile', '_bx_credits_form_credit_input_profile', '', 1, 0, 0, '', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'controls', '', 'do_submit,do_cancel', 0, 'input_set', '', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 0, 0),
('bx_credits_credit', 'bx_credits', 'do_submit', '_bx_credits_form_credit_input_do_submit', '', 0, 'submit', '_bx_credits_form_credit_input_sys_do_submit', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_credits_credit', 'bx_credits', 'do_cancel', '_bx_credits_form_credit_input_do_cancel', '', 0, 'button', '_bx_credits_form_credit_input_sys_do_cancel', '', '', 0, 0, 0, 'a:2:{s:7:"onclick";s:45:"$(''.bx-popup-applied:visible'').dolPopupHide()";s:5:"class";s:22:"bx-def-margin-sec-left";}', '', '', '', '', '', '', '', 1, 0);

INSERT INTO `sys_form_display_inputs`(`display_name`, `input_name`, `visible_for_levels`, `active`, `order`) VALUES 
('bx_credits_credit_grant', 'profile', 2147483647, 1, 1),
('bx_credits_credit_grant', 'amount', 2147483647, 1, 2),
('bx_credits_credit_grant', 'message', 2147483647, 1, 3),
('bx_credits_credit_grant', 'controls', 2147483647, 1, 4),
('bx_credits_credit_grant', 'do_submit', 2147483647, 1, 5),
('bx_credits_credit_grant', 'do_cancel', 2147483647, 1, 6),

('bx_credits_credit_withdraw_request', 'balance', 2147483647, 1, 1),
('bx_credits_credit_withdraw_request', 'rate', 2147483647, 1, 2),
('bx_credits_credit_withdraw_request', 'amount', 2147483647, 1, 3),
('bx_credits_credit_withdraw_request', 'result', 2147483647, 1, 4),
('bx_credits_credit_withdraw_request', 'message', 2147483647, 1, 5),
('bx_credits_credit_withdraw_request', 'controls', 2147483647, 1, 6),
('bx_credits_credit_withdraw_request', 'do_submit', 2147483647, 1, 7),
('bx_credits_credit_withdraw_request', 'do_cancel', 2147483647, 1, 8),

('bx_credits_credit_withdraw_confirm', 'profile', 2147483647, 1, 1),
('bx_credits_credit_withdraw_confirm', 'amount', 2147483647, 1, 2),
('bx_credits_credit_withdraw_confirm', 'message', 2147483647, 1, 3),
('bx_credits_credit_withdraw_confirm', 'controls', 2147483647, 1, 4),
('bx_credits_credit_withdraw_confirm', 'do_submit', 2147483647, 1, 5),
('bx_credits_credit_withdraw_confirm', 'do_cancel', 2147483647, 1, 6);

-- FORMS: bundle
INSERT INTO `sys_objects_form`(`object`, `module`, `title`, `action`, `form_attrs`, `table`, `key`, `uri`, `uri_title`, `submit_name`, `params`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_credits_bundle', 'bx_credits', '_bx_credits_form_bundle', '', 'a:1:{s:7:"enctype";s:19:"multipart/form-data";}', 'bx_credits_bundles', 'id', '', '', 'a:1:{i:0;s:9:"do_submit";}', '', 0, 1, 'BxCreditsFormBundle', 'modules/boonex/credits/classes/BxCreditsFormBundle.php');

INSERT INTO `sys_form_displays`(`object`, `display_name`, `module`, `view_mode`, `title`) VALUES 
('bx_credits_bundle', 'bx_credits_bundle_add', 'bx_credits', 0, '_bx_credits_form_bundle_display_add'),
('bx_credits_bundle', 'bx_credits_bundle_edit', 'bx_credits', 0, '_bx_credits_form_bundle_display_edit');

INSERT INTO `sys_form_inputs`(`object`, `module`, `name`, `value`, `values`, `checked`, `type`, `caption_system`, `caption`, `info`, `required`, `collapsed`, `html`, `attrs`, `attrs_tr`, `attrs_wrapper`, `checker_func`, `checker_params`, `checker_error`, `db_pass`, `db_params`, `editable`, `deletable`) VALUES 
('bx_credits_bundle', 'bx_credits', 'title', '', '', 0, 'text_translatable', '_bx_credits_form_bundle_input_sys_title', '_bx_credits_form_bundle_input_title', '', 1, 0, 0, '', '', '', 'AvailTranslatable', 'a:1:{i:0;s:5:"title";}', '_bx_credits_form_bundle_input_title_err', 'Xss', '', 1, 0),
('bx_credits_bundle', 'bx_credits', 'amount', '', '', 0, 'text', '_bx_credits_form_bundle_input_sys_amount', '_bx_credits_form_bundle_input_amount', '', 1, 0, 0, '', '', '', 'Avail', '', '_bx_credits_form_bundle_input_amount_err', 'Xss', '', 1, 0),
('bx_credits_bundle', 'bx_credits', 'bonus', '', '', 0, 'text', '_bx_credits_form_bundle_input_sys_bonus', '_bx_credits_form_bundle_input_bonus', '', 0, 0, 0, '', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_credits_bundle', 'bx_credits', 'price', '', '', 0, 'text', '_bx_credits_form_bundle_input_sys_price', '_bx_credits_form_bundle_input_price', '', 1, 0, 0, '', '', '', 'Avail', '', '_bx_credits_form_bundle_input_price_err', 'Xss', '', 1, 0),
('bx_credits_bundle', 'bx_credits', 'controls', '', 'do_submit,do_cancel', 0, 'input_set', '', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 0, 0),
('bx_credits_bundle', 'bx_credits', 'do_submit', '_bx_credits_form_bundle_input_do_submit', '', 0, 'submit', '_bx_credits_form_bundle_input_sys_do_submit', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_credits_bundle', 'bx_credits', 'do_cancel', '_bx_credits_form_bundle_input_do_cancel', '', 0, 'button', '_bx_credits_form_bundle_input_sys_do_cancel', '', '', 0, 0, 0, 'a:2:{s:7:"onclick";s:45:"$(''.bx-popup-applied:visible'').dolPopupHide()";s:5:"class";s:22:"bx-def-margin-sec-left";}', '', '', '', '', '', '', '', 1, 0);

INSERT INTO `sys_form_display_inputs`(`display_name`, `input_name`, `visible_for_levels`, `active`, `order`) VALUES 
('bx_credits_bundle_add', 'title', 2147483647, 1, 1),
('bx_credits_bundle_add', 'amount', 2147483647, 1, 2),
('bx_credits_bundle_add', 'bonus', 2147483647, 1, 3),
('bx_credits_bundle_add', 'price', 2147483647, 1, 4),
('bx_credits_bundle_add', 'controls', 2147483647, 1, 5),
('bx_credits_bundle_add', 'do_submit', 2147483647, 1, 6),
('bx_credits_bundle_add', 'do_cancel', 2147483647, 1, 7),

('bx_credits_bundle_edit', 'title', 2147483647, 1, 1),
('bx_credits_bundle_edit', 'amount', 2147483647, 1, 2),
('bx_credits_bundle_edit', 'bonus', 2147483647, 1, 3),
('bx_credits_bundle_edit', 'price', 2147483647, 1, 4),
('bx_credits_bundle_edit', 'controls', 2147483647, 1, 5),
('bx_credits_bundle_edit', 'do_submit', 2147483647, 1, 6),
('bx_credits_bundle_edit', 'do_cancel', 2147483647, 1, 7);


-- STUDIO: page & widget
INSERT INTO `sys_std_pages`(`index`, `name`, `header`, `caption`, `icon`) VALUES
(3, 'bx_credits', '_bx_credits', '_bx_credits', 'bx_credits@modules/boonex/credits/|std-icon.svg');
SET @iPageId = LAST_INSERT_ID();

SET @iParentPageId = (SELECT `id` FROM `sys_std_pages` WHERE `name` = 'home');
SET @iParentPageOrder = (SELECT MAX(`order`) FROM `sys_std_pages_widgets` WHERE `page_id` = @iParentPageId);
INSERT INTO `sys_std_widgets` (`page_id`, `module`, `url`, `click`, `icon`, `caption`, `cnt_notices`, `cnt_actions`) VALUES
(@iPageId, 'bx_credits', '{url_studio}module.php?name=bx_credits', '', 'bx_credits@modules/boonex/credits/|std-icon.svg', '_bx_credits', '', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_actions";s:6:"params";a:0:{}s:5:"class";s:18:"TemplStudioModules";}');
INSERT INTO `sys_std_pages_widgets` (`page_id`, `widget_id`, `order`) VALUES
(@iParentPageId, LAST_INSERT_ID(), IF(ISNULL(@iParentPageOrder), 1, @iParentPageOrder + 1));
