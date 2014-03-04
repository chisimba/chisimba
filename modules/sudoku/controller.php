<?php
/* -------------------- sudoku extends controller ----------------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Module class for the sudoku puzzle
* @copyright (c) 2004 KEWL.NextGen
* @version 1.0
* @package sudoku
* @author Kevin Cyster
*
* $Id: controller.php
*/

class sudoku extends controller
{
    public function init()
    {
        $this -> dbSudoku =& $this -> newObject('dbsudoku');
        $this -> objUser =& $this -> newObject('user', 'security');
        $this -> objLanguage =& $this -> newObject('language', 'language');
        $this -> objUtil =& $this -> newObject('confirm', 'utilities');

        //Get the activity logger class
        $this -> objLog = $this -> newObject('logactivity', 'logger');

        //Log this module call
        $this -> objLog -> log();
    }

    /**
    * This is the main method of the class
    * It calls other functions depending on the value of $action
    *
    * @param string $action
    **/
    public function dispatch($action)
    {
        // Now the main switch statement to pass values for $action
        switch($action){
            case 'add': //calls the default template for add
                $difficulty = $this -> getParam('difficulty');
                if($difficulty != ''){
                return $this -> generate($difficulty);
                }else{
                    return $this -> nextAction('');
                }
                break;

            case 'solve': //calls the puzzle template for solving
                $id = $this -> getParam('id');
                $arrPuzzleData = $this -> dbSudoku -> getRecord($id);
                $this -> setVarByRef('arrPuzzleData', $arrPuzzleData);
                $how = $this -> getParam('how');
                $this -> setVarByRef('how', $how);
                return 'puzzle_tpl.php';
                break;

            case 'submit': //submits the puzzle
                $id = $this -> getParam('id');
                $mode = $this -> getParam('mode');
                $puzzleData = $_POST;
                $timer = array_shift($puzzleData);
                $temp = array_shift($puzzleData);
                $temp = array_pop($puzzleData);
                if($mode == 'save'){
                    return $this -> saveItem($id, $puzzleData, $timer);
                }else{
                    return $this -> validate($id, $puzzleData, $timer);
                }
                break;

            case 'delete': //deletes content from the database
                $this -> dbSudoku -> deleteRecord($this -> getParam('id'));
                return $this -> nextAction('');
                break;

            default: // lists all puzzles for the user
                return $this -> showAllItems();
        }
    }

    /**
    * Method to list all suduko puzzles
    *
    **/
    private function showAllItems()
    {
        $id = $this -> getParam('id');
        $this -> setVarByRef('id', $id);
        $arrPuzzleList = $this -> dbSudoku -> listAllRecords($this -> objUser -> userId());
        $this -> setVarByRef('arrPuzzleList', $arrPuzzleList);
        return 'default_tpl.php';
    }

