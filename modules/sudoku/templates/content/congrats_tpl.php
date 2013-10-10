<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package sudoku
*/

/**
* Congratulation template for the sudoku puzzle
* Author Kevin Cyster
* */

    $this -> setLayoutTemplate('layout_tpl.php');

// set up html elements
    $this -> loadclass('htmlheading', 'htmlelements');
    $this -> loadclass('link', 'htmlelements');

// set up language items
    $congratsLabel = $this -> objLanguage -> languageText('mod_sudoku_congrats', 'sudoku');
    $sorryLabel = $this -> objLanguage -> languageText('mod_sudoku_sorry', 'sudoku');
    $returnLabel = $this -> objLanguage -> languageText('mod_sudoku_return', 'sudoku');
    $retryLabel = $this -> objLanguage -> languageText('mod_sudoku_retry', 'sudoku');

// set up heading
    $objHeader = &new htmlHeading();
    if($solved == '1'){
        $objHeader -> str = $congratsLabel;
    }else{
        $objHeader -> str = $sorryLabel;
    }
    $objHeader -> type = 1;
    $str = $objHeader -> show();
    echo $str;

// set up retry and return links
    $objLink = &new link($this -> uri(array('action' => ''),'sudoku'));
    $objLink -> link = $returnLabel;
    $returnLink = $objLink -> show();

    if($solved != '1'){
        $objLink = &new link($this -> uri(array('action' => 'solve', 'id' => $id, 'how' => 'S', 'data' => $data, 'timer' => $timer),'sudoku'));
        $objLink -> link = $retryLabel;
        $retryLink = $objLink -> show();
        echo "<br />" . $retryLink . " / " . $returnLink;
    }else{
        echo "<br />" . $returnLink;
    }
?>