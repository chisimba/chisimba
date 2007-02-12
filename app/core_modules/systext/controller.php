<?php
/* -------------------- systext extends controller ----------------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Module class for system wide text abstraction within modules
* @copyright (c) 2004 KEWL.NextGen
* @version 1.0
* @package systext
* @author Kevin Cyster
*
* $Id: controller.php
*/

class systext extends controller
{
    
    /**
     * Constructor
     *
     * @access pubic
     */
    public function init()
    {
        $this -> facet =& $this -> getObject('systext_facet');
        $this -> objUser =& $this -> getObject('user', 'security');
        $this -> objLanguage =& $this -> getObject('language', 'language');

        //Get the activity logger class
        $this -> objLog = $this -> newObject('logactivity', 'logger');

        //Log this module call
        $this -> objLog -> log();
    }

    
    /**
    * This is the main method of the class
    * It calls other functions depending on the value of $action
    *
    * @access public
    * 
    * @param string $action
    **/
    public function dispatch($action)
    {
        // Now the main switch statement to pass values for $action
        switch($action){
            case 'submit':
                return $this->_submit();

            default: //main display template
                $mode = $this -> getParam('mode');
                $systemId = $this -> getParam('systemId');
                $textId = $this -> getParam('textId');
                $canDelete = $this -> getParam('candelete');
                return $this -> showMain($mode, $systemId, $textId, $canDelete);
        }
    }

    /**
     * Method to is evoked by the 'submit' action
     * 
     * @access private
     * @return string
     */
    private function _submit()
    {
        $mode = $this -> getParam('mode');
                $cancel = $this -> getParam('cancel');
                $save = $this -> getParam('save');
                $delete = $this -> getParam('deleted');
                if($cancel == 'Cancel'){ // cancel button
                    return $this -> nextAction('');
                }
                if($save == 'Save'){ // save button
                    $systemId = $this -> getParam('systemId');
                    $systemType = $this -> getParam('systemtype');
                    $textId = $this -> getParam('textId');
                    $text = $this -> getParam('text');
                    $arrAbstract = $this -> getParam('abstract');
                    $canDelete = $this -> getParam('candelete');
                    $check = implode("", $arrAbstract);
                    if($mode == 'addsystem'){
                        if(empty($check)){
                            return $this -> nextAction('');
                        }
                        if(!empty($systemType)){
                            return $this -> addSystem($systemType, $arrAbstract);
                        }
                    }elseif($mode == 'addtext'){
                        if(empty($check)){
                            return $this -> nextAction('');
                        }
                        if(!empty($text)){
                            return $this -> addText($text, $arrAbstract);
                        }
                    }elseif($mode == 'editsystem'){
                        if(empty($check)){
                            return $this -> nextAction('');
                        }else{
                            return $this -> editSystem($systemId, $systemType, $arrAbstract);
                        }
                    }elseif($mode == 'edittext'){
                        if(empty($check)){
                            if($canDelete == 'N'){
                                return $this -> editText($textId, $text, $arrAbstract);
                            }else{
                                return $this -> nextAction('');
                            }
                        }else{
                            return $this -> editText($textId, $text, $arrAbstract);
                        }
                    }
                }
                if($delete == 'Delete'){ // delete button
                    $this -> facet -> deleteSystemType($this -> getParam('systemId'));
                    $check = $this -> facet -> getTextItem($this -> getParam('textId'));
                    if($check[0]['candelete'] != 'N' || strpos($check[0]['id'],'@') !== FALSE){
                        $this -> facet -> deleteTextItem($this -> getParam('textId'));
                    }
                    return $this -> nextAction('');
                }    
        
    }
    
    
    /**
    * Method to display the main template
    *
    * @param string $mode The mode of the template eg. add system, edit system
    * @param string $systemId The system id if any
    * @param string $textId The text id if any
    * @param string $canDelete An indicator to show if the record can be deleted
    *
    * @return The display template
    * @access public
    **/
    public function showMain($mode, $systemId, $textId, $canDelete)
    {
        $this -> setVarByRef('mode', $mode);
        $this -> setVarByRef('systemId', $systemId);
        $this -> setVarByRef('textId', $textId);
        $this -> setVarByRef('canDelete', $canDelete);
        $arrTextItems = $this -> facet -> listTextItems();
        $this -> setVarByRef('arrTextItems', $arrTextItems);
        $arrSystemTypes = $this -> facet -> listSystemTypes();
        $this -> setVarByRef('arrSystemTypes', $arrSystemTypes);
        return 'default_tpl.php';
    }

