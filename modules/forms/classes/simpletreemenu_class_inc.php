<?php

/* -------------------- forms tree class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check
/**
* This object is a wrapper class for building a jQuery Simple Tree Menu populated with
* the available objForms
*
* @package forms
* @category cmsadmin
* @copyright 2008, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Charl Mert
* @example :
*/

class simpletreemenu extends object
{

        /**
        * The forms  object
        *
        * @access private
        * @var object
        */
        protected $objForms;

        /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $objUser;

        /**
         * Constructor
         */
        public function init()
        {
            try {
                $this->objForms = & $this->newObject('dbforms', 'forms');
                $this->objUser = & $this->newObject('user', 'security');
                $this->objLanguage = & $this->newObject('language', 'language');

            } catch (Exception $e) {
                throw customException($e->getMessage());
                exit();
            }

        }

        /**
        * Method to return back the tree code
        * @param string $currentNode The currently selected node, which should remain open
           * @param bool $admin Select whether admin user or not
        * @return string
        * @access public
        */
        public function show($currentNode)
        {
            $html = $this->showTree($currentNode);
            return $html;
        }

       /**
        * Method to show the menu tree
        * @param string $currentNode The currently selected node, which should remain open
        * @return string
        * @author Charl Mert
        * @access public
        */
        public function showTree($currentNode)
        {
            //check if there are any root nodes
            
            $html = "<div id=\"sectionstreemenu\" class=\"sectionstreemenu\">\n
                        <ul class=\"simpleTree\">\n";
            $html .= "<li class='root'><span><a id='dir_root' href='?module=cms'> Forms </a></span>
                        <ul>";

            $html .= $this->buildTree($currentNode);
        
            $html .= '</ul></li></ul></div><!-- end: simpletree tree div -->';

            return $html;
        }


        /**
         * Method to build the tree
         * @param string $currentNode The currently selected node, which should remain open
         * @return string
         * @access public
         */
        public function buildTree($currentNodeId)
        {
            //gets all the child nodes of id
            $nodes = $this->objForms->getForms();

            $html = '';
            if (!empty($nodes)) {
                foreach($nodes as $node) {
                        $nodeUri = $this->uri(array('action' => 'addform', 'id' => $node['id']), 'forms');
                        $text = $node['title'];
                        $link = '<a href="'.$nodeUri.'">'.$text.'</a>'."\n";
                        $html .= '<li><span>'.$link."</span>\n";

                        $html .= '</li>'."\n";

                        //echo "SECTION NODE HERE : "; 
                        //var_dump($node);

                }
            }
            return $html;
        }


}

?>