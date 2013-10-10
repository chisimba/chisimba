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
* Default template for the sudoku puzzle
* Author Kevin Cyster
* */

    $this -> setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objIcon =& $this -> newObject('geticon', 'htmlelements');
    $this -> loadclass('htmlheading', 'htmlelements');
    $this -> loadclass('htmltable', 'htmlelements');
    $this -> loadclass('link', 'htmlelements');
    $this -> loadclass('dropdown', 'htmlelements');
    $this -> loadclass('button', 'htmlelements');
    $this -> loadclass('form', 'htmlelements');

// set up language items
    $addLabel = $this -> objLanguage -> languageText('mod_sudoku_add', 'sudoku');
    $deleteLabel = $this -> objLanguage -> languageText('mod_sudoku_delete', 'sudoku');
    $emptyLabel = $this -> objLanguage -> languageText('mod_sudoku_empty', 'sudoku');
    $deleteConfirm = $this -> objLanguage -> languageText('mod_sudoku_deleteconfirm', 'sudoku');
    $exitLabel = $this -> objLanguage -> languageText('mod_sudoku_exit', 'sudoku');
    $levelLabel = $this -> objLanguage -> languageText('mod_sudoku_level', 'sudoku');
    $easyLabel = $this -> objLanguage -> languageText('mod_sudoku_easy', 'sudoku');
    $hardLabel = $this -> objLanguage -> languageText('mod_sudoku_hard', 'sudoku');
    $difficultLabel = $this -> objLanguage -> languageText('mod_sudoku_difficult', 'sudoku');
    $giantLabel = $this -> objLanguage -> languageText('mod_sudoku_giant', 'sudoku');
    $goLabel = $this -> objLanguage -> languageText('mod_sudoku_go', 'sudoku');
    $openLabel = $this -> objLanguage -> languageText('mod_sudoku_open', 'sudoku');
    $loadLabel = $this -> objLanguage -> languageText('mod_sudoku_load', 'sudoku');
    $errMsg_1 = $this -> objLanguage -> languageText('mod_sudoku_err_required', 'sudoku');

// set up heading
    $heading = $this -> objLanguage -> code2Txt('mod_sudoku_heading', 'sudoku', array('user' => $this -> objUser -> fullName($this -> objUser -> userId())));
    $objHeader = &new htmlHeading();
    $objHeader -> str = $heading;
    $objHeader -> type = 1;
    $str = $objHeader -> show();
    echo $str;

// set up table heading
    $objHeader = &new htmlHeading();
    $objHeader -> str = $addLabel;
    $objHeader -> type = 3;
    $levelHeading = $objHeader -> show();

// set up form elements
    $objDrop = &new dropdown('difficulty');
    $objDrop -> addOption(NULL, $levelLabel);
    $objDrop -> addOption(1, $easyLabel);
    $objDrop -> addOption(2, $hardLabel);
    $objDrop -> addOption(3, $difficultLabel);
    $objDrop -> addOption(4, $giantLabel);
    $objDrop -> setSelected(NULL);
    $levelDrop = $objDrop -> show();

// set up table
    $objTable = &new htmltable();
    $objTable -> cellspacing='2';
    $objTable -> cellpadding='2';

    $objTable -> startRow();
    $objTable -> addCell($levelHeading, '', '', '', '', '');
    $objTable -> endRow();
    $objTable -> startRow();
    $objTable -> addCell($levelDrop, '', '', '', '', '');
    $objTable -> endRow();

    $levelTable = $objTable -> show();

// set up submit button
    $objButton = &new button('go', $goLabel);
    $objButton -> setToSubmit();
    $goButton = $objButton -> show();

// Set up form
    $objForm = &new form('goForm', $this -> uri(array('action' => 'add')));
    $objForm -> addToForm($levelTable . " " . $goButton);
    $str = $objForm -> show();
    echo $str;

// set up table
    $objTable = &new htmltable();
    $objTable -> cellspacing='2';
    $objTable -> cellpadding='2';

// populate table
    // without puzzles
    if(empty($arrPuzzleList)){
        $objTable -> startRow();
        $objTable -> addCell($emptyLabel, '', '', '', 'noRecordsMessage', '');
        $objTable -> endRow();
    }else{
        // with puzzles
        foreach($arrPuzzleList as $key => $puzzle){
            // set up data
            $id = $puzzle['id'];
            $difficulty = $puzzle['difficulty'];
            if($difficulty == 1){
                $level = $easyLabel;
            }elseif($difficulty == 2){
                $level = $hardLabel;
            }elseif($difficulty == 3){
                $level = $difficultLabel;
            }else{
                $level = $giantLabel;
            }
            $saved = $puzzle['saved'];
            $dateSaved = $puzzle['date_saved'];
            $dateCreated = $puzzle['date_created'];
            $solved = $puzzle['solved'];
            $dateSolved = $puzzle['date_solved'];
            $time = $puzzle['time_taken'];

            // set up label and icons
            if(empty($saved)){
                // set up open icon
                $array = array('level' => "<b>" . $level . "</b>", 'created' => "<b>" . 'created' . "</b>", 'date' => "<b>" . $dateCreated . "</b>");
                $puzzleLabel = $this -> objLanguage -> code2Txt('mod_sudoku_puzzle', 'sudoku', $array);
                $objIcon -> title = $openLabel;
                $puzzleIcon = $objIcon -> getLinkedIcon($this -> uri(array('action' => 'solve', 'id' => $id, 'how' => 'R')), 'sudokuopen');
            }else{
                // set up load icon
                $array = array('level' => "<b>" . $level . "</b>", 'created' => "<b>" . 'saved' . "</b>", 'date' => "<b>" . $dateSaved . "</b>");
                $puzzleLabel = $this -> objLanguage -> code2Txt('mod_sudoku_puzzle', 'sudoku', $array);
                $objIcon -> title = $loadLabel;
                $puzzleIcon = $objIcon -> getLinkedIcon($this -> uri(array('action' => 'solve', 'id' => $id, 'how' => 'L')), 'sudokuload');
            }
            // set up delete icon
            $objIcon -> title = $deleteLabel;
            $objIcon -> setIcon('sudokudelete');
            $icon = $objIcon -> show();
            $deleteArray = array('action' => 'delete', 'id' => $id);
            $location = $this -> uri($deleteArray, 'sudoku');
            $this -> objUtil -> setConfirm($icon, $location, $deleteConfirm);
            $deleteIcon = $this -> objUtil -> show();

            // set up table
            $objTable -> startRow();
            $objTable -> addCell("<hr />", '', '', '', '', '');
            $objTable -> endRow();
            $objTable -> startRow();
            $objTable -> addCell($puzzleIcon . " " . $deleteIcon . " " . $puzzleLabel, '', '', '', '', '');
            $objTable -> endRow();

            // set up solved label
            if($solved == '1'){
                $array = array('time' => "<b>" . $time . "</b>");
                $solvedLabel = $this -> objLanguage -> code2Txt('mod_sudoku_completed', 'sudoku', $array);
                $objTable -> startRow();
                $objTable -> addCell($solvedLabel, '', '', '', 'error', '');
                $objTable -> endRow();
            }
        }
    }
    $str = $objTable -> show();
    echo $str;

// set up exit link
    $objLink = &new link($this -> uri(array(),'_default'));
    $objLink -> link = $exitLabel;
    $exitLink = $objLink -> show();
    echo "<br />" . "<br />" . $exitLink;
?>