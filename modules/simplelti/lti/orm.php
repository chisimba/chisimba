<?php
require_once("debug.php");
require_once("db.php");

$LIST_OF_ORMS = array();
$ORM_TABLE_PREFIX = false;

function setOrmPrefix($prefix)
{
    global $ORM_TABLE_PREFIX;
    $ORM_TABLE_PREFIX = $prefix;
}

// TODO: Check to see if anything really changed...

// Sanity checks.  id must be integer, _at must be timestamp, key must be text (int later)
// Fields with spaces
function loadOrm($model, $key = false, $table = false)
{
    global $LIST_OF_ORMS;
    global $ORM_TABLE_PREFIX;
    if ( ! $model ) {
    	DPRT("Error in loadOrm - must specify model");
        return false;
    }

    if ( $LIST_OF_ORMS[$model] ) { 
    	DPRT("Model already loaded: ".$model);
        return $LIST_OF_ORMS[$model];
    }

    if ( ! $table ) {
        if ( $ORM_TABLE_PREFIX ) {
            $table = $ORM_TABLE_PREFIX . $model;
        } else {
            $table = $model;
        }
    }

    $quer = "SHOW COLUMNS FROM $table;";
    DPRT($quer);
    $result = mysql_query($quer);
    $orm = array();
    $count = 0;
    $found = false;
    // DPRT($num_rows);
    while ($row = mysql_fetch_assoc($result)) {
        // DPRT("row ".$row[Field]);
        if ( $row[Field] == $key ) $found = true;
        $orm[$count] = $row;
        $count = $count + 1;
    }
    if ( $key && ! $found ) {
        DPRT("Error - key ($key) not found in table ($table)");
        return false;
    }
    if ( $count < 1 ) {
        DPRT("Error - unable to read columns from $table");
        return false;
    }

    $meta = array();
    $meta['table'] = $table;
    $meta['key'] = $key;
    $meta['model'] = $orm;

    $LIST_OF_ORMS[$model] = $meta;
    // print_r($LIST_OF_ORMS);
    return $meta;
} 

function ormLoad($model,$value)
{

}

class ORM {

    // The kind of object we are dealing with
    private $datacolumns = false;
    private $internalcolumns = false;
    private $tablename = false;
    private $keyname = false;
    private $orm = false;
    private $modelname = false;

    // The instance data for the one we are working with
    private $keyvalue = false;
    private $idvalue = false;
    private $datafields = array();

   // Empty out any data fields - allows us to reuse a model object
   function clear()
   {
        $this->datafields = array();
        $this->keyvalue = false;
        $this->idvalue = false;
   }

   // Accessors
   function datacolumns() { $x = $this->datacolumns; return $x; } 
   function internalcolumns() { $x = $this->internalcolumns; return $x; } 
   function tablename() { return $this->tablename; } 
   function keyname() { return $this->keyname; } 
   function modelname() { return $this->modelname; } 

   function keyvalue() { return $this->keyvalue; } 

   // Always return an integer or fail
   function id() { 
      $retval = $this->idvalue;
      if ( is_string($retval) ) {
         $retval = intval($retval);
      }
      if ( $retval <= 0 ) return false;
      return $retval; 
   }

   function data($key = false) { 
     if ( $key ) {
	return $this->datafields[$key];
     } 
     $x = $this->datafields; 
     return $x; 
   } 

   function valid_column($key) {
      return (in_array($key,$this->datacolumns) || in_array($key,$this->internalcolumns) ) ;
   }

   function __construct($modelname, $keyname = false, $table = false) {
        global $LIST_OF_ORMS;
        // Load up the ORM if requested
        if ( $table ) {
             loadOrm($modelname, $keyname, $table);
        }
        if ( ! $LIST_OF_ORMS[$modelname] ) { 
            throw new Exception("Model $modelname not loaded with loadOrm().");
        } else {
	    $this->orm = $LIST_OF_ORMS[$modelname];
            if ( ! $this->orm['model'] ) {
               throw new Exception("Model $modelname - model data not found"); 
	    }
            if ( $this->orm['table'] ) {
	       $this->tablename = $this->orm['table'];
            } else {
               throw new Exception("Model $modelname - table name not found"); 
	    }
            // Logical keys are optional
            $this->keyname = $this->orm['key'];
            $this->modelname = $modelname;

            $datafld = array();
            $internalfld = array();
            $keyfound = false;
            foreach ( $this->orm['model'] as $column ) {
                 $field = $column[Field];
                 $fieldtype = $column[Type];
                 // DPRT("----- $field ($fieldtype)");
                 if ( $field == $this->keyname ) {
                    $keyfound = true;
                 } 
                 if ( $field == "id" || 
                      ( preg_match('/_id$/', $field) == 1 && preg_match("/mediumin(.*)/", $fieldtype) == 1 ) ||
                      preg_match("/_at$/", $field) == 1 || $field == $this->keyname ) {
                   $internalfld[] = $field;
                 } else { 
                   $datafld[] = $field;
                 }
            }
            if ( $key && ! $keyfound ) {
               throw new Exception("Model $modelname - key column not found"); 
            }
            $this->datacolumns = $datafld;
            $this->internalcolumns = $internalfld;
	    // print "Data Fields\n";
            // print_r($datafld); 
	    // print "Internal Fields\n";
            // print_r($internalfld); 
        }
        $this->clear();
        DPRT("Constructor complete");
   }

