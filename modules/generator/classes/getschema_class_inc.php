<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}

/**
* 
* Class to get a database schema from the database and prepare
* it for use in code generation
* 
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class getschema extends dbTableManager
{
    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init();
    }
    
    /**
    * 
    * Method to get the XML schema for a table.
    * 
    * @param $tableName The table for which to look up the schema
    * 
    */
    public function getXmlSchema($table)
    {
        return $this->getTableSchema($table);
    }
    
    public function getFieldNamesAsArray($table)
    {
    	$ret=array();
        $schema = $this->getXmlSchema($table);
        $structure = $schema['fields'];
        unset($schema);
        foreach ($structure as $key=>$valueArray) {
			$ret[] = $key;
        }
        return $ret;
    }
    
    public function getArrayOfTables()
    {
        return $this->listDbTables();
    }
    
    public function getFieldSchema($table)
    {
        $schema = $this->getXmlSchema($table);
        $structure = $schema['fields'];
        unset($schema);
        $iCount = 0;
        foreach ($structure as $key=>$valueArray) {
			$ret[$iCount]['fieldname'] = $key;
            foreach ($valueArray as $key=>$value) {
                $ret[$iCount][$key] = $value;
            }
            $iCount++;
        }
        return $ret;       
    }
    
    /**
    * 
    * Method to return the SQL creation script from a database table
    * when supplied a valid table name
    * 
    * @param string $table The name of the database table to create
    * @return string $ret The script for creating the table on registration
    * 
    */
    public function makeSqlFromTable($table)
    {
        //Get the database schema
        $schema = $this->getXmlSchema($table);
        //Extract the structure array which is fields in the array
        $structure = $schema['fields'];
        unset($schema);
        //Set up the top of the script
        $ret ="<?php\n"
          . "//Chisimba table definition for use on module registration\n"
          . "\$tablename = '" . $table . "';\n\n"
          . "//Options line for comments, encoding and character set\n"
          . "\$options = array('comment' => '{COMMENTTEXT}', 'collate' => "
          . "'utf8_general_ci', 'character_set' => 'utf8');\n\n"
          . "\$fields = array(";
        //Count the number of fields
        $fldCount = count($structure);
        //Initialize counter for looping over fields
        $jCount = 0;
        //Loop over fields
        foreach ($structure as $key=>$valueArray) {
            $jCount++;
            $ret .= "'" . $key . "' => array(\n";
            $fldProperties = count($valueArray);
            $iCount = 0;
            //Loop over field properties
            foreach ($valueArray as $key=>$value) {
                $iCount++;
                if ($iCount < $fldProperties) {
                    $ret .= "'" . $key ."' => " . $value . ",\n";
                } else {
                    $ret .= "'" . $key ."' => " . $value . "\n";
                } #if
            } #foreach
            if ($jCount < $fldCount) {
                $ret .= "),\n";
            } else {
                $ret .= ")\n";
            } #if
        } #foreach
        $ret .= ");\n?>";
    } #function
}
?>