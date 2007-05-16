<?php
/* ----------- templates class extends object ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Templates class for sitepermissions module
* @author Kevin Cyster
*/

class display extends object
{
    /**
    * @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;
     
    /**
    * @var object $objModule: The modules class in the modulecatalogue module
    * @access public
    */
    public $objModule;

    /**
    * @var object $objDbperm: The dbpermissions class in the sitepermissions module
    * @access public
    */
    public $objDbperm;

    /**
    * @var object $objIcon: The geticon class in the htmlelements module
    * @access public
    */
    public $objIcon;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {   
        // load html element classes
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('layer','htmlelements');

        // system classes
        $this->objLanguage = $this->newObject('language','language');
        $this->objModule = $this->newObject('modules', 'modulecatalogue');
        $this->objDbperm = $this->newObject('dbpermissions', 'sitepermissions');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        
        $this->objIcon->setIcon('green_bullet');
        $this->objIcon->extra = 'style="vertical-align: middle;"';
        $this->greenIcon = $this->objIcon->show();
        
        $this->objIcon->setIcon('grey_bullet');
        $this->objIcon->extra = 'style="vertical-align: middle;"';
        $this->grayIcon = $this->objIcon->show();
    }

    /**
    * Method to create modules dropdown form
    *
    * @access public
    * @return string $str: The output string
    **/
    public function showModuleForm()
    {
        // language elements
        $heading = $this->objLanguage->code2Txt('mod_sitepermissions_name', 'sitepermissions');
        $selectLabel = $this->objLanguage->languageText('mod_sitepermissions_lblSelectModule', 'sitepermissions');
        
        // get data
        $arrModules = $this->objModule->getModules(2);
                
        // headings
        $objHeader = new htmlheading();
        $objHeader->str = ucfirst($heading);
        $objHeader->type = 1;
        $header = $objHeader->show();        
        
        // module dropdown
        $objDrop = new dropdown('module_name');
        $objDrop->cssId = 'input_module_name';
        foreach($arrModules as $module){
            $objDrop->addOption($module['module_id'], $module['module_id']);
        }
        $objDrop->setSelected($this->getSession('module_name'));
        $objDrop->extra = 'onchange="javascript:$(\'form_module_form\').submit();"';
        $moduleDrop = $objDrop->show();        
 
        // main table heading
        $objTable = new htmltable();
        $objTable->cellpadding = '2';
        $objTable->cellspacing = '2';
        $objTable->startRow();
        $objTable->addCell($selectLabel, '15%', '', '', '', '');
        $objTable->addCell($moduleDrop, '', '', '', '', '');
        $objTable->endRow();        
        $moduleTable = $objTable->show();
        
        $objForm = new form('module_form', $this->uri(array()));
        $objForm->addToForm($moduleTable);
        $moduleForm = $objForm->show();
        
        $objLayer = new layer();
        $objLayer->id = 'moduleLayer';
        $objLayer->padding = '10px';
        $objLayer->addToStr($header.$moduleForm);
        $moduleLayer = $objLayer->show();
        
        $str = $moduleLayer;
        return $str;    
    }
    
