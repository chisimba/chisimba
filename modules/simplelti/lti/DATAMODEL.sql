# This builds the tables for the MySql Data model

# drop database lti
# create database lti;
# grant all on lti.* to ltiuser@'localhost' identified by 'ltipassword';
# grant all on lti.* to ltiuser@'127.0.0.1' identified by 'ltipassword';

use lti;

drop table if exists lti_org;
create table lti_org (
     id          MEDIUMINT NOT NULL AUTO_INCREMENT,
     course_id   MEDIUMINT NULL, 
     org_id       CHAR(255) NOT NULL,
     secret      CHAR(255) NULL,
     name        CHAR(255) NULL,
     title       CHAR(255) NULL,
     url         CHAR(255) NULL,
     created_at  DATETIME NOT NULL,
     updated_at  DATETIME NOT NULL,
     PRIMARY KEY (id)
 );

drop table if exists lti_user;
create table lti_user (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     user_id CHAR(255) NOT NULL,
     course_id  MEDIUMINT NULL, 
     eid CHAR(255) NULL,
     displayid CHAR(255) NULL,
     password CHAR(255) NULL,
     firstname CHAR(255) NULL,
     lastname CHAR(255) NULL,
     email CHAR(255) NULL,
     locale CHAR(255) NULL,
     org_id MEDIUMINT NULL,
     created_at DATETIME NOT NULL,
     updated_at DATETIME NOT NULL,
     PRIMARY KEY (id)
);

drop table if exists lti_session;
create table lti_session (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     user_id MEDIUMINT NOT NULL,
     course_id MEDIUMINT NOT NULL,
     PRIMARY KEY (id)
);

drop table if exists lti_course;
create table lti_course (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     course_id CHAR(255) NOT NULL,
     org_id MEDIUMINT NULL,
     code CHAR(255) NULL,
     name CHAR(255) NULL,
     title CHAR(255) NULL,
     secret CHAR(255) NULL,
     created_at DATETIME NOT NULL, 
     updated_at DATETIME NOT NULL,
     PRIMARY KEY (id)
);

drop table if exists lti_membership;
create table lti_membership ( 
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     created_at DATETIME NOT NULL,
     updated_at DATETIME NOT NULL,
     course_id MEDIUMINT NOT NULL,
     user_id MEDIUMINT NOT NULL,
     role_id MEDIUMINT NOT NULL,
     roster CHAR(255) NULL,
     PRIMARY KEY (id)
);

drop table if exists lti_tool;
create table lti_tool (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     created_at DATETIME NOT NULL,
     updated_at DATETIME NOT NULL,
     tool_name CHAR(255) NULL,
     tool_title CHAR(255) NULL,
     tool_id CHAR(255) NULL,
     targets CHAR(255) NULL,
     resource_id CHAR(255) NULL,
     resource_url CHAR(255) NULL,
     width MEDIUMINT NULL,
     height MEDIUMINT NULL,
     PRIMARY KEY (id)
);

drop table if exists lti_digest;
create table lti_digest (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     created_at DATETIME NOT NULL,
     digest BLOB NULL,
     request BLOB NOT NULL,
     PRIMARY KEY (id)
);

drop table if exists lti_launch;
create table lti_launch (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     created_at      DATETIME NOT NULL,
     updated_at      DATETIME NOT NULL,
     user_id     MEDIUMINT NOT NULL,
     course_id   MEDIUMINT NOT NULL,
     org_id      MEDIUMINT NOT NULL,
     password         CHAR(255) NULL, 
     resource_id      CHAR(255) NULL, 
     targets         CHAR(255) NULL,      -- widget,post,iframe
     resource_url     CHAR(255) NULL,      -- http://www.dr-chuck.com/
     tool_id          CHAR(255) NULL,      -- sakai.lti.168
     tool_name        CHAR(255) NULL,      -- Video
     tool_title       CHAR(255) NULL,      -- Video Review for Midterm
     width           MEDIUMINT,           -- 320
     height          MEDIUMINT,           -- 240
     PRIMARY KEY (id)
);


