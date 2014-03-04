<?php
/**
 * PandraColumnContainer
 *
 * ColumnContainer is an abstract to provide an intuitive representation of
 * Cassandra's associative array data model.  Keyspace and Key are implied in
 * the container via setKeyspace and setKeyID mutators.
 *
 *  eg:
 *      Cassandra CLI - get Keyspace1.Standard1['key']['column_name']
 *      Pandra - $Standard1['column_name'];
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 * @abstract
 */
abstract class PandraColumnContainer implements ArrayAccess, Iterator, Countable {

    /* @var string magic set/get prefixes for Columns */
    const _columnNamePrefix = 'column_';

    /* @var int UUID container type */
    const TYPE_UUID = 0;

    /* @var int 'STRING' container type (untranslated bytes/ascii/utf8/long types) */
    const TYPE_STRING = 2;

    /* @var int 'LONG' container type @todo - not implemented! */
    const TYPE_LONG = 3;

    /* @var int 'string' flag for type conversion */
    const CONTEXT_STRING = 0;

    /* @var int 'binary' flag for type conversion */
    const CONTEXT_BIN = 1;

    /* @var array complete list of errors for this object instance */
    public $errors = array();

    /* @var array validator type definitions for this columns key */
    private $_typeDefKey = array();

    /* @var this column families name (table name) */
    protected $_name = NULL;

    /* @var string row key id */
    protected $_keyID = NULL;

    /* @var string column keyspace */
    protected $_keySpace = NULL;

    /* @var array container for column objects, indexed to field name */
    protected $_columns = array();

    /* @var bool columnfamily marked for deletion */
    protected $_delete = FALSE;

    /* @var bool container (columns) have been modified */
    protected $_modified = FALSE;

    /* @var bool container columns have been loaded from Cassandra */
    protected $_loaded = FALSE;

    /* @var bool auto create columns/containers loaded from Cassandra which do not exist in the local container */
    protected $_autoCreate = TRUE;

    /* @var int default container type */
    protected $_containerType = self::TYPE_STRING;

    /* @var int default limit for autocreate queries */
    protected $_rangeLimit = DEFAULT_ROW_LIMIT;

    /* @var bool reverses slice order */
    protected $_reversed = TRUE;

    protected $_start = '';

    protected $_finish = '';

    /**
     * CF constructor, calls init()
     * @param string $keyID row key id
     * @param string $keySpace Cassandra Keyspace
     * @param string $name  container name
     * @param int $containerType one of self::TYPE_ ordering schemas (UUID STRING)
     */
    public function __construct($keyID = NULL, $keySpace = NULL, $name = NULL, $containerType = NULL) {
        $this->init();
        if ($containerType !== NULL) $this->setType($containerType);
        if ($keyID !== NULL) $this->setKeyID($keyID);
        if ($keySpace !== NULL) $this->setKeySpace($keySpace);
        if ($name !== NULL) {
            $this->setName($name);
        } else if ($name === NULL && $this->getName() === NULL) {
            // Fall back to the class name
            $this->setName(get_class($this));
        }
    }

    /**
     * init is is always called by the constructor.  Child classes can implement
     * constructor logic, schemas, defaults validators etc. via init()
     * @return void
     */
    public function init() {
    }

    /**
     * Sets the type for this container (uuid or string)
     * @param int $containerType TYPE_STRING or TYPE_UUID
     */
    public function setType($containerType) {
        // We can't set type after columns have been added
        if (empty($this->_columns)) {
            $this->_containerType = $containerType;
        } else {
            throw new RuntimeException('Cannot setType on a non-empty container');
        }
    }

    /**
     * Container type accessor
     * @return int TYPE_STRING or TYPE_UUID
     */
    public function getType() {
        return $this->_containerType;
    }

    /**
     * accessor, container name
     * @return string container name
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * mutator, container name
     * @param string $name new name
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * keySpace mutator
     * @param string $keySpace keyspace name
     */
    public function setKeySpace($keySpace) {
        $this->_keySpace = $keySpace;
    }

    /**
     * keyspace accessor
     * @return string keyspace name
     */
    public function getKeySpace() {
        return $this->_keySpace;
    }

    /**
     * keyID mutator
     * @param string $keyID row key id
     * @param bool $validate use the defined key validator
     */
    public function setKeyID($keyID, $validate = TRUE) {
        if ($validate && !empty($this->_typeDefKey)) {
            $errors = array();
            if (!PandraValidator::check($keyID, $this->getName().' KEY ('.get_class($this).')', $this->_typeDefKey, $errors)) {
                $lastError = $errors[0];
                $this->registerError($lastError);
                if ($this->_parent !== NULL) {
                    $this->_parent->registerError($lastError);
                }
                return FALSE;
            }
        }

        $this->_keyID = $keyID;
        return TRUE;
    }

