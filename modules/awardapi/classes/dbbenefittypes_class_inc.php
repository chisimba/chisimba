<?php
/**
 * AWARD data access class
 * 
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
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbbenefittypes_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core,api
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * AWARD benefits data access class
 * 
 * Class to provide AWARD Party Branch information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbbenefittypes_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbbenefittypes extends dbTable
{

    /**
	* Class Constructor
	    *
	    * @access public
	    * @return void
	    */

    public function init()
    {
        try {
            parent::init('tbl_award_benefit_types');
        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }


function getAgreeConditions($agreeId, $selection) {
		$this->loadClass('dropdown','htmlelements');
		$this->loadClass('textinput','htmlelements');
		$objAgree = &$this->getObject('dblrsagree','lrsagreements');
		$agree = $objAgree->getRow('id',$agreeId);
		$agreements = $objAgree->getAll("WHERE org_unitId = '{$agree['org_unitId']}' ORDER BY agree_date_implementation DESC");
		$objDrop = new dropdown('agreeDrop');
		foreach($agreements as $agree) {
			$objDrop->addOption($agree['id'],$agree['agree_name']);
		}
		$objDrop->setSelected($agreeId);
		$loadingPhrase = "<b>".$this->objLanguage->languageText('phrase_loading')."</b>";
		$objDrop->extra = " onchange = 'getAgreeConditions($loadingPhrase,this.value,document.getElementById(\"input_selectedTab\").value)'";
		$selected = new textinput('selectedTab',$selection,'hidden');
		$width1 = '70%';
        $width2 = '30%';
        $objBenefitTypes = &$this->getObject('dblrsbenefittypes','lrsbenefittypes');
        $objBenefitNames = &$this->getObject('dblrsbenefitnames','lrsbenefittypes');
        $this->objDbAgreeBenefits = &$this->getObject('dblrsagreebenefits','lrsbenefittypes');
        $benefitTypes = $objBenefitTypes->getAll('ORDER BY id');
        $objHeading = &$this->getObject('htmlheading','htmlelements');
        $objHeading->type=4;
       
        $nestedTab = &$this->newObject('nestedTab','lrspostlogin');
        $tabNo = 1;
        $default = false;
        foreach ($benefitTypes as $benefit) { 
        	$objHeading->str = $benefit['name'];
        	$objTable = &$this->newObject('htmltable','htmlelements');
        	$objTable->cellspacing = $objTable->cellpadding = 2;
        	$objTable->addHeader(array($this->objLanguage->languageText('phrase_condition'),
            		$this->objLanguage->languageText('word_measurement')),null,'align=left');
            $conditions = $objBenefitNames->getAll("WHERE benefit_typeId = '{$benefit['id']}'");
        	$class = 'even';
        	foreach ($conditions as $cond) {
        		$class = ($class == 'even')? 'odd' : 'even';
        		$data = $this->objDbAgreeBenefits->getAll("WHERE benefit_nameId = '{$cond['id']}' AND agreeId = '$agreeId'");
        		if (empty($data)) {
        			$value = '--';
        		} else {
        			$single = current($data);
        			$value = round($single['benefitValue'],1);
        		}
        		$objTable->startRow($class);
        		$objTable->addCell($cond['name'],$width1,null,null);
        		$objTable->addCell($value,$width2,null,null);
        		$objTable->endRow();
        	}
        	if ($selection == $tabNo) { 
        		$default = true;
        	} else {
        		$default = false;
        	}
        	$content = $objHeading->show().$objTable->show();
        	$nestedTab->addTab(array('name'=>$benefit['name'],'content'=>$content, 'onclick' => "document.getElementById('input_selectedTab').value = '$tabNo'",'default'=>$default));
        	
        	$tabNo++;
        }
        $template = "<br />".$this->objLanguage->languageText('phrase_selectagreement').": ".$objDrop->show()."<br /><br />".$nestedTab->show().$selected->show();
        return $template;
	}



}
?>