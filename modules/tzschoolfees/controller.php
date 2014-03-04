<?php

// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global unknown $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for academic module
 *
 * @category  Chisimba
 * @package   SMIS Fees
 * @author    john richard
 * @date may 6 2011
 */
class tzschoolfees extends controller {

    public $lang;


    public function init() {

        $this->lang = $this->getObject('language','language');

    }

    public function dispatch($action) {

        //action area
$action = $this->getParam('action','main');
 $this->setLayoutTemplate('general_layout_tpl.php');

  //switch for selection according to the action perfomed
  switch ($action) {
      case 'add_details':
             return 'add_payment_details_tpl.php';
             break;

      case 'add':
            return 'after_confirm_tpl.php';
            break;


      case 'view_details':
            $view = $this->getParam('submit');

            if (!empty($view)) {

                    $regno = $this->getParam('reg_no');

                    $this->setVar('option', 'view');
                    $this->setVar('regno', $regno);
                    return 'view_payment_details_tpl.php';
                }
                else {
                    return 'view_payment_details_tpl.php';
                }

      default:
          return 'fee_home_page_tpl.php';
          break;
  }



        }

 public function  requiresLogin($action) {
        return false;
    }
}

?>