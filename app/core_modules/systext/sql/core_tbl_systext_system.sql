################################# Systext ##############################3

#
# Table structure for table `tbl_systext_system`
#

CREATE TABLE tbl_systext_system(
    id VARCHAR(32) NOT NULL,
    systemType VARCHAR(15) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    canDelete TINYTEXT NULL,
    PRIMARY KEY(id),
    KEY(creatorId),
    CONSTRAINT `Systext_system_creator` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`)
    ) TYPE=InnoDB COMMENT='Table to hold system types for text abstraction';

#
# Data for `tbl_systext_system`
#

INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated, canDelete)
    values('init_1', 'default', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated)
    values('init_2', 'elearn', '1', '0000-00-00');
INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated)
    values('init_3', 'groups', '1', '0000-00-00');
INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated)
    values('init_4', 'workgroups', '1', '0000-00-00');
INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated)
    values('init_5', 'pgrad', '1', '0000-00-00');
INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated)
    values('init_6', 'alumni', '1', '0000-00-00');
INSERT INTO tbl_systext_system(id, systemType, creatorId, dateCreated)
    values('init_7', 'content', '1', '0000-00-00');