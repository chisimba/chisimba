<?php

/**
 * Triplestore controller class
 *
 * Class to control the Triplestore module
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
 * @category  chisimba
 * @package   triplestore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 16050 2009-12-26 01:34:10Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global unknown $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Triplestore controller class
 *
 * Class to control the Triplestore module
 *
 * @category  Chisimba
 * @package   triplestore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za/
 */
class triplestore extends controller {

    /**
     * Instance of the dbtriplestore class of the triplestore module.
     *
     * @access protected
     * @var    object
     */
    protected $objTriplestore;
    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     *
     * @access public
     */
    public function init() {
        $this->objTriplestore = $this->getObject('dbtriplestore', 'triplestore');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     * Standard dispatch method to handle the various possible actions.
     *
     * @access public
     */
    public function olddispatch() {
        $filters = array();
        $filterTypes = array('subject', 'predicate', 'object');

        foreach ($filterTypes as $filterType) {
            $filter = $this->getParam($filterType);
            if ($filter) {
                $filters[$filterType] = $filter;
            }
        }

        $nestedTriples = $this->objTriplestore->getNestedTriples($filters);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($nestedTriples);
    }

    public function dispatch($action)
    {
        // Convert the action into a method.
        $method = $this->__getMethod($action);
        // Return the template determined by the action.
        return $this->$method();
    }

    /**
     * Action to retrieve data from the triplestore in JSON format.
     *
     * @access private
     */
    private function __view() {
        $filters = array();
        $filterTypes = array('id', 'userid', 'subject', 'predicate', 'object');
        foreach ($filterTypes as $filterType) {
            $filter = $this->getParam($filterType);
            if ($filter) {
                $filters[$filterType] = $filter;
            }
        }
        $format = $this->getParam('format', 'flat');
        switch ($format) {
            case 'nested':
                $triples = $this->objTriplestore->getNestedTriples($filters);
                break;
            case 'flat':
                $triples = $this->objTriplestore->getTriples($filters);
                break;
            default:
                $triples = FALSE;
        }
        if (is_array($triples)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($triples);
        } else {
            header('HTTP/1.1 400 Bad Request');
        }
    }

    private function __getmytripples() {
        $pageSize = $this->getParam('pagesize', 15);
        $page = $this->getParam('page', 1);
        $targetUrl = $this->uri(array('action' => 'getpage',
                    'pagesize' => $pageSize), 'triplestore');
        // Create an instance of the ingrid grid class
        $objIh = & $this->getObject('ingridhelper', 'jqingrid');
        $objIh->loadIngrid();
        $objIh->loadCss();
        $objIh->loadReadyFunction($targetUrl);
        $objTripleUi = $this->getObject('tripleui', 'triplestore');
        $str = $objTripleUi->getMyTriples($page, $pageSize);
        $this->setvarByRef('str', $str);
        return 'dump_tpl.php';
    }

    private function __getpage() {
        $page = $this->getParam('page', 1);
        $pageSize = $this->getParam('pagesize', 15);
        $objTripleUi = $this->getObject('tripleui', 'triplestore');
        $str = $objTripleUi->getMyTriples($page, $pageSize);
        $this->setvarByRef('str', $str);
        $this->setPageTemplate('plain_tpl.php');
        return 'dump_tpl.php';
    }

    /**
     *
     * Method corresponding to the edit action. It sets the mode to
     * edit and returns the edit template.
     * @access private
     *
     */
    private function __edit()
    {
        $id = $this->getParam('id');

        if ($id) {
            $triples = $this->objTriplestore->getTriples(array('id' => $id));
            $this->setVarByRef('triple', $triples[0]);
        }

        return 'editform_tpl.php';
    }

    /**
     * Action to delete a triple.
     *
     * @access private
     */
    private function __delete()
    {
        $id = $this->getParam('id');
        $this->objTriplestore->delete($id);
        $this->nextAction('search');
    }

    /**
     *
     * Method corresponding to the asjson action.  It gets a specific
     * set of triples as json
     *
     * @access private
     *
     */
    private function __asjson() {
        $this->setvar('str', "JSON output will be here");
        return 'dump_tpl.php';
    }

    /**
     *
     * Method corresponding to the add action. It sets the mode to
     * add and returns the edit content template.
     * @access private
     *
     */
    private function __add() {
        $this->setvar('mode', 'add');
        return 'editform_tpl.php';
    }

    /**
     *
     * Method corresponding to the save action.
     *
     * @access private
     *
     */
    private function __save()
    {
        $id = $this->getParam('id');
        $subject = $this->getParam('subject');
        $predicate = $this->getParam('predicate');
        $object = $this->getParam('object');

        if ($id) {
            $this->objTriplestore->update($id, $subject, $predicate, $object);
        } else {
            $this->objTriplestore->insert($subject, $predicate, $object);
        }

        $this->nextAction('search');
    }

    /**
     *
     * Method corresponding to the save action.
     *
     * @access private
     *
     */
    private function __saveinline() {
        $id = $this->getParam('id', FALSE);
        if ($id) {
            $arId = explode('|', $id);
            $parameter = $arId[0];
            $key = $arId[1];
        }
        $value = $this->getParam('value', NULL);
        $subject = FALSE;
        if ($parameter == 'predicate') {
            $predicate = $value;
            $object = FALSE;
        }
        if ($parameter == 'object') {
            $predicate = FALSE;
            $object = $value;
        }
        $this->objTriplestore->update($key, $subject, $predicate, $object);
        $extjs['success'] = true;
        echo json_encode($extjs);
        exit(0);
    }

    /**
     *
     * Method to return an error when the action is not a valid
     * action method
     *
     * @access private
     * @return string The dump template populated with the error message
     *
     */
    private function __actionError() {
        $this->setVar('str', "<h3>"
                . $this->objLanguage->languageText("phrase_unrecognizedaction")
                . ": " . $this->getParam('action', NULL) . "</h3>");
        return 'dump_tpl.php';
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
    function __validAction(& $action) {
        if (method_exists($this, "__" . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return stromg the name of the method
     *
     */
    function __getMethod(& $action) {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
        }
    }

    /**
     * Overide the login object in the parent class.
     *
     * @access public
     * @param  string $action The name of the action
     * @return bool
     */
    public function requiresLogin() {
        $action = $this->getParam('action', 'NULL');
        switch ($action) {
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
    }

    /**
     * Default Action for triplestore module
     * @access private
     */
    private function __home()
    {
        $id = $this->getParam('id');
        $subject = $this->getParam('subject');
        $predicate = $this->getParam('predicate');
        $object = $this->getParam('object');

        $filters = array();
        if ($id) $filters['id'] = $id;
        if ($subject) $filters['subject'] = $subject;
        if ($predicate) $filters['predicate'] = $predicate;
        if ($object) $filters['object'] = $object;

        $columns = array('id', 'subject', 'predicate', 'object');

        $this->triples = $this->objTriplestore->getTriples($filters, $columns);
        $this->setVarByRef('triples', $triples);

        return 'triplestore_tpl.php';
    }

    /**
     * Method to upload the csv or xml files to the triplestore
     *
     * @access private
     * @return json formatted string
     */
    private function __upload() {

        $filetype = $this->getParam('filetype');
        $this->objUpload = $this->getObject('upload', 'filemanager');
        $this->objUpload->setUploadFolder("users/" . $this->objUser->userId());
        // Upload File
        $results = $this->objUpload->uploadFiles();
        $filename = $_FILES['path1']['name'];
        $fileid = $results[$filename]['fileid'];
        error_log(var_export($results, true));
        // Use it
        $fileurl = $results[$filename]['fullpath'];

        if ($filetype == 'csv') {
            $delimiter = $this->getParam('delimiter', ',');
            $this->objTriplestore->importCSV($fileurl, $subject, $delimiter);
        } else {
            $this->objTriplestore->importXML($fileurl, $subject);
        }

        // Delete it
        $this->objFiles = $this->getObject('dbfile', 'filemanager');
        $this->objFiles->deleteFile($fileid, False);

        // Respond to the client
        $extjs['success'] = true;
        //header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($extjs);
        exit(0);
    }

    /**
     * Method to get the tree array with the following format
     * [{     text: 'A leaf Node'
     * 	},{
     * 	    text: 'A folder Node',
     * 	    children: [{
     * 	        text: 'A child Node'
     * 	     }]
     * }]
     * @access private
     * @return json formatted string
     */
    private function __gettree() {
        $filters = array();
        $triples = $this->objTriplestore->getTriples($filters, null, 'subject');
        $allarr = array();
        if (count($triples) > 0) {
            foreach ($triples as $triple) {
                $arr = array();
                $arr['id'] = 'subject|' . $triple['id'];
                $arr['text'] = $triple['subject'];
                $arr['iconCls'] = 'add';

                $filters = array();
                $filters['subject'] = $triple['subject'];
                $predicates = $this->objTriplestore->getTriples($filters, null, 'predicate');

                $allpre = array();
                if (count($predicates) > 0) {
                    foreach ($predicates as $predicate) {
                        $prearr = array();
                        $prearr['id'] = 'predicate|' . $predicate['id'];
                        $prearr['text'] = $predicate['predicate'];
                        $prearr['leaf'] = true;
                        $prearr['iconCls'] = 'add';
                        $allpre[] = $prearr;
                    }
                    $arr['children'] = $allpre;
                }
                $allarr[] = $arr;
            }
        }
        //header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($allarr);
	exit(0);
    }

    /**
     * Method to get the triples of the selected subject or predicate
     *
     * @access private
     * @return json formatted string
     */
    private function __getdata() {

        $id = $this->getParam('node');
        $ids = explode("|", $id);
        $id = $ids[1];
        $data = $ids[0];
        $i = $this->objTriplestore->getTriples(array('id' => $id));
        $arr = array();
        if ($data == 'predicate') {
            $arr['subject'] = $i[0]['subject'];
            $arr['predicate'] = $i[0]['predicate'];
        } else if ($data == 'subject') {
            $arr['subject'] = $i[0]['subject'];
        }

        $filters = array();
        $filterTypes = array('id', 'userid', 'subject', 'predicate', 'object');
        foreach ($filterTypes as $filterType) {
            $filter = $arr[$filterType];
            if ($filter) {
                $filters[$filterType] = $filter;
            }
        }
        $triples = $this->objTriplestore->getTriples($filters);
        $allarr = array();
        if (count($triples) > 0) {
            foreach ($triples as $triple) {
                $arr = array();
                $arr['id'] = $triple['id'];
                $arr['subject'] = $triple['subject'];
                $arr['predicate'] = $triple['predicate'];
                $arr['object'] = $triple['object'];
                $allarr[] = $arr;
            }
        }
        $arr = array();
        $arr['data'] = $allarr;
        //header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($arr);
	exit(0);
    }

    /**
     * Method to remove a seleted triple
     *
     * @access private
     * @return json formatted string
     */
    private function __removeTriples() {
        $id = $this->getParam('id');
        $ids = explode("|", $id);
        foreach ($ids as $id) {
            $i = $this->objTriplestore->getTriples(array('id' => $id));
            $arr = array();
            if ($i) {
                $result = $this->objTriplestore->delete($id);
                $result != null ? $arr['success'] = true : $arr['success'] = false;
            } else {
                $arr['success'] = false;
            }
        }
        //header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($arr);
        exit(0);
    }

    /**
     * Method to get a single triple
     *
     * @access private
     * @return json formatted string
     */
    private function __getsingletriples() {

        $id = $this->getParam('id');
        $filters = array();
        $filters['id'] = $id;
        $data = $this->objTriplestore->getTriples($filters);
        $arr['success'] = true;
        $arr['data'] = $data[0];
        //header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($arr);
        exit(0);
    }

    /**
     * Method to get a single triple
     *
     * @access private
     * @return json formatted string
     */
    private function __getformdata(){
        $id = $this->getParam('id');
        $ids = explode("|", $id);
	$id2 = $ids[1];
        $field = $ids[0];
        $data = $this->objTriplestore->getTriples(array('id' => $id2));
	$arr = array();
	$arr['success'] = true;
        $arr['data']['subject'] = $data[0]['subject'];
	if($field == 'predicate'){
		$arr['data']['predicate'] = $data[0]['predicate'];
	}
	echo json_encode($arr);
        exit(0);
    }
}
?>
