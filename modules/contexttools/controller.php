<?php
/**
 * This class is used by ckeditor to display links to plugins, context
 * content, filters etc such that they can be embbeded inside the editor as easily
 * as possible
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @author David Wafula
 *
 *
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class contexttools extends controller {

    function init() {
        $this->objContext = $this->getObject ( 'dbcontext','context' );
        $this->objUser=$this->getObject('user','security');
        $this->objConfig = $this->getObject ( 'altconfig', 'config' );
        $this->objUtils=$this->getObject('contexttoolsutils');
        $this->dbstories=$this->getObject('dbstories');
    }

    public function dispatch($action) {
    /*
    * Convert the action into a method (alternative to
    * using case selections)
    */
        $method = $this->getMethod($action);
    /*
    * Return the template determined by the method resulting
    * from action
    */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__'.$action;
        }
        else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
     * the default home template. it show all the functionalities, grouped
     * by tabs
     * @return <type>
     */
    function __home() {

        $instancename=$this->getParam('instancename');
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $this->setVarByRef('instancename',$instancename);
        return "home_tpl.php";

    }
    /**
     * this function returns a list of context that the user belongs to
     * in json format
     */
    protected function __jsonusercontexts() {
        $ctstart = $this->getParam('start');
        if (empty($ctstart)) {
            $ctstart = 0;
        }
        $ctlimit = $this->getParam('limit');
        if (empty($ctlimit)) {
            $ctlimit = 500;
        }
        $objUserContext = $this->getObject('usercontext', 'context');
        $objDisplayContext = $this->getObject ( 'displaycontext', 'context' );
        $userContexts = $objUserContext->jsonUserCourses($this->objUser->userId(), $ctstart, $ctlimit);
        if ( count ( $userContexts ) > 0 ) {
            echo $objDisplayContext->jsonContextOutput( $userContexts );
            exit(0);
        }
    }

    /**
     * this function reads filters.xml file and returns the results in json
     * format
     * @return <type>
     */
    public function __getfilters() {
        return $this->objUtils->readFiltersXml();

    }

    function __getfilterparams() {
        $filtername=$this->getParam('filtername');
        return $this->objUtils->readFilterParams($filtername);
    }

    function __getStories() {
        return $this->dbstories->getStories();
    }
    /**
     * this gets input fields for a filter
     * @return <type>
     */
    function __getfilterinput() {
        $filtername=$this->getParam('filtername');
        return $this->objUtils->readFilterInput($filtername);
    }
}
?>
