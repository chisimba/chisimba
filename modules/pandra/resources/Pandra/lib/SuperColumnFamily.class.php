<?php
/**
 * PandraSuperColumnFamily
 *
 * SuperColumnFamily is a container of SuperColumns.
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

/**
 * @abstract
 */
class PandraSuperColumnFamily extends PandraColumnFamily implements PandraColumnPathable {

    /* @var string magic get/set prefix for Super Columns */
    const _columnNamePrefix = 'super_';

    /**
     * Helper function to add a Super Column instance to this Super Column Family
     * addSuper overrides the parent container reference in the object instance
     * To add the same supercolumn instance to multiple columnfamilies, use object clones
     * instead.
     * @param PandraSuperColumn $scObj
     * @return PandraSuperColumn
     */
    public function addSuper(PandraSuperColumn $scObj) {
        $superName = $scObj->getName();

        if ($this->getType() == self::TYPE_UUID && !UUID::isBinary($scObj->getName())) {
            $scObj->setName(UUID::toBin($scObj->getName()));
        }

        $scObj->setParent($this, false);

        $this->_columns[$superName] = $scObj;

        return $this->getColumn($superName);
    }

    /**
     * Define a new named SuperColumn, anologous to ColumnFamily->addColumn
     * The only real difference between addColumn and addSuper in a SuperColumn
     * context, is addColumn will not overwrite the column with a new named instance
     * @param string $superName super column name
     * @return PandraSuperColumn reference to created column
     */
    public function addColumn($superName, $containerType = NULL) {
        if (!array_key_exists($superName, $this->_columns)) {
            $this->_columns[$superName] = new PandraSuperColumn(
                    $this->typeConvert($superName, self::CONTEXT_BIN),
                    $this->getKeyID(),
                    $this->getKeySpace(),
                    $this,
                    $containerType);
        }
        return $this->getColumn($superName);
    }

    /**
     * Adds a supercolumn object to this super cf, overwrites existing supercolumn
     * @param PandraSuperColumn $columnObj
     */
    public function addColumnObj(PandraSuperColumn $columnObj) {
        if ($columnObj->getName() === NULL) throw new RuntimeException('SuperColumn has no name');
        $this->_columns[$columnObj->getName()] = $columnObj;
    }

    /**
     * Adds a supercolumn object to this super cf, overwrites existing supercolumn (context helper)
     * @param PandraSuperColumn $columnObj
     */
    public function addSuperColumnObj(PandraSuperColumn $columnObj) {
        $this->addColumnObj($columnObj);
    }

    /**
     * getColumn alias (context helper)
     * @param <type> $superName
     * @return <type>
     */
    public function getSuper($superName) {
        return $this->getColumn($superName);
    }

    public function save($consistencyLevel = NULL) {

        if (!$this->isModified()) return FALSE;

        $ok = $this->pathOK();

        if ($ok) {

            // Deletes the entire columnfamily by key
            if ($this->isDeleted()) {
                $columnPath = new cassandra_ColumnPath();
                $columnPath->column_family = $this->getName();

                $ok = PandraCore::deleteColumnPath(
                        $this->getKeySpace(),
                        $this->getKeyID(),
                        $columnPath,
                        NULL,
                        $consistencyLevel);
                if (!$ok) $this->registerError(PandraCore::$lastError);

            } else {
                /* @todo must be a better way */
                foreach ($this->_columns as $colName => $superColumn) {
                    $ok = $superColumn->save();
                    if (!$ok) {
                        $this->registerError(PandraCore::$lastError);
                        break;
                    }
                }
            }
        }

        return $ok;
    }

    /**
     * Loads an entire columnfamily by keyid
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
                        new cassandra_ColumnParent(
                        array(
                                'column_family' => $this->getName())),
                        $predicate,
                        $consistencyLevel);

                // otherwise by defined columns (slice query)
            } else {

                $predicate->column_names = $this->getColumnNames();

                $result = PandraCore::getCFSliceMulti(
                        $this->getKeySpace(),
                        array($keyID),
                        new cassandra_ColumnParent(
                        array(
                                'column_family' => $this->getName())),
                        $predicate,
                        $consistencyLevel);

                $result = $result[$keyID];
            }

            if ($result !== NULL) {
                $this->init();
                foreach ($result as $superColumn) {
                    $sc = $superColumn->super_column;
                    $newSuper = new PandraSuperColumn($this->typeConvert($sc->name, UUID::UUID_STR), NULL, NULL, $this, $this->getType());
                    if ($this->addSuper($newSuper)->populate($sc->columns, $autoCreate)) {
                        $this->setLoaded(TRUE);
                    } else {
                        $this->setLoaded(FALSE);
                        break;
                    }
                }

                if ($this->isLoaded()) $this->setKeyID($keyID);

            } else {
                $this->registerError(PandraCore::$lastError);
            }
        }
        return ($ok && $this->isLoaded());
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

        if (is_array($data) && count($data)) {

            foreach ($data as $idx => $colValue) {

                // Allow named SuperColumns to be populated into this CF
                if ($colValue instanceof PandraSuperColumn) {
                    if ($this->getAutoCreate($colAutoCreate) || array_key_exists($idx, $this->_columns)) {
                        $this->_columns[$idx] = $colValue;
                    }

                } elseif ($colValue instanceof cassandra_ColumnOrSuperColumn && !empty($colValue->super_column)) {
                    $columnName =  $this->typeConvert($colValue->super_column->name, UUID::UUID_STR);

                    if ($this->getAutoCreate($colAutoCreate) || array_key_exists($columnName, $this->_columns)) {
                        $this->addSuper(new PandraSuperColumn($columnName))->populate($colValue->super_column->columns);
                    }

                } else {
                    if ($this->getAutoCreate($colAutoCreate) || array_key_exists($idx, $this->_columns)) {
                        $this->addSuper(new PandraSuperColumn($idx, NULL, NULL, $this))->populate($colValue);
                    }
                }
            }
        } else {
            return FALSE;
        }

        return empty($this->errors);
    }
}
?>