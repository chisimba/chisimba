################################### tbl_dublincoremetadata ##############################
#
# Table structure for table  tbl_dublincoremetadata 
#

CREATE TABLE tbl_dublincoremetadata (
  id VARCHAR(32) NOT NULL ,
  provider varchar(255),
  url varchar(255),
  enterdate datetime,
  oai_identifier varchar(255),
  oai_set varchar(255),
  datestamp datetime,
  deleted enum('false', 'true') NOT NULL,
  dc_title TEXT NULL ,
  dc_subject TEXT NULL ,
  dc_description TEXT NULL,
  dc_type VARCHAR(255) NULL,
  dc_source VARCHAR(255) NULL,
  dc_sourceurl VARCHAR(255) NULL,
  dc_relationship VARCHAR(255) NULL,
  dc_coverage VARCHAR(255) NULL,
  dc_creator VARCHAR(255) NULL,
  dc_publisher VARCHAR(255) NULL,
  dc_contributor VARCHAR(255) NULL,
  dc_rights VARCHAR(255) NULL,
  dc_date VARCHAR(20) NULL,
  dc_format VARCHAR(255) NULL,
  dc_identifier VARCHAR(255) NULL,
  dc_language VARCHAR(255) NULL,
  dc_audience VARCHAR(255) NULL,
  PRIMARY KEY(id)
  
)
TYPE=InnoDB;
