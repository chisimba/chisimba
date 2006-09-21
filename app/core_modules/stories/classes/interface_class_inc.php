<?php
/* -------------------- interface class for stories module ----------------*/

/**
*
* Class for providing interface elements to the stories module
*
* @author Derek Keats
*
*/
class storyinterface extends object {

    var $objUser;
    var $objLanguage;
    var $objDbStories;
    var $objH;
    var $objParse;

    /**
    *
    * Constructor method to define the table
    *
    */
    function init()
    {
        parent::init('tbl_stories');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objDbStories = & $this->getObject('dbstories');
        $this->objH =& $this->getObject('htmlheading', 'htmlelements');
        //Get the smiley parser
        $this->objParse = &$this->getObject('parse4display', 'strings');
    }

    /**
    * Method to display data ,,-- needs edit for using array
    *
    * @todo -cstories Rewrite the table output to use the table class
    *
    */
    function displayData($rs, $module, $allowAdmin = false, $key = null)
    {
        // Start with no keyValue for the search key
        $keyValue = null;
        // Initialize the output string to NULL
        $str = null;
        // Duh..make a table tag!
        $tableStart = "<table width=\"90%\">\n";
        // Duh..make an end table tag!
        $tableEnd = "</table>\n";
        // Build the output string with the table headings, with sort by heading enabled.
        $str = null;
        $str .= $tableStart;

        /*
        $paramArray = array('order' => 'id');
        $str .= "<tr><td class=\"heading\"><a href=\""
         . $this->uri($paramArray, "stories")
         . "\">id</a></td>\n";*/

        $paramArray = array('order' => 'category');
        $str .= "<td class=\"heading\"><a href=\""
         . $this->uri($paramArray, "stories")
         . "\">" . $this->objLanguage->languageText("word_category",'stories')
         . "</a></td>\n";

        $paramArray = array('order' => 'language');
        $str .= "<td class=\"heading\"><a href=\""
         . $this->uri($paramArray, "stories")
         . "\">" . $this->objLanguage->languageText('word_language','security')
         . "</a></td>\n";

        $paramArray = array('order' => 'title');
        $str .= "<td class=\"heading\"><a href=\""
         . $this->uri($paramArray, "stories")
         . "\">" . $this->objLanguage->languageText("word_title",'useradmin')
         . "</a></td>\n";



        $paramArray = array('order' => 'expirationDate');
        $str .= "<td class=\"heading\"><a href=\""
         . $this->uri($paramArray, "stories")
         . "\">" . $this->objLanguage->languageText('phrase_expirationdate','security')
         . "</a></td>\n";

        $paramArray = array('order' => 'isActive');
        $str .= "<td class=\"heading\"><a href=\""
         . $this->uri($paramArray, "stories")
         . "\">" . $this->objLanguage->languageText("phrase_isactive",'stories')
         . "</a></td>\n";

        $objGetIcon = $this->newObject('geticon', 'htmlelements');
        if ($allowAdmin) {
            $paramArray = array('action' => 'add');
            $str .= "<td class=\"heading\" align=\"right\" width=\"30\">"
             . $objGetIcon->getAddIcon($this->uri($paramArray, "stories"))
             . "</td>\n";
        }
        $str .= "</tr>\n";
        // Initialize the row to 0, the first row
        $rowcount = 0;
          foreach ($rs as $line) {
              $oddOrEven = ($rowcount == 0) ? "odd" : "even";
              $str .= "<tr>";
            $id = $line['id'];
            $category = $line['category'];
            $language = $line['language'];
            $title = $line['title'];
            $expirydate = $line['expirationDate'];
            $isActive = $line['expirationDate'];

            //$str .= "<td class=\"" . $oddOrEven . "\">" . $id . "</td>\n";
            $str .= "<td class=\"" . $oddOrEven . "\">" . $category . "</td>\n"
              . "<td class=\"" . $oddOrEven . "\">" . $language . "</td>\n"
              . "<td class=\"" . $oddOrEven . "\">" . $title . "</td>\n";



            //Instantiate the classe for checking expiration
            $objExp = & $this->getObject('expiration', 'datetime');

            if ( $objExp->hasExpired($expirydate) ) {
                $str .= '<td class="' . $oddOrEven
                . '"><span class="error"><strong>' . $expirydate
                . '<strong></span> '.$objExp->getExpiredIcon().'</td>';
            } else {
                $str .= "<td class=\"" . $oddOrEven . "\">"
                . $expirydate . "</td>\n";
            }

            // Active / InActive?
            $objGetIcon = $this->newObject('geticon', 'htmlelements');
            if ($line['isActive'] == 1) {
                $objGetIcon->setIcon('online');
            } else {
                $objGetIcon->setIcon('offline');
            }
            $str .= "<td class=\"" . $oddOrEven . "\">" . $objGetIcon->show() . "</td>\n";

            if ($allowAdmin) {
                // Put the logic here
                $str .= "<td class=\"" . $oddOrEven . "\"><nobr>";
                $editArray = array('action' => 'edit',
                    'id' => $id);
                $deleteArray = array('action' => 'delete',
                    'id' => $id);
                $str .= $objGetIcon->getEditIcon($this->uri($editArray, "stories"));
                $str .= " ".$objGetIcon->getDeleteIcon($this->uri($deleteArray, "stories"));
                $str .= "</nobr></td>\n";
            }
            // Set rowcount for bitwise determination of odd or even
            $rowcount = ($rowcount == 0) ? 1 : 0;

            $str .= "</tr>\n\n\n";

        }
        $str .= $tableEnd;
        return $str;
    }
}  #end of class
?>