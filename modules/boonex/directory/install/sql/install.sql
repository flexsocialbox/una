-- TABLE: entries
CREATE TABLE IF NOT EXISTS `bx_directory_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `changed` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `thumb` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `text` mediumtext NOT NULL,
  `views` int(11) NOT NULL default '0',
  `rate` float NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `rrate` float NOT NULL default '0',
  `rvotes` int(11) NOT NULL default '0',
  `score` int(11) NOT NULL default '0',
  `sc_up` int(11) NOT NULL default '0',
  `sc_down` int(11) NOT NULL default '0',
  `favorites` int(11) NOT NULL default '0',
  `comments` int(11) NOT NULL default '0',
  `reports` int(11) NOT NULL default '0',
  `featured` int(11) NOT NULL default '0',
  `allow_view_to` varchar(16) NOT NULL DEFAULT '3',
  `status` enum('active','awaiting','hidden') NOT NULL DEFAULT 'active',
  `status_admin` enum('active','hidden') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title_text` (`title`,`text`)
);

-- TABLE: types of categories
CREATE TABLE IF NOT EXISTS `bx_directory_categories_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `display_add` varchar(255) NOT NULL DEFAULT '',
  `display_edit` varchar(255) NOT NULL DEFAULT '',
  `display_view` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);

INSERT INTO `bx_directory_categories_types` (`id`, `name`, `title`, `display_add`, `display_edit`, `display_view`) VALUES
(1, 'price', '_bx_directory_cat_type_price', 'bx_directory_entry_price_add', 'bx_directory_entry_price_edit', 'bx_directory_entry_price_view'),
(2, 'price_year', '_bx_directory_cat_type_price_year', 'bx_directory_entry_price_year_add', 'bx_directory_entry_price_year_edit', 'bx_directory_entry_price_year_view');

-- TABLE: categories
CREATE TABLE IF NOT EXISTS `bx_directory_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `items` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  FULLTEXT KEY `title_text` (`title`,`text`)
);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 1, 'job', '_bx_directory_cat_title_job', '', 'user-md', 1, 1);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 1, 'job_finance', '_bx_directory_cat_title_accounting_finance', '', '', 1, 1),
(@iParentId, 1, 1, 'job_education', '_bx_directory_cat_title_education_nonprofit', '', '', 1, 2),
(@iParentId, 1, 1, 'job_legal', '_bx_directory_cat_title_government_legal', '', '', 1, 3),
(@iParentId, 1, 1, 'job_programming', '_bx_directory_cat_title_programming_web_design', '', '', 1, 4);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 2, 'music', '_bx_directory_cat_title_music', '', 'music', 1, 2);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 2, 'music_isale', '_bx_directory_cat_title_instrument_sale', '', '', 1, 1),
(@iParentId, 1, 2, 'music_iwanted', '_bx_directory_cat_title_instrument_wanted', '', '', 1, 2);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 1, 'housing', '_bx_directory_cat_title_housing', '', 'home', 1, 3);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 1, 'housing_apartments', '_bx_directory_cat_title_apartments_housing', '', '', 1, 1),
(@iParentId, 1, 1, 'housing_office', '_bx_directory_cat_title_office_commercial', '', '', 1, 2),
(@iParentId, 1, 1, 'housing_re_sale', '_bx_directory_cat_title_real_estate_sale', '', '', 1, 3),
(@iParentId, 1, 1, 'housing_roommate', '_bx_directory_cat_title_roommate', '', '', 1, 4),
(@iParentId, 1, 1, 'housing_temp_rental', '_bx_directory_cat_title_temporary_rental', '', '', 1, 5);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 1, 'service', '_bx_directory_cat_title_service', '', 'wrench', 1, 4);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 1, 'service_automotive', '_bx_directory_cat_title_automotive', '', '', 1, 1),
(@iParentId, 1, 1, 'service_educational', '_bx_directory_cat_title_educational', '', '', 1, 2),
(@iParentId, 1, 1, 'service_financial', '_bx_directory_cat_title_financial', '', '', 1, 3),
(@iParentId, 1, 1, 'service_labor', '_bx_directory_cat_title_labor_move', '', '', 1, 4),
(@iParentId, 1, 1, 'service_legal', '_bx_directory_cat_title_legal', '', '', 1, 5);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 1, 'casting', '_bx_directory_cat_title_casting', '', 'eye', 1, 5);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 1, 'casting_acting', '_bx_directory_cat_title_acting', '', '', 1, 1),
(@iParentId, 1, 1, 'casting_dance', '_bx_directory_cat_title_dance', '', '', 1, 2),
(@iParentId, 1, 1, 'casting_modeling', '_bx_directory_cat_title_modeling', '', '', 1, 3),
(@iParentId, 1, 1, 'casting_musician', '_bx_directory_cat_title_musician', '', '', 1, 4),
(@iParentId, 1, 1, 'casting_rshow', '_bx_directory_cat_title_reality_show', '', '', 1, 5);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 2, 'personal', '_bx_directory_cat_title_personal', '', 'user', 1, 6);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 2, 'personal_mw', '_bx_directory_cat_title_men_women', '', '', 1, 1),
(@iParentId, 1, 2, 'personal_wm', '_bx_directory_cat_title_women_men', '', '', 1, 2),
(@iParentId, 1, 2, 'personal_missed', '_bx_directory_cat_title_missed_connection', '', '', 1, 3);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 2, 'sale', '_bx_directory_cat_title_sale', '', 'shopping-cart', 1, 7);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 2, 'sale_barter', '_bx_directory_cat_title_barter', '', '', 1, 1),
(@iParentId, 1, 2, 'sale_clothing', '_bx_directory_cat_title_clothing', '', '', 1, 1),
(@iParentId, 1, 2, 'sale_collectible', '_bx_directory_cat_title_collectible', '', '', 1, 1);

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(0, 0, 2, 'sale_car', '_bx_directory_cat_title_sale_car', '', 'truck', 1, 8);
SET @iParentId = LAST_INSERT_ID();

