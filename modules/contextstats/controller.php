<?php
  /**
   * Context Statistics Controller
   *
   * Controller class for the Context Statistic in Chisimba
   *
   * PHP version 5
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
   * @package   contextstats
   * @author    Qhamani Fenama <qfenama@gmail.com/qfenama@uwc.ac.za>
   * @copyright 2010 Qhamani Fenama
   * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
   * @version   Release: @package_version@
   * @link      http://avoir.uwc.ac.za
   * @see       packages
   */
  // security check - must be included in all scripts
  if (!/**
   * Description for $GLOBALS
   * @global entry point $GLOBALS['kewl_entry_point_run']
   * @name   $kewl_entry_point_run
   */
  $GLOBALS['kewl_entry_point_run'])
  {
      die("You cannot view this page directly");
  }
  // end security check
  
  
  /**
   * Context Statistics Controller
   *
   * Controller class for the Context Creation/Management in Chisimba
   *
   * @category  Chisimba
   * @package   contextstats
   * @author    Qhamani Fenama <qfenama@gmail.com/qfenama@uwc.ac.za>
   * @copyright 2010 Qhamani Fenama
   * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
   * @version   Release: @package_version@
   * @link      http://avoir.uwc.ac.za
   * @see       packages
   */
  class contextstats extends controller
  {
      /**
       * Constructor
       */
      public function init()
      {
          $this->objUser = $this->getObject('user', 'security');
          $this->objContext = $this->getObject('dbcontext', 'context');
          $this->objUserContext = $this->getObject('usercontext', 'context');
          $this->objLanguage = $this->getObject('language', 'language');
          $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
          $this->fromdate = $this->getParam('fromdate');
          $this->todate = $this->getParam('todate');
          $this->pagesize = '';
          $this->fromdate = '';
          $this->todate = '';
       }
      
      
      /**
       * Standard Dispatch Function for Controller
       *
       * @access public
       * @param string $action Action being run
       * @return string Filename of template to be displayed
       */
      public function dispatch($action)
      {
          // check that the current user has access to do this stuff.
          $hasAccess = $this->objUser->isAdmin();
          if (!$hasAccess) {
              // This module is very sensitive, so get the intruder out asap, with no way to get back here!
              throw new customException($this->objLanguage->languageText("mod_contextstats_insufficientperms", "contextstats"));
          }
          
          $pagesize = $this->getParam('pagesize', 15);
          $fromdate = $this->getParam('fromdate', $this->getMinDate());
          $todate = $this->getParam('todate', date('Y-m-d'));
          
          if ($pagesize != null && $action != 'viewallajax') {
              $this->setSession('pages', $pagesize);
          }
          
          if ($fromdate != null && $action != 'viewallajax') {
              $this->setSession('fromdate', $fromdate);
          }
          
          if ($todate != null && $action != 'viewallajax') {
              $this->setSession('todate', $todate);
          }
          
          $this->pagesize = $this->getSession('pages');
          $this->fromdate = $this->getSession('fromdate');
          $this->todate = $this->getSession('todate');
          
          $this->setVarByRef('pagesize', $this->pagesize);
          $this->setVarByRef('fromdate', $this->fromdate);
          $this->setVarByRef('todate', $this->todate);
          
          // Method to set the layout template for the given action
          $this->setLayoutTemplate('contextstats_layout_tpl.php');
          
          /*
           * Convert the action into a method (alternative to
           * using case selections)
           */
          $method = $this->getMethod($action);
          /*
           * Return the template determined by the method resulting
           * from action
           */
          return $this->$method();
      }
      
      /**
       *
       * Method to convert the action parameter into the name of
       * a method of this class.
       *
       * @access private
       * @param string $action The action parameter passed byref
       * @return string the name of the method
       *
       */
      private function getMethod(&$action)
      {
          if ($this->validAction($action)) {
              return '__' . $action;
          } else {
              return '__home';
          }
      }
      
      /**
       *
       * Method to check if a given action is a valid method
       * of this class preceded by double underscore (__). If it __action
       * is not a valid method it returns FALSE, if it is a valid method
       * of this class it returns TRUE.
       *
       * @access private
       * @param string $action The action parameter passed byref
       * @return boolean TRUE|FALSE
       *
       */
      private function validAction(&$action)
      {
          if (method_exists($this, '__' . $action)) {
              return true;
          } else {
              return false;
          }
      }
      
      /**
       * Context Statistics Home
       */
      private function __home()
      {
          $start = 0;
          $count = count($this->getContextRange($start, $this->pagesize, $this->fromdate, $this->todate));
          $pages = ceil($count / $this->pagesize);
          $this->setVarByRef('pages', $pages);
          header("Content-Type: text/html;charset=utf-8");
          return 'contextstats_tpl.php';
      }
      
      private function __viewallajax()
      {
          $page = intval($this->getParam('page', 0));
          if ($page < 0) {
              $page = 0;
          }
          $start = $page * $this->pagesize;
          $records = $this->getContextRange($start, $this->pagesize, $this->fromdate, $this->todate);
          $this->setVarByRef('records', $records);
          
          header("Content-Type: text/html;charset=utf-8");
          return 'contextstats_ajax_tpl.php';
      }
      
      private function getContextRange($min, $max, $fromdate = null, $todate = null)
      {
          $where = '';
          if (!empty($fromdate) && !empty($todate)) {
              $fromdate = $fromdate . ' 00:00:00';
              $todate = $todate . ' 23:59:59';
              $where .= " WHERE lastaccessed > '{$fromdate}' AND lastaccessed < '{$todate}'";
          }
          $limit = '';
          if (!empty($min) && !empty($max)) {
              $limit .= " LIMIT {$min}, {$max}";
          }
          
          $sql = $where . " ORDER BY title ASC" . $limit;
          $result = $this->objContext->getAll($sql);
          return $result;
      }
      
      private function getMinDate()
      {
          $result = $this->objContext->getArray("SELECT min(lastaccessed) AS lastaccessed FROM tbl_context");
          $result = $result[0]['lastaccessed'];
          return substr($result, 0, 10);
      }
  }
?>
