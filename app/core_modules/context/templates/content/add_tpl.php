<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2003
 **/
//$bodyParams = "onload=\"HTMLArea.replaceAll();\"";
//$this->setVarByRef('bodyParams', $bodyParams);
  $this->setVar('footerStr',$this->getContextLinks().'&nbsp;'.$this->getContentLinks());

$this->loadClass('textarea','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$editor=&$this->newObject('htmlarea','htmlelements');
$objLink = & $this->newObject('link','htmlelements');
$h3=&$this->newObject('htmlheading','htmlelements');
$multiTab  = & $this->newObject('multitabbedbox','htmlelements');
$table  = & $this->newObject('htmltable','htmlelements');


$this->objDBContentNodes->resetTable();
$line=$this->objDBContentNodes->getRow('id',$this->nodeId);

        switch($this->getParam('action')) {
            case 'editnode':
                $addType='editnode';
                $h3->str='Edit Node';
                break;
            case 'addchildnode':
                $h3->str=$this->objLanguage->languageText("mod_context_addchildnode",'context').'&nbsp;'.$line['title'];
                break;
            case 'addnode':
                $h3->str=$this->objLanguage->languageText("mod_context_addsiblingnode",'context').'&nbsp;'.$line['title'];
                break;
        }





        if ($this->getParam('action')=='editnode')
            $action='save_edit';
        else
            $action='save_add';

        $this->objHelp=& $this->getObject('helplink','help');
        //Add the help link to the output
       $strTitle = '';
       $strBody = '';
       $strMenuText = '';
       $strScript = '';

        if ($this->getParam('action')=='editnode') {
            $line=$this->objDBContentNodes->getRow('id',$this->nodeId);
            $body=$this->objDBContentNodes->getBody($this->nodeId);
            $menuText=$this->objDBContentNodes->getMenuText($this->nodeId);

            $strTitle =  stripslashes($line['title']);
            $strMenuText = stripslashes($menuText);
            $strBody = stripslashes($body);
            $strScript = stripslashes($line['script']);
        }

        //the form
        $objForm= new form('addfrm');
        $objForm->setAction($this->uri(array('action'=>$action,'nodeId'=>$this->getParam('nodeid'))));
        $objForm->setDisplayType(1);

        //the hidden id field
        $table->startRow();
        $nodeId=new textinput('nodeId');
        $nodeId->setValue($this->nodeId);
        $nodeId->fldType='hidden';
        $table->addCell($nodeId->show());
        $table->endRow();

        //the addtype
        $table->startRow();
        $mode=new textinput('addtype');
        $mode->setValue($addType);
        $mode->fldType='hidden';
        $table->addCell($mode->show());
        $table->endRow();

        //the label
        $table->startRow();
        $objTImenutext=new textinput('label');
        $objTImenutext->size = '97';
         $objTImenutext->setValue($strMenuText);
        $table->addCell($this->objLanguage->languageText("mod_contextadmin_menutext",'context'));
        $table->addCell($objTImenutext->show());
        $objForm->addRule('label',$this->objLanguage->languageText("mod_context_errsuppmenutext",'context'),'required');
        $objForm->addRule(array('name'=>'label','length'=>100),$this->objLanguage->languageText("mod_context_errmenutextminlength",'context'),'maxlength');
        $table->endRow();

         //the title
        $table->startRow();
        $objTItitle=new textinput('nodetitle');
        $objTItitle->size='97';
        $objTItitle->setValue($strTitle);
        $table->addCell($this->objLanguage->languageText("word_title"));
        $table->addCell($objTItitle->show());
        $objForm->addRule('nodetitle',$this->objLanguage->languageText("mod_context_errsupptitle",'context'),'required');
        $table->endRow();

       //the editor with body
        $table->startRow();
        $editor->setName('body');
        $editor->context = TRUE;
        $editor->setContent($strBody);
        $helpstr = "&nbsp;".$this->objHelp->show('mod_html_help_editor');
        $table->addCell($this->objLanguage->languageText("mod_context_pagecontents",'context'). $helpstr );
        $table->addCell($editor->show());


        $objButton=new button('save');
        $objButton->setToSubmit();
        $objButton->setValue($this->objLanguage->languageText("mod_contextadmin_save",'context'));
        $table->startRow();
        $table->addCell('');
        $table->addCell($objButton->show(),'','','center');
        $table->endRow();

         //dublin core metadata
         //==============================
        $objLink->link = $this->objLanguage->languageText("mod_dublin_dcm",'context');
        $objLink->extra = ' onclick="toggle(\'dublincore\');return false;" ';
        $objLink->href='#';

        //the hidden dublin toggle field
        $hasmetadata=new textinput('hasmetadata');
        $hasmetadata->setValue($this->nodeId);
        $hasmetadata->fldType='hidden';
       //==================================

       //================================
       //JAVASCRIPT
       $scripteditor = new textarea('script','',20,80);
       $scripteditor->value = stripslashes($strScript);
       $tab3 = '<center>'.$this->objLanguage->languageText("mod_context_javascript_help",'context').$scripteditor->show().$objButton->show().'</center>';
       //==============================




        //setup tabbed box

       $tab1=$table->show();
           if($addType=='editnode'){
            $mode='edit';
        } else {
            $mode = 'add';
         }
        $tab2='<center>'.$this->objDublinCore->getInputs($this->nodeId, $mode);
        $tab2.=$hasmetadata->show();
        $tab2.=$objButton->show().'</center>';

         $multiTab->width ='800px';
         $multiTab->height = '470px';
         $multiTab->addTab(array('name'=>$this->objLanguage->languageText("mod_context_content",'context'),'url'=>'http://localhost','content' => $tab1,'default' => true));
         $multiTab->addTab(array('name'=>$this->objLanguage->languageText("mod_dublin_dcm",'context'),'url'=>'http://localhost','content' => $tab2));
         $multiTab->addTab(array('name'=>$this->objLanguage->languageText("mod_context_javascript",'context'),'url'=>'http://localhost','content' => $tab3));

       //=================================
       //CREATIVE COMMOMS TAB
       $objModule =& $this->getObject('modules','modulecatalogue');
       if($objModule->checkIfRegistered('creativecommons','creativecommons'))
       {
           $objCreativeCommons = & $this->newObject('dbcreativecommons','creativecommons');
		   $objConfig = & $this->newObject('config', 'config');
		   $objSkin = & $this->newObject('skin', 'skin');

           $ccStr ='<center><h1>'.$this->objLanguage->languageText("mod_creativecommons_title",'context').'</h1>';
           $ccStr .= $objCreativeCommons->getLisences($nodeId);
           //user the creative commons engine to generate the license
           $ccStr .= '<iframe width="80%" height="400" src="http://creativecommons.org/license/?partner={partner}&exit_url=http://'.$_SERVER['SERVER_NAME'].$objConfig->siteRoot().'index.php?deed_url=[deed_url]%26license_button=[license_button]%26license_url=[license_url]%26license_name=[license_name]%26module=creativecommons%26action=ccresults&stylesheet=http://'.$_SERVER['SERVER_NAME'].$objConfig->siteRoot().$objSkin->getSkinUrl().'kewl_css.php&partner_icon_url=http://creativecommons.org/images/public/somerights20.gif"  />';
           $ccStr .= $objButton->show().'</center>';
           $multiTab->addTab(array('name'=>$this->objLanguage->languageText("mod_creativecommons_name",'context'),'url'=>'http://localhost','content' => $ccStr));
       }

       //==================================


        $objForm->addToForm($multiTab);
        //$objForm->addToForm($objButton);

        $center =  $h3->show();
        $center .=  $objForm->show();

 $this->contentNav = & $this->newObject('layer','htmlelements');
    $this->contentNav->id = "content2";
    $this->contentNav->height = '600px';
    $this->contentNav->addToStr($center);
    echo $this->contentNav->show();

?>


