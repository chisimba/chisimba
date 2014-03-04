<?php

/**
 * XML-RPC interface class
 * 
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: xmlrpcapi_class_inc.php 137 2008-08-26 09:13:25Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
 * XML-RPC Class
 * 
 * Class to provide XML-RPC functionality to Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class xmlrpcapi extends object
{	
	/**
	 * Chisimba LRS API 
	 * @var    object
	 * @access public
	 */
	public $objAwardIndex;
    
	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			require_once($this->getPearResource('XML/RPC/Server.php'));
			require_once($this->getPearResource('XML/RPC/Dump.php'));
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
        	// User Object
        	$this->objUser = $this->getObject('user', 'security');
        	
        	// Award API abstraction object
        	$this->objApiIndex = $this->getObject('apiindex');
        	$this->objApiAgree = $this->getObject('apiagree');
        	$this->objApiUnit = $this->getObject('apiunit');
        	$this->objApiUnion = $this->getObject('apiunion');
        	$this->objApiBranch = $this->getObject('apibranch'); 
        	$this->objApiRegion = $this->getObject('apiregion');
        	$this->objApiSic = $this->getObject('apisic');
        	$this->objApiSoc = $this->getObject('apisoc');
        	$this->objApiWages = $this->getObject('apiwages');
        	$this->objApiConditions = $this->getObject('apiconditions');
        	$this->objApiDecentWork = $this->getObject('apidecentwork');
        	
		}
		catch (customException $e)
		{
			// garbage collection
			customException::cleanUp();
			// die, as we are screwed anyway
			exit;
		}
	}
	
    /**
     * server method
     * 
     * Create and deploy the XML-RPC server for use on an URL
     * 
     * @return object server object
     * @access public
     */
	public function serve()
	{
		// map web services to methods
		$server = new XML_RPC_Server(
   					array('getMsg' => array('function' => array($this->objApiIndex, 'getMessage'),
      			  		  					'signature' =>
                     							array(
                         							array('string', 'string'),
                     							),
                     	  // RPC functions for Indexes
                								'docstring' => 'Return a given string'),
   					      'listIndexes' => array('function' => array($this->objApiIndex, 'listIndexes'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'Request a list of indexes from AWARD'),
                	                               
                          'getIndexValues' => array('function' => array($this->objApiIndex, 'getIndexValues'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'Request a list of index values from AWARD'),
                	      
                	      'createIndex' => array('function' => array($this->objApiIndex, 'createIndex'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'Create a new index'),
                	                               
   					      'editIndex' => array('function' => array($this->objApiIndex, 'editIndex'),
                	                               'signature' => array(
                	                                                array('string','string', 'string','string'),
                	                                                ),
                	                               'docstring' => 'edit an index'),
                	                               
   					      'deleteIndex' => array('function' => array($this->objApiIndex, 'deleteIndex'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'Delete an index'),
                	                               
   					      'updateIndexValues' => array('function' => array($this->objApiIndex, 'updateIndexValues'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'Update index values on AWARD'),
                          // RPC Functions for Bargaining Units                         
   					      'listBu' => array('function' => array($this->objApiUnit, 'listBu'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'Find bargaining units given a text string'),
                	      'award.listSicMD' => array('function' => array($this->objApiSic, 'getSicMDList'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List SIC major divisions on AWARD'),
                          'award.listSicD' => array('function' => array($this->objApiSic, 'getSicDList'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SIC divisions on AWARD'),
                          'award.listSicMG' => array('function' => array($this->objApiSic, 'getSicMGList'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SIC Major Groups on AWARD'),
                          'award.listSicG' => array('function' => array($this->objApiSic, 'getSicGList'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SIC Groups on AWARD'),
                          'award.listSic' => array('function' => array($this->objApiSic, 'getSicSGList'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SIC Sub Groups on AWARD'),
                          'award.getBUValues' => array('function' => array($this->objApiUnit, 'BUValues'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SIC Party and region infor for a BU on AWARD'),
                          'award.unitOverview' => array('function' => array($this->objApiUnit, 'getOverview'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'get BU overview on AWARD'),
                          'award.listAgree' => array('function' => array($this->objApiUnit, 'listAgree'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List agreements belonging to a BU on AWARD'),
                          'award.addUnit' => array('function' => array($this->objApiUnit, 'addUnit'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'add a BU on AWARD'),
                          
                          'award.editUnit' => array('function' => array($this->objApiUnit, 'editUnit'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'edit a BU on AWARD'),
                          
                          'award.deleteUnit' => array('function' => array($this->objApiUnit, 'deleteUnit'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'delete a BU on AWARD'),
                          
                          // Agreement section
                          'award.deleteAgree' => array('function' => array($this->objApiAgree, 'deleteAgree'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'delete an agreement on AWARD'),
                          'award.listAgreeTypes' => array('function' => array($this->objApiAgree, 'listAgreeTypes'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                                                   'docstring' => 'list the agreement types on AWARD'),
                          'award.listWages' => array('function' => array($this->objApiWages, 'listWages'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'list the wages of an agreement on AWARD'),
                          'award.getAgreeValues' => array('function' => array($this->objApiAgree, 'getAgreeValues'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'list the agreement values as ids on AWARD'),
                          'award.getAgreeDetails' => array('function' => array($this->objApiAgree, 'getAgreeDetails'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'list the agreement values in words on AWARD'),
                          'award.addAgree' => array('function' => array($this->objApiAgree, 'addAgree'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'add an agreement to AWARD'),
                          'award.editAgree' => array('function' => array($this->objApiAgree, 'editAgree'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'edit an agreement on AWARD'),
                          // Wage Section
                          'award.getSocList' => array('function' => array($this->objApiWages, 'getSocList'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                                                   'docstring' => 'list the socs on AWARD according to a search string'),
                          'award.getWageValues' => array('function' => array($this->objApiWages, 'getWageValues'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                                                   'docstring' => 'list the properties of a wage on AWARD'),
                          'award.addWage' => array('function' => array($this->objApiWages, 'addWage'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string'),
                	                                                ),
                                                   'docstring' => 'add a wage on AWARD'),
                          'award.editWage' => array('function' => array($this->objApiWages, 'editWage'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string'),
                	                                                ),
                                                   'docstring' => 'edit a wage on AWARD'),
                          'award.listPayPeriodTypes' => array('function' => array($this->objApiWages, 'listPayPeriodTypes'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                                                   'docstring' => 'list the pay period types on AWARD'),
                          'award.deleteWage' => array('function' => array($this->objApiWages, 'deleteWage'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'delete a wage on AWARD'),
                          
                          // The Trade Union API Starts
                          'award.listTradeUnions' => array('function' => array($this->objApiUnion, 'listTradeUnions'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List trade union values on AWARD'),
                          'award.createUnion' => array('function' => array($this->objApiUnion, 'insertUnion'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'Insert trade union values on AWARD'),
                          'award.editUnion' => array('function' => array($this->objApiUnion, 'updateUnion'),
                	                               'signature' => array(
                	                                                array('string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'Update trade union values on AWARD'),
                          'award.getUnionInfo' => array('function' => array($this->objApiUnion, 'tradeUnionInfo'),
                	                               'signature' => array(
                	                                                array('string', 'string'),
                	                                                ),
                	                               'docstring' => 'Returns the trade union information on AWARD'),
                          'award.listBranches' => array('function' => array($this->objApiUnion, 'getBranches'),
                	                               'signature' => array(
                	                                                array('string', 'string'),
                	                                                ),
                	                               'docstring' => 'Returns the branches belonging to a trade union'),
                          'award.deleteUnion' => array('function' => array($this->objApiUnion, 'deleteUnion'),
                	                               'signature' => array(
                	                                                array('string', 'string'),
                	                                                ),
                	                               'docstring' => 'Delete trade union values on AWARD'),
	                      // The Region API Starts
                          'award.listRegion' => array('function' => array($this->objApiRegion, 'getRegionList'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List SOC major groups on AWARD'),
        
                          'award.updateRegion' => array('function' => array($this->objApiRegion, 'updateRegion'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'update SOC major group on AWARD'),
                       	  'award.insertRegion' => array('function' => array($this->objApiRegion, 'insertRegion'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'insert SOC major group on AWARD'),
                       	  // The Decent Work API Starts
                          'award.listDWCategories' => array('function' => array($this->objApiDecentWork, 'listCategories'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List decent work categories on AWARD'),
						  'award.listDWValues' => array('function' => array($this->objApiDecentWork, 'listValues'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List decent work values on AWARD'),
        
                          'award.updateCat' => array('function' => array($this->objApiDecentWork, 'updateCategory'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'update decent work category on AWARD'),
                       	  'award.insertCat' => array('function' => array($this->objApiDecentWork, 'insertCategory'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'insert decent work category on AWARD'),
                       	  'award.deleteCat' => array('function' => array($this->objApiDecentWork, 'deleteCategory'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'delete decent work category on AWARD'),
                       	  'award.updateValue' => array('function' => array($this->objApiDecentWork, 'updateValue'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'update decent work value on AWARD'),
                       	  'award.insertValue' => array('function' => array($this->objApiDecentWork, 'insertValue'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'insert decent work value on AWARD'),
                       	  'award.deleteValue' => array('function' => array($this->objApiDecentWork, 'deleteValue'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'delete decent work value on AWARD'),
                       	  'award.getDWValueData' => array('function' => array($this->objApiDecentWork, 'getValueData'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'get decent work value data on AWARD'),
                       	  // Conditions API
                          'award.listConditionTypes' => array('function' => array($this->objApiConditions, 'listTypes'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List condition types on AWARD'),
                          'award.loadConditions' => array('function' => array($this->objApiConditions, 'loadValues'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'Load condition values on AWARD'),
                          'award.saveConditions' => array('function' => array($this->objApiConditions, 'saveValues'),
                	                               'signature' => array(
                	                                                array('string','array'),
                	                                                ),
                	                               'docstring' => 'save condition values on AWARD'),
                          // The SOC API Starts
                          'award.listSocMajorGroup' => array('function' => array($this->objApiSoc, 'listSocMajorGroup'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List SOC major groups on AWARD'),
        
                          'award.updateSoc' => array('function' => array($this->objApiSoc, 'updateSocMajorGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'update SOC major group on AWARD'),
                       	  'award.insertSoc' => array('function' => array($this->objApiSoc, 'insertSocMajorGroup'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'insert SOC major group on AWARD'),
                       	  'award.listSocSubMajorGroup' => array('function' => array($this->objApiSoc, 'listSocSubMajorGroup'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SOC sub major groups on AWARD'),
        
                          'award.updateSubSoc' => array('function' => array($this->objApiSoc, 'updateSocSubMajorGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'update SOC submajor group on AWARD'),
                       	  'award.insertSubSoc' => array('function' => array($this->objApiSoc, 'insertSocSubMajorGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string'),
                	                                                ),
                	                               'docstring' => 'insert SOC submajor group on AWARD'),
                       	  'award.listSocMinorGroup' => array('function' => array($this->objApiSoc, 'listSocMinorGroup'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SOC minor groups on AWARD'),
        
                          'award.updateMinorSoc' => array('function' => array($this->objApiSoc, 'updateSocMinorGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'update SOC minor group on AWARD'),
                       	  'award.insertMinorSoc' => array('function' => array($this->objApiSoc, 'insertSocMinorGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'insert SOC minor group on AWARD'),
                       	  'award.listSocUnitGroup' => array('function' => array($this->objApiSoc, 'listSocUnitGroup'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SOC unit groups on AWARD'),
        
                          'award.updateUnitSoc' => array('function' => array($this->objApiSoc, 'updateSocUnitGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'update SOC unit group on AWARD'),
                       	  'award.insertUnitSoc' => array('function' => array($this->objApiSoc, 'insertSocUnitGroup'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'insert SOC unit group on AWARD'),
                       	  'award.listSocName' => array('function' => array($this->objApiSoc, 'listSocName'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List SOC unit groups on AWARD'),
        
                          'award.updateSocName' => array('function' => array($this->objApiSoc, 'updateSocName'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'update SOC name on AWARD'),
                       	  'award.insertSocName' => array('function' => array($this->objApiSoc, 'insertSocName'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'insert SOC name on AWARD'),
                       	      // The Party Branch API Starts
                          'award.listPartyBranches' => array('function' => array($this->objApiBranch, 'listPartyBranches'),
                	                               'signature' => array(
                	                                                array('string', 'string'),
                	                                                ),
                	                               'docstring' => 'List party branches values on AWARD'),
                       	  'award.listRegions' => array('function' => array($this->objApiRegion, 'getRegionList'),
                	                               'signature' => array(
                	                                                array('string'),
                	                                                ),
                	                               'docstring' => 'List Regions values on AWARD'),
                          'award.listBranchUnits' => array('function' => array($this->objApiBranch, 'getBranchUnits'),
                	                               'signature' => array(
                	                                                array('string','string'),
                	                                                ),
                	                               'docstring' => 'List units associated with a branch on AWARD'),
                          'award.insertBranch' => array('function' => array($this->objApiBranch, 'insertBranch'),
                	                               'signature' => array(
                	                                                array('string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'Insert Party Branch values on AWARD'),
                          'award.updateBranch' => array('function' => array($this->objApiBranch, 'updateBranch'),
                	                               'signature' => array(
                	                                                array('string','string','string','string','string'),
                	                                                ),
                	                               'docstring' => 'Update Party Branch values on AWARD'),
                	  'award.deleteBranch' => array('function' => array($this->objApiBranch, 'deleteBranch'),
                	                               'signature' => array(
                	                                                array('string', 'string'),
                	                                                ),
                	                               'docstring' => 'Delete branch on AWARD')
                	                               
   					), 1, 0);
   					

		return $server;
	}
}
?>