    /**
     * keyID accessor
     * @return string row key id
     */
    public function getKeyID() {
        return $this->_keyID;
    }

    /**
     * Sets the validator(s) for key changes
     * @param array $typeDefs PandraValidator primitive or complex types
     */
    public function setKeyValidator($typeDefs) {
        if (empty($typeDefs)) return;

        $typeDefs = (array) $typeDefs;

        foreach ($typeDefs as $typeDef) {
            if (!PandraValidator::exists($typeDef)) {
                throw new RuntimeException("$typeDef is not a Validator type");
            }
        }

        $this->_typeDefKey = $typeDefs;
    }

    /**
     * Key typedef accessor
     * @return array type definitions for key
     */
    public function getKeyValidator() {
        return $this->_typeDefKey;
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
     * mutator, modified
     * @param bool $modified
     */
    protected function setModified($modified = TRUE) {
        $this->_modified = $modified;
    }

    /**
     * marks the container and subcolumns (or subcontainers) for deletion
     * operation cascades to columns
     * @return void
     */
    public function delete() {
        $this->_delete = $this->_modified = TRUE;
        foreach ($this->_columns as &$column) {
            $column->delete();
        }
    }

    /**
     * mutator, marks this column for deletion
     * @param bool $delete
     */
    protected function setDelete($delete) {
        $this->_delete = $delete;
    }

    /**
     * accessor, delete
     * @return bool container is marked for deletion
     */
    public function getDelete() {
        return $this->_delete;
    }

    /**
     * Creates an error entry in this column and propogate to parent
     * @param string $errorStr error string
     */
    public function registerError($errorStr) {
        if (empty($errorStr)) return;
        $this->errors[] = $errorStr;
    }

    /**
     * Destroys all errors in this container, and its children
     * @param bool $childPropogate optional propogate destroy to children (default TRUE)
     */
    public function destroyErrors($childPropogate = TRUE) {
        $this->errors = array();
        if ($childPropogate) {
            foreach ($this->_columns as $column) {
                $column->destroyErrors();
            }
        }
    }

    /**
     * Grabs all errors for the container instance
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
     * Sets value with a validator.  To skip validation, use explicit
     * PandraColumn->setValue($value, FALSE); or do not provide a typeDef
     * @param string $offset column name
     * @param mixed $value new column value
     */
    public function offsetSet($offset, $value) {
        $this->__set($offset, $value);
    }

    /**
     * Check for column existence
     * @param string $offset column name
     * @return bool column eixsts
     */
    public function offsetExists($offset) {
        return isset($this->_columns[$offset]);
    }

    /**
     * This only unsets the column in the container, to delete use the
     * PandraColumn->delete() function
     * @param string $columnName column name
     * @return void
     */
    public function offsetUnset($columnName) {
        $this->unsetColumn($columnName);
    }

    /**
     * Get column value by column name
     * @param string $offset column name
     * @return mixed column value
     */
    public function offsetGet($columnName) {
        if ($columnName instanceof PandraClause || $columnName instanceof PandraQuery) {
            return $this->getColumn($columnName);
        }
        return $this->__get($columnName);
    }

    public function unsetColumn($columnName) {
        if (array_key_exists($columnName, $this->_columns)) {
            $this->_columns[$columnName]->disown(FALSE);
            unset($this->_columns[$columnName]);
        }
    }

    /**
     * Count children
     * @return int number of immediate children in container
     */
    public function count() {
        return count($this->_columns);
    }

    /**
     * Get current offset
     * @return object current column offset
     */
    public function current() {
        return current($this->_columns);
    }

    /**
     * Return the key of the current child
     * @return string child key (column name)
     */
    public function key() {
        return key($this->_columns);
    }

    /**
     * Rewind iterator for first element
     */
    public function rewind() {
        reset($this->_columns);
    }

    /**
     * Advance to the next element
     */
    public function next() {
        next($this->_columns);
    }

    /**
     * Current element exists
     * @return boolean current element exists
     */
    public function valid() {
        return (boolean) $this->current();
    }

    /**
     * retrieve the last error for this container
     * @return <type>
     */
    public function lastError() {
        if (!empty($this->errors)) {
            return $this->errors[0];
        }
        return NULL;
    }

    public function setLimit($limit) {
        $this->_rangeLimit = $limit;
    }

    public function limit($limit) {
        $this->setLimit($limit);
        return $this;
    }

    public function getLimit() {
        return $this->_rangeLimit;
    }

    public function setReversed($reverse) {
        $this->_reversed = $reverse;
    }

    public function reverse($reverse) {
        $this->setReversed($reverse);
        return $this;
    }

    public function getReversed() {
        return $this->_reversed;
    }

    private function _setStartFinish($value, $attrib = '_start') {
        if ($this->_containerType == self::TYPE_UUID ||
                $this->_containerType == self::TYPE_LONG) {
            $this->$attrib = $this->typeConvert($value, self::CONTEXT_BIN);
        } else {
            $this->$attrib = $value;
        }
    }

    public function setStart($start) {
        $this->_setStartFinish($start);
    }

    public function start($start) {
        $this->setStart($start);
        return $this;
    }

    public function getStart() {
        return $this->_start;
    }

    public function setFinish($finish) {
        $this->_setStartFinish($finish, '_finish');
    }

    public function finish($finish) {
        $this->setFinish($finish);
        return $this;
    }

    public function getFinish() {
        return $this->_finish;
    }

    /**
     * Converts the given column name to it's expected container type context (UUID or String)
     *
     * This stub can also potentially handle utf8 cf types
     *
     * @param string $columnName column name
     * @param int $toFmt convert to type CONTEXT_BIN OR CONTEXT_STRING
     * @return mixed converted column name
     */
    protected function typeConvert($columnName, $toFmt) {
        // @todo move to generic helper
        $bin = UUID::isBinary($columnName);

        // Save accidental double-conversions on binaries
        if (($bin && $toFmt == self::CONTEXT_BIN) ||
                (!$bin && $toFmt == self::CONTEXT_STRING)) {
            return $columnName;
        }

        if (($this->_containerType == self::TYPE_UUID)	) {

            if (!$bin && !UUID::validUUID($columnName)) {
                throw new RuntimeException('Column Name ('.$columnName.') cannot be converted');
            }

            if ($toFmt == self::CONTEXT_BIN) {
                return UUID::toBin($columnName);
            } elseif ($toFmt == self::CONTEXT_STRING) {
                return UUID::toStr($columnName);
            }

        } else if ($this->_containerType == self::TYPE_LONG) {
            // unpack the long
            if ($bin && $toFmt == self::CONTEXT_STRING) {
                $columnName = array_pop(unpack('N', $columnName));

                // pack the long
            } elseif (!$bin && $toFmt == self::CONTEXT_BIN) {
                $columnName = pack('NN', $columnName, 0);
            }
        }

        return $columnName;
    }

    /**
     * Define a new column, type definition and callback
     * @param string $columnName column name
     * @param array $typeDef validator type definitions
     * @param string $callbackOnSave callback function pre-save
     * @return PandraColumn reference to created column
     */
    public function addColumn($columnName, $typeDef = array(), $callbackOnSave = NULL) {

        // can't use array_key_exists for longs - floats are truncated
        if ($this->_containerType == self::TYPE_LONG) {
            $foundKey = FALSE;
            foreach ($this->_columns as $key => $value) {
                $foundKey = ($key == $columnName);
                if ($foundKey) break;
            }
        } else {
            $foundKey = array_key_exists($columnName, $this->_columns);
        }

        if (!$foundKey) {

            $this->_columns[$columnName] =
                    new PandraColumn($this->typeConvert($columnName, self::CONTEXT_BIN), $typeDef);
            $this->_columns[$columnName]->setParent($this, FALSE);
        }

        // pre-save callback
        if (!empty($callbackOnSave)) $this->getColumn($columnName)->setCallback($callbackOnSave);

        // @todo php sucks balls, lets lose our precision.
        if (!PANDRA_64 && $this->_containerType == self::TYPE_LONG) {
            $columnName = (int) $columnName;
        }

        return $this->getColumn($columnName);
    }

    /**
     * Adds a column object to this column container, overwrites existing column (context helper)
     * @param PandraSuperColumn $columnObj
     */
    public function addColumnObj(PandraColumn $columnObj) {
        if ($columnObj->getName() === NULL) throw new RuntimeException('Column has no name');
        $this->_columns[$this->typeConvert($columnObj->name, self::CONTEXT_STRING)] = $columnObj;
    }

    /**
     * Get reference to named PandraColumn
     * @param string $columnName column name
     * @return PandraColumn
     */
    public function getColumn($columnMatch) {

        // @todo document that only empty containers can act as dynamic views.
        if ($columnMatch instanceof PandraQuery && empty($this->_columns)) {
            $columnMatch->setInvoker($this);
            $columnMatch->graphContext(get_class($this));
            return $columnMatch;

            // Extract matching named columns based on clause
        } elseif ($columnMatch instanceof PandraClause) {
            $matches = array();

            foreach ($this->_columns as $columnName => &$column) {
                if ($columnMatch->match($columnName)) {
                    $matches[$columnName] = $this->_columns[$columnName];
                }
            }

            return $matches;
        }

        if ($this->_gsMutable($columnMatch)) {
            return $this->_columns[$columnMatch];
        }
        return NULL;
    }

    /**
     * columns accessor
     * @return array returns array of children
     */
    public function getColumns() {
        return $this->_columns;
    }

    /**
     * Returns array of column names at depth 1
     * @return array string column names
     */
    public function getColumnNames() {
        return array_keys($this->_columns);
    }

    /**
     * Determins if column name exists in children at depth 1
     * @param string $columnName column name
     * @return bool column exists
     */
    public function columnIn($columnName) {
        if (UUID::isBinary($columnName)) {
            $columnName = UUID::toStr($columnName);
        }
        return array_key_exists($columnName, $this->_columns);

    }

    /**
     * Sets a columns value for this slice
     * @todo pretty sure this breaks ContainerChild interface
     *
     * @param string $columnName Column name to set
     * @param string $value New value for column
     * @param bool $validate opt validate from typeDef (default TRUE)
     * @return bool column set ok
     */
    public function setColumn($columnName, $value, $validate = TRUE) {
        if (is_object($value)) {
            $this->_columns[$columnName] = $value;
            return TRUE;
        }

        return (array_key_exists($columnName, $this->_columns) && $this->_columns[$columnName]->setValue($value, $validate));
    }

    /**
     * Get a list of columns in the container
     * @return array list of column names
     */
    public function listColumns() {
        return array_keys($this->_columns);
    }

    /**
     * unmarks container and subcolumns (or subcontainers) for deletion
     * cascades to columns, unsets modified flag
     */
    public function reset() {
        $this->setDelete(FALSE);
        $this->setModified(FALSE);
        $cReset = FALSE;
        foreach ($this->_columns as &$column) {
            $cReset = $column->reset();
            if ($cReset == FALSE) break;
        }

        return (!$this->_delete && !$this->_modified && $cReset);
    }

    /**
     * removes a column from the container (does not delete from Cassandra)
     * @param string $columnName column name
     */
    public function destroyColumns($columnName = NULL) {
        if ($columnName !== NULL) {
            if (array_key_exists($columnName, $this->_columns)) {
                unset($this->_columns[$columnName]);
            }
        } else {
            $this->_columns = array();
        }
        $this->reset();
    }

    /**
     * Get working autocreate mode (either this set autocreate or overriden)
     * @param bool $override
     * @return bool working autocreate mode
     */
    public function getAutoCreate($override = NULL) {
        $autoCreate = $this->_autoCreate;

        if ($override !== NULL) {
            $autoCreate = $override;
        }

        return $autoCreate;
    }

    /**
     * AutoCreate mutator, loads (super) columns from Cassandra
     * which do not exist in the local container. Setting this to false will
     * retrieve an entire Column Family or SuperColumn for the key, handle
     * with care.
     *
     * @param bool $autoCreate new mode
     */
    public function setAutoCreate($autoCreate) {
        $this->_autoCreate = $autoCreate;
    }

    /**
     * Populates container object (ColumnFamily, ColumnFamilySuper or SuperColumn)
     * @param mixed $data associative string array, array of cassandra_Column's or JSON string of key => values.
     * @return bool column values set without error
     */
    public function populate($data, $colAutoCreate = NULL) {

        if (is_string($data)) {
            $data = json_decode($data, TRUE);
        }

        if ($data instanceof cassandra_KeySlice) {
            $this->setKeyID($data->key);
            $data = $data->columns;
        }

        if (is_array($data) && count($data)) {

            // Check depth, take first few keys as keyspace/columnfamily/key
            foreach ($data as $idx => $column) {
                if ($column instanceof cassandra_Column) {
                    $columnName =  $this->typeConvert($column->name, self::CONTEXT_STRING);

                    if ($this->getAutoCreate($colAutoCreate) ) {
                        $this->_columns[$columnName] = PandraColumn::cast($column, $this);
                    } else if (array_key_exists($columnName, $this->_columns)) {
                        $this->_columns[$columnName]->setValue($column->column->value);
                    }

                    // circular dependency?
                } elseif ($column instanceof cassandra_ColumnOrSuperColumn && !empty($column->column)) {
                    $columnName =  $this->typeConvert($column->column->name, self::CONTEXT_STRING);

                    if ($this->getAutoCreate($colAutoCreate) ) {
                        $this->_columns[$columnName] = PandraColumn::cast($column->column, $this);
                    } else if (array_key_exists($columnName, $this->_columns)) {
                        $this->_columns[$columnName]->setValue($column->column->value);
                    }

                } else {
                    $colExists = array_key_exists($idx, $this->_columns);
                    // Create a new named column object
                    if ($this->getAutoCreate($colAutoCreate) && !array_key_exists($idx, $this->_columns)) {
                        $this->addColumn($idx);
                    }

                    // Set Value
                    if (array_key_exists($idx, $this->_columns)) {
                        if ($this->_columns[$idx] instanceof PandraColumn) {
                            $this->_columns[$idx]->setValue($column);
                        }
                    }
                }
            }
        } else {
            return FALSE;
        }

        return empty($this->errors);
    }

    /**
     * determine if get/set field exists/is mutable, strips field prefix from magic get/setters
     * @param string $columnName field name to check
     * @return bool field exists
     */
    protected function _gsMutable(&$columnName) {
        $pfx = "/^".constant(get_class($this).'::_columnNamePrefix')."/";
        $columnName = preg_replace($pfx, "", $columnName);

        return array_key_exists($columnName, $this->_columns);
    }

    /**
     * Magic getter
     * @param string $columnName field name to get
     * @return string value
     */
    public function __get($columnName) {
        if ($this->_gsMutable($columnName)) {

            if ($this->_columns[$columnName] instanceof PandraColumn) {
                return $this->_columns[$columnName]->value;

            } else if ($this->_columns[$columnName] instanceof PandraColumnContainer) {
                return $this->_columns[$columnName];
            }
        }
        return NULL;
    }

    /**
     * Magic setter
     * @todo propogate an exception for setcolumn if it returns false.  magic __set's are void return type
     * @param string $columnName field name to set
     * @param string $value  value to set for field
     * @return bool field set ok
     */
    public function __set($columnName, $value) {
        $mutable = $this->_gsMutable($columnName);

        if (!$mutable && $this->getAutoCreate()) {
            $this->addColumn($columnName);
        }

        if (!$this->setColumn($columnName, $value)) {
            throw new RuntimeException('Property '.$columnName.' could not be set');
        }
    }

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
     * accessor, checks if container has been explicitly modified, or it sub columns
     * @return <type>
     */
    public function isModified() {
        foreach ($this->_columns as $column) {
            if ($column->isModified()) return TRUE;
        }
        return $this->_modified;
    }

    /**
     * Column will be deleted
     * @return bool Column is marked for deletion and is modified
     */
    public function isDeleted() {
        return ($this->_modified && $this->_delete);
    }

    /**
     * Binds current time to all modified columns
     * @param int $timeOverride optional timestamp, will default to time() if NULL
     */
    public function bindTimeModifiedColumns($timeOverride = NULL) {
        foreach ($this->_columns as &$cObj) {
            if ($cObj->isModified()) {
                $cObj->bindTime($timeOverride);
            }
        }
    }

    /**
     * Returns all columns which have been modified
     * @return array array of  modified columns
     */
    public function getModifiedColumns() {
        $modColumns = array();
        foreach ($this->_columns as &$cObj) {
            if ($cObj->isModified()) $modColumns[] = &$cObj;
        }
        return $modColumns;
    }

    /**
     * Converts this container into its equivalent JSON representation
     * @param bool $keyPath include keyspace/columnfamily/key prefix
     * @return string JSON container
     */
    public function toJSON($keyPath = FALSE) {
        return json_encode($this->toArray($keyPath));
    }

    /**
     * Converts this container into its equivalent array representation
     * @param bool $keyPath include keyspace/columnfamily/key prefix
     * @return array container
     */
    public function toArray($keyPath = FALSE) {
        $retArr = array();

        foreach ($this->_columns as $columnName => $column) {
            //$columnName =  $this->typeConvert($column->getName(), UUID::UUID_STR);

            if ($column instanceof PandraColumn) {
                $retArr[$columnName] = $column->value;

            } else {
                // keyspace/CF/key/{column or supercolumn}
                if ($keyPath) {
                    $retArr[$this->getKeySpace()][$this->getName()][$this->getKeyID()][$columnName] = $column->toArray();
                } else {
                    $retArr[$columnName] = $column->toArray($keyPath);
                }
            }
        }
        return $retArr;
    }
}
?>