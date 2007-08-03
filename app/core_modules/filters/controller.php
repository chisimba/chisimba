<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   filters
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/* -------------------- stories class extends controller ----------------*/
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
/**
* Blog class for KEWL.NextGen
* 
* @author Derek Keats
*         
*/
class filters extends controller {

    /**
    * Intialiser for the filter object
    */
    function init()
    { 
        // Get the multimedia parser
        $this->objParse4Mmedia = &$this->getObject('parse4mmedia'); 
        // Get the multimedia parser
        $this->objParse4KngText = &$this->getObject('parse4kngtext'); 
        // Get the smiley parser
        $this->objParse4smileys = &$this->getObject('parse4smileys'); 
    } 

    /**
    * Run test for the functionality of the parsers
    */
    function dispatch($action = null)
    {
        $strCode="This is outside of the code block at the\n
          top of the code block. THe next bit is in a code block\n
          [code]
          \$a='Now is the time';
          \$b=' for all good dudes';
          \$c=' to come to the aid of ';
          \$d=' the party goers. ';
          \$d=str_replace(\$a, \$b, \$c)
          echo \$a . \$b . \$c \$d;
          str_replace(\$a, \$b, \$c);
          [/code]\n
          This is out side of the code block.";
        //MP3 Test
        $strTest="This is a test. I am merely testing.
          There is nothing happening here. It is only 
          a test. How about some <a href=\"http://localhost/tf/test.mp3\">
          music</a> inserted here.";
        //WAV test
        $strTest1="This is a test. I am merely testing.
          There is nothing happening here. It is only 
          a test. How about some <a href=\"http://localhost/tf/test.mov\">
          music</a> inserted here.";
        //AVI test
        $strTest2="This is a test. I am merely testing.
          There is nothing happening here. It is only 
          a test. How about some <a href=\"http://localhost/tf/test.avi\">
          video</a> inserted here.";
        //MPG test
        $strTest3="This is a test. I am merely testing.
          There is nothing happening here. It is only 
          a test. How about some <a href=\"http://localhost/tf/test.mpg\">
          video</a> inserted here.";
        $strTest4="This is a test. I am merely testing.
          There is nothing happening here. It is only 
          a test. How about some <a href=\"http://localhost/tf/test.wmv\">
          video</a> inserted here.";
        $strTest5="This is a test. I am merely testing.
          There is nothing happening here. It is only 
          a test. How about some <a href=\"http://localhost/tf/test.swf\">
          flash movie</a> inserted here.";
        $smtest="This is a smiley here :) dont you think :) and another 
          smile :-) and wink ;). This one is based on [] tags [smile].
          This on is another wink ;-) and a [] one for wink [wink].
          This is a mixed one :-/ and [] [mixed]. It does not yet exclude smiles
          that are anchor tags, as <a href='a.b.c'>[smile]</a>
          This is a test of them all<br />
          [biggrin][wink][mixed][thoughtful][tongueout][cool][approve][wideeyes]
          [clown][sad][shy][blush][kiss][surprise][blackeye][angry][dead][sleepy][evil]";
          
        $strTest6 = $strTest . $strTest1 . $strTest2 . $strTest3 . $strTest4 . $strTest5;
        $this->setvar('strOut', $this->objParse4KngText->parseCode($strCode));
        //$this->setvar('strOut', $this->objParse4smileys->parseSmiley($smtest));
        //$this->setvar('strOut', $this->objParse4Mmedia->parseMp3($strTest));
        //$this->setvar('strOut', $this->objParse4Mmedia->parseAll($strTest6));
        return 'utest_tpl.php';
    
    } 
} 

?>