INSERT INTO `bx_directory_categories` (`parent_id`, `level`, `type`, `name`, `title`, `text`, `icon`, `active`, `order`) VALUES 
(@iParentId, 1, 2, 'sale_car_part', '_bx_directory_cat_title_auto_part', '', '', 1, 1),
(@iParentId, 1, 2, 'sale_car_auto', '_bx_directory_cat_title_auto_truck', '', '', 1, 2),
(@iParentId, 1, 2, 'sale_car_motorcycle', '_bx_directory_cat_title_motorcycle', '', '', 1, 3);

-- TABLE: storages & transcoders
CREATE TABLE IF NOT EXISTS `bx_directory_covers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(128) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `ext` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(128) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `ext` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(128) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `ext` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_photos_resized` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(128) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `ext` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(128) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `ext` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_videos_resized` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(128) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `ext` varchar(32) NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

-- TABLE: comments
CREATE TABLE IF NOT EXISTS `bx_directory_cmts` (
  `cmt_id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_parent_id` int(11) NOT NULL DEFAULT '0',
  `cmt_vparent_id` int(11) NOT NULL DEFAULT '0',
  `cmt_object_id` int(11) NOT NULL DEFAULT '0',
  `cmt_author_id` int(11) NOT NULL DEFAULT '0',
  `cmt_level` int(11) NOT NULL DEFAULT '0',
  `cmt_text` text NOT NULL,
  `cmt_mood` tinyint(4) NOT NULL DEFAULT '0',
  `cmt_rate` int(11) NOT NULL DEFAULT '0',
  `cmt_rate_count` int(11) NOT NULL DEFAULT '0',
  `cmt_time` int(11) unsigned NOT NULL DEFAULT '0',
  `cmt_replies` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cmt_id`),
  KEY `cmt_object_id` (`cmt_object_id`,`cmt_parent_id`),
  FULLTEXT KEY `search_fields` (`cmt_text`)
);

