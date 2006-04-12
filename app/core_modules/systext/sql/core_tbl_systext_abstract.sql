################################# Systext ##############################3

#
# Table structure for table `tbl_systext_abstract`
#

CREATE TABLE tbl_systext_abstract(
    id VARCHAR(32) NOT NULL,
    systemId VARCHAR(32) NOT NULL,
    textId VARCHAR(32) NOT NULL,
    abstract VARCHAR(50) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    canDelete TINYTEXT NULL,
    PRIMARY KEY(id),
    KEY(systemId),
    KEY(textId),
    KEY(creatorId),
    CONSTRAINT `Systext_abstract_system` FOREIGN KEY (`systemId`) REFERENCES `tbl_systext_system` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `Systext_abstract_text` FOREIGN KEY (`textId`) REFERENCES `tbl_systext_text` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `Systext_abstract_creator` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`)
    ) TYPE=InnoDB COMMENT='List of text items to be abstracted';

#
# Data for `tbl_systext_abstract`
#

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_1', 'init_1', 'init_1', 'course', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_2', 'init_1', 'init_2', 'courses', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_3', 'init_1', 'init_3', 'lecturer', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_4', 'init_1', 'init_4', 'lecturers', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_5', 'init_1', 'init_5', 'organisation', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_6', 'init_1', 'init_6', 'organisations', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_7', 'init_1', 'init_7', 'student', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_8', 'init_1', 'init_8', 'students', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_9', 'init_1', 'init_9', 'workgroup', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_10', 'init_1', 'init_10', 'workgroups', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_11', 'init_1', 'init_11', 'story', '1', '0000-00-00', 'N');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('init_12', 'init_1', 'init_12', 'stories', '1', '0000-00-00', 'N');

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_13', 'init_2', 'init_1', 'course', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_14', 'init_2', 'init_2', 'courses', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_15', 'init_2', 'init_3', 'lecturer', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_16', 'init_2', 'init_4', 'lecturers', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_17', 'init_2', 'init_5', 'organisation', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_18', 'init_2', 'init_6', 'organisations', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_19', 'init_2', 'init_7', 'student', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_20', 'init_2', 'init_8', 'students', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_21', 'init_2', 'init_9', 'workgroup', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_22', 'init_2', 'init_10', 'workgroups', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_23', 'init_2', 'init_11', 'story', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_24', 'init_2', 'init_12', 'stories', '1', '0000-00-00');

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_25', 'init_3', 'init_1', 'group', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_26', 'init_3', 'init_2', 'groups', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_27', 'init_3', 'init_3', 'group author', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_28', 'init_3', 'init_4', 'group authors', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_29', 'init_3', 'init_7', 'readonly member', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_30', 'init_3', 'init_8', 'readonly members', '1', '0000-00-00');

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_31', 'init_4', 'init_1', 'workgroup', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_32', 'init_4', 'init_2', 'workgroups', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_33', 'init_4', 'init_3', 'workgroup author', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_34', 'init_4', 'init_4', 'workgroup authors', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_35', 'init_4', 'init_7', 'workgroup user', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_36', 'init_4', 'init_8', 'workgroup users', '1', '0000-00-00');

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_37', 'init_5', 'init_1', 'supervision unit', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_38', 'init_5', 'init_2', 'supervision units', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_39', 'init_5', 'init_3', 'supervisor', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_40', 'init_5', 'init_4', 'supervisors', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_41', 'init_5', 'init_7', 'student', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_42', 'init_5', 'init_8', 'students', '1', '0000-00-00');

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_43', 'init_6', 'init_1', 'alumni group', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_44', 'init_6', 'init_2', 'alumni groups', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_45', 'init_6', 'init_3', 'content author', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_46', 'init_6', 'init_4', 'content authors', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_47', 'init_6', 'init_5', 'organisation', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_48', 'init_6', 'init_6', 'organisations', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_49', 'init_6', 'init_7', 'alumnus', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_50', 'init_6', 'init_8', 'alumni', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_51', 'init_6', 'init_9', 'interest group', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_52', 'init_6', 'init_10', 'interest groups', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_53', 'init_6', 'init_11', 'announcement', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_54', 'init_6', 'init_12', 'announcements', '1', '0000-00-00');

INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_55', 'init_7', 'init_1', 'content area', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_56', 'init_7', 'init_2', 'content areas', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_57', 'init_7', 'init_3', 'content author', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_58', 'init_7', 'init_4', 'content authors', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_59', 'init_7', 'init_7', 'user', '1', '0000-00-00');
INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('init_60', 'init_7', 'init_8', 'users', '1', '0000-00-00');