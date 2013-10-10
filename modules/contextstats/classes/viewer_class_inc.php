<?php
  /**
   *
   *
   * PHP version 5.1.0+
   *
   * This program is free software; you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation; either version 2 of the License, or
   * (at your option) any later version.
   * This program is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   * GNU General Public License for more details.
   * You should have received a copy of the GNU General Public License
   * along with this program; if not, write to the
   * Free Software Foundation, Inc.,
   * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
   *
   * @category  Chisimba
   * @package   Contextstats
   * @author    Qhamani Fenama
   * @copyright 2010 AVOIR
   * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
   * @version
   * @link      http://avoir.uwc.ac.za
   */
  
  // security check - must be included in all scripts
  if (!/**
   * The $GLOBALS is an array used to control access to certain constants.
   * Here it is used to check if the file is opening in engine, if not it
   * stops the file from running.
   *
   * @global entry point $GLOBALS['kewl_entry_point_run']
   * @name   $kewl_entry_point_run
   *
   */
  $GLOBALS['kewl_entry_point_run'])
  {
      die("You cannot view this page directly");
  }
  // end security check
  
  class viewer extends object
  {
      /**
       *
       * @var string $objLanguage String object property for holding the
       * language object
       * @access public
       *
       */
      public $objLanguage;
      public $objConfig;
      public $objSysConfig;
      public $objWashout;
      public $objUser;
      
      /**
       * Constructor
       *
       * @access public
       *
       */
      public function init()
      {
          $this->objLanguage = $this->getObject('language', 'language');
          $this->objConfig = $this->getObject('altconfig', 'config');
          $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
          $this->objWashout = $this->getObject('washout', 'utilities');
          $this->objUser = $this->getObject('user', 'security');
          $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
      }
      
      
      public function renderOutputForBrowser($records)
      {
          $ret = null;
          
          $objTable = $this->newObject('htmltable', 'htmlelements');
          
          if (count($records) == 0) {
              $objTable->startRow();
              $objTable->addCell($this->objLanguage->code2Txt('mod_contextstats_phrasenodata', 'contextstats'), '99%', 'center', 'center', '', '');
              $objTable->endRow();
          } else {
              foreach ($records as $record) {
                  $objTable->startRow();
                  $objTable->addCell($record['contextcode'], '8%', 'center', 'left', '', '');
                  $objTable->addCell(' ', '2%', 'center', 'left', '', '');
                  $objTable->addCell($record['title'], '22%', 'center', 'left', '', '');
                  $objTable->addCell(' ', '3%', 'center', 'left', '', '');
                  
                  $wsdl = $this->objSysConfig->getValue('WSDL', 'contextstats');
                  if ($wsdl != 'FALSE') {
                      $objutil = $this->getObject('utility', 'contextstats');
                      $data = $objutil->getDeptartment($record['contextcode']);
                      $dept = $data['department'];
                      if ($dept == null) {
                          $dept = $this->objLanguage->code2Txt('mod_contextstats_phrasena', 'contextstats');
                      }
                      $objTable->addCell($dept, '22%', 'center', 'left', '', '');
                      $objTable->addCell(' ', '3%', 'center', 'left', '', '');
                  }
                  //get the e-tools
                  $objContextModules = $this->getObject('dbcontextmodules', 'context');
                  $contextModules = $objContextModules->getContextModules($record['contextcode']);
                  $str = '';
                  if (!$contextModules) {
                      $str = '<div class="warning">' . $this->objLanguage->code2Txt('mod_contextstats_phrasenotools', 'contextstats') . '</div>';
                  } else {
                      
                      foreach ($contextModules as $etool) {
                          $str .= ucwords($this->objLanguage->code2Txt('mod_' . $etool . '_name', $etool)) . ', ';
                      }
                      $str = htmlentities(substr($str, 0, -2));
                  }
                  
                  $str = '<div class="wrapperDarkBkg" >
          <div class="wrapperLightBkg">' . $str . '
          </div>
          </div><br/>';
                  $objTable->addCell($str, '40%', 'center', 'left', '', '');
                  $objTable->endRow();
              }
          }
          //shows the array in a table
          $ret = $objTable->show();
          header("Content-Type: text/html;charset=utf-8");
          return $ret . '<br/>';
      }
      
      public function renderTopBoxen()
      {
      }
      
      public function renderLeftBoxen()
      {
      }
      
      public function renderRightBoxen()
      {
      }
  }
?>
