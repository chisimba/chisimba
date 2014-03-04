<?php
/**
 *
 *  MicroSites interface
 *
 *  A simple module to feed content in the form of json 
 *  to a static site. The data is stored in the database and requested
 *  using ajax methods. 
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
 * @category  Chisimba
 * @package   microsites
 * @author    Wesley Nitsckie
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Controller class for Chisimba for the module microsites
*
* @author Wesley Nitsckie
* @package microsites
*
*/
class microsites extends controller
{
    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;
    
    /**
    *
    * Intialiser for the twitter controller
    * @access public
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objOps = $this->getObject('ops');
        $this->objDBSites = $this->getObject('dbsites');
        $this->objDBContent = $this->getObject('dbcontent');
    }
    
    /**
     *
     * The standard dispatch method for the twitter module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'demo');
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }
    
    
    public function __json_getpagecontent(){
        echo json_encode(array('content' => $this->objOps->getPage($this->getParam('pageid'))));
    }
    
    public function getpagecontent(){
        echo $this->objOps->getPage($this->getParam('pageid'));
        exit(0);
    }
    
    public function __getpagecontent(){
        //$page = $this->objDBContent->getPage($this->getParam('pageid'));
        //echo $page['content'];
        // exit(0);
        
        // Specify domains from which requests are allowed
        header('Access-Control-Allow-Origin: *');

        // Specify which request methods are allowed
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

       /*
        * jQuery < 1.4.0 adds an X-Requested-With header which requires pre-flighting
        * requests. This involves an OPTIONS request before the actual GET/POST to 
        * make sure the client is allowed to send the additional headers.
        * We declare what additional headers the client can send here.
        */

       // Additional headers which may be sent along with the CORS request
       header('Access-Control-Allow-Headers: X-Requested-With');

       // Set the age to 1 day to improve speed/caching.
       header('Access-Control-Max-Age: 86400');

       // Exit early so the page isn't fully loaded for options requests
       if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
           exit();
       }

      // If raw post data, this could be from IE8 XDomainRequest
      // Only use this if you want to populate $_POST in all instances
      if (isset($HTTP_RAW_POST_DATA)) {
          $data = explode('&', $HTTP_RAW_POST_DATA);
          foreach ($data as $val) {
              if (!empty($val)) {
                  list($key, $value) = explode('=', $val);   
                  $_POST[$key] = urldecode($value);
              }
          }
      }

     $page = $this->objDBContent->getPage($this->getParam('pageid'));
     echo $page['content'];

//        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//            echo 'Your name is ' . htmlentities($_POST['name']);
//        }
        
    }
    
    public function __savepagecontent(){
        echo $this->objDBContent->savePage($this->getParam("pageid"), $this->getParam("content"));
        exit(0);
    
    }
    
    public function __json_authenticate(){
        echo "1";
        exit(0);
    }
    
    public function __updatePageContent(){
    
    }
    
    public function __addSite(){       
        $sitename = $this->getParam('sitename');
        echo $this->objDBSites->addSite(array('sitename' => $this->getParam('sitename'),
                                              'url' => $this->getParam('url')
                                              ));
    }
    
    public function __showAddSite(){
        return "addsite_tpl.php";
    }
    
    public function __showAddContent(){
        //get the list of pages      
        $this->setVar("pagesArr", $this->objDBContent->getSiteContent($this->getParam('siteid')));
        return "addcontent_tpl.php";
    }
    
    public function __saveaddpage(){
        $params = array();
        $params['site_id']  = $this->getParam("siteid");
        $params['content_title']  = $this->getParam("content_title");
        $params['content']  = $this->getParam("content");
        
        $this->objDBContent->add($params);
        return __home();
    }
    
    public function __json_getsitecontent()
    {
        $siteId = $this->getParam('siteid');
        echo json_encode(array('content' => $this->objDBContent->getSiteContent($siteId)));
        exit(0);
    
    }
    
    public function __home(){
    
        $this->setVar('sitesArr', $this->objDBSites->getSites());
        return 'main_tpl.php';
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
    private function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
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
    private function __actionError()
    {
        $str =  "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $action . "</h3>";
        echo $str;
    }
    
    /**
    *
    * This is a method to determine if the user has to
    * be logged in or not. Note that this is an example,
    * and if you use it view will be visible to non-logged in
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        return FALSE;
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
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
//            return "__actionError";
            return "__home";
        }
    }

}
