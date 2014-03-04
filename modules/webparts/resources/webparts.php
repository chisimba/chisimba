<?PHP

/*
 *  This file implements the use case for importing an existing mysql data structure into a module
 *  The sample xml mysqldump file used is for the libraryexams module
 * 
 *  To get a valid mysql xml dump use the following mysql command:
 *  mysqldump -X -u[username] -p[password] [databasename] > mysql_xml_export.sql
 * 
 *  Don't forget to set your module name:
 *  $projModuleName = '[yourmodulename]';
 *
 *  The project will be written to usrfiles/webparts/[yourmodulename]
 *  Only the sql files are generated.  
 *
 *  Usage: Place this file in the root of your chisimba installation and execute via "php webparts.php"
 */

$GLOBALS['kewl_entry_point_run'] = true;
require_once 'classes/core/engine_class_inc.php';

$eng = new engine;
$objImport = $eng->getObject('import', 'webparts');

//Sample Mysql XML Export
/*
//Not using the following string as input
//Will use the sample mysqldump for the libraryexams module
$mysqlXmlDump = '
<mysqldump>
<database name="exams">
    <table_structure name="course_units">
        <field Field="new_m_code" Type="varchar(10)" Null="NO" Key="PRI" Default="" Extra="" />
        <field Field="old_m_code" Type="varchar(10)" Null="NO" Key="" Default="" Extra="" />
        <field Field="m_name" Type="varchar(250)" Null="YES" Key="" Extra="" />
        <field Field="d_code" Type="varchar(10)" Null="YES" Key="" Extra="" />
        <key Table="course_units" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="new_m_code" Collation="A" Cardinality="3993" Null="" Index_type="BTREE" Comment="" />
        <options Name="course_units" Engine="MyISAM" Version="10" Row_format="Dynamic" Rows="3993" Avg_row_length="53" Data_length="214176" Max_data_length="281474976710655" Index_length="56320" Data_free="0" Create_time="2009-06-04 11:36:11" Update_time="2009-06-04 11:36:12" Collation="latin1_swedish_ci" Create_options="" Comment="" />
    </table_structure>

	<table_structure name="users">
        <field Field="id" Type="int(10)" Null="NO" Key="PRI" Extra="auto_increment" />
        <field Field="username" Type="varchar(8)" Null="NO" Key="" Default="" Extra="" />
        <field Field="password" Type="varchar(50)" Null="YES" Key="" Extra="" />
        <field Field="reg_date" Type="date" Null="YES" Key="" Extra="" />
        <field Field="fullname" Type="varchar(50)" Null="YES" Key="" Extra="" />
        <key Table="users" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Cardinality="0" Null="" Index_type="BTREE" Comment="" />
        <options Name="users" Engine="MyISAM" Version="10" Row_format="Dynamic" Rows="0" Avg_row_length="0" Data_length="0" Max_data_length="281474976710655" Index_length="1024" Data_free="0" Auto_increment="61" Create_time="2009-06-04 11:36:12" Update_time="2009-06-04 11:36:12" Collation="latin1_swedish_ci" Create_options="" Comment="" />
    </table_structure>

    <table_data name="users">
    </table_data>
</database>
</mysqldump>
';
//*/

//$mysqlXmlDump = htmlspecialchars($mysqlXmlDump);
//$result = $objImport->generateMdb2DataStruct($mysqlXmlDump);

$projModuleName = 'libraryexams';

$objImport->setWpModuleName($projModuleName);
$xmlFile = 'packages/webparts/resources/exams.xml';
$result = $objImport->generateMdb2DataStruct($xmlFile);
echo $result;

?>
