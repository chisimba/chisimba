<?php

class giftops extends object {

    /**
     * Initialises classes to be used
     */
    public function init() {
        // importing classes
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass("layer", "htmlelements");
        $this->loadClass("mouseoverpopup", "htmlelements");
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('treemenu', 'tree');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('treenode', 'tree');
        $this->loadClass('htmllist', 'tree');
        $this->loadClass('htmldropdown', 'tree');
        $this->loadClass('dhtml', 'tree');

        $this->objLanguage = $this->getObject("language", "language");
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objDepartments = $this->getObject('dbdepartments', 'gift');
        $this->divisionLabel = $this->objSysConfig->getValue('DIVISION_LABEL', 'gift');
        $this->rootTitle = $this->objSysConfig->getValue('ROOT_TITLE', 'gift');
        $this->objUser = $this->getObject("user", "security");
        $this->objDbGift = $this->getObject('dbgift');
    }

    /**
     * Builds the form for the addition of a new gift or editing an
     * existing gift from the database.
     * @param string $rname
     * @param array $data
     * @return string
     */
    public function displayForm($data, $action) {
        $extbase = '<script language="JavaScript" src="' . $this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js', 'htmlelements') . '" type="text/javascript"></script>';
        $extalljs = '<script language="JavaScript" src="' . $this->getResourceUri('ext-3.0-rc2/ext-all.js', 'htmlelements') . '" type="text/javascript"></script>';
        $extallcss = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'htmlelements') . '"/>';
        $this->appendArrayVar('headerParams', $extbase);
        $this->appendArrayVar('headerParams', $extalljs);
        $this->appendArrayVar('headerParams', $extallcss);

        // set up language items
        $dnlabel = $this->objLanguage->languageText('mod_addedit_donor', 'gift') . ":";
        $rnlabel = $this->objLanguage->languageText("mod_addedit_receiver", "gift") . ":";
        $gnlabel = $this->objLanguage->languageText("mod_addedit_giftname", "gift") . ":";
        $descriplabel = $this->objLanguage->languageText("mod_addedit_description", "gift") . ":";
        $gvaluelabel = $this->objLanguage->languageText("mod_addedit_value", "gift") . ":";

        if (sizeof($data) == 0) {
            $objForm = new form('contactdetailsform', $this->uri(array('action' => 'submitAdd')));
        } else {
            $objForm = new form('contactdetailsform', $this->uri(array('action' => 'submitEdit', 'id' => $data['id'])));
        }

        $mainjs = "Ext.onReady(function(){
            new Ext.ToolTip({
            target: 'donortip',
            html: '" . $this->objLanguage->languageText('mod_add_donortip', 'gift') . "',
            width: 200
            })

            new Ext.ToolTip({
            target: 'giftnametip',
            html: '" . $this->objLanguage->languageText('mod_add_giftnametip', 'gift') . "',
            width: 200
            })

            new Ext.ToolTip({
            target: 'descriptiontip',
            html: '" . $this->objLanguage->languageText('mod_add_descriptiontip', 'gift') . "',
            width: 200
            })

            new Ext.ToolTip({
            target: 'valuetip',
            html: '" . $this->objLanguage->languageText('mod_add_valuetip', 'gift') . "',
            width: 200
            })