   function getValueForColumn($column, $datafields, $skipfields = true)
   {
       $fieldname = $column[Field];
       $fieldtype = $column[Type];
       $fieldvalue = $datafields[$fieldname];
       // DPRT("Field $fieldname($fieldtype)=$fieldvalue(".gettype($fieldvalue).")");

       // id and logical key are handled separately
       if ( $skipfields && $fieldname == 'id' ) return;
       if ( $skipfields && $fieldname == $this->keyname ) return;

       // The pre-defined time fields
       if ( $fieldname == "created_at" ) return 'NOW()';
       if ( $fieldname == "updated_at" ) return 'NOW()';

       if ( ! isset($datafields[$fieldname]) ) return;
       // Someday this will cause problems :)
       if ( is_string($fieldvalue) && $fieldvalue == "NULL" ) return "NULL";
       if ( preg_match("/char(.*)/", $fieldtype) == 1 || 
            preg_match("/text(.*)/", $fieldtype) == 1 ) {
          // DPRT("$fieldname is a text field");
          // Concatenate to force a string in case it was not
          return "'".mysql_real_escape_string($fieldvalue."")."'"; 
       }
       if ( preg_match("/mediumint(.*)/", $fieldtype) == 1 ) {
          DPRT("$fieldname is a integer field");
          if ( is_string($fieldvalue) ) {
             if ( $fieldvalue == "0" ) {
                $fieldvalue = 0;
             } else { 
                $ival = intval($fieldvalue);
                if ( $ival != 0 ) $fieldvalue = $ival;
             }
          }
          if ( ! is_int($fieldvalue) ) {
              throw new Exception("Model $this->modelname - field name=$fieldname requires an integer"); 
          }
          return $fieldvalue;
       }
       // TODO: Move this check earlier in the load so people see the error ar load time
       throw new Exception("Model $this->modelname - field name=$fieldname type=$fieldtype not recognized"); 
   }

   // Insert the new record - Make sure it is not there
   function insert($keyvalue = false) { return $this->create($keyvalue); }
   function create($keyvalue = false) {
      $keyname = $this->keyname();
      if ( $keyname && ! $keyvalue ) {
          DPRT("Model ".$this->modelname()." requires a logical key=$keyname for insert"); 
          return false;
      }
      if ( ! $keyname && $keyvalue ) {
          DPRT("Model ".$this->modelname()." does not support locical key on insert value=$keyvalue"); 
          return false;
      }
      // mysql_query("BEGIN;");
      DPRT("Inserting record into ".$this->tablename." key=$keyname value=$keyvalue"); 
      // If we have a logical key - check for dups
      if( $keyname ) {
          $sql = "SELECT * FROM $this->tablename WHERE $this->keyname = '".mysql_real_escape_string($keyvalue)."';";
          $result = mysql_query($sql);
          $num_rows = mysql_num_rows($result);
          if ( $num_rows > 0 ) {
             // mysql_query("ROLLBACK;");
             throw new Exception("Model $this->modelname - key $keyvalue already exists"); 
          }
      }
      // TODO: Allow integer and other keys than string
      // Time to make an insert statement
      // DPRTR( $this->datafields);
      $fieldlist = "";
      $valuelist = "";
      if ( $keyname ) {
          $fieldlist = $keyname;
          $valuelist = "'".mysql_real_escape_string($keyvalue)."'";
      }
      foreach ( $this->orm['model'] as $column ) {
          $fieldvalue = $this->getValueForColumn($column,$this->datafields);
          $fieldname = $column[Field];
	  // DPRT("GOT A VALUE $fieldname=$fieldvalue");
          if ( ! isset($fieldvalue) ) continue;
          if ( $fieldlist != "" ) $fieldlist = $fieldlist.", ";
          if ( $valuelist != "" ) $valuelist = $valuelist.", ";
          $fieldlist = $fieldlist.$fieldname;
          $valuelist = $valuelist.$fieldvalue;
      }
      DPRT("Fields $fieldlist");
      DPRT("Values $valuelist");

      $sql = "INSERT INTO ".$this->tablename." ( $fieldlist ) VALUES ( $valuelist ) ;";
      $result = mysql_query($sql);
      $retval = mysql_affected_rows();
      $idvalue = mysql_insert_id();
      if ( $idvalue <= 0 || $retval != 1 ) {
	  // Leave fields alone
          $this->idvalue = false;
          $this->keyvalue = false;
          $retval = false;
      } else {
          $this->idvalue = $idvalue;
          $this->keyvalue = $keyvalue;
      }
      DPRT("rows=$retval id=$idvalue ".$sql);
      return $retval;
   }

