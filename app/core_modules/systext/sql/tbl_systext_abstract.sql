<?php
$sqldata[] = "CREATE TABLE tbl_systext_abstract(
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
    ) TYPE=InnoDB COMMENT='List of text items to be abstracted'";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_1', 'course', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_2', 'courses', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_3', 'lecturer', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_4', 'lecturers', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_5', 'organisation', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_6', 'organisations', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_7', 'student', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_8', 'students', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_9', 'workgroup', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_10', 'workgroups', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_11', 'story', '1', '0000-00-00', 'N')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated, canDelete)
    values('PKVALUE', 'init_1', 'init_12', 'stories', '1', '0000-00-00', 'N')";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_1', 'course', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_2', 'courses', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_3', 'lecturer', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_4', 'lecturers', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_5', 'organisation', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_6', 'organisations', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_7', 'student', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_8', 'students', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_9', 'workgroup', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_10', 'workgroups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_11', 'story', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_2', 'init_12', 'stories', '1', '0000-00-00')";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_3', 'init_1', 'group', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_3', 'init_2', 'groups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_3', 'init_3', 'group author', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_3', 'init_4', 'group authors', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_3', 'init_7', 'readonly member', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_3', 'init_8', 'readonly members', '1', '0000-00-00')";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_4', 'init_1', 'workgroup', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_4', 'init_2', 'workgroups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_4', 'init_3', 'workgroup author', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_4', 'init_4', 'workgroup authors', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_4', 'init_7', 'workgroup user', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_4', 'init_8', 'workgroup users', '1', '0000-00-00')";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_5', 'init_1', 'supervision unit', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_5', 'init_2', 'supervision units', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_5', 'init_3', 'supervisor', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_5', 'init_4', 'supervisors', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_5', 'init_7', 'student', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_5', 'init_8', 'students', '1', '0000-00-00')";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_1', 'alumni group', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_2', 'alumni groups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_3', 'content author', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_4', 'content authors', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_5', 'organisation', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_6', 'organisations', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_7', 'alumnus', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_8', 'alumni', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_9', 'interest group', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_10', 'interest groups', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_11', 'announcement', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_6', 'init_12', 'announcements', '1', '0000-00-00')";

$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_7', 'init_1', 'content area', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_7', 'init_2', 'content areas', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_7', 'init_3', 'content author', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_7', 'init_4', 'content authors', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_7', 'init_7', 'user', '1', '0000-00-00')";
$sqldata[] = "INSERT INTO tbl_systext_abstract(id, systemId, textId, abstract, creatorId, dateCreated)
    values('PKVALUE', 'init_7', 'init_8', 'users', '1', '0000-00-00')";
?>