            Ext.QuickTips.init();

        });";

        //Setting up input text boxes
        $objInputh1 = new textinput('dnvalue', $data['donor'], '', '74');
        $dnvalue = $objInputh1->show() . "<br><br>";

        $hiddenid = "<input type=\"hidden\" name=\"id\" value=\"" . $data['id'] . "\" />";

        $objInputh2b = new textinput('gname', $data['giftname'], '', '74');
        $gnvalue = $objInputh2b->show() . "<br><br>";

        $objInputh3a = new textarea('descripvalue', $data['description'], 15, 55);
        $descripvalue = $objInputh3a->show() . "<br><br>";

        $objInputh3b = new textinput('gvalue', $data['value'], '', '30');
        $gvalue = $objInputh3b->show() . "<br><br>";

        //Buttons OK and cancel
        $this->objSubmitButton = new button('Submit');
        $this->objSubmitButton->setValue($this->objLanguage->languageText("mod_addedit_btnSave", "gift"));
        $this->objSubmitButton->setToSubmit();

        $this->objResetButton = new button('Reset');
        $this->objResetButton->setValue($this->objLanguage->languageText("mod_addedit_btnReset", "gift"));
        $this->objResetButton->setToReset();

        $this->objCancelButton = new button('cancel');
        $this->objCancelButton->setValue($this->objLanguage->languageText("mod_addedit_btnCancel", "gift"));

        if ($action == 'add')
            $this->objCancelButton->setOnClick("window.location='" . $this->uri(NULL) . "';");
        else
            $this->objCancelButton->setOnClick("window.location='" . $this->uri(array('action' => 'result')) . "';");

        //Defining table
        $objTable = new htmltable();
        $objTable->cellpadding = '2';
        $objTable->border = '0';

        $width = 100;
        $valign = 'top';
        $objTable->startRow();
        $objTable->addCell($dnlabel);
        $objTable->addCell($dnvalue);
        $objTable->addCell('<div id="donortip">[?]</div>', $width, 'top', 'right');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($gnlabel);
        $objTable->addCell($gnvalue);
        $objTable->addCell('<div id="giftnametip">[?]</div>', $width, 'top', 'right');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($descriplabel, '', 'top');
        $objTable->addCell($descripvalue, '', '', '', '', 'colspan="3"');
        $objTable->addCell('<div id="descriptiontip">[?]</div>', $width, NULL, 'left');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($gvaluelabel);
        $objTable->addCell($gvalue);
        $objTable->addCell('<div id="valuetip">[?]</div>', $width, 'top', 'right');
        $objTable->endRow();

        $infoTable = $objTable->show();

        //Setting Up Form by adding all objects....
        $objForm->addRule("dnvalue", $this->objLanguage->languageText("mod_addedit_donorrequired", "gift"), "required");
        $objForm->addRule("gname", $this->objLanguage->languageText("mod_addedit_giftnamerequired", "gift"), "required");
        $objForm->addRule("descripvalue", $this->objLanguage->languageText("mod_addedit_descriptionrequired", "gift"), "required");
        $objForm->addRule("gvalue", $this->objLanguage->languageText("mod_addedit_giftvaluerequired", "gift"), "required");
        $objForm->addRule("gvalue", $this->objLanguage->languageText("mod_addedit_giftvaluenumeric", "gift"), "numeric");

        $objForm->addToForm($infoTable);

        $objForm->addToForm('<br/> ');
        $objForm->addToForm($this->objSubmitButton);
        $objForm->addToForm($this->objResetButton);
        $objForm->addToForm($this->objCancelButton);
        $composeForm = $objForm->show();

        $pageData = $composeForm;

        //Defining Layer
        $objLayer = new layer();
        $objLayer->padding = '10px';
        $objLayer->str = $pageData;
        $pageLayer = $objLayer->show() . '<script type="text/javascript">' . $mainjs . '</script>';

        return $pageLayer;
    }

    function sendEmail($subject, $body) {

        $objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $adminemail = $objSysconfig->getValue('adminmail', 'gifts');
        $objMailer = $this->getObject('mailer', 'mail');
        $to = array($adminemail, 'ana.m.ferreira@wits.ac.za');
        $objMailer->setValue('to', $to);
        $objMailer->setValue('from', 'noreply@wits.ac.za');
        $objMailer->setValue('subject', $subject);
        $objMailer->setValue('body', $body);
        $objMailer->send(FALSE);
    }

    function getTree($treeType='dhtml', $selected='', $treeMode='side', $action='') {
        $depts = $this->objDepartments->getDepartments();

        $objDbGift = $this->getObject("dbgift");


        if ($selected == '') {
            $selected = $this->objDepartments->getDepartmentName($this->getSession("departmentid"));
        }

        $icon = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';
        $cssClass = "";
        if ($treeType == 'htmldropdown') {

            $allFilesNode = new treenode(array('text' => $this->rootTitle, 'link' => '-1'));
        } else {
            $allFilesNode = new treenode(array('text' => $this->rootTitle, 'link' => $this->uri(array('action' => 'viewgifts')), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
        }



        $refArray = array();
        $refArray[$this->rootTitle] = & $allFilesNode;

        //Create a new tree
        $menu = new treemenu();


        if (count($depts) > 0) {
            foreach ($depts as $dept) {
                $folderText = $dept['name'];

                $folderShortText = substr($dept['name'], 0, 200) . '...';
                if ($this->objUser->isAdmin()) {
                    $initialCount = $this->objDbGift->getGiftCountByDepartment($dept['id']);
                    $displayCount = $initialCount;

                    //then get total count for child depts, if any
                    if ($dept['level'] == '1') {
                        $childcount = $this->getChildGiftCount($dept['path']);
                        $displayCount = $initialCount + $childcount;

                    }
                    $folderShortText = "(" . $displayCount . ")&nbsp;" . $folderShortText;
                }
                if ($dept['name'] == $selected) {
                    $folderText = '<strong>' . $folderText . '</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }
                if ($treeType == 'htmldropdown') {
                    // echo "css class == $cssClass<br/>";
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $dept['id'], 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                } else {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $this->uri(array('action' => 'home', 'departmentid' => $dept['id'], 'departmentname' => $dept['name'])), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                }

                $parent = $this->getParent($dept['path']);
                // if(!($parent == $this->rootTitle)){
                //echo $folderText . " parent== " . $parent . " path ==" . $dept['path'] . " level == " . $dept['level'] . '<br/>';
                // }
                if (array_key_exists($parent, $refArray)) {
                    $refArray[$parent]->addItem($node);
                }

                $refArray[$dept['path']] = & $node;


                //$allFilesNode->addItem($node);
            }
        }

        $menu->addItem($allFilesNode);
        if ($treeType == 'htmldropdown') {
            $treeMenu = &new htmldropdown($menu, array('inputName' => 'selecteddepartment', 'id' => 'input_parentfolder', 'selected' => $selected));
        } else {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
            $this->setVar('pageSuppressXML', TRUE);
            $objSkin = & $this->getObject('skin', 'skin');
            $treeMenu = &new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        }

        return $treeMenu->getMenu();
    }

    function getChildGiftCount($path) {
        $count = 0;
        $depts = $this->objDepartments->getDepartmentsLike($path);
        foreach ($depts as $dept) {
            if ($path != $dept['path']) {
                $count += $this->objDbGift->getGiftCountByDepartment($dept['id']);
            }
        }

        return $count;
    }

    function getParent($path) {

        $parent = "";
        $parts = explode("/", $path);
        $count = count($parts);
        for ($i = 0; $i < $count - 1; $i++) {
            if ($parent == '') {
                $parent.= $parts[$i];
            } else {
                $parent.="/" . $parts[$i];
            }
        }
        if ($parent == '') {
            $parent = $this->rootTitle;
        }
        return $parent;
    }

    function showCreateDepartmentForm($name='') {

        $form = new form('createdepartment', $this->uri(array('action' => 'createdepartment')));
        $textinput = new textinput('departmentname');
        $textinput->value=$name;
        $label = new label('Name of ' . $this->divisionLabel . ': ', 'input_departmentname');
        $form->addToForm("<br/>Create in " . $this->getTree('htmldropdown'));
        $form->addToForm(' &nbsp; ' . $label->show() . $textinput->show());


        $button = new button('create', 'Create ' . $this->divisionLabel);
        $button->setToSubmit();

        $form->addToForm('<br/>' . $button->show());

        $fs = new fieldset();
        $fs->setLegend($this->divisionLabel);
        $fs->addContent($form->show());
        return $fs->show();
    }

    function showSearchByDatesForm($action) {

        $form = new form('searchbydatesform', $this->uri(array('action' => $action)));

        $objDateTime = $this->getObject('dateandtime', 'utilities');
        $objDatePicker = $this->newObject('datepicker', 'htmlelements');
        $objDatePicker->name = 'date_from';
        $content = "Date From: &nbsp;" . $objDatePicker->show();

        $objDatePicker = $this->newObject('datepicker', 'htmlelements');
        $objDatePicker->name = 'date_to';
        $content .= "Date To: &nbsp;" . $objDatePicker->show();

        $form->addToForm($content);
        $button = new button('view', 'View');
        $button->setToSubmit();

        $form->addToForm(' ' . $button->show());

        $fs = new fieldset();
        $fs->setLegend("View by date");
        $fs->addContent($form->show());
        return $fs->show();
    }

    /**
     * formats money
     * @param <type> $number
     * @param <type> $fractional
     * @return <type> 
     */
    function formatMoney($number, $fractional=false) {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return $number;
    }

    /**
     * allows the user to donwload the selected file
     * @param <type> $filename
     */
    function downloadFile($filepath, $filename) {

        //check if user has access to the parent folder before accessing it

        $baseDir = $this->objSysConfig->getValue('UPLOADS_DIR', 'wicid');
        // Detect missing filename
        if (!$filename && !$filepath)
            die("I'm sorry, you must specify a file name to download.");

        // Make sure we can't download files above the current directory location.
        if (eregi("\.\.", $filepath))
            die("I'm sorry, you may not download that file.");
        $file = str_replace("..", "", $filepath);

        // Make sure we can't download .ht control files.
        if (eregi("\.ht.+", $filepath))
            die("I'm sorry, you may not download that file.");

        // Combine the download path and the filename to create the full path to the file.
        $file = $baseDir . '/' . $filepath;

        // Test to ensure that the file exists.
        if (!file_exists($file))
            die("I'm sorry, the file doesn't seem to exist.");

        // Extract the type of file which will be sent to the browser as a header
        $type = filetype($file);

        // Get a date and timestamp
        $today = date("F j, Y, g:i a");
        $time = time();


        // Send file headers
        header("Content-type: $type");
        header("Content-Disposition: attachment;filename=" . urlencode($filename));
        header('Pragma: no-cache');
        header('Expires: 0');

        // Send the file contents.
        readfile($file);
    }

}

?>
