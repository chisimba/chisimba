################################# Systext ##############################3

#
# Table structure for table `tbl_systext_text`
#

CREATE TABLE tbl_systext_text(
    id VARCHAR(32) NOT NULL,
    text VARCHAR(50) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    canDelete TINYTEXT NULL,
    PRIMARY KEY(id),
    KEY(creatorId),
    CONSTRAINT `Systext_text_creator` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`)
    ) TYPE=InnoDB COMMENT='List of text items to be abstracted';

#
# Data for `tbl_systext_text`
#

INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_1', 'context', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_2', 'contexts', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_3', 'author', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_4', 'authors', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_5', 'organisation', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_6', 'organisations', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_7', 'readonly', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_8', 'readonlys', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_9', 'workgroup', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_10', 'workgroups', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_11', 'story', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_text(id, text, creatorId, dateCreated, canDelete)
    values('init_12', 'stories', '1', '0000-00-00', 'N');