    /**
    * Method to create the condition / rule grid
    *
    * @access public
    * @param string $mode: The mode of the grid show/edit item
    * @param string $id: The id of the item to edit
    * @param string $name: The name of the rule
    * @return string $str: The output string
    */
    public function showRuleConditions($mode = 'show', $id = '', $name = '')
    {
        // language elements
        $rulesLabel = $this->objLanguage->languageText('mod_sitepermissions_lblRules', 'sitepermissions');
        $conditionsLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCondition', 'sitepermissions');
        
        // get data
        $arrRules = $this->objDbperm->listRules($this->getSession('module_name'));
        if(!$arrRules){
            $arrRules = array(
                array(
                    'id' => '',
                    'name' => $name,
                ),
            );
        }elseif($name != ''){
            $arrRules[] = array(
                    'id' => '',
                    'name' => $name,
            );
        }
        $arrConditions = $this->objDbperm->listConditions();
        $arrRuleConditions = $this->objDbperm->listRuleConditions($this->getSession('module_name'));
        if(!$arrRuleConditions){
            $arrRuleConditions = array(
                array(
                    'id' => '',
                    'ruleid' => '',
                    'conditionid' => '',
                ),
            );
        }

        $colspan = 'colspan="'.count($arrRules).'"';
        $colWidth = (100 - 20)/count($arrRules).'%';
        
        // create table
        $objTable = new htmltable();
        $objTable->cellpadding = '2';
        //$objTable->cellspacing = '2';
        $objTable->border = '1';
        // create heading
        $objTable->startRow();
        $objTable->addCell('', '20%', '', '', '', '');
        $objTable->addCell($rulesLabel, '', '', 'center', 'heading', $colspan);
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($conditionsLabel, '', '', '', 'heading', '');
        // create ruleconditions
        foreach($arrRules as $rule){
            if($mode == 'rule' && $rule['id'] == $id){
                // create input and function links for ruleconditions
                $ruleItem = $this->createInputbox($rule['name'], $mode ,$id);
            }elseif($rule['id'] == ''){
                $ruleItem = '';
            }else{
                // create link to update ruleconditions
                $ruleItem = $this->createLink($rule['name'], 'rule', $rule['id']);
            }
            $objTable->addCell($ruleItem, '', '', 'center', 'heading', '');
        }
        $objTable->endRow();
        $i = 0;
        // create conditions
        if(!empty($arrConditions)){
            foreach($arrConditions as $condition){
                if($mode == 'condition' && $condition['id'] == $id){
                    // create function links for conditions
                    $conditionItem = $this->createInputbox($condition['name'], $mode ,$id, 'no');
                }else{
                    // create links to update conditions
                    $conditionItem = $this->createLink($condition['name'], 'condition', $condition['id']);
                }
                $class = (($i++%2)==0)?'even':'odd';
                $objTable->startRow();
                $objTable->addCell($conditionItem, '', '', '', 'heading', '');            
                // create matrix
                foreach($arrRules as $rule){
                    $index = '';
                    foreach($arrRuleConditions as $ruleCondition){
                        if($condition['id'] == $ruleCondition['conditionid'] && $rule['id'] == $ruleCondition['ruleid'] && $ruleCondition['id'] != ''){
                            $index = $ruleCondition['id'];    
                        }
                    }
                    if($index != ''){
                        // display selected matrix point
                        if($rule['id'] == '' && $mode == 'show'){
                            $objTable->addCell('&nbsp;', $colWidth, '', 'center', $class, '');
                        }elseif($id == $rule['id'] || $id == $condition['id']){
                             // display checkbox
                            $objCheck = new checkbox('rulecondition[]');
                            if($id == $rule['id']){
                                $objCheck->setValue($condition['id']);
                            }else{
                                $objCheck->setValue($rule['id']);
                            }
                            $objCheck->setChecked(TRUE);
                            $checkBox = $objCheck->show();
                        
                            $objTable->addCell($checkBox, $colWidth, '', 'center', $class, '');
                        }else{
                            // display icon
                            $objTable->addCell($this->greenIcon, $colWidth, '', 'center', $class, '');
                        }
                    }else{
                        // display unselected matrix point
                        if($rule['id'] == '' && $mode == 'show'){
                            $objTable->addCell('&nbsp;', $colWidth, '', 'center', $class, '');
                        }elseif($id == $rule['id'] || $id == $condition['id']){
                            // display checkbox
                            $objCheck = new checkbox('rulecondition[]');
                            if($id == $rule['id']){
                                $objCheck->setValue($condition['id']);
                            }else{
                                $objCheck->setValue($rule['id']);
                            }
                            $checkBox = $objCheck->show();

                            $objTable->addCell($checkBox, $colWidth, '', 'center', $class, '');
                        }else{
                            // display icon
                            $objTable->addCell($this->grayIcon, $colWidth, '', 'center', $class, '');
                        }
                    }
                }
                $objTable->endRow();
            }
        }   
        $ruleConditionTable = $objTable->show();
        
        $objLayer = new layer();
        $objLayer->id = 'ruleConditionLayer';
        $objLayer->padding = '10px';
        $objLayer->addToStr($ruleConditionTable);
        $ruleConditionLayer = $objLayer->show();
        
        $str = $ruleConditionLayer;
        return $str;    
    } 

