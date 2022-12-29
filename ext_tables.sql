#
# Table structure for table 'tx_instagram_domain_model_feed'
#
CREATE TABLE tx_instagram_domain_model_feed
(
	uid        int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
	pid        int(11) DEFAULT 0 NOT NULL,

	token      varchar(500) DEFAULT NULL,
	type       varchar(500) DEFAULT NULL,
	expires_at int(11) unsigned DEFAULT NULL,
	user_id    varchar(255) DEFAULT NULL,
	username   varchar(255) DEFAULT NULL,
	posts      int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY        parent (pid)
);


#
# Table structure for table 'tx_instagram_domain_model_post'
#
CREATE TABLE tx_instagram_domain_model_post
(
	uid         int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
	pid         int(11) DEFAULT 0 NOT NULL,

	caption     mediumtext,
	posted_at   int(11) unsigned DEFAULT NULL,
	instagramid varchar(255)  DEFAULT '',
	hashtags    varchar(5000) DEFAULT '',
	link        varchar(255)  DEFAULT '',
	media_type  varchar(255)  DEFAULT '',
	lastupdate  int(11) unsigned DEFAULT NULL,
	images      varchar(255)  DEFAULT NULL,
	videos      varchar(255)  DEFAULT NULL,
	feed int(11) DEFAULT '0' NOT NULL,


	PRIMARY KEY (uid),
	KEY         parent (pid)
);
