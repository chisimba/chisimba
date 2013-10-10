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
* Puzzle template for the sudoku puzzle
* Author Kevin Cyster
* */

    $script = '<script type="text/javascript">
        /*
        JavaScript Timer
        Written by Jerry Aman, Optima System, June 1, 1996.
    	Part of the PageSpinner distribution.

    	Portions based upon the JavaScript setTimeout example at:
	    http://home.netscape.com/eng/mozilla/Gold/handbook/javascript/

    	We will not be held responsible for any unwanted
	    effects due to the usage of this script or any derivative.
    	No warrantees for usability for any specific application are
	    given or implied.

    	You are free to use and modify this script,
	    if the credits above are given in the source code

	    Modified for chisimba by Kevin Cyster
        */
        var	timerID = null;
        var	timerRunning = false;
        var	startDate;
        var	startSecs;
        var clockTime;

        function stopclock()
        {
        	if(timerRunning){
                clearTimeout(timerID);
        		timerRunning = false;
            }
        }

        function startclock(clock)
        {
            if(clock == \'0:00:00\'){
                clockTime = 0;
            }else{
                if(clock.length == 7){
                    clockTime = (clock.substring(0,1)*60*60) + (clock.substring(2,4)*60) + (clock.substring(5)*1);
                }else{
                    clockTime = (clock.substring(0,2)*60*60) + (clock.substring(3,5)*60) + (clock.substring(6)*1);
                }
            }
            startDate = new Date();
        	startSecs = (startDate.getHours()*60*60) + (startDate.getMinutes()*60) + startDate.getSeconds();

        	stopclock();
        	showtime();
        }

        /*	-------------------------------------------------
        	showtime()
        	Puts the amount of time that has passed since
        	loading the page into the field named timerField in
        	the form named timeForm
        	-------------------------------------------------	*/

        function showtime()
        {
        	// this doesn\'t work correctly at midnight...

        	var now = new Date();
        	var nowSecs = (now.getHours()*60*60) + (now.getMinutes()*60) + now.getSeconds();
        	if(clockTime == 0){
            	var elapsedSecs = nowSecs - startSecs;
            }else{
                var elapsedSecs = clockTime;
                startSecs = nowSecs - clockTime;
                clockTime = 0;
            }

        	var hours = Math.floor( elapsedSecs / 3600 );
        	elapsedSecs = elapsedSecs - (hours*3600);

        	var minutes = 	Math.floor( elapsedSecs / 60 );
        	elapsedSecs = elapsedSecs - (minutes*60);

        	var seconds = elapsedSecs;

        	var timeValue = "" + hours;
        	if(minutes &lt; 10){
                timeValue  += ":0" + minutes;
            }else{
                timeValue  += ":" + minutes;
            }
        	if(seconds &lt; 10){
                timeValue  += ":0" + seconds;
            }else{
                timeValue  += ":" + seconds;
            }

    		// Update display
            document.getElementById(\'input_timer\').value = timeValue ;
        	timerID = setTimeout("showtime()",1000);
        	timerRunning = true;
        }
        </script>';

    // set up data
    $id = $arrPuzzleData[0]['id'];
    $difficulty = $arrPuzzleData[0]['difficulty'];
    $dateSaved = $arrPuzzleData[0]['date_saved'];
    $dateCreated = $arrPuzzleData[0]['date_created'];
    $solved = $arrPuzzleData[0]['solved'];
    $dateSolved = $arrPuzzleData[0]['date_solved'];
    $time = $arrPuzzleData[0]['time_taken'];
    $timer = $this -> getParam('timer');
    if($time == NULL && $timer == NULL){
        $clock = '0:00:00';
    }else{
        if($timer != NULL){
            $clock = $timer;
        }else{
            $clock = $time;
        }
    }
    if($how == 'R'){
        $solved = '';
        $clock = '0:00:00';
    }elseif($how == 'S'){
        $solved = '';
    }


    $this -> appendArrayVar('headerParams',$script);
    $body = ' onload="startclock(\'' . $clock . '\');"';
    $this -> setVarByRef('bodyParams', $body);

// set up html elements
    $objIcon =& $this -> newObject('geticon', 'htmlelements');
    $this -> loadclass('htmlheading', 'htmlelements');
    $this -> loadclass('htmltable', 'htmlelements');
    $this -> loadclass('link', 'htmlelements');
    $this -> loadclass('dropdown', 'htmlelements');
    $this -> loadclass('textinput', 'htmlelements');
    $this -> loadclass('button', 'htmlelements');
    $this -> loadclass('form', 'htmlelements');
    $this -> loadclass('fieldset', 'htmlelements');

// set up language items
    $deleteLabel = $this -> objLanguage -> languageText('mod_sudoku_delete', 'sudoku');
    $loadLabel = $this -> objLanguage -> languageText('mod_sudoku_load', 'sudoku');
    $deleteConfirm = $this -> objLanguage -> languageText('mod_sudoku_deleteconfirm', 'sudoku');
    $resetLabel = $this -> objLanguage -> languageText('mod_sudoku_reset', 'sudoku');
    $returnLabel = $this -> objLanguage -> languageText('mod_sudoku_return', 'sudoku');
    $easyLabel = $this -> objLanguage -> languageText('mod_sudoku_easy', 'sudoku');
    $hardLabel = $this -> objLanguage -> languageText('mod_sudoku_hard', 'sudoku');
    $difficultLabel = $this -> objLanguage -> languageText('mod_sudoku_difficult', 'sudoku');
    $giantLabel = $this -> objLanguage -> languageText('mod_sudoku_giant', 'sudoku');
    $saveLabel = $this -> objLanguage -> languageText('mod_sudoku_save', 'sudoku');
    $submitLabel = $this -> objLanguage -> languageText('mod_sudoku_submit', 'sudoku');

    // set up label and board based on difficulty
    if($difficulty == 1){
        $level = $easyLabel;
        $size = 3;
    }elseif($difficulty == 2){
        $level = $hardLabel;
        $size = 3;
    }elseif($difficulty == 3){
        $level = $difficultLabel;
        $size = 3;
    }else{
        $level = $giantLabel;
        $size = 5;
    }

    // Suppress normal page elements and layout based on difficulty
    if($difficulty != 4){
        $this -> setLayoutTemplate('layout_tpl.php');
    }else{
        $this->setVar('pageSuppressBanner', FALSE);
        $this->setVar('pageSuppressToolbar', FALSE);
        $this->setVar('suppressFooter', FALSE);
    }

    // set up reset icon
    $objIcon -> title = $resetLabel;
    $resetIcon = $objIcon -> getLinkedIcon($this -> uri(array('action' => 'solve', 'id' => $id, 'how' => 'R')), 'sudokurefresh');

    // set up load icon
    $objIcon -> title = $loadLabel;
    $loadIcon = $objIcon -> getLinkedIcon($this -> uri(array('action' => 'solve', 'id' => $id, 'how' => 'L')), 'sudokuload');

    // set up delete icon
    $objIcon -> title = $deleteLabel;
    $objIcon -> setIcon('sudokudelete');
    $icon = $objIcon -> show();
    $deleteArray = array('action' => 'delete', 'id' => $id);
    $location = $this -> uri($deleteArray, 'sudoku');
    $this -> objUtil -> setConfirm($icon, $location, $deleteConfirm);
    $deleteIcon = $this -> objUtil -> show();

    // set up heading and select data
    if($solved == '1'){
        $array = array('level' => $level, 'created' => 'solved', 'date' => $dateSolved);
        $icons = $resetIcon . " " . $deleteIcon;
        $puzzleLabel = $this -> objLanguage -> code2Txt('mod_sudoku_puzzle', 'sudoku', $array);
        $data = explode(",", $arrPuzzleData[0]['puzzle']);
        $saved = explode(",", $arrPuzzleData[0]['solution']);
        $selected = array_diff_assoc($saved, $data);
    }elseif($how == 'L'){
        $array = array('level' => $level, 'created' => 'saved', 'date' => $dateSaved);
        $icons = $resetIcon . " " . $loadIcon . " " . $deleteIcon;
        $puzzleLabel = $this -> objLanguage -> code2Txt('mod_sudoku_puzzle', 'sudoku', $array);
        $data = explode(",", $arrPuzzleData[0]['puzzle']);
        $saved = explode(",", $arrPuzzleData[0]['saved']);
        $selected = array_diff_assoc($saved, $data);
    }elseif($how == 'S'){
        $array = array('level' => $level, 'created' => 'created', 'date' => $dateCreated);
        $icons = $resetIcon . " " . $loadIcon . " " . $deleteIcon;
        $puzzleLabel = $this -> objLanguage -> code2Txt('mod_sudoku_puzzle', 'sudoku', $array);
        $data = explode(",", $arrPuzzleData[0]['puzzle']);
        $temp = $this -> getParam('data');
        $saved = explode(",", $temp);
        $selected = array_diff_assoc($saved, $data);
    }elseif($how == 'R'){
        $array = array('level' => $level, 'created' => 'created', 'date' => $dateCreated);
        $icons = $resetIcon . " " . $loadIcon . " " . $deleteIcon;
        $puzzleLabel = $this -> objLanguage -> code2Txt('mod_sudoku_puzzle', 'sudoku', $array);
        $data = explode(",", $arrPuzzleData[0]['puzzle']);
    }
    $objHeader = &new htmlHeading();
    $objHeader -> str = $puzzleLabel;
    $objHeader -> type = 1;
    $str = $objHeader -> show();
    echo $str;

    echo $icons;

    // set up form elements
    // set up table
    $objTable = &new htmltable();
    //$objTable -> cellspacing='2';
    $objTable -> cellpadding='2';
    $objTable -> border = '3';
    $x = 0;
    for($i = 1; $i <= $size; $i++){ // loop for rows in a block
        for($ii = 1; $ii <= $size; $ii++){ //loop for rows
            $objTable -> startRow();
            for($iii = 1; $iii <= $size; $iii++){ //loop for cells in a row
                if(($i % 2) == 0){
                    $class = (($iii % 2) == 0) ? 'sudokuOdd':'sudokuEven';
                }else{
                    $class = (($iii % 2) == 0) ? 'sudokuEven':'sudokuOdd';
                }
                for($iiii = 1; $iiii <= $size; $iiii++){ //loop for cells
                    $cellClass = '<font>';
                    if($data[$x] != ''){
                        $objText = &new textinput($x, $data[$x]);
                        $objText -> fldType = 'hidden';
                        $numberText = $objText -> show();
                        $number = $data[$x] . $numberText;
                    }elseif($solved == '1'){
                        $number = $selected[$x];
                        $cellClass = '<font class="error">';
                    }else{
                        $objDrop = &new dropdown($x);
                        $objDrop -> addOption(NULL, '-');
                        //$objDrop -> extra = 'width="100px;"';
                        for($xx = 1; $xx <= pow($size, 2); $xx++){
                            $objDrop -> addOption($xx, $xx);
                        }
                        if(!empty($selected[$x])){
                            $objDrop -> setSelected($selected[$x]);
                        }else{
                            $objDrop -> setSelected(NULL);
                        }

                        $number = $objDrop -> show();
                    }
                    $numStr = $cellClass . "<b>" . $number . "</b>" . "</font>";
                    $objTable -> addCell($numStr, '10%', '', 'center', $class, '');
                    $x = $x + 1;
               }
            }
            $objTable -> endRow();
        }
    }
    $puzzleTable = $objTable -> show();

    if($solved != '1'){
        // set up timer
        $objText = &new textinput('timer', $clock, 'text', 5);
        $timerText = $objText -> show();

        // set up hidden field
        $objText = &new textinput('mode');
        $objText -> fldType = 'hidden';
        $hiddenText = $objText -> show();

        // set up save button
        $objButton = &new button('save', $saveLabel, 'document.getElementById(\'input_mode\').value = \'save\';');
        $objButton -> setToSubmit();
        $saveButton = $objButton -> show();

        // set up submit button
        $objButton = &new button('submit', $submitLabel);
        $objButton -> setToSubmit();
        $submitButton = $objButton -> show();

        // Set up form
        $objForm = &new form('saveForm', $this -> uri(array('action' => 'submit', 'id' => $id)));
        $objForm -> addToForm("<br />".$timerText);
        $objForm -> addToForm($hiddenText . $puzzleTable . "<p>" . $saveButton . " " . $submitButton . "</p>");
        $str = $objForm -> show();
        echo $str;
    }else{
        $str = $puzzleTable;
        echo $str;
    }

// set up exit link
    $objLink = &new link($this -> uri(array(),'sudoku'));
    $objLink -> link = $returnLabel;
    $returnLink = $objLink -> show();
    echo "<br />" . $returnLink;
?>