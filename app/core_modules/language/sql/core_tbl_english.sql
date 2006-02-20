CREATE TABLE `tbl_english` (
  `code` varchar(50) NOT NULL default '',
  `Content` mediumtext,
  `isInNextGen` tinyint(1) default NULL,
  `dateCreated` datetime default '2004-06-23 19:46:00',
  `creatorUserId` varchar(25) default '1',
  `dateLastModified` datetime default NULL,
  `modifiedByUserId` varchar(25) default NULL,
  PRIMARY KEY  (code)
) TYPE=InnoDB ;

#
# Dumping data for table `tbl_languagetext` and 'tbl_english'
#
INSERT INTO tbl_languagetext (code,description) VALUES ('error_languageitemmissing','Use to output the error when a language item is missing');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('error_languageitemmissing','Language item missing','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('word_username','The word username');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('word_username','Username','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('word_password','the word password');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('word_password','Password','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('phrase_networkid','network id');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('phrase_networkid','Network Id','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('word_login','the word login');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('word_login','Login','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('word_logout','the word logout');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('word_logout','Logout','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('phrase_languagelist','language selection');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('phrase_languagelist','Select Language','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('phrase_selectskin','skin selection');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('phrase_selectskin','Select Skin','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('word_go','the word go');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('word_go','Go','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('word_register','the word register');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('word_register','Register','1',NOW());

INSERT INTO tbl_languagetext (code,description) VALUES ('mod_context_entercourse','The phrase Enter Course');
INSERT INTO tbl_english (code,content,isInNextgen,dateCreated) VALUES ('mod_context_entercourse','Enter Course','1',NOW());