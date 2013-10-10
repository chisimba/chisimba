<?php
/*
* Template to evaluate multiple choice answers and output the result.
* @package pbl
*/

/*
* Template to evaluate multiple choice answers and output the result.
*/

// Suppress Page Variables
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" '; 
$this->setVarByRef('bodyParams',$bodyParams);

$sesOk = $this->getSession('ok');
$sesNChoices = $this->getSession('nchoices');


// get answer string
$ansstr = $sesOk;
$ansstr = trim($ansstr);
$answers = explode(",",$ansstr);

// fill array with negative answers
$ok=array_fill(1, $sesNChoices, FALSE);

    // correct answers using the users input
    foreach($answers as $answer){
    $ok[$answer] = TRUE;
    }
    $nErr = 0;

    // step through array and compare users input to the mcq answers
    for($i = 1; $i <= $sesNChoices; $i++){
        $idx = 'mcq'.$i;
        if(isset($_POST[$idx]) && $ok[$i] == FALSE){
            echo $this->objLanguage->code2Txt('mod_pbl_optionwasFALSEchoseTRUE', 'pbl', array('option' => $i)) .'<br />';
            $nErr++;
        }
        if(!isset($_POST[$idx]) && $ok[$i] == TRUE){
            echo $this->objLanguage-> code2Txt('mod_pbl_optionwasTRUEchoseFALSE', 'pbl', array('option' => $i)) .'<br />';
            $nErr++;
        }
    }
    // display result to the user : num of correct & incorrect answers
    if($nErr>0){ 
        if($nErr == 1){
            echo $this->objLanguage->languageText('mod_pbl_youhaderror', 'pbl').'<br />';
        }else{
            echo $this->objLanguage->code2Txt('mod_pbl_youhaderrors', 'pbl', array('errors' => $nErr)) .'<br />';
        }
    }else{
        echo $this->objLanguage->languageText('mod_pbl_correctanswer', 'pbl') .'<br />';
    }
?>