   // Update
   function update() {
      if ( ! $this->id() ) {
          WARN("update() called $this->modelname with id not set");
          return false;
      }
      // DPRT("Update record into ".$this->tablename." id=".$this->id()); 
      // print_r( $this->datafields);
      $setlist = "";
      foreach ( $this->orm['model'] as $column ) {
          $fieldname = $column[Field];
          if ( $fieldname == "created_at" ) continue;
          $fieldvalue = $this->getValueForColumn($column,$this->datafields);
	  // DPRT("GOT A VALUE $fieldname=$fieldvalue");
          if ( ! $fieldvalue ) continue;
          if ( $setlist != "" ) $setlist = $setlist.", ";
          $setlist = $setlist.$fieldname." = ".$fieldvalue;
      }

      if ( $setlist == "" ) {
         DPRT("update() No data to change ".$this->tablename." id=".$this->id());
         return false;
      }

      $sql = "UPDATE ".$this->tablename." SET $setlist WHERE id = ".$this->id();
      // DPRT($sql);
      $result = mysql_query($sql);
      $retval = mysql_affected_rows();
      DPRT("rows=$retval ".$sql);
      return $retval;
   }

   // DELETE
   function delete($keyvalue = false) {
      $keyname = $this->keyname;
      if ( $keyvalue && $keyname ) {
          $sql = "DELETE FROM ".$this->tablename." WHERE $this->keyname = '".mysql_real_escape_string($keyvalue)."'";
      } else if ( $this->id() ) {
          $sql = "DELETE FROM ".$this->tablename." WHERE id = ".$this->id();
      } else { 
          DPRT("Model ".$this->modelname()." neither has a key value specified not an active record.");
          return false;
      }
      $result = mysql_query($sql);
      $retval = mysql_affected_rows();
      DPRT("rows=$retval ".$sql);
      $this->clear();
      return $retval;
   }

   function find($whereclause) {
       
   }

   function makewhere($keyvalues) {
         $where = "";
         foreach( array_keys($keyvalues) as $keyname ) { 
             if ( ! $this->valid_column($keyname) ) continue;
             $keyvalue = $keyvalues[$keyname];
             if ( $where != "" ) $where = $where . " AND ";
             $where = $where . $keyname . " = ";
             if ( is_int($keyvalue) ) {
                 $where = $where . $keyvalue;
             } else {
                 $where = $where . "'" . mysql_real_escape_string($keyvalue) . "'";
             }
         }
      return $where;
   } 

   function load_from_row($result, $row) {
      $this->clear();
      $count = mysql_num_fields($result);
      $newrow = array();
      for ($i=0; $i<$count; $i+=1 ) {
         $fld = mysql_fetch_field($result, $i);
         if ( $this->tablename != $fld->table ) continue;
         DPRT("Row Adding $fld->name = $row[$i] \n");
         if ( $fld->name == "id" ) {
           $this->idvalue = $row[$i];
         } else {
           $newrow[$fld->name] = $row[$i];
         }
      }
      DPRTR($newrow); 
      $this->datafields = $newrow;
      if ( ! $this->idvalue ) {
         WARN("Model $this->modelname load_from_row did not find id column");
         return false;
      }
      return true;
   } 

