<?php

define("CREATE_TABLE_QUERY",

"CREATE TABLE laender (
  id int(11) unsigned NOT NULL auto_increment,
  land varchar(15) default NULL,
  feedname varchar(15) default NULL,
  meisterstatus tinyint(4) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE newsboard (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  datum datetime default NULL,
  text text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE sessions (
  id int(10) unsigned NOT NULL auto_increment,
  sessionid varchar(36) NOT NULL default '',
  userid int(10) unsigned NOT NULL default '0',
  validstamp datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE spielkategorien (
  id int(10) unsigned NOT NULL auto_increment,
  bezeichnung varchar(36) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE spiele (
  id int(10) unsigned NOT NULL auto_increment,
  ms1 int(10) unsigned NOT NULL default '0',
  ms2 int(10) unsigned NOT NULL default '0',
  datum datetime NOT NULL default '0000-00-00 00:00:00',
  matchday int(10) NOT NULL default '0',
  status tinyint(4) NOT NULL default '0',
  tore1 tinyint(3) unsigned NOT NULL default '0',
  tore2 tinyint(3) unsigned NOT NULL default '0',
  anz_er smallint(5) unsigned default '0',
  anz_tr smallint(5) unsigned default '0',
  anz_sr smallint(5) unsigned default '0',
  anz_f smallint(5) unsigned default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE tipps (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  spielid int(10) unsigned NOT NULL default '0',
  tore1 tinyint(3) unsigned NOT NULL default '0',
  tore2 tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE user (
  id int(11) NOT NULL auto_increment,
  login varchar(10) default NULL,
  name varchar(30) NOT NULL default '',
  passwort varchar(32) NOT NULL,
  salt varchar(32) default NULL,
  pwresettoken varchar(32) default NULL,
  email varchar(30) default NULL,
  adminlevel tinyint(4) NOT NULL default '0',
  meistertip int(11) NOT NULL default '0',
  wettbewerb tinyint(4) default '0',
  attr1 tinyint(4) default '0',
  attr2 tinyint(4) default '0',
  attr3 tinyint(4) default '0',
  anz_er smallint(5) unsigned default '0',
  anz_tr smallint(5) unsigned default '0',
  anz_sr smallint(5) unsigned default '0',
  anz_f smallint(5) unsigned default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY login (login)
) TYPE=MyISAM;

CREATE TABLE matchdays (
	id int(10) unsigned NOT NULL auto_increment,
	name varchar(32) NOT NULL default '',
	PRIMARY KEY (id)
) TYPE=MyISAM;"
);


?>