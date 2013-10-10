<?php
/**
 * PandraSuperColumn
 *
 * SuperColumns are special kinds of Column Containers which can contain both
 * Columns and have a ColumnFamily parent (implements ContainerChild)
 *
 * 'super column family' => // Super Column Family
 *                      'my supercolumn' => {  // Super Column
 *                                 'column1',  // Column
 *                                 'column2',  // Column
 *                                 'column3'   // Column
 *                               },
 *                      'my supercolumn2' => { // Super Column
 *                                 'column4',  // Column
 *                                 'column5',  // Column
 *                                 'column6'   // Column
 *                               },
 *                      }
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraSuperColumn extends PandraColumnContainer implements PandraContainerChild, PandraColumnPathable {

    /* @var PandraColumnFamily column family parent reference */
    private $_parent = NULL;

    /* @var string parent column family name, overrides parent */
    private $_columnFamilyName = NULL;

    /**
     * Supercolumn constructor
     * @param string $superName Super Column name
     * @param PandraSuperColumnFamily $parent
     */
    public function __construct($superName, $keyID = NULL, $keySpace = NULL, $parent = NULL, $containerType = NULL) {

        // SuperColumn name
        $this->setName($superName);

        // Reference parent ColumnFamilySuper
        if ($parent !== NULL) {
            $this->setParent($parent, !$parent->columnIn($superName));
        }

        parent::__construct($keyID, $keySpace, $superName, $containerType);
    }

    /**
     * Checks we have a bare minimum attributes on the entity, to perform a columnpath search
     * @param string $keyID optional overriding row key
     * @return bool columnpath looks ok
     */
    public function pathOK($keyID = NULL) {
        if ($this->_parent === NULL) {
            return parent::pathOK($keyID);
        }
        return $this->_parent->pathOK($keyID);
    }

    /**
     * Save all columns in this loaded columnfamily
     * @return void
     */
    public function save($consistencyLevel = NULL) {
        if (!$this->isModified()) return FALSE;

        $ok = $this->pathOK();

        if ($ok) {
            if ($this->isDeleted()) {

                $columnPath = new cassandra_ColumnPath();
                $columnPath->column_family = $this->getColumnFamilyName();
                $columnPath->super_column = $this->getName();
                $ok = PandraCore::deleteColumnPath($this->getKeySpace(),
                        $this->getKeyID(),
                        $columnPath,
                        NULL,
                        $consistencyLevel);
            } else {

                $deletions = array();
                $insertions = array();

                $selfKey = $this->getKeyID();
                $selfName = $this->getName();
                $selfCFName = $this->getColumnFamilyName();

                // build mutation
                $map = array($selfKey => array($selfCFName => array()));

                $ptr = &$map[$selfKey][$selfCFName];

                $this->bindTimeModifiedColumns();
                $modifiedColumns = $this->getModifiedColumns();

                if (!empty($modifiedColumns)) {
                    foreach ($modifiedColumns as &$cObj) {
                        if ($cObj->isDeleted()) {
                            $deletions[] = $cObj->getName();
                        } else {
                            $insertions[] = $cObj;
                        }
                    }

                    if (!empty($deletions)) {
                        $p = new PandraSlicePredicate(PandraSlicePredicate::TYPE_COLUMNS, $deletions);
                        $sd = new cassandra_Deletion(array('timestamp' => PandraCore::getTime(), 'predicate' => $p));
                        $ptr[] = new cassandra_Mutation(array('deletion' => $sd));
                    }

                    if (!empty($insertions)) {
                        $sc = new cassandra_SuperColumn(array('name' => $selfName, 'columns' => $insertions));
                        $csc = new cassandra_ColumnOrSuperColumn(array('super_column' => $sc));
                        $ptr[] = new cassandra_Mutation(array('column_or_supercolumn' => $csc));
                    }

                    $ok = PandraCore::batchMutate($this->getKeySpace(), $map, $consistencyLevel);
                }
            }

            if ($ok) {
                $this->reset();
            } else {
                $this->registerError(PandraCore::$lastError);
            }
        }

        return $ok;
    }

    /**
     * Loads a SuperColumn for key
     *
     * @param string $keyID optional row key
     * @param int $consistencyLevel cassandra consistency level
     * @return bool loaded OK
     */
    public function load($keyID = NULL, $consistencyLevel = NULL) {

        if ($keyID === NULL) $keyID = $this->getKeyID();

        $ok = $this->pathOK($keyID);

        $this->setLoaded(FALSE);

        if ($ok) {

            $autoCreate = $this->getAutoCreate();

            $predicate = new cassandra_SlicePredicate();

            // if autocreate is turned on, get latest limited everything
            if ($autoCreate) {

                $predicate->slice_range = new cassandra_SliceRange();
                $predicate->slice_range->start = $this->getStart();
                $predicate->slice_range->finish = $this->getFinish();
                $predicate->slice_range->count = $this->getLimit();
                $predicate->slice_range->reversed = $this->getReversed();

                $result = PandraCore::getCFSlice(
                        $this->getKeySpace(),
                        $keyID,
                        new cassandra_ColumnParent(array(
                                'column_family' => $this->getColumnFamilyName(),
                                'super_column' => $this->getName())),
                        $predicate,
                        $consistencyLevel);

                // otherwise by defined columns (slice query)
            } else {

                $predicate->column_names = $this->getColumnNames();

                $result = PandraCore::getCFSliceMulti(
                        $this->getKeySpace(),
                        array($keyID),
                        $predicate,
                        new cassandra_ColumnParent(
                        array(
                                'column_family' => $this->getColumnFamilyName(),
                                'super_column' => $this->getName())),
                        $consistencyLevel);

                $result = $result[$keyID];
            }

            if (!empty($result)) {
                $this->init();
                $this->setLoaded($this->populate($result, $autoCreate));
                if ($this->isLoaded()) $this->setKeyID($keyID);
            } else {
                $this->registerError(PandraCore::$lastError);
            }
        }
        return ($ok && $this->isLoaded());
    }

    /**
     * Sets parent Column Container
     * @param PandraColumnContainer $parent SuperColumnFamily container object, or NULL
     */
    public function setParent(PandraColumnContainer $parent, $bindToParent = TRUE) {

        if (!($parent instanceof PandraSuperColumnFamily))
            throw new RuntimeException('Parent must be an instance of PandraSuperColumnFamily');

        if ($bindToParent) $parent->addSuperColumnObj($this);

        // unbind existing parent
        $this->detach();

        $this->_parent = $parent;
    }

    /**
     * Gets the current working parent column family
     * @return <type>
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * Nullifies a parent
     */
    public function disown($localDetach = TRUE) {
        if ($localDetach) $this->detach();
        $this->_parent = NULL;
    }

    /**
     * Calls parent unset for this column
     */
    public function detach() {
        if ($this->_parent !== NULL) {
            $this->_parent->unsetColumn($this->getName());
        }
    }

    /**
     * accessor, parent column family name
     * @return string container name
     */
    public function getColumnFamilyName() {
        $parent = $this->getParent();
        if ($this->_columnFamilyName === NULL && $parent !== NULL) {
            return $parent->getName();
        }
        return $this->_columnFamilyName;
    }

    /**
     * mutator, container name
     * @param string $name new name
     */
    public function setColumnFamilyName($columnFamilyName) {
        $this->_columnFamilyName = $columnFamilyName;
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
     * Creates an error entry in this column and propogate to parent
     * @param string $errorStr error string
     */
    public function registerError($errorStr) {
        if (!empty($errorStr)) {
            array_push($this->errors, $errorStr);
            if ($this->_parent !== NULL) $this->_parent->registerError($errorStr);
        }
    }

    public function _getName() {
        $parent = $this->getParent();
        if ($parent !== NULL) {
            if ($parent->getType() == self::TYPE_UUID) {
                return UUID::toStr($this->_name);
            }
        }
        return parent::getName();
    }
}
?>