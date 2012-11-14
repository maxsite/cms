# ======================================= 
#    MaxSite CMS (http://max-3000.com/)
#      Создание таблиц базы данных
# ======================================= 
# Запросы разделяйте тройным #
# _PREFIX_ - заменяется на префикс таблиц
# _CHARSETCOLLATE_ - заменяется на DEFAULT CHARACTER SET xxx COLLATE xxx
# _USERNAME_
# _USERPASSWORD_
# _USEREMAIL_
# _NAMESITE_
# _IP_
# ======================================= 

DROP TABLE IF EXISTS _PREFIX_options;
###
CREATE TABLE _PREFIX_options (
	options_id bigint(20) NOT NULL auto_increment,
	options_key varchar(255) NOT NULL default '',
	options_value longtext,
	options_type varchar(255) default 'general',
	PRIMARY KEY (options_id),
	KEY options_key (options_key),
	KEY options_type (options_type)
) _CHARSETCOLLATE_ ENGINE=MyISAM;


###
DROP TABLE IF EXISTS _PREFIX_groups;
###
CREATE TABLE _PREFIX_groups (
	groups_id bigint(20) NOT NULL auto_increment,
	groups_name varchar(255) NOT NULL default 'groups',
	groups_rules longtext,
	PRIMARY KEY (groups_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_groups (groups_name) VALUES ('admins');
###
INSERT INTO _PREFIX_groups (groups_name) VALUES ('users');



###
DROP TABLE IF EXISTS _PREFIX_users;
###
CREATE TABLE _PREFIX_users (
	users_id bigint(20) NOT NULL auto_increment,
	users_login varchar(255) default '',
	users_password varchar(255) default '',	
	users_levels_id bigint(20) NOT NULL default '1',
	users_groups_id bigint(20) NOT NULL default '2',
	users_first_name varchar(255) default '',
	users_last_name varchar(255) default '',
	users_nik varchar(255) default '',
	users_count_comments bigint(20) NOT NULL default '0',
	users_icq varchar(255) default '',
	users_email varchar(255) default '',
	users_url varchar(255) default '',
	users_msn varchar(255) default '',
	users_jaber varchar(255) default '',
	users_skype varchar(255) default '',
	users_date_registr datetime NOT NULL default '2008-01-01 00:00:00',
	users_date_birth datetime NOT NULL default '1970-01-01 00:00:00',
	users_last_visit datetime NOT NULL default '2008-01-01 00:00:00',
	users_avatar_url varchar(255) default '',
	users_description longtext,
	users_ip_register varchar(50) default '',
	users_show_smiles enum('0','1') NOT NULL default '1',
	users_show_wis_editor enum('0','1') NOT NULL default '1',
	users_time_zone bigint(20) NOT NULL default '7200',
	users_language varchar(5) default 'ru',
	users_skins varchar(255) default '',
	users_notify enum('0','1') NOT NULL default '0',
	users_admin_note longtext,
	users_activate_string varchar(255) default '',
	users_activate_key varchar(255) default '',
	users_rules longtext,
	PRIMARY KEY (users_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_users (users_login, users_password, users_nik, users_email, users_date_registr, users_last_visit, users_ip_register, users_levels_id, users_groups_id) VALUES ('_USERNAME_', '_USERPASSWORD_', '_USERNAME_', '_USEREMAIL_', NOW(), NOW(), '_IP_', 10, 1 );


###
DROP TABLE IF EXISTS _PREFIX_meta;
###
CREATE TABLE _PREFIX_meta (
	meta_id bigint(20) NOT NULL auto_increment,
	meta_key varchar(255) default NULL,
	meta_id_obj bigint(20) NOT NULL default '0',
	meta_table varchar(255) default '',
	meta_value longtext,
	meta_desc longtext,
	meta_menu_order bigint(20) NOT NULL default '0',
	meta_slug varchar(255) default NULL,
	PRIMARY KEY (meta_id),
	KEY meta_key (meta_key),
	KEY meta_table (meta_table),
	KEY meta_id_obj (meta_id_obj),
	KEY meta_value (meta_value(256))
) _CHARSETCOLLATE_ ENGINE=MyISAM;


###
DROP TABLE IF EXISTS _PREFIX_page_type;
###
CREATE TABLE _PREFIX_page_type (
	page_type_id bigint(20) NOT NULL auto_increment,
	page_type_name varchar(255) NOT NULL,
	page_type_desc longtext,
	PRIMARY KEY (page_type_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_page_type (page_type_name, page_type_desc) VALUES ('blog', 'Записи для блога');
###
INSERT INTO _PREFIX_page_type (page_type_name, page_type_desc) VALUES ('static', 'Постоянные страницы');
###



DROP TABLE IF EXISTS _PREFIX_cat2obj;
###
CREATE TABLE _PREFIX_cat2obj (
	cat2obj_id bigint(20) NOT NULL auto_increment,
	page_id bigint(20) NOT NULL default '0',
	category_id bigint(20) NOT NULL default '0',
	links_id bigint(20) NOT NULL default '0',
	PRIMARY KEY (cat2obj_id),
	KEY category_id (category_id),
	KEY page_id (page_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_cat2obj (page_id, category_id) VALUES ('1','1');
###
INSERT INTO _PREFIX_cat2obj (category_id, links_id) VALUES ('2','1');
###



DROP TABLE IF EXISTS _PREFIX_category;
###
CREATE TABLE _PREFIX_category (
	category_id bigint(20) NOT NULL auto_increment,
	category_id_parent bigint(20) NOT NULL default '0',
	category_type enum('page','links') NOT NULL default 'page',
	category_name varchar(255) default '',
	category_desc longtext,
	category_slug varchar(255) default '',
	category_menu_order bigint(20) NOT NULL default '0',
	PRIMARY KEY (category_id),
	KEY category_slug (category_slug),
	KEY category_id_parent (category_id_parent)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_category (category_name, category_desc, category_slug) VALUES ('Новости','Новости проекта','news');
###
INSERT INTO _PREFIX_category (category_name, category_desc, category_slug, category_type) VALUES ('Blogroll','Мои друзья','blogroll', 'links');
###



DROP TABLE IF EXISTS _PREFIX_links;
###
CREATE TABLE _PREFIX_links (
	links_id bigint(20) NOT NULL auto_increment,
	links_url varchar(255) default '',
	links_name varchar(255) default '',
	links_desc longtext,
	links_rel varchar(255) default '',
	links_target varchar(255) default '',
	links_menu_order bigint(20) NOT NULL default '0',
	links_visible bigint(20) NOT NULL default '1',
	links_rating bigint(20) NOT NULL default '0',
	links_image varchar(255) default '',
	links_rss varchar(255) default '',
	PRIMARY KEY (links_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_links (links_url, links_name, links_desc, links_rss) VALUES ('http://maxsite.org/', 'MaxSite CMS', 'Официальный сайт MaxSite CMS', 'http://maxsite.org/feed');
###



DROP TABLE IF EXISTS _PREFIX_page;
###
CREATE TABLE _PREFIX_page (
	page_id bigint(20) NOT NULL auto_increment,
	page_type_id bigint(20) NOT NULL default '1',
	page_id_parent bigint(20) NOT NULL default '0',
	page_id_autor bigint(20) NOT NULL default '1',
	page_title varchar(255) default 'no-title',
	page_content longtext,
	page_content2 longtext,
	page_date_publish datetime NOT NULL default '2008-01-01 00:00:00',
	page_date_dead datetime NOT NULL default '0000-00-00 00:00:00',
	page_last_modified datetime NOT NULL default '2008-01-01 00:00:00',
	page_status enum('publish','draft','private') NOT NULL default 'publish',
	page_menu_order bigint(20) NOT NULL default '0',
	page_slug varchar(255) default '',
	page_view_count bigint(20) NOT NULL default '0',
	page_rating bigint(20) NOT NULL default '0',
	page_rating_count bigint(20) NOT NULL default '0',
	page_password varchar(255) default '',
	page_comment_allow bigint(20) NOT NULL default '1',
	page_ping_allow bigint(20) NOT NULL default '1',
	page_feed_allow bigint(20) NOT NULL default '1',
	page_min_user_level bigint(20) NOT NULL default '0',
	page_allow_group bigint(20) NOT NULL default '0',
	page_lang varchar(255) default '',
	PRIMARY KEY (page_id),
	KEY page_type_id (page_type_id),
	KEY page_date_publish (page_date_publish),
	KEY page_menu_order (page_menu_order),
	KEY page_rating (page_rating),
	KEY page_view_count (page_view_count),
	KEY page_id_autor (page_id_autor)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###
INSERT INTO _PREFIX_page (page_title, page_content, page_slug, page_date_publish, page_last_modified) VALUES ('Привет, мир!', '<p>Это ваша первая запись. Вы можете её отредактировать или удалить через админ-панель.</p>', 'hello', NOW(), NOW());
###
INSERT INTO _PREFIX_page (page_title, page_content, page_slug, page_date_publish, page_last_modified, page_type_id) VALUES ('О сайте', '<p>На этой странице вы можете написать о чем ваш сайт.</p>', 'about', NOW(), NOW(), 2);
###

DROP TABLE IF EXISTS _PREFIX_comusers;
###
CREATE TABLE _PREFIX_comusers (
	comusers_id bigint(20) NOT NULL auto_increment,
	comusers_email varchar(255) default '',
	comusers_password varchar(255) default '',
	comusers_nik varchar(255) default '',
	comusers_allow_publish enum('0','1') NOT NULL default '0',
	comusers_count_comments bigint(20) NOT NULL default '0',
	comusers_icq varchar(255) default '',
	comusers_url varchar(255) default '',
	comusers_msn varchar(255) default '',
	comusers_jaber varchar(255) default '',
	comusers_skype varchar(255) default '',
	comusers_date_registr datetime NOT NULL default '2008-01-01 00:00:00',
	comusers_last_visit datetime NOT NULL default '2008-01-01 00:00:00',
	comusers_date_birth datetime NOT NULL default '1970-01-01 00:00:00',
	comusers_avatar_url varchar(255) default '',
	comusers_description longtext,
	comusers_ip_register varchar(50) default '',
	comusers_language varchar(5) default 'ru',
	comusers_skins varchar(255) default '',
	comusers_notify enum('0','1') NOT NULL default '1',
	comusers_admin_note longtext,
	comusers_activate_string varchar(255) default '',
	comusers_activate_key varchar(255) default '',
	PRIMARY KEY (comusers_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###



DROP TABLE IF EXISTS _PREFIX_comments;
###
CREATE TABLE _PREFIX_comments (
	comments_id bigint(20) NOT NULL auto_increment,
	comments_page_id bigint(20) NOT NULL default '0',
	comments_parent_id bigint(20) NOT NULL default '0',
	comments_users_id bigint(20) NOT NULL default '0',
	comments_comusers_id bigint(20) NOT NULL default '0',
	comments_author_name varchar(255) NOT NULL,
	comments_author_ip varchar(100) NOT NULL default '',
	comments_date datetime NOT NULL default '2008-01-01 00:00:00',
	comments_content longtext,
	comments_rating bigint(20) NOT NULL default '0',
	comments_approved bigint(20) NOT NULL default '0',
	comments_type varchar(50) NOT NULL default '',
	PRIMARY KEY (comments_id),
	KEY comments_page_id (comments_page_id)
) _CHARSETCOLLATE_ ENGINE=MyISAM;
###

