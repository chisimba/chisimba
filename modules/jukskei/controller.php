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
if(!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end of security

class jukskei extends controller {

    public  $objConfig;

    public  $realtimeManager;

    public  $presentManager;
    /**
     * Constructor
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objViewerUtils = $this->getObject('viewerutils');
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->storyparser=$this->getObject('storyparser');
        $this->dbtopics=$this->getObject('dbtopics');
        $this->dbgroups=$this->getObject('dbgroups');
        $this->articles=$this->getObject('dbarticles');
    }
    /**
     * Method to override login for certain actions
     * @param <type> $action
     * @return <type>
     */
    public function requiresLogin($action) {
        $required = array('login','storyadmin');


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
    function validAction(& $action) {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    function __home() {
        return 'home_tpl.php';
    }

    function __viewtopic() {
        $id=$this->getParam('id');
        $this->setVarByRef('id',$id);
        return 'topic_tpl.php';
    }

    function __viewtopics() {
        $parentid=$this->getParam('parentid');
        $this->setVarByRef('parentid',$parentid);
        return 'topics_tpl.php';
    }



    function __admin() {
        return "admin_tpl.php";
    }

    function __viewstory() {
        $storyid=$this->getParam('storyid');
        $this->setVarByRef('storyid',$storyid);
        $this->setVar('pageSuppressToolbar', TRUE);
        return "story_tpl.php";
    }
    function __viewarticle() {
        $storyid=$this->getParam('storyid');
        $articleid=$this->getParam('articleid');
        $this->setVarByRef('storyid',$storyid);
        $this->setVarByRef('articleid',$articleid);
        $this->setVar('pageSuppressToolbar', TRUE);
        return "article_tpl.php";
    }

    function __storyadmin() {
        return "storylist_tpl.php";
    }

    function __addtopic() {
        $this->setVarByRef('mode','add');
        return "addedittopic_tpl.php";

    }
    function __addarticle() {

        $topicid=$this->getParam('topicid');
        $this->setVarByRef('mode','add');
        $this->setVarByRef('topicid',$topicid);
        return "addeditarticle_tpl.php";

    }
    function __savetopic() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $active=$this->getParam('activefield');
        $topicid=$this->dbtopics->saveTopic($title,$content,$active);
        $this->dbgroups->adduser($topicid);
        return $this->nextAction('storyadmin');
    }
    function __savearticle() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $topicid=$this->getParam('topicid');
        $this->articles->saveArticle($title,$content,$topicid);
        return $this->nextAction('viewtopicarticles',array('topicid'=>$topicid));
    }
    function __updatearticle() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $articleid=$this->getParam('articleid');
        $topicid=$this->getParam('topicid');
        $this->articles->updateArticle($title,$content,$articleid);
        return $this->nextAction('viewtopicarticles',array('topicid'=>$topicid));
    }

    function __updateTopic() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $topicid=$this->getParam('topicid');
         $active=$this->getParam('activefield');
        $topicid=$this->dbtopics->updateTopic($title,$content,$topicid,$active);
        return $this->nextAction('storyadmin');
    }
    function __addmembertotopic() {
        $topicid=$this->getParam('topicid');
        $userid=$this->getParam('userid');
        $this->dbgroups->adduser($topicid,$userid);
        return $this->nextAction('topicmembers',array('topicid'=>$topicid));
    }
    function __topicmembers() {
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('topicid',$topicid);
        return 'topicmembers_tpl.php';
    }
    function __deletemember() {
        $topicid=$this->getParam('topicid');
        $userid=$this->getParam('userid');
        $this->dbgroups->deleteMember($userid);
        $this->setVarByRef('topicid',$topicid);
        return 'topicmembers_tpl.php';
    }
    function __editTopic() {
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('mode','edit');
        $this->setVarByRef('topicid',$topicid);
        return "addedittopic_tpl.php";
    }

    function __deletetopic() {
        $topicid=$this->getParam('topicid');
        $this->dbtopics->deleteTopic($topicid);
        return $this->nextAction('storyadmin');
    }
    function __deletearticle() {
        $topicid=$this->getParam('articleid');
        $this->articles->deleteArticle($topicid);
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('topicid',$topicid);
        return "articlelist_tpl.php";
    }
    function __editarticle() {
        $articleid=$this->getParam('articleid');
        $this->setVarByRef('mode','edit');
        $topicid=$this->getParam('topicid');

        $this->setVarByRef('topicid',$topicid);
        $this->setVarByRef('articleid',$articleid);
        return "addeditarticle_tpl.php";
    }
    function __viewtopicarticles() {
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('topicid',$topicid);
        return "articlelist_tpl.php";
    }

 
}
?>