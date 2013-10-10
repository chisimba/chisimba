<?php
/**
 * PandraColumnFamily
 *
 * Column Container for Cassandra Column Families
 *
 * 'my columns' => {    // ColumnFamily
 *                   'column1',  // Column
 *                   'column2',  // Column
 *                   'column3'   // Column
 *                 } ... etc
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraColumnFamily extends PandraColumnContainer implements PandraColumnPathable {

    /**
     * Loads an entire columnfamily by keyid
     * @param string $keyID optional row key
     * @param bool $colAutoCreate create columns in the object instance which have not been defined
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
                // Clean slate
                $this->destroyColumns();
                $this->destroyErrors();
                $this->init();

                // Try populating
                $this->setLoaded($this->populate($result, $autoCreate));

                // If we're loaded, use a new key
                if ($this->isLoaded()) $this->setKeyID($keyID);
            } else {
                $this->registerError(PandraCore::$lastError);
            }
        }

        return ($ok && $this->isLoaded());
    }

    /**
     * Save this column family and any modified columns to Cassandra
     * @param cassandra_ColumnPath $columnPath
     * @param int $consistencyLevel Cassandra consistency level
     * @return bool save ok
     */
    public function save($consistencyLevel = NULL) {

        $ok = $this->pathOK();

        if ($ok) {
            if ($this->getDelete()) {

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
                $deletions = array();
                $selfKey = $this->getKeyID();
                $selfName = $this->getName();

                // build mutation
                $map = array($selfKey => array($selfName => array()));

                $ptr = &$map[$selfKey][$selfName];

                $modifiedColumns = $this->getModifiedColumns();

                // @todo - test delete mutate
                foreach ($modifiedColumns as &$cObj) {
                    $timestamp = $cObj->bindTime();

                    if ($cObj->isDeleted()) {
                        $deletions[] = $cObj->getName();
                    } else {
                        $sc = new cassandra_ColumnOrSuperColumn(array('column' => $cObj));
                        $ptr[] = new cassandra_Mutation(array('column_or_supercolumn' => $sc));
                    }
                }

                if (!empty($deletions)) {
                    $p = new PandraSlicePredicate(PandraSlicePredicate::TYPE_COLUMNS, $deletions);
                    $sd = new cassandra_Deletion(array('timestamp' => PandraCore::getTime(), 'predicate' => $p));
                    $ptr[] = new cassandra_Mutation(array('deletion' => $sd));
                }

                $ok = PandraCore::batchMutate($this->getKeySpace(), $map, $consistencyLevel);
            }
            if ($ok) $this->reset();
        }
        return $ok;
    }
}
?>