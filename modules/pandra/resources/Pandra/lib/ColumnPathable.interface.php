<?php
/**
 * PandraColumnPathable
 *
 * ColumnPathable children are expected to be loadable and savable to
 * Cassandra independently, which includes standard load and save methods and
 * state management such as marking themselves for deletion, or whether their
 * structure has been modified
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 * @abstract
 */
interface PandraColumnPathable {

    /**
     * Loads a container column path by keyid
     * @param string $keyID optional row key
     * @param int $consistencyLevel cassandra consistency level
     * @return bool loaded OK
     */
    public function load($keyID = NULL, $consistencyLevel = NULL);

    /**
     * Save this container column path and any modified columns to Cassandra
     * @param cassandra_ColumnPath $columnPath
     * @param int $consistencyLevel Cassandra consistency level
     * @return bool save ok
     */
    public function save($consistencyLevel = NULL);

    /**
     * Local reset method, handling delete/modified flags
     */
    public function reset();

    /**
     * Delete method
     */
    public function delete();

    /**
     * Modified accessor
     * @return bool child is marked as modified
     */
    public function isModified();

    /**
     * Deleted accessor
     * @return bool child is marked for deletion
     */
    public function isDeleted();

    /**
     * Child name accessor
     * @return string child name
     */
    public function getName();

    /**
     * keyID mutator
     * @param string $keyID row key id
     * @param boolean $validate (optional) attempt to validate the key
     */
    public function setKeyID($keyID, $validate = TRUE);

    /**
     * keyID accessor if local member has not been set, attempts to return the set parents attribute instead
     * @return string
     */
    public function getKeyID();

    /**
     * key type def mutator. Sets a validating type definition for key
     * @param array $typeDefs PandraValidator primitive or complex types
     */
    public function setKeyValidator($typeDefs);

    /**
     * key validator accessor
     * @return array list of registered PandraValidator primitive or complex types
     */
    public function getKeyValidator();

    /**
     * keySpace mutator
     * @param string $keySpace keyspace name
     */
    public function setKeySpace($keySpace);

    /**
     * keySpace accessor if local member has not been set, attempts to return the set parents attribute instead
     * @return string
     */
    public function getKeySpace();

    /**
     * Checks we have a bare minimum attributes on the entity, to perform a columnpath search
     * @param string $keyID optional overriding row key
     * @return bool columnpath looks ok
     */
    public function pathOK($keyID = NULL);

    /**
     * Creates an error entry in this column and propogate to parent
     * @param string $errorStr error string
     */
    public function registerError($errorStr);

    /**
     * Grabs all errors for the column instance
     * @return array all errors
     */
    public function getErrors();

    /**
     * Grabs the last logged error
     * @return string last error message
     */
    public function getLastError();

    /**
     * Destroys all errors in this container, and its children
     * @param bool $childPropogate optional propogate destroy to children (default TRUE)
     */
    public function destroyErrors();

}
?>