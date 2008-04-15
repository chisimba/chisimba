<?php
/**
 * This file contains the ajaxsearch class which is used to generate
 * HTML ajaxsearch elements for forms
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
 */
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

/**
 * Include the HTML base class
 */
require_once("abhtmlbase_class_inc.php");

/**
 * Include the HTML interface class
 */
require_once("ifhtml_class_inc.php");

/**
 * Include the HTML textinput class
 */
require_once("textinput_class_inc.php");

/**
 * Include the HTML layer class
 */
require_once("layer_class_inc.php");

/**
 * Include the HTML form class
 */
//require_once("form_class_inc.php");

/**
 * Include the HTML button class
 */
require_once("button_class_inc.php");

/**
 * The ajaxsearch class implements a search htmlelement that uses ajax.
 * @category  Chisimba
 * @package   htmlelements
 * @author    Jeremy O'Connor <jeremyoconnor@telkomsa.net>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: $
 * @link      http://avoir.uwc.ac.za
 * @example:
 */
class ajaxsearch extends abhtmlbase implements ifhtml
{

    /**
    * @var object Holds an object.
    * @access private
    */
	private $object;

    /**
    * @var string The name of the element.
    * @access private
    */
	//private $name;

    /**
    * @var string The parameters to be passed.
    * @access private
    */
	private $params;

	/**
	* @var string Callback class that will supply the data.
	* @access private
	*/
	private $callback_class;

	/**
	* @var string Callback module.
	* @access private
	*/
	private $callback_module;

	/**
	* @var string ID of submit button.
	* @access private
	*/
    private $submitButton;

    /**
    * Constructor
    *
    * @param string name of the element
    * @param string Callback class that will supply the data
    * @param string Callback module
    */
    public function ajaxsearch($name, $params, $callback_class, $callback_module, $submitButton=NULL)
    {
	    global $_globalObjEngine;
	    $this->object = new object($_globalObjEngine, 'htmlelements');
        $this->name = $name;
        $this->params = $params;
        $this->callback_class = $callback_class;
        $this->callback_module = $callback_module;
        if (!is_null($submitButton)) {
            $this->submitButton = $submitButton;
        }
    }

    /**
    * Method to render the ajaxsearch as an HTML string
    *
	* @return string Returns the html
    */
    public function show()
    {

        $javascript_activatesearch = isset($this->submitButton)?"
            $('input_{$this->submitButton}').disabled = 'disabled';
        ":"";

        $javascript_searchsuccess = isset($this->submitButton)?"
            $('input_{$this->submitButton}').disabled = '';
        ":"";
		$Javascript = "
			<script type=\"text/javascript\">
			var firsttimefocus = true;
			var ajaxoperated = false;
			function activatesearch(e){
			 	if (firsttimefocus) {
			 		e.value = '';
			 		firsttimefocus = false;
			 	}
			 	if (ajaxoperated) {
			 		$('searchResultsDiv').innerHTML = '';
			 		ajaxoperated = false;
			 		{$javascript_activatesearch}
			 	}
			}
			function dosearch(q)
			{
			    //$('indicatorSpan').style.display='inline';
			    $('indicatorSpan').style.visibility='visible';
			    new Ajax.Updater('searchResultsDiv', 'index.php', {parameters:'module=htmlelements&action=composelist&_search='+q+'&name={$this->name}&params='+encodeURI('{$this->params}')+'&callback_module={$this->callback_module}&callback_class={$this->callback_class}', onSuccess:searchsuccess, onComplete:searchcomplete});
			}
			function searchsuccess(req)
			{
			     {$javascript_searchsuccess}
			}
			function searchcomplete(req)
			{
			    //$('indicatorSpan').style.display='none';
			    $('indicatorSpan').style.visibility='hidden';
			    ajaxoperated = true;
			}
			/*
			function verifysubmit()
			{
				 return ajaxoperated;
			}
			*/
			</script>
		";

		$objInput = new textinput('_search','Type you search terms here','','100');
		$objInput->extra = '';
		$objInput->extra .= ' onfocus="javascript:activatesearch(this);"';
		$Input = $objInput->show();

		$objIcon = $this->object->newObject('geticon','htmlelements');
		$objIcon->setIcon('spinner');
		$objIcon->alt = 'Working...';
		$Spinner = $objIcon->show();

		//<img src="/images/spinner.gif" alt="Working..." />
		$Indicator = '<span id="indicatorSpan" style="visibility: hidden;">'.$Spinner.'</span>';

		$objLayer = new layer();
		$objLayer->id = 'searchResultsDiv';
		$Layer = $objLayer->show();

		$objButton = new button('_dosearch','Go',"javascript:dosearch($('input__search').value);");
		$Search = $objButton->show();

		$objFieldset = $this->object->newObject('fieldset','htmlelements');
		$objFieldset->extra = 'class="tabbox"';
		$objFieldset->setLegend('Search');
		$objFieldset->addContent($Input.$Indicator.$Search.'<br />'.$Layer);
		$Fieldset = $objFieldset->show();

		$str = $Javascript.$Fieldset;

        /*
        .isset($this->submitButton)?"
			<script type=\"text/javascript\">
            $('input_{$this->submitButton}').disabled = 'disabled';
			</script>
        ":"";
        */

		return $str;
    }
}

?>