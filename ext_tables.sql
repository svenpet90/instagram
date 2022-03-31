#
# Table structure for table 'tx_instagram_domain_model_longlivedaccesstoken'
#
CREATE TABLE tx_instagram_domain_model_longlivedaccesstoken
(
	uid       int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
	pid       int(11) DEFAULT 0 NOT NULL,

	token     varchar(500) DEFAULT NULL,
	type      varchar(500) DEFAULT NULL,
	expiresat int(11) unsigned DEFAULT NULL,
	userid    varchar(500) DEFAULT NULL,
	account   int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY       parent (pid)
);

#
# Table structure for table 'tx_instagram_domain_model_post'
#
CREATE TABLE tx_instagram_domain_model_post
(
	uid         int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
	pid         int(11) DEFAULT 0 NOT NULL,

	text        varchar(5000) DEFAULT '',
	createdtime int(11) DEFAULT NULL,
	instagramid varchar(255)  DEFAULT '',
	tags        varchar(5000) DEFAULT '',
	link        varchar(255)  DEFAULT '',
	type        varchar(255)  DEFAULT '',
	lastupdate  int(11) unsigned DEFAULT NULL,
	account     int(11) DEFAULT '0' NOT NULL,
	images      varchar(255)  DEFAULT NULL,
	videos      varchar(255)  DEFAULT NULL,

	PRIMARY KEY (uid),
	KEY         parent (pid)
);

#
# Table structure for table 'tx_instagram_domain_model_account'
#
CREATE TABLE tx_instagram_domain_model_account
(
	uid                  int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
	pid                  int(11) DEFAULT 0 NOT NULL,

	userid               varchar(255) DEFAULT '' NOT NULL,
	username             varchar(255) DEFAULT '' NOT NULL,
	posts                int(11) DEFAULT '0' NOT NULL,
	lastupdate           varchar(255) DEFAULT '' NOT NULL,
	longlivedaccesstoken int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY                  parent (pid)
);