   function load_one_object($where) {
      $sql = "SELECT * FROM $this->tablename WHERE $where";
      $result = mysql_query($sql);
      $num_rows = mysql_num_rows($result);
      DPRT("rows=$num_rows $sql");
      if ( $num_rows > 1 ) {
         WARN("Model $this->modelname $where should be unique - found $num_rows rows");
         // Perhaps we should whack the extra rows here... Hmmm  thinking.
      }
      
      while ($row = mysql_fetch_assoc($result)) {
          // print_r($row);
          if ( $row[id] ) {
              $this->idvalue = $row[id];
          } else {
              throw new Exception("Model $this->modelname id column not found");
          }
          if ( ! $this->keyname() ) {
              $this->keyval = false;
          } else if ( $row[$this->keyname] ) {
              $this->keyval = $row[$this->keyname];
          } else {
              throw new Exception("Model $this->modelname $this->keyname logical key column not found");
          }
          
          // Yes the id field and logical key go there - but we ignore them
          $this->datafields = $row;
          break;  // Only do the first record -- sorry about that
      }
      return $num_rows > 0 ;
   }

   // Load by primary key
   // Calling get directly is deprecated - calling
   // calling read() with an integer is preferred
   function get($idvalue) {
      // Quick string convert if it is a valid integer
      if ( is_string($idvalue) ) {
          $ival = intval($idvalue);
          if ( $ival > 0 ) $idvalue = $ival;
      }

      if ( ! is_int($idvalue) ) {
          DPRT("get() ".$this->modelname()." requires an integer primary key");
          return false;
      }
      $where = "id = " . $idvalue;
      return $this->load_one_object($where);
   }

   // Load by primary key (integer) logical key (string) or by field/value list
   function read($keyvalue) {
      if ( is_int($keyvalue) ) {
          return $this->get($keyvalue);
      }
      $keyname = $this->keyname();
      if ( $keyname && ! is_string($keyvalue) ) {
          DPRT("Model ".$this->modelname()." requires a locical string key=$keyname for read");
          return false;
      }
      if ( ! $keyname && ! is_array($keyvalue) ) {
          DPRT("Model ".$this->modelname()." does not support locical key on insert value=$keyvalue");
          return false;
      }

      if ( $keyname && is_string($keyvalue) ) {
         $where = "$this->keyname = '".mysql_real_escape_string($keyvalue)."';";
      } else if ( is_array($keyvalue) ) {
         $where = $this->makewhere($keyvalue);
      } 
      return $this->load_one_object($where);
   }

   // Deptecated - use set instead
   function setall($values, $regex = false, $strict = false) {
       // DPRT("Running setall() for $this->modelname");
       foreach( array_keys($values) as $keyname ) {
           if ( $regex ) {
               if ( preg_match($regex, $keyname) != 1 ) continue;
	   }
           $value = $values[$keyname];
           // DPRT("Input Key=".$keyname." value=".$value);
           if ( in_array($keyname,$this->datacolumns) || in_array($keyname,$this->internalcolumns) ) {
                 // DPRT("Exact match");
                 $this->datafields[$keyname] = $value;
                 continue;
           }
           if ( $strict ) continue;
           if ( strlen($keyname) <= 2 ) continue;
           // Remove underscores, to lower case 
           $softkeyname = strtolower(str_replace("_","",$keyname));
           // DPRT("Soft key $softkeyname key $keyname");
           foreach ( $this->datacolumns as $fieldname ) {
               $softfieldname = strtolower(str_replace("_","",$fieldname));
               // DPRT("Searching $softkeyname for $softfieldname at beginning or end");
               if ( preg_match('/^'.$softfieldname.'/', $softkeyname) == 1 ||
                    preg_match('/'.$softfieldname.'$/', $softkeyname) == 1 ) {
                  // DPRT("Soft match $keyname == $fieldname");
                  $this->datafields[$fieldname] = $value;
                  break;
               }
           } 
       }
   }

   // Two forms of set
   //  set("keyname", value)
   //  set( array( "key1" => value1, "key2" >= value) )
   function set($parm1,$parm2 = false, $strict = false) {
      // Handle the arrray variant - pass to setall
      if ( is_array($parm1) ) {
         return $this->setall($parm1, $parm2, $strict);
      }
      
      $key = $parm1;
      $value = $parm2;
      if ( in_array($key,$this->datacolumns) || in_array($key,$this->internalcolumns) ) {
         $this->datafields[$key] = $value;
      } else { 
        throw new Exception("Key ($key) not found in model ($modelname)"); 
      }
   }
}

?>
