<?php
/**
 * PandraColumn
 *
 * Column is Cassandra's atomic datatype, consisting of a name/value and timestamp
 * This class extends the Thrift cassandra_Column class with input validation,
 * time binding and pre-insert callbacks
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraColumn extends cassandra_Column implements PandraContainerChild, PandraColumnPathable {

    /* @var array validator type definitions for this column */
    private $_typeDef = array();

    /* @var array validator type definitions for this columns key */
    private $_typeDefKey = array();

    /* @var string last processing error */
    public $errors = array();

    /* @var string callback function for this column pre-save */
    private $_callback = NULL;

    /* @var bool column value has been modified since load() or init */
    private $_modified = FALSE;

    /* @var $delete column is marked for deletion */
    private $_delete = FALSE;

    /* @var bool column has been loaded from Cassandra */
    protected $_loaded = FALSE;

    /* @var PandraColumnFamily column family parent reference */
    private $_parent = NULL;

    /* @var string row key id */
    private $_keyID = NULL;

    /* @var string column keyspace */
    private $_keySpace = NULL;

    /* @var string column family name */
    private $_columnFamilyName = NULL;

    /* @var string super column name */
    private $_superColumnName = NULL;

    // ----------------- CONSTRUCTOR AND PARENT BINDING

    /**
     * Column constructor (extends cassandra_Column)
     * @param string $name Column name
     * @param PandraColumnContainer $parent parent column family (standard or super), or supercolumn
     * @param array $typeDef validator type definitions
     */
    public function __construct($name, $typeDefs = array(), PandraColumnContainer $parent = NULL, $callback = NULL) {

        parent::__construct(array('name' => $name));

        if ($parent !== NULL) $this->setParent($parent, !$parent->columnIn($name));

        if ($callback !== NULL) $this->setCallback($callback);

        $this->setTypeDef($typeDefs);

    }

    /**
     * Binds a ColumnFamily or SuperColumn as parent
     * @param PandraColumnContainer $parent ColumnFamily or SuperColumn parent object or NULL
     */
    public function setParent(PandraColumnContainer $parent, $bindToParent = TRUE) {

        if ( ! ($parent instanceof PandraColumnFamily || $parent instanceof PandraSuperColumn) || $parent instanceof PandraSuperColumnFamily) {
            throw new RuntimeException('Column Family or Super Column parent expected, received : '.get_class($parent));
        }

        if ($bindToParent) $parent->addColumnObj($this);

        // unbind existing parent
        $this->detach();

        $this->_parent = $parent;
    }

    public function disown($localDetach = TRUE) {
        if ($localDetach) $this->detach();
        $this->_parent = NULL;
    }

    /**
     * Gets the current working parent column family
     * @return <type>
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * Calls parent unset for this column
     */
    public function detach() {
        if ($this->_parent !== NULL) {
            $this->_parent->unsetColumn($this->getName());
        }
    }

    // ----------------- UTILITIES AND HELPERS

    /**
     * Compares a given value to the local value when normalised against the
     * same pre-save callback
     * @param mixed $cmpValue value to compare
     * @return bool comparitor and local value are the same
     */
    public function compareToCB($cmpValue) {
        $cmpValue = $this->callbackvalue($cmpValue);
        return ($this->value == $cmpValue);
    }

    /**
     * Checks a given value against the validator for this column
     * @param mixed $cmpValue value to check
     * @param array $errors array to store error messages
     * @return bool value checked out ok
     */
    public function checkValue($cmpValue, array &$errors) {
        return PandraValidator::check($cmpValue, $this->name, $this->_typeDef, $errors);
    }

    // ----------------- MUTATORS AND ACCESSORS

    /**
     * Binds a timestamp to the column, defaults to current time if no override defined
     * @param int $time new time stamp
     * @return int new timestamp
     */
    public function bindTime($time = NULL) {
        $this->timestamp = ($time === NULL) ? PandraCore::getTime() : intval($time);
        $this->setModified();
        return $this->timestamp;
    }

    /**
     * Sets the value of the column
     * @param mixed $value new value
     * @param bool $validate validate the value, if _typeDef is set
     * @return bool column set ok (check errors for details)
     */
    public function setValue($value, $validate = TRUE) {
        if ($validate && !empty($this->_typeDef)) {
            if (!PandraValidator::check($value, $this->name, $this->_typeDef, $this->errors)) {
                if ($this->_parent !== NULL) {
                    $this->_parent->registerError($this->errors[0]);
                }
                return FALSE;
            }
        }

        if ($value === NULL) return FALSE;

        $this->value = $value;
        $this->setModified();
        return TRUE;
    }

    public function setTypeDef($typeDefs, $onKey = FALSE) {
        if (empty($typeDefs)) return;

        $typeDefs = (array) $typeDefs;

        foreach ($typeDefs as $typeDef) {
            if (!PandraValidator::exists($typeDef)) {
                throw new RuntimeException("$typeDef is not a Validator type");
            }
        }

        if ($onKey) {
            $this->_typeDefKey = $typeDefs;
        } else {
            $this->_typeDef = $typeDefs;
        }
    }

    public function getTypeDef() {
        return $this->_typeDef;
    }

    public function setKeyValidator($typeDefs) {
        $this->setTypeDef($typeDefs, TRUE);
    }

    public function getKeyValidator() {
        return $this->_typeDefKey;
    }


    /**
     * Value accessor (cassandra_Column->value is public anyway, suggest using this incase that changes)
     * @return string column value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Name accessor (cassandra_Column->name is public anyway, suggest using this incase that changes)
     * @return string column name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Callback mutator, throws a RuntimeException if function does not exist
     * @param string $cbFunc callback function name
     */
    public function setCallback($cbFunc) {
        if (!function_exists($cbFunc)) {
            throw new RuntimeException("Function $cbFunc could not be found");
        } else {
            $this->_callback = $cbFunc;
        }
    }

    /**
     * Callback accessor
     * @return string pre-save callback function name
     */
    public function getCallback() {
        return $this->_callback;
    }

    /**
     * returns the callback function value for this->value
     * @return mixed result of callback eval
     */
    public function callbackvalue($value = NULL) {
        if ($this->_callback === NULL) {
            return $this->value;
        }
        $val = ($value === NULL) ? $this->value :  $value;
        return call_user_func($this->_callback, $val);
    }

    /**
     * keyID mutator
     * @param string $keyID row key id
     * @param bool $validate use the defined key validator
     */
    public function setKeyID($keyID, $validate = TRUE) {
        if ($validate && !empty($this->_typeDefKey)) {
            if (!PandraValidator::check($keyID, $this->name.' KEY ('.get_class($this).')', $this->_typeDefKey, $this->errors)) {
                if ($this->_parent !== NULL) {
                    $this->_parent->registerError($this->errors[0]);
                }
                return FALSE;
            }
        }

        $this->_keyID = $keyID;
        return TRUE;
    }

    /**
     * keyID accessor if local member has not been set, attempts to return the set parents attribute instead
     * @return string
     */
    public function getKeyID() {
        $parent = $this->getParent();
        if ($this->_keyID === NULL && $parent !== NULL) {
            return $parent->getKeyID();
        }
        return $this->_keyID;
    }

    /**
     * keySpace mutator
     * @param string $keySpace keyspace name
     */
    public function setKeySpace($keySpace) {
        $this->_keySpace = $keySpace;
    }

    /**
     * keySpace accessor if local member has not been set, attempts to return the set parents attribute instead
     * @return string
     */
    public function getKeySpace() {
        $parent = $this->getParent();
        if ($this->_keySpace === NULL && $parent !== NULL) {
            return $parent->getKeySpace();
        }
        return $this->_keySpace;
    }

    /**
     * columnFamilyName mutator
     * @param string $columnFamilyName parent column family name
     */
    public function setColumnFamilyName($columnFamilyName) {
        $this->_columnFamilyName = $columnFamilyName;
    }

    /**
     * columnFamilyName accessor if local member has not been set, attempts to return the set parents attribute instead
     * @return string
     */
    public function getColumnFamilyName() {
        $parent = $this->getParent();
        if ($this->_columnFamilyName === NULL) {
            if ($parent instanceof PandraSuperColumn) {
                return $parent->getParent()->getName();
            } elseif ($parent instanceof PandraColumnFamily) {
                return $parent->getName();
            }
        }
        return $this->_columnFamilyName;
    }

    /**
     * superColumnName mutator
     * @param string $superColumnName parent supercolumn name
     */
    public function setSuperColumnName($superColumnName) {
        $this->_superColumnName = $superColumnName;
    }

    /**
     * superColumnName accessor if local member has not been set, attempts to return the set parents attribute instead
     * @return string
     */
    public function getSuperColumnName() {
        $parent = $this->getParent();
        if ($this->_superColumnName === NULL && $parent instanceof PandraSuperColumn) {
            return $parent->getParent()->getName();
        }
        return $this->_superColumnName;
    }

    // ----------------- Saves and Loads

    /**
     * Casts from a cassandra_ColumnOrSuperColumn->column or cassandra_Column types, to PandraColumn
     * @param cassandra_Column $object source objct
     * @param PandraColumnContainer $parent parent container
     * @return PandraColumn new column object or NULL on empty cassandra_ColumnOrSuperColumn->column
     */
    static public function cast($object, PandraColumnContainer $parent = NULL) {

        if ($object instanceof cassandra_ColumnOrSuperColumn) {
            if (!empty($object->column->name)) {
                $object = $object->column;
            } else {
                return NULL;
            }

        } elseif (!($object instanceof cassandra_Column)) {
            throw new RuntimeException('Cast expected cassandra_Column[OrSuperColumn], recieved '.get_class($object));
        }

        $newObj = new PandraColumn($object->name);

        if ($parent !== NULL) $newObj->setParent($parent);

        $newObj->setValue($object->value);
        $newObj->bindTime($object->timestamp);

        return $newObj;
    }

    /**
     * Checks we have a bare minimum attributes on the entity, to perform a columnpath search
     * @param string $keyID optional overriding row key
     * @return bool columnpath looks ok
     */
    public function pathOK($keyID = NULL) {
        $ok = ( ($keyID !== NULL || $this->getKeyID() !== NULL) && $this->getKeySpace() !== NULL && $this->getName() !== NULL);
        if (!$ok) $this->registerError('Required field (Keyspace, ColumnFamily or KeyID) not present');
        return $ok;
    }

    /**
     * Loads a Column for key, maintaining parent binding
     * @param string $keyID optional row key
     * @param bool $colAutoCreate dummy interface, NULL
     * @param int $consistencyLevel cassandra consistency level
     * @return bool loaded OK
     */
    public function load($keyID = NULL, $colAutoCreate = NULL, $consistencyLevel = NULL) {

        if ($keyID === NULL) $keyID = $this->getKeyID();

        $ok = $this->pathOK($keyID);

        $this->setLoaded(FALSE);

        if ($ok) {
            $result = PandraCore::getColumn(
                    $this->getKeySpace(),
                    $keyID === NULL ? $this->getKeyID() : $keyID,
                    $this->getColumnFamilyName(),
                    $this->getName(),
                    $this->getSuperColumnName(),
                    PandraCore::getConsistency($consistencyLevel));

            if (!empty($result) && $result instanceof cassandra_ColumnOrSuperColumn) {
                $column = $result->column;
                $this->setValue($column->value);
                $this->bindTime($column->timestamp);
                $this->reset();
                $this->setLoaded(TRUE);
            } else {
                $this->registerError(PandraCore::$lastError);
            }
        }
        return ($ok && $this->isLoaded());
    }

    /**
     * Saves this individual column path, where a parent has been set (setParent()) keyid, keyspace, columnfamily or supercolumn
     * will be inherited for the save.
     * @return bool save ok
     */
    public function save($consistencyLevel = NULL) {

        if (!$this->isModified()) {
            $this->registerError("Column ".$this->name." is not modified");
            return FALSE;
        }

        // Build the column path for modifying this individual column
        $columnPath = new cassandra_ColumnPath();
        $columnPath->column_family = $this->getColumnFamilyName();
        $columnPath->super_column = $this->getSuperColumnName();
        $columnPath->column = $this->getName();

        $ok = FALSE;

        if ($this->isDeleted()) {
            $ok = PandraCore::deleteColumnPath(
                    $this->getKeySpace(),
                    $this->getKeyID(),
                    $columnPath,
                    $this->bindTime(),
                    PandraCore::getConsistency($consistencyLevel));

        } else {
            $ok = PandraCore::saveColumnPath(
                    $this->getKeySpace(),
                    $this->getKeyID(),
                    $columnPath,
                    $this->callbackvalue(),
                    $this->bindTime(),
                    PandraCore::getConsistency($consistencyLevel));
        }

        if (!$ok) {
            if (empty(PandraCore::$lastError)) {
                $errorStr = 'Unknown Error';
            } else {
                $errorStr = PandraCore::$lastError;
            }

            $this->registerError($errorStr);
        }

        if ($ok) $this->reset();
        return $ok;
    }

    // ----------------- ERROR HANDLING

    /**
     * Creates an error entry in this column and propogate to parent
     * @param string $errorStr error string
     */
    public function registerError($errorStr) {
        if (!empty($errorStr)) {
            array_push($this->errors, $errorStr);
            if ($this->_parent !== NULL) {
                $this->_parent->registerError($errorStr);
            }
        }
    }

    /**
     * Grabs all errors for the column instance
     * @return array all errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Grabs the last logged error
     * @return string last error message
     */
    public function getLastError() {
        if (count($this->errors)) {
            return $this->errors[0];
        }
        return NULL;
    }

    /**
     * Destroys all errors in this container, and its children
     * @param bool $childPropogate optional propogate destroy to children (default TRUE)
     */
    public function destroyErrors() {
        unset($this->errors);
        $this->errors = array();
    }

    // ----------------- MODIFY/DELETE MUTATORS AND ACCESSORS

    /**
     * loaded mutator, Mark container path as loaded via cassandra
     * @param bool $loaded mark as loaded
     */
    protected function setLoaded($loaded) {
        $this->_loaded = $loaded;
    }

    /**
     * loaded accessor
     * @return bool  container path has been marked as loaded
     */
    public function isLoaded() {
        return $this->_loaded;
    }

    /**
     * Removes any modified or delete flags, (does not revert values)
     */
    public function reset() {
        $this->_modified = FALSE;
        $this->_delete = FALSE;
        return TRUE;
    }

    /**
     * mutator, marks this column for deletion and sets modified
     */
    public function delete() {
        $this->_delete = $this->_modified = TRUE;
    }

    /**
     * delete mutator
     * @param bool $delete mark column as deleted
     */
    public function setDelete($delete) {
        $this->_delete = $delete;
    }

    /**
     * delete accessor
     * @return bool column has been marked for deletion
     */
    public function getDelete() {
        return $this->_delete;
    }

    /**
     * Column will be deleted
     * @return bool Column is marked for deletion and is modified
     */
    public function isDeleted() {
        return ($this->_delete && $this->_modified);
    }

    /**
     * Modified accessor
     * @return bool Column is marked as modified
     */
    public function isModified() {
        return $this->_modified;
    }

    /**
     * Modified mutator
     * @param bool $modified column is modified
     */
    public function setModified($modified = TRUE) {
        $this->_modified = $modified;
    }

    /**
     * Modified accessor
     * @return bool column has been modified since instance construct/load
     */
    public function getModified() {
        return $this->_modified;
    }
}
?>