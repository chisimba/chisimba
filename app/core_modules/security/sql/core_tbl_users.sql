################# Users ##############################
#
# Table structure for table `tbl_users`
#

CREATE TABLE tbl_users (
  id varchar(32) NOT NULL,
  userId varchar(25) NOT NULL default '0',
  username varchar(25) NOT NULL default '',
  title varchar(25) NOT NULL default '',
  firstName varchar(50) NOT NULL default '',
  surname varchar(50) NOT NULL default '',
  PASSWORD varchar(100) NOT NULL default '',
  creationDate date NOT NULL default '0000-00-00',
  emailAddress varchar(100) NOT NULL default '',
  logins int(11) default '0',
  sex char(1) default '',
  country char(2) default '',
  accesslevel char(1) default '0',
  isActive CHAR(1) DEFAULT '1',
  howCreated VARCHAR(32) DEFAULT 'unknown',
  updated timestamp(14),
  PRIMARY KEY  (id),
  INDEX userId (userId)
) TYPE=InnoDB  COMMENT='Primary user information';

INSERT INTO tbl_users (id,userId,username,title,firstName,surname,PASSWORD,creationDate,emailAddress,logins,sex,country,accessLevel,isActive,howCreated) VALUES ('init_1','1', 'admin', 'Dr', 'Administrative', 'User', '86f7e437faa5a7fce15d1ddcb9eaeaea377667b8', '0000-00-00', 'admin@localhost.local', 0, 'M', 'ZA','1','1','install');