-- TABLE: votes
CREATE TABLE IF NOT EXISTS `bx_directory_votes` (
  `object_id` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `sum` int(11) NOT NULL default '0',
  UNIQUE KEY `object_id` (`object_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_votes_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_nip` int(11) unsigned NOT NULL default '0',
  `value` tinyint(4) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `vote` (`object_id`, `author_nip`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_reactions` (
  `object_id` int(11) NOT NULL default '0',
  `reaction` varchar(32) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  `sum` int(11) NOT NULL default '0',
  UNIQUE KEY `reaction` (`object_id`, `reaction`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_reactions_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_nip` int(11) unsigned NOT NULL default '0',
  `reaction` varchar(32) NOT NULL default '',
  `value` tinyint(4) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `vote` (`object_id`, `author_nip`)
);

-- TABLE: views
CREATE TABLE IF NOT EXISTS `bx_directory_views_track` (
  `object_id` int(11) NOT NULL default '0',
  `viewer_id` int(11) NOT NULL default '0',
  `viewer_nip` int(11) unsigned NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  KEY `id` (`object_id`,`viewer_id`,`viewer_nip`)
);

-- TABLE: metas
CREATE TABLE IF NOT EXISTS `bx_directory_meta_keywords` (
  `object_id` int(10) unsigned NOT NULL,
  `keyword` varchar(255) NOT NULL,
  KEY `object_id` (`object_id`),
  KEY `keyword` (`keyword`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_meta_mentions` (
  `object_id` int(10) unsigned NOT NULL,
  `profile_id` int(10) unsigned NOT NULL,
  KEY `object_id` (`object_id`),
  KEY `profile_id` (`profile_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_meta_locations` (
  `object_id` int(10) unsigned NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `country` varchar(2) NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `street_number` varchar(255) NOT NULL,
  PRIMARY KEY (`object_id`),
  KEY `country_state_city` (`country`,`state`(8),`city`(8))
);

-- TABLE: reports
CREATE TABLE IF NOT EXISTS `bx_directory_reports` (
  `object_id` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  UNIQUE KEY `object_id` (`object_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_reports_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_nip` int(11) unsigned NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `text` text NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `report` (`object_id`, `author_nip`)
);

-- TABLE: favorites
CREATE TABLE IF NOT EXISTS `bx_directory_favorites_track` (
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  KEY `id` (`object_id`,`author_id`)
);

-- TABLE: scores
CREATE TABLE IF NOT EXISTS `bx_directory_scores` (
  `object_id` int(11) NOT NULL default '0',
  `count_up` int(11) NOT NULL default '0',
  `count_down` int(11) NOT NULL default '0',
  UNIQUE KEY `object_id` (`object_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_scores_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_nip` int(11) unsigned NOT NULL default '0',
  `type` varchar(8) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `vote` (`object_id`, `author_nip`)
);

-- TABLE: polls
CREATE TABLE IF NOT EXISTS `bx_directory_polls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL default '0',
  `content_id` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `search_fields` (`text`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_polls_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `rate` float NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_polls_answers_votes` (
  `object_id` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `sum` int(11) NOT NULL default '0',
  UNIQUE KEY `object_id` (`object_id`)
);

CREATE TABLE IF NOT EXISTS `bx_directory_polls_answers_votes_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_nip` int(11) unsigned NOT NULL default '0',
  `value` tinyint(4) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `vote` (`object_id`, `author_nip`)
);

-- STORAGES & TRANSCODERS
SET @sStorageEngine = (SELECT `value` FROM `sys_options` WHERE `name` = 'sys_storage_default');

INSERT INTO `sys_objects_storage` (`object`, `engine`, `params`, `token_life`, `cache_control`, `levels`, `table_files`, `ext_mode`, `ext_allow`, `ext_deny`, `quota_size`, `current_size`, `quota_number`, `current_number`, `max_file_size`, `ts`) VALUES
('bx_directory_covers', @sStorageEngine, '', 360, 2592000, 3, 'bx_directory_covers', 'allow-deny', 'jpg,jpeg,jpe,gif,png', '', 0, 0, 0, 0, 0, 0),

('bx_directory_photos', @sStorageEngine, '', 360, 2592000, 3, 'bx_directory_photos', 'allow-deny', 'jpg,jpeg,jpe,gif,png', '', 0, 0, 0, 0, 0, 0),
('bx_directory_photos_resized', @sStorageEngine, '', 360, 2592000, 3, 'bx_directory_photos_resized', 'allow-deny', 'jpg,jpeg,jpe,gif,png', '', 0, 0, 0, 0, 0, 0),

('bx_directory_videos', @sStorageEngine, '', 360, 2592000, 3, 'bx_directory_videos', 'allow-deny', 'avi,flv,mpg,mpeg,wmv,mp4,m4v,mov,qt,divx,xvid,3gp,3g2,webm,mkv,ogv,ogg,rm,rmvb,asf,drc', '', 0, 0, 0, 0, 0, 0),
('bx_directory_videos_resized', @sStorageEngine, '', 360, 2592000, 3, 'bx_directory_videos_resized', 'allow-deny', 'jpg,jpeg,jpe,gif,png,avi,flv,mpg,mpeg,wmv,mp4,m4v,mov,qt,divx,xvid,3gp,3g2,webm,mkv,ogv,ogg,rm,rmvb,asf,drc', '', 0, 0, 0, 0, 0, 0),

('bx_directory_files', @sStorageEngine, '', 360, 2592000, 3, 'bx_directory_files', 'deny-allow', '', 'action,apk,app,bat,bin,cmd,com,command,cpl,csh,exe,gadget,inf,ins,inx,ipa,isu,job,jse,ksh,lnk,msc,msi,msp,mst,osx,out,paf,pif,prg,ps1,reg,rgs,run,sct,shb,shs,u3p,vb,vbe,vbs,vbscript,workflow,ws,wsf', 0, 0, 0, 0, 0, 0);

INSERT INTO `sys_objects_transcoder` (`object`, `storage_object`, `source_type`, `source_params`, `private`, `atime_tracking`, `atime_pruning`, `ts`, `override_class_name`, `override_class_file`) VALUES 
('bx_directory_preview', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_covers";}', 'no', '1', '2592000', '0', '', ''),
('bx_directory_gallery', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_covers";}', 'no', '1', '2592000', '0', '', ''),
('bx_directory_cover', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_covers";}', 'no', '1', '2592000', '0', '', ''),

('bx_directory_preview_photos', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_photos";}', 'no', '1', '2592000', '0', '', ''),
('bx_directory_gallery_photos', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_photos";}', 'no', '1', '2592000', '0', '', ''),

('bx_directory_videos_poster', 'bx_directory_videos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_videos";}', 'no', '0', '0', '0', 'BxDolTranscoderVideo', ''),
('bx_directory_videos_poster_preview', 'bx_directory_videos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_videos";}', 'no', '0', '0', '0', 'BxDolTranscoderVideo', ''),
('bx_directory_videos_mp4', 'bx_directory_videos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_videos";}', 'no', '0', '0', '0', 'BxDolTranscoderVideo', ''),
('bx_directory_videos_mp4_hd', 'bx_directory_videos_resized', 'Storage', 'a:1:{s:6:"object";s:19:"bx_directory_videos";}', 'no', '0', '0', '0', 'BxDolTranscoderVideo', ''),

('bx_directory_preview_files', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:18:"bx_directory_files";}', 'no', '1', '2592000', '0', '', ''),
('bx_directory_gallery_files', 'bx_directory_photos_resized', 'Storage', 'a:1:{s:6:"object";s:18:"bx_directory_files";}', 'no', '1', '2592000', '0', '', '');

INSERT INTO `sys_transcoder_filters` (`transcoder_object`, `filter`, `filter_params`, `order`) VALUES 
('bx_directory_preview', 'Resize', 'a:3:{s:1:"w";s:3:"300";s:1:"h";s:3:"200";s:11:"crop_resize";s:1:"1";}', '0'),
('bx_directory_gallery', 'Resize', 'a:1:{s:1:"w";s:3:"500";}', '0'),
('bx_directory_cover', 'Resize', 'a:1:{s:1:"w";s:4:"2000";}', '0'),

('bx_directory_preview_photos', 'Resize', 'a:3:{s:1:"w";s:3:"300";s:1:"h";s:3:"200";s:11:"crop_resize";s:1:"1";}', '0'),
('bx_directory_gallery_photos', 'Resize', 'a:1:{s:1:"w";s:3:"500";}', '0'),

('bx_directory_videos_poster_preview', 'Resize', 'a:3:{s:1:"w";s:3:"300";s:1:"h";s:3:"200";s:13:"square_resize";s:1:"1";}', 10),
('bx_directory_videos_poster_preview', 'Poster', 'a:2:{s:1:"h";s:3:"480";s:10:"force_type";s:3:"jpg";}', 0),
('bx_directory_videos_poster', 'Poster', 'a:2:{s:1:"h";s:3:"318";s:10:"force_type";s:3:"jpg";}', 0),
('bx_directory_videos_mp4', 'Mp4', 'a:2:{s:1:"h";s:3:"318";s:10:"force_type";s:3:"mp4";}', 0),
('bx_directory_videos_mp4_hd', 'Mp4', 'a:3:{s:1:"h";s:3:"720";s:13:"video_bitrate";s:4:"1536";s:10:"force_type";s:3:"mp4";}', 0),

('bx_directory_preview_files', 'Resize', 'a:3:{s:1:"w";s:3:"300";s:1:"h";s:3:"200";s:11:"crop_resize";s:1:"1";}', '0'),
('bx_directory_gallery_files', 'Resize', 'a:1:{s:1:"w";s:3:"500";}', '0');


-- FORMS: category
INSERT INTO `sys_objects_form`(`object`, `module`, `title`, `action`, `form_attrs`, `table`, `key`, `uri`, `uri_title`, `submit_name`, `params`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_directory_category', 'bx_directory', '_bx_directory_form_category', '', 'a:1:{s:7:"enctype";s:19:"multipart/form-data";}', 'bx_directory_categories', 'id', '', '', 'a:1:{i:0;s:9:"do_submit";}', '', 0, 1, 'BxDirFormCategory', 'modules/boonex/directory/classes/BxDirFormCategory.php');

INSERT INTO `sys_form_displays`(`object`, `display_name`, `module`, `view_mode`, `title`) VALUES 
('bx_directory_category', 'bx_directory_category_add', 'bx_directory', 0, '_bx_directory_form_category_display_add'),
('bx_directory_category', 'bx_directory_category_delete', 'bx_directory', 0, '_bx_directory_form_category_display_delete'),
('bx_directory_category', 'bx_directory_category_edit', 'bx_directory', 0, '_bx_directory_form_category_display_edit');

INSERT INTO `sys_form_inputs`(`object`, `module`, `name`, `value`, `values`, `checked`, `type`, `caption_system`, `caption`, `info`, `required`, `collapsed`, `html`, `attrs`, `attrs_tr`, `attrs_wrapper`, `checker_func`, `checker_params`, `checker_error`, `db_pass`, `db_params`, `editable`, `deletable`) VALUES 
('bx_directory_category', 'bx_directory', 'delete_confirm', 1, '', 0, 'checkbox', '_bx_directory_form_category_input_sys_delete_confirm', '_bx_directory_form_category_input_delete_confirm', '_bx_directory_form_category_input_delete_confirm_info', 1, 0, 0, '', '', '', 'Avail', '', '_bx_directory_form_category_input_delete_confirm_error', '', '', 1, 0),
('bx_directory_category', 'bx_directory', 'parent_id', '', '', 0, 'select', '_bx_directory_form_category_input_sys_parent_id', '_bx_directory_form_category_input_parent_id', '', 0, 0, 0, '', '', '', '', '', '', 'Int', '', 1, 0),
('bx_directory_category', 'bx_directory', 'title', '', '', 0, 'text_translatable', '_bx_directory_form_category_input_sys_title', '_bx_directory_form_category_input_title', '', 1, 0, 0, '', '', '', 'AvailTranslatable', 'a:1:{i:0;s:5:"title";}', '_bx_directory_form_category_input_title_err', 'Xss', '', 1, 0),
('bx_directory_category', 'bx_directory', 'text', '', '', 0, 'textarea_translatable', '_bx_directory_form_category_input_sys_text', '_bx_directory_form_category_input_text', '', 0, 0, 2, '', '', '', '', '', '', 'XssHtml', '', 1, 0),
('bx_directory_category', 'bx_directory', 'type', '', '', 0, 'select', '_bx_directory_form_category_input_sys_type', '_bx_directory_form_category_input_type', '_bx_directory_form_category_input_type_info', 1, 0, 0, '', '', '', 'Avail', '', '_bx_directory_form_category_input_type_err', 'Int', '', 0, 0),
('bx_directory_category', 'bx_directory', 'type_clone', 'on', '', 0, 'switcher', '_bx_directory_form_category_input_sys_type_clone', '_bx_directory_form_category_input_type_clone', '', 0, 0, 0, 'a:1:{s:8:"onchange";s:33:"oBxDirStudio.onChangeClone(this);";}', '', '', '', '', '', '', '', 0, 0),
('bx_directory_category', 'bx_directory', 'type_title', '', '', 0, 'text_translatable', '_bx_directory_form_category_input_sys_type_title', '_bx_directory_form_category_input_type_title', '', 0, 0, 0, 'a:1:{s:8:"disabled";s:8:"disabled";}', 'a:1:{s:5:"style";s:13:"display:none;";}', '', '', '', '', '', '', 0, 0),
('bx_directory_category', 'bx_directory', 'icon', '', '', 0, 'text', '_bx_directory_form_category_input_sys_icon', '_bx_directory_form_category_input_icon', '', 0, 0, 0, '', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_directory_category', 'bx_directory', 'controls', '', 'do_submit,do_cancel', 0, 'input_set', '', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 0, 0),
('bx_directory_category', 'bx_directory', 'do_submit', '_bx_directory_form_category_input_do_submit', '', 0, 'submit', '_bx_directory_form_category_input_sys_do_submit', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory_category', 'bx_directory', 'do_cancel', '_bx_directory_form_category_input_do_cancel', '', 0, 'button', '_bx_directory_form_category_input_sys_do_cancel', '', '', 0, 0, 0, 'a:2:{s:7:"onclick";s:45:"$(''.bx-popup-applied:visible'').dolPopupHide()";s:5:"class";s:22:"bx-def-margin-sec-left";}', '', '', '', '', '', '', '', 1, 0);

INSERT INTO `sys_form_display_inputs`(`display_name`, `input_name`, `visible_for_levels`, `active`, `order`) VALUES 
('bx_directory_category_add', 'parent_id', 2147483647, 1, 1),
('bx_directory_category_add', 'type', 2147483647, 1, 2),
('bx_directory_category_add', 'type_clone', 2147483647, 1, 3),
('bx_directory_category_add', 'type_title', 2147483647, 1, 4),
('bx_directory_category_add', 'title', 2147483647, 1, 5),
('bx_directory_category_add', 'text', 2147483647, 1, 6),
('bx_directory_category_add', 'icon', 2147483647, 1, 7),
('bx_directory_category_add', 'controls', 2147483647, 1, 8),
('bx_directory_category_add', 'do_submit', 2147483647, 1, 9),
('bx_directory_category_add', 'do_cancel', 2147483647, 1, 10),

('bx_directory_category_delete', 'delete_confirm', 2147483647, 1, 1),
('bx_directory_category_delete', 'do_submit', 2147483647, 1, 2),

('bx_directory_category_edit', 'parent_id', 2147483647, 1, 1),
('bx_directory_category_edit', 'title', 2147483647, 1, 2),
('bx_directory_category_edit', 'text', 2147483647, 1, 3),
('bx_directory_category_edit', 'icon', 2147483647, 1, 4),
('bx_directory_category_edit', 'controls', 2147483647, 1, 5),
('bx_directory_category_edit', 'do_submit', 2147483647, 1, 6),
('bx_directory_category_edit', 'do_cancel', 2147483647, 1, 7);


-- FORMS: entry (ad)
INSERT INTO `sys_objects_form`(`object`, `module`, `title`, `action`, `form_attrs`, `table`, `key`, `uri`, `uri_title`, `submit_name`, `params`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_directory', 'bx_directory', '_bx_directory_form_entry', '', 'a:1:{s:7:"enctype";s:19:"multipart/form-data";}', 'bx_directory_entries', 'id', '', '', 'a:1:{i:0;s:9:"do_submit";}', '', 0, 1, 'BxDirFormEntry', 'modules/boonex/directory/classes/BxDirFormEntry.php');

INSERT INTO `sys_form_displays`(`object`, `display_name`, `module`, `view_mode`, `title`) VALUES 
('bx_directory', 'bx_directory_entry_add', 'bx_directory', 0, '_bx_directory_form_entry_display_add'),
('bx_directory', 'bx_directory_entry_delete', 'bx_directory', 0, '_bx_directory_form_entry_display_delete'),

('bx_directory', 'bx_directory_entry_price_add', 'bx_directory', 0, '_bx_directory_form_entry_price_display_add'),
('bx_directory', 'bx_directory_entry_price_edit', 'bx_directory', 0, '_bx_directory_form_entry_price_display_edit'),
('bx_directory', 'bx_directory_entry_price_view', 'bx_directory', 1, '_bx_directory_form_entry_price_display_view'),

('bx_directory', 'bx_directory_entry_price_year_add', 'bx_directory', 0, '_bx_directory_form_entry_price_year_display_add'),
('bx_directory', 'bx_directory_entry_price_year_edit', 'bx_directory', 0, '_bx_directory_form_entry_price_year_display_edit'),
('bx_directory', 'bx_directory_entry_price_year_view', 'bx_directory', 1, '_bx_directory_form_entry_price_year_display_view');

INSERT INTO `sys_form_inputs`(`object`, `module`, `name`, `value`, `values`, `checked`, `type`, `caption_system`, `caption`, `info`, `required`, `collapsed`, `html`, `attrs`, `attrs_tr`, `attrs_wrapper`, `checker_func`, `checker_params`, `checker_error`, `db_pass`, `db_params`, `editable`, `deletable`) VALUES 
('bx_directory', 'bx_directory', 'allow_view_to', '', '', 0, 'custom', '_bx_directory_form_entry_input_sys_allow_view_to', '_bx_directory_form_entry_input_allow_view_to', '', 1, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'delete_confirm', 1, '', 0, 'checkbox', '_bx_directory_form_entry_input_sys_delete_confirm', '_bx_directory_form_entry_input_delete_confirm', '_bx_directory_form_entry_input_delete_confirm_info', 1, 0, 0, '', '', '', 'Avail', '', '_bx_directory_form_entry_input_delete_confirm_error', '', '', 1, 0),
('bx_directory', 'bx_directory', 'do_submit', '_bx_directory_form_entry_input_do_submit', '', 0, 'submit', '_bx_directory_form_entry_input_sys_do_submit', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'location', '', '', 0, 'location', '_sys_form_input_sys_location', '_sys_form_input_location', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'covers', 'a:1:{i:0;s:18:"bx_directory_html5";}', 'a:2:{s:19:"bx_directory_simple";s:26:"_sys_uploader_simple_title";s:18:"bx_directory_html5";s:25:"_sys_uploader_html5_title";}', 0, 'files', '_bx_directory_form_entry_input_sys_covers', '_bx_directory_form_entry_input_covers', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'pictures', 'a:1:{i:0;s:25:"bx_directory_photos_html5";}', 'a:2:{s:26:"bx_directory_photos_simple";s:26:"_sys_uploader_simple_title";s:25:"bx_directory_photos_html5";s:25:"_sys_uploader_html5_title";}', 0, 'files', '_bx_directory_form_entry_input_sys_pictures', '_bx_directory_form_entry_input_pictures', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'videos', 'a:1:{i:0;s:25:"bx_directory_videos_html5";}', 'a:2:{s:26:"bx_directory_videos_simple";s:26:"_sys_uploader_simple_title";s:25:"bx_directory_videos_html5";s:25:"_sys_uploader_html5_title";}', 0, 'files', '_bx_directory_form_entry_input_sys_videos', '_bx_directory_form_entry_input_videos', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'files', 'a:1:{i:0;s:24:"bx_directory_files_html5";}', 'a:2:{s:25:"bx_directory_files_simple";s:26:"_sys_uploader_simple_title";s:24:"bx_directory_files_html5";s:25:"_sys_uploader_html5_title";}', 0, 'files', '_bx_directory_form_entry_input_sys_files', '_bx_directory_form_entry_input_files', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'polls', '', '', 0, 'custom', '_bx_directory_form_entry_input_sys_polls', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'text', '', '', 0, 'textarea', '_bx_directory_form_entry_input_sys_text', '_bx_directory_form_entry_input_text', '', 1, 0, 2, '', '', '', 'Avail', '', '_bx_directory_form_entry_input_text_err', 'XssHtml', '', 1, 0),
('bx_directory', 'bx_directory', 'title', '', '', 0, 'text', '_bx_directory_form_entry_input_sys_title', '_bx_directory_form_entry_input_title', '', 1, 0, 0, '', '', '', 'Avail', '', '_bx_directory_form_entry_input_title_err', 'Xss', '', 1, 0),
('bx_directory', 'bx_directory', 'price', '', '', 0, 'text', '_bx_directory_form_entry_input_sys_price', '_bx_directory_form_entry_input_price', '', 0, 0, 0, '', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_directory', 'bx_directory', 'year', '', '', 0, 'text', '_bx_directory_form_entry_input_sys_year', '_bx_directory_form_entry_input_year', '', 0, 0, 0, '', '', '', '', '', '', 'Xss', '', 1, 0),
('bx_directory', 'bx_directory', 'category', '', '', 0, 'hidden', '_bx_directory_form_entry_input_sys_category', '', '', 0, 0, 0, '', '', '', '', '', '', 'Int', '', 0, 0),
('bx_directory', 'bx_directory', 'category_view', '', '', 0, 'text', '_bx_directory_form_entry_input_sys_category_view', '_bx_directory_form_entry_input_category_view', '', 0, 0, 0, 'a:1:{s:8:"disabled";s:8:"disabled";}', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'category_select', '', '', 0, 'select', '_bx_directory_form_entry_input_sys_category_select', '_bx_directory_form_entry_input_category_select', '', 1, 0, 0, 'a:1:{s:8:"onchange";s:35:"oBxDirEntry.onChangeCategory(this);";}', '', '', '', '', '', '', '', 0, 0),
('bx_directory', 'bx_directory', 'added', '', '', 0, 'datetime', '_bx_directory_form_entry_input_sys_date_added', '_bx_directory_form_entry_input_date_added', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'changed', '', '', 0, 'datetime', '_bx_directory_form_entry_input_sys_date_changed', '_bx_directory_form_entry_input_date_changed', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'attachments', '', '', 0, 'custom', '_bx_directory_form_entry_input_sys_attachments', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0),
('bx_directory', 'bx_directory', 'labels', '', '', 0, 'custom', '_sys_form_input_sys_labels', '_sys_form_input_labels', '', 0, 0, 0, '', '', '', '', '', '', '', '', 1, 0);

INSERT INTO `sys_form_display_inputs`(`display_name`, `input_name`, `visible_for_levels`, `active`, `order`) VALUES 
('bx_directory_entry_add', 'category_select', 2147483647, 1, 1),

('bx_directory_entry_delete', 'delete_confirm', 2147483647, 1, 1),
('bx_directory_entry_delete', 'do_submit', 2147483647, 1, 2),

('bx_directory_entry_price_add', 'category', 2147483647, 1, 1),
('bx_directory_entry_price_add', 'category_view', 2147483647, 1, 2),
('bx_directory_entry_price_add', 'title', 2147483647, 1, 3),
('bx_directory_entry_price_add', 'price', 2147483647, 1, 4),
('bx_directory_entry_price_add', 'text', 2147483647, 1, 5),
('bx_directory_entry_price_add', 'attachments', 2147483647, 1, 6),
('bx_directory_entry_price_add', 'pictures', 2147483647, 1, 7),
('bx_directory_entry_price_add', 'videos', 2147483647, 1, 8),
('bx_directory_entry_price_add', 'files', 2147483647, 1, 9),
('bx_directory_entry_price_add', 'polls', 2147483647, 1, 10),
('bx_directory_entry_price_add', 'covers', 2147483647, 1, 11),
('bx_directory_entry_price_add', 'allow_view_to', 2147483647, 1, 12),
('bx_directory_entry_price_add', 'location', 2147483647, 1, 13),
('bx_directory_entry_price_add', 'do_submit', 2147483647, 1, 14),

('bx_directory_entry_price_edit', 'category_view', 2147483647, 1, 1),
('bx_directory_entry_price_edit', 'title', 2147483647, 1, 2),
('bx_directory_entry_price_edit', 'price', 2147483647, 1, 3),
('bx_directory_entry_price_edit', 'text', 2147483647, 1, 4),
('bx_directory_entry_price_edit', 'attachments', 2147483647, 1, 5),
('bx_directory_entry_price_edit', 'pictures', 2147483647, 1, 6),
('bx_directory_entry_price_edit', 'videos', 2147483647, 1, 7),
('bx_directory_entry_price_edit', 'files', 2147483647, 1, 8),
('bx_directory_entry_price_edit', 'polls', 2147483647, 1, 9),
('bx_directory_entry_price_edit', 'covers', 2147483647, 1, 10),
('bx_directory_entry_price_edit', 'allow_view_to', 2147483647, 1, 11),
('bx_directory_entry_price_edit', 'location', 2147483647, 1, 12),
('bx_directory_entry_price_edit', 'do_submit', 2147483647, 1, 13),

('bx_directory_entry_price_view', 'category_view', 2147483647, 1, 1),
('bx_directory_entry_price_view', 'price', 2147483647, 1, 2),
('bx_directory_entry_price_view', 'added', 2147483647, 1, 3),
('bx_directory_entry_price_view', 'changed', 2147483647, 1, 4),

('bx_directory_entry_price_year_add', 'category', 2147483647, 1, 1),
('bx_directory_entry_price_year_add', 'category_view', 2147483647, 1, 2),
('bx_directory_entry_price_year_add', 'title', 2147483647, 1, 3),
('bx_directory_entry_price_year_add', 'price', 2147483647, 1, 4),
('bx_directory_entry_price_year_add', 'year', 2147483647, 1, 5),
('bx_directory_entry_price_year_add', 'text', 2147483647, 1, 6),
('bx_directory_entry_price_year_add', 'attachments', 2147483647, 1, 7),
('bx_directory_entry_price_year_add', 'pictures', 2147483647, 1, 8),
('bx_directory_entry_price_year_add', 'videos', 2147483647, 1, 9),
('bx_directory_entry_price_year_add', 'files', 2147483647, 1, 10),
('bx_directory_entry_price_year_add', 'polls', 2147483647, 1, 11),
('bx_directory_entry_price_year_add', 'covers', 2147483647, 1, 12),
('bx_directory_entry_price_year_add', 'allow_view_to', 2147483647, 1, 13),
('bx_directory_entry_price_year_add', 'location', 2147483647, 1, 14),
('bx_directory_entry_price_year_add', 'do_submit', 2147483647, 1, 15),

('bx_directory_entry_price_year_edit', 'category_view', 2147483647, 1, 1),
('bx_directory_entry_price_year_edit', 'title', 2147483647, 1, 2),
('bx_directory_entry_price_year_edit', 'price', 2147483647, 1, 3),
('bx_directory_entry_price_year_edit', 'year', 2147483647, 1, 4),
('bx_directory_entry_price_year_edit', 'text', 2147483647, 1, 5),
('bx_directory_entry_price_year_edit', 'attachments', 2147483647, 1, 6),
('bx_directory_entry_price_year_edit', 'pictures', 2147483647, 1, 7),
('bx_directory_entry_price_year_edit', 'videos', 2147483647, 1, 8),
('bx_directory_entry_price_year_edit', 'files', 2147483647, 1, 9),
('bx_directory_entry_price_year_edit', 'polls', 2147483647, 1, 10),
('bx_directory_entry_price_year_edit', 'covers', 2147483647, 1, 11),
('bx_directory_entry_price_year_edit', 'allow_view_to', 2147483647, 1, 12),
('bx_directory_entry_price_year_edit', 'location', 2147483647, 1, 13),
('bx_directory_entry_price_year_edit', 'do_submit', 2147483647, 1, 14),

('bx_directory_entry_price_year_view', 'category_view', 2147483647, 1, 1),
('bx_directory_entry_price_year_view', 'price', 2147483647, 1, 2),
('bx_directory_entry_price_year_view', 'year', 2147483647, 1, 3),
('bx_directory_entry_price_year_view', 'added', 2147483647, 1, 4),
('bx_directory_entry_price_year_view', 'changed', 2147483647, 1, 5);

-- FORMS: poll
INSERT INTO `sys_objects_form` (`object`, `module`, `title`, `action`, `form_attrs`, `submit_name`, `table`, `key`, `uri`, `uri_title`, `params`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES
('bx_directory_poll', 'bx_directory', '_bx_directory_form_poll', '', '', 'do_submit', 'bx_directory_polls', 'id', '', '', 'a:1:{s:14:"checker_helper";s:26:"BxDirFormPollCheckerHelper";}', 0, 1, 'BxDirFormPoll', 'modules/boonex/directory/classes/BxDirFormPoll.php');

INSERT INTO `sys_form_displays` (`display_name`, `module`, `object`, `title`, `view_mode`) VALUES
('bx_directory_poll_add', 'bx_directory', 'bx_directory_poll', '_bx_directory_form_poll_display_add', 0);

INSERT INTO `sys_form_inputs` (`object`, `module`, `name`, `value`, `values`, `checked`, `type`, `caption_system`, `caption`, `info`, `required`, `collapsed`, `html`, `attrs`, `attrs_tr`, `attrs_wrapper`, `checker_func`, `checker_params`, `checker_error`, `db_pass`, `db_params`, `editable`, `deletable`) VALUES
('bx_directory_poll', 'bx_directory', 'text', '', '', 0, 'textarea', '_bx_directory_form_poll_input_sys_text', '_bx_directory_form_poll_input_text', '', 1, 0, 3, '', '', '', 'Avail', '', '_bx_directory_form_poll_input_text_err', 'Xss', '', 1, 0),
('bx_directory_poll', 'bx_directory', 'answers', '', '', 0, 'custom', '_bx_directory_form_poll_input_sys_answers', '_bx_directory_form_poll_input_answers', '', 1, 0, 0, '', '', '', 'AvailAnswers', '', '_bx_directory_form_poll_input_answers_err', '', '', 1, 0),
('bx_directory_poll', 'bx_directory', 'controls', '', 'do_submit,do_cancel', 0, 'input_set', '', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 0, 0),
('bx_directory_poll', 'bx_directory', 'do_submit', '_bx_directory_form_poll_input_do_submit', '', 0, 'submit', '_bx_directory_form_poll_input_sys_do_submit', '', '', 0, 0, 0, '', '', '', '', '', '', '', '', 0, 0),
('bx_directory_poll', 'bx_directory', 'do_cancel', '_bx_directory_form_poll_input_do_cancel', '', 0, 'button', '_bx_directory_form_poll_input_do_cancel', '', '', 0, 0, 0, 'a:2:{s:7:"onclick";s:45:"$(''.bx-popup-applied:visible'').dolPopupHide()";s:5:"class";s:22:"bx-def-margin-sec-left";}', '', '', '', '', '', '', '', 0, 0);

INSERT INTO `sys_form_display_inputs` (`display_name`, `input_name`, `visible_for_levels`, `active`, `order`) VALUES
('bx_directory_poll_add', 'text', 2147483647, 1, 1),
('bx_directory_poll_add', 'answers', 2147483647, 1, 2),
('bx_directory_poll_add', 'controls', 2147483647, 1, 3),
('bx_directory_poll_add', 'do_submit', 2147483647, 1, 4),
('bx_directory_poll_add', 'do_cancel', 2147483647, 1, 5);


-- COMMENTS
INSERT INTO `sys_objects_cmts` (`Name`, `Module`, `Table`, `CharsPostMin`, `CharsPostMax`, `CharsDisplayMax`, `Html`, `PerView`, `PerViewReplies`, `BrowseType`, `IsBrowseSwitch`, `PostFormPosition`, `NumberOfLevels`, `IsDisplaySwitch`, `IsRatable`, `ViewingThreshold`, `IsOn`, `RootStylePrefix`, `BaseUrl`, `ObjectVote`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldAuthor`, `TriggerFieldTitle`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES
('bx_directory', 'bx_directory', 'bx_directory_cmts', 1, 5000, 1000, 3, 5, 3, 'tail', 1, 'bottom', 1, 1, 1, -3, 1, 'cmt', 'page.php?i=view-post&id={object_id}', '', 'bx_directory_entries', 'id', 'author', 'title', 'comments', '', '');

-- VOTES
INSERT INTO `sys_objects_vote` (`Name`, `TableMain`, `TableTrack`, `PostTimeout`, `MinValue`, `MaxValue`, `IsUndo`, `IsOn`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldAuthor`, `TriggerFieldRate`, `TriggerFieldRateCount`, `ClassName`, `ClassFile`) VALUES 
('bx_directory', 'bx_directory_votes', 'bx_directory_votes_track', '604800', '1', '1', '0', '1', 'bx_directory_entries', 'id', 'author', 'rate', 'votes', '', ''),
('bx_directory_reactions', 'bx_directory_reactions', 'bx_directory_reactions_track', '604800', '1', '1', '1', '1', 'bx_directory_entries', 'id', 'author', 'rrate', 'rvotes', 'BxTemplVoteReactions', ''),
('bx_directory_poll_answers', 'bx_directory_polls_answers_votes', 'bx_directory_polls_answers_votes_track', '604800', '1', '1', '0', '1', 'bx_directory_polls_answers', 'id', 'author_id', 'rate', 'votes', 'BxDirVotePollAnswers', 'modules/boonex/directory/classes/BxDirVotePollAnswers.php');

-- SCORES
INSERT INTO `sys_objects_score` (`name`, `module`, `table_main`, `table_track`, `post_timeout`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_score`, `trigger_field_cup`, `trigger_field_cdown`, `class_name`, `class_file`) VALUES 
('bx_directory', 'bx_directory', 'bx_directory_scores', 'bx_directory_scores_track', '604800', '0', 'bx_directory_entries', 'id', 'author', 'score', 'sc_up', 'sc_down', '', '');

-- REPORTS
INSERT INTO `sys_objects_report` (`name`, `table_main`, `table_track`, `is_on`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_directory', 'bx_directory_reports', 'bx_directory_reports_track', '1', 'page.php?i=view-post&id={object_id}', 'bx_directory_entries', 'id', 'author', 'reports', '', '');

-- VIEWS
INSERT INTO `sys_objects_view` (`name`, `table_track`, `period`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_directory', 'bx_directory_views_track', '86400', '1', 'bx_directory_entries', 'id', 'author', 'views', '', '');

-- FAFORITES
INSERT INTO `sys_objects_favorite` (`name`, `table_track`, `is_on`, `is_undo`, `is_public`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_directory', 'bx_directory_favorites_track', '1', '1', '1', 'page.php?i=view-post&id={object_id}', 'bx_directory_entries', 'id', 'author', 'favorites', '', '');

-- FEATURED
INSERT INTO `sys_objects_feature` (`name`, `is_on`, `is_undo`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_flag`, `class_name`, `class_file`) VALUES 
('bx_directory', '1', '1', 'page.php?i=view-post&id={object_id}', 'bx_directory_entries', 'id', 'author', 'featured', '', '');

-- CONTENT INFO
INSERT INTO `sys_objects_content_info` (`name`, `title`, `alert_unit`, `alert_action_add`, `alert_action_update`, `alert_action_delete`, `class_name`, `class_file`) VALUES
('bx_directory', '_bx_directory', 'bx_directory', 'added', 'edited', 'deleted', '', ''),
('bx_directory_cmts', '_bx_directory_cmts', 'bx_directory', 'commentPost', 'commentUpdated', 'commentRemoved', 'BxDolContentInfoCmts', '');

INSERT INTO `sys_content_info_grids` (`object`, `grid_object`, `grid_field_id`, `condition`, `selection`) VALUES
('bx_directory', 'bx_directory_administration', 'id', '', ''),
('bx_directory', 'bx_directory_common', 'id', '', '');

-- SEARCH EXTENDED
INSERT INTO `sys_objects_search_extended` (`object`, `object_content_info`, `module`, `title`, `active`, `class_name`, `class_file`) VALUES
('bx_directory', 'bx_directory', 'bx_directory', '_bx_directory_search_extended', 1, '', ''),
('bx_directory_cmts', 'bx_directory_cmts', 'bx_directory', '_bx_directory_search_extended_cmts', 1, 'BxTemplSearchExtendedCmts', '');

-- STUDIO: page & widget
INSERT INTO `sys_std_pages`(`index`, `name`, `header`, `caption`, `icon`) VALUES
(3, 'bx_directory', '_bx_directory', '_bx_directory', 'bx_directory@modules/boonex/directory/|std-icon.svg');
SET @iPageId = LAST_INSERT_ID();

SET @iParentPageId = (SELECT `id` FROM `sys_std_pages` WHERE `name` = 'home');
SET @iParentPageOrder = (SELECT MAX(`order`) FROM `sys_std_pages_widgets` WHERE `page_id` = @iParentPageId);
INSERT INTO `sys_std_widgets` (`page_id`, `module`, `url`, `click`, `icon`, `caption`, `cnt_notices`, `cnt_actions`) VALUES
(@iPageId, 'bx_directory', '{url_studio}module.php?name=bx_directory', '', 'bx_directory@modules/boonex/directory/|std-icon.svg', '_bx_directory', '', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_actions";s:6:"params";a:0:{}s:5:"class";s:18:"TemplStudioModules";}');
INSERT INTO `sys_std_pages_widgets` (`page_id`, `widget_id`, `order`) VALUES
(@iParentPageId, LAST_INSERT_ID(), IF(ISNULL(@iParentPageOrder), 1, @iParentPageOrder + 1));

