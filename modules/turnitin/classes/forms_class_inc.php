<?php
/**
 * Methods which intergrates the Turnitin API
 * into the Chisimba framework
 * 
 * This module requires a valid Turnitin account/license which can 
 * purhase at http://www.turnitin.com
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
 * @package   turnitin
 * @author    Wesley Nitsckie
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
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


/**
 * Class to supply an easy API for use from this module or even other modules.
 * @author Wesley Nitsckie
 * @package turnitin
 */
class forms extends object
{
	
	public function init()
	{
		
		$this->objLanguage = $this->getObject ( 'language', 'language' );
	}
	
	public function addAssignmentForm()
	{
		
		try {
            $this->loadClass ( 'form', 'htmlelements' );
            $this->loadClass ( 'textinput', 'htmlelements' );
            $this->loadClass ( 'textarea', 'htmlelements' );
            $this->loadClass ( 'button', 'htmlelements' );
            //$this->loadClass('htmlarea', 'htmlelements');
            $this->loadClass ( 'dropdown', 'htmlelements' );
            $this->loadClass ( 'label', 'htmlelements' );
           // $objCaptcha = $this->getObject ( 'captcha', 'utilities' );
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
        
        $cform = new form ( 'submitassessment');
		$cform->action = $this->uri ( array ('action' => 'submitassessment' ,'module' => 'turnitin') ) ;
        $cfieldset = $this->getObject ( 'fieldset', 'htmlelements' );
        $ctbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $ctbl->cellpadding = 5;

        //textarea for the message
        $commlabel = new label ( $this->objLanguage->languageText ( 'mod_im_message', 'im' ) . ':', 'input_comminput' );
        $ctbl->startRow ();
        $ctbl->addCell ( $commlabel->show () );
        $ctbl->endRow ();
        $ctbl->startRow ();
        
        $ctbl->endRow ();

        //end off the form and add the buttons
        $this->objCButton = &new button ( $this->objLanguage->languageText ( 'mod_im_send', 'im' ) );
        $this->objCButton->setValue ( $this->objLanguage->languageText ( 'mod_im_send', 'im' ) );
        $this->objCButton->setToSubmit ();

        $cfieldset->addContent ( $ctbl->show () );
        $cform->addToForm ( $cfieldset->show () );
        $cform->addToForm ( $this->objCButton->show () );

        return $cform->show();
        
		return "add assignment form";
	}
	
	 public function jsonGetAssessments($start = 0, $limit=25)
    {
    	
    	/*$contexts = $this->objDBContext->getAll("ORDER BY updated DESC limit $start, $limit");
    	$all = $this->objDBContext->getAll();
    	
    	$contextCount = count($contexts);
    	$cnt = 0;
    	$str = '{"totalCount":"'.count($all).'","courses":[';
    	if($contextCount > 0)
    	{
    		foreach($contexts as $context)
    		{
    			$cnt++;
    			$str .= '{';
    			//$str .= '"id":"'.$context['id'].'",';
    			$str .= '"contextcode":"'.$context['contextcode'].'",';    			
    			$str .= '"title":"'.$context['title'].'",';
    			$str .= '"author":"'.htmlentities($this->objUser->fullname($context['userid'])).'",'; 
    			$str .= '"datecreated":"'.$context['datecreated'].'",'; 
    			$str .= '"lastupdated":"'.$context['updated'].'",'; 
    			$str .= '"excerpt":"'.addslashes($context['about']).'"'; 
    			$str .= '}';
    			if ($cnt < $contextCount)
    			{
    				$str .= ',';
    			}
    		}
    	}
    	
    	$str .= ']}';
    	return $str;
    	*/
    	
    	return "[
    ['3m Co',71.72,0.02,0.03,'4/2 12:00am', 'Manufacturing'],
    ['Alcoa Inc',29.01,0.42,1.47,'4/1 12:00am', 'Manufacturing'],
    ['Altria Group Inc',83.81,0.28,0.34,'4/3 12:00am', 'Manufacturing'],
    ['American Express Company',52.55,0.01,0.02,'4/8 12:00am', 'Finance'],
    ['American International Group, Inc.',64.13,0.31,0.49,'4/1 12:00am', 'Services'],
    ['AT&T Inc.',31.61,-0.48,-1.54,'4/8 12:00am', 'Services'],
    ['Boeing Co.',75.43,0.53,0.71,'4/8 12:00am', 'Manufacturing'],
    ['Caterpillar Inc.',67.27,0.92,1.39,'4/1 12:00am', 'Services'],
    ['Citigroup, Inc.',49.37,0.02,0.04,'4/4 12:00am', 'Finance'],
    ['E.I. du Pont de Nemours and Company',40.48,0.51,1.28,'4/1 12:00am', 'Manufacturing'],
    ['Exxon Mobil Corp',68.1,-0.43,-0.64,'4/3 12:00am', 'Manufacturing'],
    ['General Electric Company',34.14,-0.08,-0.23,'4/3 12:00am', 'Manufacturing'],
    ['General Motors Corporation',30.27,1.09,3.74,'4/3 12:00am', 'Automotive'],
    ['Hewlett-Packard Co.',36.53,-0.03,-0.08,'4/3 12:00am', 'Computer'],
    ['Honeywell Intl Inc',38.77,0.05,0.13,'4/3 12:00am', 'Manufacturing'],
    ['Intel Corporation',19.88,0.31,1.58,'4/2 12:00am', 'Computer'],
    ['International Business Machines',81.41,0.44,0.54,'4/1 12:00am', 'Computer'],
    ['Johnson & Johnson',64.72,0.06,0.09,'4/2 12:00am', 'Medical'],
    ['JP Morgan & Chase & Co',45.73,0.07,0.15,'4/2 12:00am', 'Finance'],
    ['McDonald\'s Corporation',36.76,0.86,2.40,'4/2 12:00am', 'Food'],
    ['Merck & Co., Inc.',40.96,0.41,1.01,'4/2 12:00am', 'Medical'],
    ['Microsoft Corporation',25.84,0.14,0.54,'4/2 12:00am', 'Computer'],
    ['Pfizer Inc',27.96,0.4,1.45,'4/8 12:00am', 'Services', 'Medical'],
    ['The Coca-Cola Company',20,0.26,0.58,'4/1 12:00am', 'Food'],
    ['The Home Depot, Inc.',34,0.35,1.02,'4/8 12:00am', 'Retail'],
    ['The Procter & Gamble Company',6,0.01,0.02,'4/1 12:00am', 'Manufacturing'],
    ['United Technologies Corporation',63,0.55,0.88,'4/1 12:00am', 'Computer'],
    ['Verizon Communications',3,0.39,1.11,'4/3 12:00am', 'Services'],
    ['Wal-Mart Stores, Inc.',44,0.73,1.63,'4/3 12:00am', 'Retail'],
    ['Wesleyl-Mart Stores, Inc.',,0.73,1.63,'4/3 12:00am', 'Retail'],
    ['Walt Disney Company (The) (Holding Company)',29.89,0.24,0.81,'4/1 12:00am', 'Services']
]";
    }
}