    /**
    * Method to create the action / rule grid
    *
    * @access public
    * @param string $mode: The mode of the grid show/edit item
    * @param string $id: The id of the item to edit
    * @param string $name: The name of the rule
    * @return string $str: The output string
    */
    public function showRuleActions($mode = 'show', $id = '', $name = '')
    {
        // language elements
        $rulesLabel = $this->objLanguage->languageText('mod_sitepermissions_lblRules', 'sitepermissions');
        $actionsLable = $this->objLanguage->languageText('mod_sitepermissions_lblAction', 'sitepermissions');
         
        // get data
        $arrRules = $this->objDbperm->listRules($this->getSession('module_name'));
        if(!$arrRules){
            $arrRules = array(
                array(
                    'id' => '',
                    'name' => $name,
                ),
            );
        }elseif($name != ''){
            $arrRules[] = array(
                    'id' => '',
                    'name' => $name,
            );
        }
        $arrActions = $this->objDbperm->listActions($this->getSession('module_name'));
        if(!$arrActions){
            $arrActions = array(
                array(
                    'id' => '',
                    'name' => '',
                ),
            );
        }
        $arrActionRules = $this->objDbperm->listActionRules($this->getSession('module_name'));
        if(!$arrActionRules){
            $arrActionRules = array(
                array(
                    'id' => '',
                    'ruleid' => '',
                    'actionid' => '',
                ),
            );
        }
        $colspan = 'colspan="'.count($arrRules).'"';
        $colWidth = (100 - 20)/count($arrRules).'%';
        
        // create table
        $objTable = new htmltable();
        $objTable->cellpadding = '2';
        //$objTable->cellspacing = '2';
        $objTable->border = '1';
        // create heading
        $objTable->startRow();
        $objTable->addCell($actionsLable, '', '', '', 'heading', '');
        // create actionrules 
        foreach($arrRules as $rule){
            $objTable->addCell($rule['name'], '', '', 'center', 'heading', '');
        }
        $objTable->endRow();
        $i = 0;
        // create actions
        foreach($arrActions as $action){
            if($mode == 'action' && $action['id'] == $id){
                // create input and function links for actions
                $actionItem = $this->createInputbox($action['name'], $mode ,$id, 'no');
            }else{
                // create links to update actions
                $actionItem = $this->createLink($action['name'], 'action', $action['id']);
            }
            $class = (($i++%2)==0)?'even':'odd';
            $objTable->startRow();
            $objTable->addCell($actionItem, '', '', '', 'heading', '');            
            // create matrix
            foreach($arrRules as $rule){
                $index = '';
                foreach($arrActionRules as $actionRule){
                    if($action['id'] == $actionRule['actionid'] && $rule['id'] == $actionRule['ruleid'] && $actionRule['id'] != ''){
                        $index = $actionRule['id'];    
                    }
                }
                if($index != ''){
                    // display selected matrix point
                    if($rule['id'] == '' && $mode == 'show'){
                        $objTable->addCell('&nbsp;', $colWidth, '', 'center', $class, '');
                    }elseif($id == $action['id'] || $id == $rule['id']){
                        $objCheck = new checkbox('actionrule[]');
                        if($id == $rule['id']){
                            $objCheck->setValue($action['id']);
                        }else{
                            $objCheck->setValue($rule['id']);
                        }
                        $objCheck->setChecked(TRUE);
                        $checkBox = $objCheck->show();

                        $objTable->addCell($checkBox, $colWidth, '', 'center', $class, '');
                    }else{
                        $objTable->addCell($this->greenIcon, $colWidth, '', 'center', $class, '');
                    }
                }else{
                    // display unselected matrix point
                    if($rule['id'] == '' && $mode == 'show'){
                        $objTable->addCell('&nbsp;', $colWidth, '', 'center', $class, '');
                    }elseif($id == $action['id'] || $id == $rule['id']){
                        $objCheck = new checkbox('actionrule[]');
                        if($id == $rule['id']){
                            $objCheck->setValue($action['id']);
                        }else{
                            $objCheck->setValue($rule['id']);
                        }
                        $checkBox = $objCheck->show();

                        $objTable->addCell($checkBox, $colWidth, '', 'center', $class, '');
                    }else{
                        $objTable->addCell($this->grayIcon, $colWidth, '', 'center', $class, '');
                    }
                }
            }
            $objTable->endRow();
        }
        $actionRuleTable = $objTable->show();

        $objLayer = new layer();
        $objLayer->id = 'actionRuleLayer';
        $objLayer->padding = '10px';
        $objLayer->addToStr($actionRuleTable);
        $actionRuleLayer = $objLayer->show();
        
        $str = $actionRuleLayer;
        return $str;    
    }
    
    /**
    * Method to generate links
    *
    * @access private
    * @param string $name: The text to create a link of
    * @param string $type: The type of link to create
    * @param string $id: The id of the item to be linked
    * @return string $str: The link output string
    */
    
    private function createLink($name, $type ,$id)
    {
        $objLink = new link($this->uri(array(
            'action' => 'edit',
            'param' => $type,
            'id' => $id,
        )));
        $objLink->link = $name;
        $str = $objLink->show();
        return $str;       
    } 

