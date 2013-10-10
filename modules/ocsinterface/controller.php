<?php
    /**
     *
     *  PHP version 5
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
     * @package   webpresent
     * @author    Tohir Solomons, Derek Keats and later modifications by David Wafula
     *
     * @copyright 2008 Free Software Innnovation Unit
     * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
     * @link      http://avoir.uwc.ac.za
     * @see       References to other sections (if any)...
     */
if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end of security

class ocsinterface extends controller
{

    public  $objConfig;


    /**
     * Constructor
     */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objViewerUtils = $this->getObject('viewerutils');
        $this->objWashout = $this->getObject('washout', 'utilities');
    }
        /**
         * Method to override login for certain actions
         * @param <type> $action
         * @return <type>
         */
    public function requiresLogin($action)
    {
        $required = array('login');


        if (in_array($action, $required)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }



       /**
        * Standard Dispatch Function for Controller
        * @param <type> $action
        * @return <type>
        */
    public function dispatch($action)
    {

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
    function getMethod(& $action)
    {
        if ($this->validAction($action)) {
            return '__'.$action;
        } else {
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
    function validAction(& $action)
    {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

      /**
       * Method to show the Home Page of the Module
       */
    function __home()
    {
        return 'home_tpl.php';
    }

    function __viewtopic(){
        $id=$this->getParam('id');
        $this->setVarByRef('id',$id);
        return 'topic_tpl.php';
    }

        function __viewtopics(){
        $parentid=$this->getParam('parentid');
        $this->setVarByRef('parentid',$parentid);
        return 'topics_tpl.php';
    }

    function __viewarticle(){
        $title=$this->getParam('title');
        $this->setVarByRef('title',$title);
        return 'articles_tpl.php';
    }

    function __admin(){
        return "admin_tpl.php";
    }

    function __viewstory(){
        $storyid=$this->getParam('storyid');
        $this->setVarByRef('storyid',$storyid);
        return "story_tpl.php";
    }
    function __viewsections(){
        $data=
        "
[{
    topic:'ColumnTree Example',
    owner:'Admin',
    uiProvider:'col',
    cls:'master-task',
    iconCls:'task-folder',
    children:[
    {
        topic:'Abstract rendering in TreeNodeUI',
        owner:'admin',
        uiProvider:'col',
        leaf:true,
        iconCls:'task'
    },
   {
        topic:'Test and make sure it works',
        owner:'1 hour',
        uiProvider:'col',
        leaf:true,
        iconCls:'task'
    }
]

}]
";
        echo $data;
        die();
    }

}


?>