CREATE TABLE `tbl_menu_category` ( 
    `id` varchar(32) NOT NULL, 
    `category` varchar(120), 
    `module` varchar(60), 
    `adminOnly` TINYINT NOT NULL Default 0,
    `permissions` varchar(120),
    `dependsContext` TINYINT NOT NULL Default 0,
    PRIMARY KEY (id)
    ) Type=InnoDB ;