    /**
    * Method to add puzzle
    * puzzle generated using the "Peraita method - Author unknown"
    * @param string $difficulty The difficulty raing of the puzzle (used to remove numbers)
    * $size the base size of the grid eg. 3 = 9x9 board, 5 = 25*25 board
    **/
    private function generate($difficulty)
    {
        // set up board size
        if($difficulty != 4){
            $size = 3;
        }else{
            $size = 5;
        }
        // randomize numbers
        $numbers = range(1, pow($size, 2));
        shuffle($numbers);

        // row index sets
        $x = 1;
        for($i = 1; $i <= pow($size, 2); $i++){
            $a = "rowIndex_" . $i; //set up variable names eg $rowIndex_1
            for($ii = 1; $ii <= pow($size, 2); $ii++){
                ${$a}[$ii] = $x; //set up variable eg $rowIndex[0] = 1
                $x = $x + 1;
            }
            $allRows[$i] = $$a; //set up array eg $temp[0] = $rowIndex_1
        }
        $temp = array_chunk($allRows, $size, true);
        foreach($temp as $key => $arrRow){
            $a = "arrRow_" . $key; // set up variable names
            $$a = $arrRow; // set up variable
            $arrAllRows[$key] = $$a; // set up array
        }

        // column index sets
        for($i = 1; $i <= pow($size, 2); $i++){
            $a = "colIndex_" . $i; // set up variable names
            $x = $i;
            for($ii = 1; $ii <= pow($size, 2); $ii++){
                ${$a}[$ii] = $x; // set up variable
                $x = $x + pow($size, 2);
            }
            $allCols[$i] = $$a; // set up array
        }
        $temp = array_chunk($allCols, $size, true);
        foreach($temp as $key => $arrCol){
            $a = "arrCol_" . $key; // set up variable names
            $$a = $arrCol;  // set up variable
            $arrAllCols[$key] = $$a; // set up array
        }

        // block index sets
        $x = 1;
        $y = 1;
        for($i = 1; $i <= $size; $i++){
            for($ii = 1; $ii <= $size; $ii++){
                $a = "blockIndex_" . $x; // set up variable names
                $z = 1;
                for($iii = 1; $iii <= $size; $iii++){
                    for($iv = 1; $iv <= $size; $iv++){
                        ${$a}[$z++] = $y; // set up variable
                        $y = $y + 1;
                    }
                    $y = $y + ((pow($size, 2)) - ($size));
                }
                $arrAllBlocks[$x] = $$a; // set up array
                $x = $x + 1;
            }
            $y = ($i * $size) + 1;
        }

        // set up basic board
        for($i = 1; $i <= pow($size, 2); $i++){
            foreach($arrAllBlocks as $block){
                $temp = $numbers;
                foreach($block as $index){
                    $data[$index] = array_shift($temp);
                }
                $firstNumber = array_shift($numbers);
                $numbers = array_pad($numbers, pow($size, 2), $firstNumber);
            }
        }
        ksort($data);

        // shuffle rows
        for($i = 0; $i <= $size - 2; $i++){
            foreach($arrAllRows as $arrRows){
                shuffle($arrRows);
                $arrRows = array_slice($arrRows, 0, 2); // takes first 2 rows
                foreach($arrRows as $key => $row){
                    foreach($row as $rowKey => $index){
                        if($key == 0){
                            $row_1[$rowKey] = $data[$index];
                        }else{
                            $row_2[$rowKey] = $data[$index];
                        }
                    }
                }
                foreach($arrRows as $key => $row){ // swops them
                    foreach($row as $rowKey => $index){
                        if($key == 0){
                            $data[$index] = $row_2[$rowKey];
                        }else{
                            $data[$index] = $row_1[$rowKey];
                        }
                    }
                }
            }
        }

        // shuffle columns
        for($i = 0; $i <= $size - 2; $i++){
            foreach($arrAllCols as $arrCols){
                shuffle($arrCols);
                $arrCols = array_slice($arrCols, 0, 2); // takes first 2 columns
                foreach($arrCols as $key => $col){
                    foreach($col as $colKey => $index){
                        if($key == 0){
                            $col_1[$colKey] = $data[$index];
                        }else{
                            $col_2[$colKey] = $data[$index];
                        }
                    }
                }
                foreach($arrCols as $key => $col){ // swops them
                    foreach($col as $colKey => $index){
                        if($key == 0){
                            $data[$index] = $col_2[$colKey];
                        }else{
                            $data[$index] = $col_1[$colKey];
                        }
                    }
                }
            }
        }
        $solution = implode(",", $data);

        //remove pairs of numbers symetrically
        if($difficulty == 1){
            $pairs = 16;
        }elseif($difficulty == 2){
            $pairs = 22;
        }elseif($difficulty == 3){
            $pairs = 30;
        }else{
            $pairs = 170;
        }
        for($i = 1; $i <= $pairs; $i++){
            do{
                $number_1 = rand(1, pow($size, 4));
            }while($number_1 == (((pow($size, 4) - 1) / 2) + 1));
            $data[$number_1] = '';
            $number_2 = (pow($size, 4) + 1) - $number_1;
            $data[$number_2] = '';
        }

        $puzzle = implode(",", $data);
        $id = $this -> dbSudoku -> addRecord($difficulty, $solution, $puzzle, $this -> objUser -> userId());
        return $this -> nextAction('solve', array('id' => $id, 'how' => 'R'));
    }

    /**
    * Method to save a puzzle
    *
    * @param string $id The id of the puzzle
    * @param text $saved The saved puzzle
    **/
    private function saveItem($id, $puzzleData, $timer)
    {
        foreach($puzzleData as $key => $number){
            $saved[$key] = $number;
        }
        $saved = implode(",", $saved);
        $this -> dbSudoku -> editRecord($id, $saved, '', $timer);
        return $this -> nextAction('solve', array('id' => $id, 'how' => 'L'));
    }

    /**
    * Method to validate the puzzle
    *
    * @param string $id The id of the puzzle
    * @param text $puzzleData The submited data
    **/
    private function validate($id, $puzzleData, $timer)
    {
        foreach($puzzleData as $key => $number){
            $validateData[$key] = $number;
        }
        $validateData = implode(",", $validateData);
        $arrPuzzleData = $this -> dbSudoku -> getRecord($id);
        $solution = $arrPuzzleData[0]['solution'];
        if($validateData == $solution){
            $this -> dbSudoku -> editRecord($id, '', '1', $timer);
            $solved = '1';
            $this -> setVarByRef('solved', $solved);
            return 'congrats_tpl.php';
        }else{
            $this -> setVarByRef('id', $id);
            $solved = '';
            $this -> setVarByRef('solved', $solved);
            $data = implode(",", $puzzleData);
            $this -> setVarByRef('data', $data);
            $this -> setVarByRef('timer', $timer);
            return 'congrats_tpl.php';
        }
    }
}
?>