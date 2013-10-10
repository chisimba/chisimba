<?php
/**
 * PandraContainerChild
 *
 * ContainerChildren are classes which are able to un/bind themselves to parent
 * containers.  If the ContainerChild is also ColumnPathable, it is loadable and
 * savable, but should dynamically inherit ColumnPathable attributes from its parent
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 * @abstract
 */
interface PandraContainerChild {

    /**
     * Unsets reference to this child in parent
     * @access public
     */
    public function detach();

    /**
     * Sets local parent reference to null
     * @access public
     * @param bool $localUnbind optional call local detach() method prior to nullifying
     */
    public function disown($localUnbind = TRUE);

    /**
     * Sets local parent reference
     * @access public
     * @param object $parent Container object
     * @param bool $bindToParent optional make parent aware of new column via addColumnObj
     */
    public function setParent(PandraColumnContainer $parent, $bindToParent = TRUE);

    /**
     * Parent accessor
     * @access public
     * @return PandraColumnContainer Parent container
     */
    public function getParent();
}
?>
