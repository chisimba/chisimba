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

class permissionsdisplay extends object
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
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var object $objTab: The tabber class in the htmlelements module
    * @access public
    */
    public $objTab;

    /**
    * @var object $objTabbedbox: The tabbedbox class in the htmlelements module
    * @access public
    */
    public $objTabbedbox;

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
        $this->objUser = $this->getObject('user', 'security');
        $this->objTab = $this->newObject('tabber', 'htmlelements');
        $objTabbedbox = $this->loadClass('tabbedbox', 'htmlelements');
        
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
        $heading = $this->objLanguage->code2Txt('mod_sitepermissions_lblCurrentModule', 'sitepermissions');
        $selectLabel = $this->objLanguage->languageText('mod_sitepermissions_lblSelectModule', 'sitepermissions');
        $currentLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCurrentModule', 'sitepermissions');
        
        // get data
        $arrModules = $this->objModule->getModules(2);
                
        $objHeader = new htmlheading();
        $objHeader->str = ucfirst($heading).':&#160;'.ucfirst($this->getSession('module_name'));
        $objHeader->type = 3;
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
    public function createActionLinks($mode = 'permissions') 
    {
        // langauge items
        $addLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCreateRule', 'sitepermissions');
        $updateLabel = $this->objLanguage->languageText('mod_sitepermissions_lblUpdatePerms', 'sitepermissions');
        $conditionLabel = $this->objLanguage->languageText('mod_sitepermissions_lblManageConditions', 'sitepermissions');
        $aclLabel = $this->objLanguage->languageText('mod_sitepermissions_lblManageACLs', 'sitepermissions');
        $permissionsLabel = $this->objLanguage->languageText('mod_sitepermissions_lblManagePermissions', 'sitepermissions');
        $addConLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCreateCondition', 'sitepermissions');
        $addAclLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCreateAcl', 'sitepermissions');

        //get data
        $arrRules = $this->objDbperm->listRules($this->getSession('module_name'));
        $count = $arrRules ? count($arrRules) + 1 : 1;
        $ruleName = $this->getSession('module_name').'_rule_'.$count;
        
        $string = '<ul>';
        if($mode != 'permissions'){
            $objLink = new link($this->uri(array(), 'sitepermissions'));
            $objLink->link = $permissionsLabel;
            $homeLink = $objLink->show();
            $string .= '<li>'.$homeLink.'</li>';
        }
        
        if($mode != 'conditions'){
            $objLink = new link($this->uri(array(
                'action' => 'manage_conditions',
            )));
            $objLink->link = $conditionLabel;
            $conditionLink = $objLink->show();
            $string .= '<li>'.$conditionLink.'</li>';
        }

        if($mode != 'acls'){
            $objLink = new link($this->uri(array(
                'action' => 'manage_acls',
            )));
            $objLink->link = $aclLabel;
            $aclLink = $objLink->show();
            $string .= '<li>'.$aclLink.'</li>';
        }
        $string .= '</ul>';
        
        if($mode == 'permissions'){
            $objLink = new link($this->uri(array(
                'action' => 'add',
                'name' => $ruleName,
            )));
            $objLink->link = $addLabel;
            $addLink = $objLink->show();
            $string .= $addLink;
        
            $objLink = new link($this->uri(array(
                'action' => 'update',
            )));
            $objLink->link = $updateLabel;
            $updateLink = $objLink->show();
            $string .= '&nbsp;|&nbsp;'.$updateLink;
        }        
        
        if($mode == 'conditions'){
            $objLink = new link($this->uri(array(
                'action' => 'manage_conditions',
                'mode' => 'add',
            )));
            $objLink->link = $addConLabel;
            $addLink = $objLink->show();
            $string .= $addLink;
        }        
        
        if($mode == 'acls'){
            $objLink = new link($this->uri(array(
                'action' => 'manage_acls',
                'mode' => 'add',
            )));
            $objLink->link = $addAclLabel;
            $addLink = $objLink->show();
            $string .= $addLink;
        }        
        
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
    
    /**
    * Method to create the manage conditions template
    *
    * @access public
    * @param string $mode: The mode of the display
    * @param string $id: The id of the condition
    * @return string $str: The output string
    */
    public function showCondition($mode = 'show', $id = NULL)
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $arrConditions = $this->objDbperm->listConditions();
        $arrTypes = $this->objDbperm->listConditionTypes();
        $array= array(
            'isadmin',
            'islecturer',
            'isstudent',
            'isguest',
            'iscontextlecturer',
            'iscontextstudent',
            'iscontextguest',
        );
        
        // text elements
        $nameLabel = $this->objLanguage->languageText('word_name');
        $typeLabel = $this->objLanguage->languageText('word_type');
        $targetLabel = $this->objLanguage->languageText('word_target');
        $editLabel = $this->objLanguage->languageText('word_edit');
        $saveLabel = $this->objLanguage->languageText('word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        
        // create table
        $objTable = new htmltable();
        $objTable->id = 'conditionList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell('&#160;'.$nameLabel, '20%', '', '', 'heading', '');
        $objTable->addCell('&#160;'.$typeLabel, '20%', '', '', 'heading', '');
        $objTable->addCell('&#160;'.$targetLabel, '', '', '', 'heading', '');
        $objTable->addCell('&#160;', '10%', '', '', 'heading', '');
        $objTable->endRow();
        
        if($mode == 'add'){
            $objInput = new textinput('name', '', '', '30');
            $name = $objInput->show();
                
            $objInput = new textinput('target', '', '', '80');
            $target = $objInput->show();
             
            $objDrop = new dropdown('type');            
            foreach($arrTypes as $line){
                $objDrop->addOption($line['name'], $line['name']);
            }
            $type = $objDrop->show();

            $objLink = new link('#');
            $objLink->link = $saveLabel;
            $objLink->extra = 'onclick="javascript:$(\'form_condition\').submit();"';
            $links = $objLink->show();

            $objLink = new link($this->uri(array(
                'action' => 'manage_conditions',
            ), 'sitepermissions'));
            $objLink->link = $cancelLabel;
            $links .= '&#160;|&#160;'.$objLink->show();

            $objTable->startRow();
            $objTable->addCell('&#160;'.$name, '', '', '', 'even', '');
            $objTable->addCell('&#160;'.$type, '', '', '', 'even', '');
            $objTable->addCell('&#160;'.$target, '', '', '', 'even', '');
            $objTable->addCell($links, '', '', 'center', 'even', '');
            $objTable->endRow();
        }
        
        foreach($arrConditions as $condition){
            $conditionId = $condition['id'];
            $name = $condition['name'];
            $target = $condition['target'];
            
            foreach($arrTypes as $line){
                if($line['id'] == $condition['typeid']){
                    $type = $line['name'];
                }
            }
            
            $links = '';
            $class = '';
            if(!in_array($name, $array) && $id != $conditionId){
                $objLink = new link($this->uri(array(
                    'action' => 'manage_conditions',
                    'id' => $conditionId,
                ), 'sitepermissions'));
                $objLink->link = $editLabel;
                $links = $objLink->show();
            }elseif($id == $conditionId){
                $class = 'even';
                $objInput = new textinput('id', $conditionId, 'hidden', '30');
                $name = $objInput->show();
                
                $objInput = new textinput('name', $condition['name'], '', '30');
                $name .= $objInput->show();
                
                $objInput = new textinput('target', $target, '', '80');
                $target = $objInput->show();
             
                $objDrop = new dropdown('type');            
                foreach($arrTypes as $line){
                    $objDrop->addOption($line['id'], $line['name']);
                }
                $objDrop->setSelected($condition['typeid']);            
                $type = $objDrop->show();

                $objLink = new link('#');
                $objLink->link = $saveLabel;
                $objLink->extra = 'onclick="javascript:$(\'form_condition\').submit();"';
                $links = $objLink->show();

                $objLink = new link($this->uri(array(
                    'action' => 'manage_conditions',
                ), 'sitepermissions'));
                $objLink->link = $cancelLabel;
                $links .= '&#160;|&#160;'.$objLink->show();
            }
            
            $objTable->startRow();
            $objTable->addCell('&#160;'.$name, '', '', '', $class, '');
            $objTable->addCell('&#160;'.$type, '', '', '', $class, '');
            $objTable->addCell('&#160;'.$target, '', '', '', $class, '');
            $objTable->addCell($links, '', '', 'center', $class, '');
            $objTable->endRow();
        }
        
        $objForm = new form('condition', $this->uri(array(
            'action' => 'save_conditions',
        ), 'sitepermissions'));
        $objForm->addToForm($objTable->show());
               
        $objLayer = new layer();
        $objLayer->padding = '10px';
        $objLayer->addToStr($objForm->show());
        $str = $objLayer->show();
        
        return $str;
    }
    
    /**
    * Method to create the manage acls template
    * 
    * @access public
    * @param string $mode: The mode of the display
    * @param string $id: The id of the acl
    * @return string $str: The output string
    */
    public function showAcls($mode = 'show', $id = NULL)
    {
        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $arrAcls = $this->objDbperm->listAcls();

        // text elements
        $nameLabel = $this->objLanguage->languageText('word_name');
        $descLabel = $this->objLanguage->languageText('word_description');
        $editLabel = $this->objLanguage->languageText('word_edit');
        $saveLabel = $this->objLanguage->languageText('word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        
        // create table
        $objTable = new htmltable();
        $objTable->id = 'aclList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell('&#160;'.$nameLabel, '20%', '', '', 'heading', '');
        $objTable->addCell('&#160;'.$descLabel, '', '', '', 'heading', '');
        $objTable->addCell('&#160;', '10%', '', '', 'heading', '');
        $objTable->endRow();
        
        if($mode == 'add'){
            $objInput = new textinput('name', '', '', '30');
            $name = $objInput->show();
                
            $objInput = new textinput('desc', '', '', '80');
            $desc = $objInput->show();
             
            $objLink = new link('#');
            $objLink->link = $saveLabel;
            $objLink->extra = 'onclick="javascript:$(\'form_acl\').submit();"';
            $links = $objLink->show();

            $objLink = new link($this->uri(array(
                'action' => 'manage_acls',
            ), 'sitepermissions'));
            $objLink->link = $cancelLabel;
            $links .= '&#160;|&#160;'.$objLink->show();

            $objTable->startRow();
            $objTable->addCell('&#160;'.$name, '', '', '', 'even', '');
            $objTable->addCell('&#160;'.$desc, '', '', '', 'even', '');
            $objTable->addCell($links, '', '', 'center', 'even', '');
            $objTable->endRow();
        }
        
        foreach($arrAcls as $acls){
            $aclId = $acls['id'];
            $aclName = $acls['name'];
            $desc = $acls['description'];
            
            $objLink = new link($this->uri(array(
                'action' => 'manage_acl_users',
                'aclId' => $aclId,
            ), 'sitepermissions'));
            $objLink->link = $aclName;
            $name = $objLink->show();
            
            $links = '';
            $class = '';
            $objLink = new link($this->uri(array(
                'action' => 'manage_acls',
                'id' => $aclId,
            ), 'sitepermissions'));
            $objLink->link = $editLabel;
            $links = $objLink->show();
        
            if($id == $aclId){
                $class = 'even';
                $objInput = new textinput('id', $aclId, 'hidden', '30');
                $name = $objInput->show();
                
                $objInput = new textinput('name', $aclName, '', '30');
                $name .= $objInput->show();
                
                $objInput = new textinput('desc', $desc, '', '80');
                $desc = $objInput->show();
             
                $objLink = new link('#');
                $objLink->link = $saveLabel;
                $objLink->extra = 'onclick="javascript:$(\'form_acl\').submit();"';
                $links = $objLink->show();

                $objLink = new link($this->uri(array(
                    'action' => 'manage_acls',
                ), 'sitepermissions'));
                $objLink->link = $cancelLabel;
                $links .= '&#160;|&#160;'.$objLink->show();
            }
                            
            $objTable->startRow();
            $objTable->addCell('&#160;'.$name, '', '', '', $class, '');
            $objTable->addCell('&#160;'.$desc, '', '', '', $class, '');
            $objTable->addCell($links, '', '', 'center', $class, '');
            $objTable->endRow();
        }
        
        $objForm = new form('acl', $this->uri(array(
            'action' => 'save_acls',
        ), 'sitepermissions'));
        $objForm->addToForm($objTable->show());
               
        $objLayer = new layer();
        $objLayer->padding = '10px';
        $objLayer->addToStr($objForm->show());
        $str = $objLayer->show();
        
        return $str;
    }
    
    /**
    * Method to create the manage acl users template
    * 
    * @access public
    * @param string $aclId: The acl id
    * @return string $str: The output string
    */
    public function showUsers($aclId, $mode = 'show', $id = NULL)
    {
        $style = '<style type="text/css">
            div.autocomplete {
            position:absolute;
            background-color:white;
        }    
        div.autocomplete ul {
            list-style-type:none;
            margin:0px;
            padding:0px;
        }    
        div.autocomplete ul li.selected {
            border:1px solid #888;
            background-color: #ffb;
        }
        div.autocomplete ul li {
            border:1px solid #888;
            list-style-type:none;
            display:block;
            margin:0;
            cursor:pointer;
        }
        </style>';
        $this->appendArrayVar('headerParams', $style);

        // add javascript to sort table
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // text elements
        $heading = $this->objLanguage->languageText('mod_sitepermissions_lblAcl', 'sitepermissions');
        $groupsLabel = $this->objLanguage->languageText('word_groups');
        $usersLabel = $this->objLanguage->languageText('word_users');
        $noRecordsLabel = $this->objLanguage->languageText('mod_sitepermissions_lblNoRecords', 'sitepermissions');
        $groupLabel = $this->objLanguage->languageText('word_users');
        $nameLabel = $this->objLanguage->languageText('word_name');
        $surnameLabel = $this->objLanguage->languageText('word_surname');
        $saveLabel = $this->objLanguage->languageText('word_save');
        $editLabel = $this->objLanguage->languageText('word_edit');
        $cancelLabel = $this->objLanguage->languageText('word_cancel');
        $deleteLabel = $this->objLanguage->languageText('word_delete');
        $addGroupLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCreateAclGroup', 'sitepermissions');
        $addUserLabel = $this->objLanguage->languageText('mod_sitepermissions_lblCreateAclUser', 'sitepermissions');
        $confirmLabel = $this->objLanguage->languageText('mod_sitepermissions_lblDeleteConfirm', 'sitepermissions');
        $searchLabel = $this->objLanguage->languageText('mod_sitepermissions_lblSearch', 'sitepermissions');
        
        // get data
        $arrAcl = $this->objDbperm->getAclById($aclId);
        $arrGroups = $this->objDbperm->getAclGroups($aclId);
        $arrUsers = $this->objDbperm->getAclUsers($aclId);        
        $type = 0;
        
        // text elements
        $objHeader = new htmlheading();
        $objHeader->str = ucfirst($heading).':&#160;'.$arrAcl['name'];
        $objHeader->type = 3;
        $string = $objHeader->show();
        
        $objLink = new link($this->uri(array(
            'action' => 'manage_acl_users',
            'mode' => 'addgroup',
            'aclId' => $aclId,
        ), 'sitepermissions'));
        $objLink->link = $addGroupLabel;
        $string .= $objLink->show();
        
        // create table
        $objTable = new htmltable();
        $objTable->id = 'groupList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        $objTable->startRow();
        $objTable->addCell('&#160;'.$groupsLabel, '', '', '', 'heading', '');
        $objTable->addCell('&#160;', '15%', '', '', 'heading', '');
        $objTable->endRow();
        
        if($mode == 'addgroup'){
            $type = 1;
            $objInput = new textinput('group', '', '', '60');
            $group = $objInput->show();
                
            $objLink = new link('#');
            $objLink->link = $saveLabel;
            $objLink->extra = 'onclick="javascript:$(\'form_aclusers\').submit();"';
            $links = $objLink->show();

            $objLink = new link($this->uri(array(
                'action' => 'manage_acl_users',
                'aclId' => $aclId,
            ), 'sitepermissions'));
            $objLink->link = $cancelLabel;
            $links .= '&#160;|&#160;'.$objLink->show();

            $objTable->startRow();
            $objTable->addCell('&#160;'.$group, '', '', '', 'even', '');
            $objTable->addCell($links, '', '', 'center', 'even', '');
            $objTable->endRow();
        }
        
        if($arrGroups === FALSE){
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
            $objTable->endRow();
        }else{
            foreach($arrGroups as $group){
                $groupId = $group['id'];
                $groupName = $group['typeid'];
            
                $links = '';
                $objLink = new link($this->uri(array(
                    'action' => 'manage_acl_users',
                    'mode' => 'editgroup',
                    'aclId' => $aclId,
                    'id' => $groupId,
                ), 'sitepermissions'));
                $objLink->link = $editLabel;
                $links = $objLink->show();
        
                $objLink = new link($this->uri(array(
                    'action' => 'delete_acl_user',
                    'aclId' => $aclId,
                    'id' => $groupId,
                ), 'sitepermissions'));
                $objLink->link = $deleteLabel;
                $objLink->extra = 'onclick="javascript:return confirm(\''.$confirmLabel.'\')"';
                $links .= '&#160;|&#160;'.$objLink->show();
        
                if($id == $groupId){
                    $type = 1;
                    $class = 'even';
                    $objInput = new textinput('id', $groupId, 'hidden', '');
                    $groupName = $objInput->show();
                
                    $objInput = new textinput('group', $group['typeid'], '', '60');
                    $groupName .= $objInput->show();
                    
                    $objLink = new link('#');
                    $objLink->link = $saveLabel;
                    $objLink->extra = 'onclick="javascript:$(\'form_aclusers\').submit();"';
                    $links = $objLink->show();

                    $objLink = new link($this->uri(array(
                        'action' => 'manage_acl_users',
                        'aclId' => $aclId,
                    ), 'sitepermissions'));
                    $objLink->link = $cancelLabel;
                    $links .= '&#160;|&#160;'.$objLink->show();
                }                           
                $objTable->startRow();
                $objTable->addCell('&#160;'.$groupName, '', '', '', '', '');
                $objTable->addCell($links, '', '', 'center', '', '');
                $objTable->endRow();
            }
        }
        $string .= $objTable->show().'<br />';

        $objTable = new htmltable();
        $objTable->id = 'userList';
        $objTable->css_class = 'sorttable';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->row_attributes = ' name="row_'.$objTable->id.'"';
        // create table
        $objTable->startRow();
        $objTable->addCell('&#160;'.$nameLabel, '', '', '', 'heading', '');
        $objTable->addCell('&#160;'.$surnameLabel, '', '', '', 'heading', '');
        $objTable->addCell('&#160;', '15%', '', '', 'heading', '');
        $objTable->endRow();
        

        if($mode == 'adduser'){         
            $type = 2;

            // create table
            $objInput = new textinput('name', '', '', '60');
            $objInput->extra = 'onfocus="javascript:$(\'input_surname\').value=\'\';" onclick="javascript:var url = \'index.php\';var input = \'input_name\';var target = \'nameDiv\';var pars = \'module=sitepermissions&amp;action=listusers&amp;search=name\';new Ajax.Autocompleter(input, target, url, {parameters: pars});"';
            $name = $objInput->show();
                
            $objLayer = new layer();
            $objLayer->id = 'nameDiv';
            $objLayer->cssClass = 'autocomplete';
            $nameLayer = $objLayer->show();

            $objInput = new textinput('surname', '', '', '60');
            $objInput->extra = 'onfocus="javascript:$(\'input_name\').value=\'\';" onclick="javascript:var url = \'index.php\';var input = \'input_surname\';var target = \'surnameDiv\';var pars = \'module=sitepermissions&amp;action=listusers&amp;search=surname\';new Ajax.Autocompleter(input, target, url, {parameters: pars});"';
            $surname = $objInput->show();
                
            $objLayer = new layer();
            $objLayer->id = 'surnameDiv';
            $objLayer->cssClass = 'autocomplete';
            $surnameLayer = $objLayer->show();

            // set up hidden userid input
            $objInput = new textinput('userid', '', 'hidden', '');
            $useridInput = $objInput->show();

            $objLink = new link('#');
            $objLink->link = $saveLabel;
            $objLink->extra = 'onclick="javascript:$(\'form_aclusers\').submit();"';
            $links = $objLink->show();

            $objLink = new link($this->uri(array(
                'action' => 'manage_acl_users',
                'aclId' => $aclId,
            ), 'sitepermissions'));
            $objLink->link = $cancelLabel;
            $links .= '&#160;|&#160;'.$objLink->show();
                
            $objTable->startRow();
            $objTable->addCell('&#160;'.$name.$nameLayer, '', '', '', 'even', '');
            $objTable->addCell('&#160;'.$surname.$surnameLayer.$useridInput, '', '', '', 'even', '');
            $objTable->addCell($links, '', '', 'center', 'even', '');
            $objTable->endRow();
        }else{
            $objLink = new link($this->uri(array(
                'action' => 'manage_acl_users',
                'mode' => 'adduser',
                'aclId' => $aclId,
            ), 'sitepermissions'));
            $objLink->link = $addUserLabel;
            $string .= $objLink->show();
        }
        
        if($arrUsers === FALSE){
            $objTable->startRow();
            $objTable->addCell($noRecordsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
            $objTable->endRow();
        }else{
            foreach($arrUsers as $user){
                $userId = $user['id'];
                $name = $this->objUser->getFirstname($user['typeid']);
                $surname = $this->objUser->getSurname($user['typeid']);
            
                $class = '';
                $objLink = new link($this->uri(array(
                    'action' => 'delete_acl_user',
                    'aclId' => $aclId,
                    'id' => $userId,
                ), 'sitepermissions'));
                $objLink->link = $deleteLabel;
                $objLink->extra = 'onclick="javascript:return confirm(\''.$confirmLabel.'\')"';
                $link = $objLink->show();
        
                $objTable->startRow();
                $objTable->addCell('&#160;'.$name, '', '', '', '', '');
                $objTable->addCell('&#160;'.$surname, '', '', '', '', '');
                $objTable->addCell($link, '', '', 'center', '', '');
                $objTable->endRow();
            }
        }
        $string .= $objTable->show();

        $objForm = new form('aclusers', $this->uri(array(
            'action' => 'save_acl_users',
            'aclId' => $aclId, 
            'type' => $type,   
        ), 'sitepermissions'));
        $objForm->addToForm($string);
        $form = $objForm->show();
               
        $objLayer = new layer();
        $objLayer->padding = '10px';
        $objLayer->addToStr($form);
        $str = $objLayer->show();
        
        return $str;
    }

    /**
    * This method is called by Ajax for the User list
    * Puts a response with the user list.
    *
    * @access public
    * @param string $search: The field to search
    * @param string $criteria: The value to search for
    * @return
    */
    public function searchList($search, $criteria)
    {
        $arrUserList = $this->objDbperm->getUsers($search, $criteria);
        if ($arrUserList != FALSE) {
            $response = '<ul>';
            foreach ($arrUserList as $user) {
                $response .= '<li onclick="javascript:$(\'input_userid\').value=\''.$user['userid'].'\'"><strong>';
                if($search == 'firstName'){
                    $response .= $user['firstname'].'&#160;'.$user['surname'];
                }else{
                    $response .= $user['surname'].',&#160;'.$user['firstname'];
                }
                $response .= '</strong></li>';
            }
            $response .= '</ul>';            
        } else {
            $response = '';
        }
        echo $response;
    }
}
?>