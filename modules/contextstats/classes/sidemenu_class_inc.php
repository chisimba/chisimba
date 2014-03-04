<?php
  /**
   * Class sidemenu extends object.
   * @package contextstats
   * @filesource sidemenu_class_inc.php
   */
  
  // security check - must be included in all scripts
  if (!$GLOBALS['kewl_entry_point_run']) {
      die("You cannot view this page directly");
  }
  
  /**
   * Side Menu Class
   *
   * @author Qhamani Fenama <qfenama@uwc.ac.za>
   * @copyright (c) 2010 University of the Western Cape
   * @package contextstats
   * @version 1
   */
  class sidemenu extends object
  {
      /**
       * Constructor method to instantiate objects and get variables
       */
      public function init()
      {
          $this->objLanguage = $this->getObject('language', 'language');
          $this->moduleCheck = $this->newObject('modules', 'modulecatalogue');
          $this->objUser = $this->getObject('user', 'security');
          $this->globalTable = $this->newObject('htmltable', 'htmlelements');
          $this->loadClass('fieldset', 'htmlelements');
          $this->loadClass('button', 'htmlelements');
          $this->loadClass('dropdown', 'htmlelements');
          $this->globalTable->cellpadding = 5;
          $this->globalTable->width = '99%';
          $objSkin = $this->getObject('skin', 'skin');
      }
      
      /**
       * This method returns the finished menu
       *
       * @return string $menu - the finished menu
       */
      public function show($pagesize, $fromdate, $todate)
      {
          $headerParams = $this->getJavascriptFile('ts_picker.js', 'htmlelements');
          $headerParams .= "<script>/*Script by Denis Gritcyuk: tspicker@yahoo.com
    Submitted to JavaScript Kit (http://javascriptkit.com)
    Visit http://javascriptkit.com for this script*/ 
    </script>";
          $this->appendArrayVar('headerParams', $headerParams);
          $objHead = new htmlheading();
          
          $objFields = new fieldset();
          $objFields->setLegend('<b>' . $this->objLanguage->languageText('mod_contextstats_phrasefilterreport', 'contextstats') . '</b>');
          
          $formAction = $this->URI(array(), 'contextstats');
          
          //Create a Form object
          $objForm = new form('filterform', $formAction);
          
          $objHead->str = $this->objLanguage->languageText('mod_contextstats_phraseactivefrom', 'contextstats') . ': ';
          $objHead->type = 5;
          
          $dateFromInput = $this->newObject('datepicker', 'htmlelements');
          $dateFromInput->setName('fromdate');
          $dateFromInput->setDefaultDate($fromdate);
          
          $output = $objHead->show();
          $output .= $dateFromInput->show();
          
          $objHead->str = $this->objLanguage->languageText('mod_contextstats_phraseactiveto', 'contextstats') . ': ';
          $objHead->type = 5;
          
          $this->loadClass('textinput', 'htmlelements');
          
          $txtdate = new textinput('todate');
          if (isset($todate)) {
              $txtdate->value = date('dd-mm-yyyy');
          }
          $output .= $objHead->show();
          
          //$strdiv = '<div id="disabledate">'.$dateToInput->show().'</div>';
          
          $output .= '<input id="datetext" type="text" disabled value="' . date('d F Y') . '" name="date"/>';
          
          $objHead->str = $this->objLanguage->languageText('mod_contextstats_phraseshow', 'contextstats') . ': ';
          
          $objHead->type = 5;
          
          $objdropdown = new dropdown();
          $objdropdown->addOption('15', '15');
          $objdropdown->addOption('30', '30');
          $objdropdown->addOption('50', '50');
          $objdropdown->setSelected($pagesize);
          $objdropdown->dropdown('pagesize');
          
          $output .= $objHead->show() . $objdropdown->show();
          
          //--- Create a submit button
          $objButton = new button('submit', $this->objLanguage->languageText('mod_contextstats_phrasefilterresults', 'contextstats'));
          // Set the button type to submit
          $objButton->setToSubmit();
          
          // Add the button to the form
          $output .= '<br />' . $objButton->show() . '<br/>';
          
          $objFields->addContent($output);
          
          $objForm->addToForm($objFields->show());
          
          return '<br/><br/>' . $objForm->show();
      }
      
      public function showSummary($pagesize, $fromdate, $todate)
      {
          $objFields = new fieldset();
          $objFields->setLegend('<b>' . $this->objLanguage->languageText('mod_contextstats_phrasereportsummary', 'contextstats') . '</b>');
          
          $objStr = $this->objLanguage->languageText('mod_contextstats_phraseactivefrom', 'contextstats') . ' : ' . $fromdate;
          $objFields->addContent('' . $objStr . '<br/>');
          
          $objStr = $this->objLanguage->languageText('mod_contextstats_phraseactiveto', 'contextstats') . ' : ' . $todate;
          $objFields->addContent('' . $objStr . '<br/>');
          
          $objStr = $this->objLanguage->languageText('mod_contextstats_phraseshow', 'contextstats') . ' ' . $pagesize;
          $objFields->addContent('' . $objStr . '<br/>');
          
          return $objFields->show();
      }
}
?>