    /**
    * Method to generate textinput boxes
    *
    * @access private
    * @param string $name: The name of the item
    * @param string $type: The type of the item
    * @param string $id: The id of the item
    * @return string $str: The textbox output string
    */
    
    private function createInputbox($name, $type ,$id, $input = '')
    {
        if($input == ''){
            $objInput = new textinput('item', $name, '', '');
            $str = $objInput->show();
        }else{
            $str = $name;
        }
        
        $objInput = new textinput('id', $id, 'hidden', '');
        $str .= $objInput->show();
        
        $links = $this->createFunctionLinks($type, $id);
        $str .= '<br />'.$links;
        
        return $str;       
    } 

    /**
    * Method to generate function links
    *
    * @access private
    * @param string $type: The type of the item
    * @param string $id: The id of the item
    * @return string $str: The textbox output string
    */
    
    private function createFunctionLinks($type, $id)
    {
        $saveLabel = $this->objLanguage->languageText('word_save');
        $deleteLabel = $this->objLanguage->languageText('word_delete');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        $confirmLabel = $this->objLanguage->languageText('mod_sitepermissions_lblDeleteConfirm', 'sitepermissions');
        
        $objLink = new link('#');
        $objLink->link = $saveLabel;
        $objLink->extra = 'onclick="javascript:$(\'form_saveform\').submit();"';
        $saveLink = $objLink->show();
        $str = $saveLink;
        
        $objLink = new link($this->uri(array(
            'action' => 'delete',
            'mode' => $type,
            'id' => $id,
        )));
        $objLink->link = $deleteLabel;
        $objLink->extra = 'onclick="javascript:return confirm(\''.$confirmLabel.'\');"';
        $deleteLink = $objLink->show();
        $str .= '&nbsp;|&nbsp;'.$deleteLink;
        
        $objLink = new link($this->uri(array('')));
        $objLink->link = $cancelLabel;
        $cancelLink = $objLink->show();              
        $str .= '&nbsp;|&nbsp;'.$cancelLink;
        return $str;
    }
    
    /**
    * Method to create action links
    * 
    * @access public
    * @return string $str: The output string
    */
    public function createActionLinks() 
    {
        // langauge items
        $addLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCreateRule', 'sitepermissions');
        $updateLabel = $this->objLanguage->languageText('mod_sitepermissions_lblUpdatePerms', 'sitepermissions');

        //get data
        $arrRules = $this->objDbperm->listRules($this->getSession('module_name'));
        $count = $arrRules ? count($arrRules) + 1 : 1;
        $ruleName = $this->getSession('module_name').'_rule_'.$count;
        
        $objLink = new link($this->uri(array(
            'action' => 'add',
            'name' => $ruleName,
        )));
        $objLink->link = $addLabel;
        $addLink = $objLink->show();
        $string = $addLink;
        
        $objLink = new link($this->uri(array(
            'action' => 'update',
        )));
        $objLink->link = $updateLabel;
        $updateLink = $objLink->show();
        $string .= '&nbsp;|&nbsp;'.$updateLink;

        $objLayer = new layer();
        $objLayer->id = 'linkLayer';
        $objLayer->padding = '10px';
        $objLayer->addToStr($string);
        $linkLayer = $objLayer->show();
        
        $str = $linkLayer;
        return $str;    
    }

    /**
    * Method to create the exit link
    * 
    * @access public
    * @return string $str: The output string
    */
    public function createExitLinks() 
    {
        // langauge items
        $exitLabel = $this->objLanguage->languageText('word_exit');
                
        $objLink = new link($this->uri(array(), '_default'));
        $objLink->link = $exitLabel;
        $exitLink = $objLink->show();
        $string = $exitLink;
        
        $objLayer = new layer();
        $objLayer->id = 'exitLayer';
        $objLayer->padding = '10px';
        $objLayer->addToStr($string);
        $exitLayer = $objLayer->show();
        
        $str = $exitLayer;
        return $str;    
    }
    
    /**
    * Method to create the submission form
    *
    * @access public
    * @param string $mode: The mode of the grid show/edit item
    * @param string $id: The id of the item to edit
    * @param string $name: The name of the rule
    * @return string $str: The output string
    */
    public function createForm($mode = '', $id = '', $name = '')
    {
        $string = $this->showRuleConditions($mode, $id, $name);
        $string .= $this->showRuleActions($mode, $id, $name);
        
        $objForm = new form('saveform', $this->uri(array(
            'action' => 'save',
            'mode' => $mode,
        )));
        $objForm->addToForm($string);
        $saveForm = $objForm->show();
        $str = $saveForm;
        
        return $str;
    }
}
?>