    /**
    * Method to add a new system type with abtracted text for all text items
    *
    * @param string $systemType The name of the new system type
    * @param array $arrAbstract An array containing the abstracted text
    * 
    * @access public
    * @return object
    */
    public function addSystem($systemType, $arrAbstract)
    {
        $userId = $this -> objUser -> userId();
        $systemId = $this -> facet -> addSystemType($systemType, $userId);
        $arrTextItems = $this -> facet -> listTextItems();
        foreach($arrTextItems as $key => $item){
            if(!empty($arrAbstract[$key])){
                $this -> facet -> addAbstractText($systemId, $item['id'], $arrAbstract[$key], $userId);
            }
        }
        return $this -> nextAction('');
    }

    /**
    * Method to add a new text item with abtracted text for all system types
    *
    * @param string $newText The new text item
    * @param array $arrAbstract An array containing the abstracted text
    * 
    * @access public
    */
    
    public  function addText($text, $arrAbstract)
    {
        $userId = $this -> objUser -> userId();
        $textId = $this -> facet -> addTextItem($text, $userId);
        $arrSystemTypes = $this -> facet -> listSystemTypes();
        foreach($arrSystemTypes as $key => $systemType){
            if(!empty($arrAbstract[$key])){
                $this -> facet -> addAbstractText($systemType['id'], $textId, $arrAbstract[$key], $userId);
            }
        }
        return $this -> nextAction('');
    }

    /**
    * Method to edit abstracts for a system type
    *
    * @param string $systemId The id of the system type
    * @param string $systemType The name of the system type
    * @param array $arrAbstract An array containing the abstracted text
    */
    public function editSystem($systemId, $systemType, $arrAbstract)
    {
        if(!empty($systemType)){ // edits the system type name
            $this -> facet -> editSystemType($systemId, $systemType);
        }
        $i = 0;
        foreach($arrAbstract as $abstract){ // checks for number of abstract elements
            if(!empty($abstract)){
                $i = $i + 1;
            }
        }
        if($i > 1){
            $oneElement = FALSE;
        }else{
            $oneElement = TRUE;
        }
        foreach($arrAbstract as $key => $abstract){
            $arrId = explode("-", $key);
            $check = $this -> facet -> getAbstractTextById($arrId[0]);
            if(!empty($check)){ // record exists
                if(!empty($abstract)){
                    if($oneElement){ // cannot delete the last element
                        $this -> facet -> editAbstractText($arrId[0], $abstract, 'N');
                    }else{
                        $this -> facet -> editAbstractText($arrId[0], $abstract, 'Y');
                    }
                }else{
                    if($check[0]['canDelete'] != 'N'){
                       $this -> facet -> deleteAbstractText($arrId[0]);
                    }
                }
            }else{
                if(!empty($abstract)){ // add record
                    if($oneElement){
                        $this -> facet -> addAbstractText($arrId[1], $arrId[2], $abstract, $this -> objUser -> userId(), 'N');
                    }else{
                        $this -> facet -> addAbstractText($arrId[1], $arrId[2], $abstract, $this -> objUser -> userId(), 'Y');
                    }
                }
            }
        }
        return $this -> nextAction('');
    }

    /**
    * Method to edit abstracts for a text item
    *
    * @param string $textId The id of the text item
    * @param string $text The text item
    * @param array $arrAbstract An array containing the abstracted text
    */
    public function editText($textId, $text, $arrAbstract)
    {
        if(!empty($text)){ // edits the text item name
            $this -> facet -> editTextItem($textId, $text);
        }
        $i = 0;
        foreach($arrAbstract as $abstract){ // checks for number of abstract elements
            if(!empty($abstract)){
                $i = $i + 1;
            }
        }
        if($i > 1){
            $oneElement = FALSE;
        }else{
            $oneElement = TRUE;
        }
        foreach($arrAbstract as $key => $abstract){
            $arrId = explode("-", $key);
            $check = $this -> facet -> getAbstractTextById($arrId[0]);
            if(!empty($check)){ // record exists
                if(!empty($abstract)){
                    if($oneElement){ // cannot delete the last element
                        $this -> facet -> editAbstractText($arrId[0], $abstract, 'N');
                    }else{
                        $this -> facet -> editAbstractText($arrId[0], $abstract, 'Y');
                    }
                }else{
                    if($check[0]['canDelete'] != 'N'){
                        $this -> facet -> deleteAbstractText($arrId[0]);
                    }
                }
            }else{ // add new record
                if(!empty($abstract)){
                    if($oneElement){
                        $this -> facet -> addAbstractText($arrId[1], $arrId[2], $abstract, $this -> objUser -> userId(), 'N');
                    }else{
                        $this -> facet -> addAbstractText($arrId[1], $arrId[2], $abstract, $this -> objUser -> userId(), 'Y');
                    }
                }
            }
        }
        return $this -> nextAction('');
    }
}
?>
