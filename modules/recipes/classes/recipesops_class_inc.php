<?php
/**
 *
 * recipes helper class
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
 * @package   recipes
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * recipes helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package recipes
 *
 */
class recipesops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->objDbCook     = $this->getObject('dbrecipes');
    }
    
    public function addCookbookForm($editparams = NULL) {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'recipes', 'Required').'</span>';
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('mod_recipes_createcookbook', 'recipes');
        $ret = NULL;
        $ret .= $header->show();
        // start the form
        if(isset($editparams['cookbookname'])) {
            $form = new form ('updatecookbook', $this->uri(array('action'=>'updatecookbook', 'cbid' => $editparams['id']), 'recipes'));
        }
        else {
            $form = new form ('newcookbook', $this->uri(array('action'=>'newcookbook'), 'recipes'));
        }
        // add some rules
        $form->addRule('cookbookname', $this->objLanguage->languageText("mod_recipes_needcookbookname", "recipes"), 'required');
        //$form->addRule('license', $this->objLanguage->languageText("mod_recipes_needcookbooklicense", "recipes"), 'email');
        // cookbook name
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $cookbookname = new textinput('cookbookname');
        if(isset($editparams['cookbookname'])) {
            $cookbookname->setValue($editparams['cookbookname']);
        }
        $cookbooknameLabel = new label($this->objLanguage->languageText('cookbookname', 'recipes').'&nbsp;', 'input_cookbookname');
        $table->addCell($cookbooknameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($cookbookname->show().$required);
        $table->endRow();
        // cookbook description
        $defmsg = $this->objLanguage->languageText("mod_recipes_defaultcookbookdesc", "recipes");
        $table->startRow();
        $cdesc = $this->newObject('htmlarea', 'htmlelements');
        $cdesc->name = 'cdesc';
        if(isset($editparams['cookbookdesc'])) {
            $cdesc->value = $editparams['cookbookdesc'];
        }
        else {
            $cdesc->value = $defmsg;
        }
        $cdesc->width ='50%';
        $cdescLabel = new label($this->objLanguage->languageText('mod_recipes_cookbookdesc', 'recipes').'&nbsp;', 'input_cdesc');
        $table->addCell($cdescLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $cdesc->toolbarSet = 'simple';
        $table->addCell($cdesc->show());
        $table->endRow();
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = $this->objLanguage->languageText('mod_recipes_makeacookbook', 'recipes');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        if(isset($editparams['cookbookname'])) {
            $button = new button ('submitform', $this->objLanguage->languageText("mod_recipes_editcookbook", "recipes"));
        }
        else {
            $button = new button ('submitform', $this->objLanguage->languageText("mod_recipes_createcookbook", "recipes"));
        }
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    
    }
    
    public function listCookbooks($userid) {
        // get the users cookbooks from the db
        $books = $this->objDbCook->listCookBooks($userid);
        $fb = $this->newObject('featurebox', 'navigation');
        $ret = NULL;
        
        foreach($books as $book) {
            $body = NULL;
            $body .= $book['cookbookdesc'];
            // check for fav
            if($book['favourite'] == 1 && $this->objUser->isLoggedIn()) {
                $fav = $this->newObject('geticon', 'htmlelements');
                $fav->setIcon('favourite', 'png', 'icons/events');
                $fav->alt = $this->objLanguage->languageText("mod_recipes_fav", "recipes");
                
                $header = $fav->show().$book['cookbookname'];
            }
            else {
                $header = $book['cookbookname'];
                if($this->objUser->isLoggedIn()) {
                    // make this a favourite
                    $mkfav = $this->newObject('geticon', 'htmlelements');
                    $mkfav->setIcon('addtofavourites', 'gif', 'icons/');
                    $mkfav->alt = $this->objLanguage->languageText("mod_recipes_makefav", "recipes");
                    // make a link to set the fav
                    $favl = $this->newObject('link', 'htmlelements');
                    $favl->href = $this->uri(array('action' => 'favcookbook', 'id' => $book['id']));
                    $favl->link = $mkfav->show();
                    $body .= $favl->show();
                }
            }
            
            // get a recipe count
            $reccount = $this->objDbCook->countRecipesInBook($book['id'], $userid);
            $header .= " ( ".$reccount." ".$this->objLanguage->languageText("mod_recipes_word_recipes", "recipes")." )";
            
            if($this->objUser->isLoggedIn() && $this->objUser->userId() == $book['userid']) {
                // set up a delete cookbook link
                $delicon = $this->newObject('geticon', 'htmlelements');
                $delicon->setIcon('delete', 'gif', 'icons/'); 
                $delicon->alt = $this->objLanguage->languageText("mod_recipes_deletecookbook", "recipes");
                $delbook = $this->newObject('link', 'htmlelements');
                $delbook->href = $this->uri(array('action' => 'deletecookbook', 'cbid' => $book['id'], 'userid' => $book['userid'] ));
                $delbook->link = $delicon->show();
                $body .= $delbook->show();
            }
            // set up a view cookbook link
            $vicon = $this->newObject('geticon', 'htmlelements');
            $vicon->setIcon('view', 'gif', 'icons/'); 
            $vicon->alt = $this->objLanguage->languageText("mod_recipes_viewcookbook", "recipes");
            $vbook = $this->newObject('link', 'htmlelements');
            $vbook->href = $this->uri(array('action' => 'viewcookbook', 'cbid' => $book['id'] ));
            $vbook->link = $vicon->show();
            $body .= $vbook->show();
            
            // make up the fb
            $ret .= $fb->show($header, $body, $book['id'], 'hidden');

        }
                
        return $ret;
    }
    
    public function addRecipeForm($editparams = NULL) {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'ingredients', 'Required').'</span>';
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('mod_recipes_addrecipe', 'recipes');
        $ret = NULL;
        $ret .= $header->show();
        
        // start the form
        if(isset($editparams['id'])) {
            $form = new form ('updaterecipe', $this->uri(array('action'=>'updaterecipe', 'id' => $editparams['id']), 'recipes'));
        }
        else {
            $form = new form ('newrecipe', $this->uri(array('action'=>'newrecipe'), 'recipes'));
        }
        // add some rules
        $form->addRule('recipename', $this->objLanguage->languageText("mod_recipes_needrecipename", "recipes"), 'required');
        //$form->addRule('license', $this->objLanguage->languageText("mod_recipes_needcookbooklicense", "recipes"), 'email');
        
        $table = $this->newObject('htmltable', 'htmlelements');
       
        // cookbook dropdown
        $cblist = $this->objDbCook->listCookBooks($this->objUser->userId());
        $cb = new dropdown('cookbook');
        if(isset($editparams['cookbookid'])) {
            $cb->setSelected($editparams['cookbookid']);
        }
        $cb->addOption();
        foreach($cblist as $cbs) {
            $cb->addOption($cbs['id'], $cbs['cookbookname']);
        }
        $table->startRow();
        $cookbookLabel = new label($this->objLanguage->languageText('cookbook', 'recipes').'&nbsp;', 'input_cookbook');
        $table->addCell($cookbookLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($cb->show());
        $table->endRow();
        
        // recipe name
        $table->startRow();
        $recipename = new textinput('recipename');
        if(isset($editparams['recipename'])) {
            $recpiename->setValue($editparams['recipename']);
        }
        $recipenameLabel = new label($this->objLanguage->languageText('recipename', 'recipes').'&nbsp;', 'input_recipename');
        $table->addCell($recipenameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($recipename->show());
        $table->endRow();
        
        // category (dropdown)
        $cats = new dropdown('category');
        // get cats from database
        $cats->addOption();
        
        $table->startRow();
        $catadd = new textinput('category');
        $catsLabel = new label($this->objLanguage->languageText('recipecategory', 'recipes').'&nbsp;', 'input_category');
        $table->addCell($catsLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($cats->show()." ".$this->objLanguage->languageText("mod_recipes_oraddacat", "recipes")." ".$catadd->show());
        $table->endRow();
        
        // ingredients
        $table->startRow();
        $ing = $this->newObject('htmlarea', 'htmlelements');
        $ing->name = 'ingredients';
        if(isset($editparams['ingredients'])) {
            $ing->value = $editparams['ingredients'];
        }
        else {
            $ing->value = '';
        }
        $ing->width ='80%';
        $ing->height = '20%';
        $ingLabel = new label($this->objLanguage->languageText('mod_recipes_recipeingredients', 'recipes').'&nbsp;', 'input_ingredients');
        $table->addCell($ingLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $ing->toolbarSet = 'simple';
        $table->addCell($ing->show());
        $table->endRow();
        
        // recipe yield
        $table->startRow();
        $recipeyield = new textinput('recipeyield');
        if(isset($editparams['yield'])) {
            $recpieyield->setValue($editparams['yield']);
        }
        $recipeyieldLabel = new label($this->objLanguage->languageText('recipeyield', 'recipes').'&nbsp;', 'input_recipeyield');
        $table->addCell($recipeyieldLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($recipeyield->show());
        $table->endRow();
        
        // recipe instructions
        $defmsg = $this->objLanguage->languageText("mod_recipes_defaultrecipeins", "recipes");
        $table->startRow();
        $rins = $this->newObject('htmlarea', 'htmlelements');
        $rins->name = 'instructions';
        if(isset($editparams['instructions'])) {
            $rins->value = $editparams['instructions'];
        }
        else {
            $rins->value = $defmsg;
        }
        $rins->width ='80%';
        $rinsLabel = new label($this->objLanguage->languageText('mod_recipes_recipeinstructions', 'recipes').'&nbsp;', 'input_instructions');
        $table->addCell($rinsLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        //$cdesc->toolbarSet = 'simple';
        $table->addCell($rins->show());
        $table->endRow();
        
        // recipe duration (preptime)
        // dropdown of time units
        $tu = new dropdown('timeunits');
        $tu->addOption('s', $this->objLanguage->languageText("mod_recipes_time_seconds", "recipes"));
        $tu->addOption('m', $this->objLanguage->languageText("mod_recipes_time_minutes", "recipes"));
        $tu->addOption('h', $this->objLanguage->languageText("mod_recipes_time_hours", "recipes"));
        $tu->addOption('d', $this->objLanguage->languageText("mod_recipes_time_days", "recipes"));
        
        $table->startRow();
        $recipeprep = new textinput('recipeprep');
        if(isset($editparams['duration'])) {
            $recpieprep->setValue($editparams['duration']);
        }
        $recipeprepLabel = new label($this->objLanguage->languageText('recipeprep', 'recipes').'&nbsp;', 'input_recipeprep');
        $table->addCell($recipeprepLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($recipeprep->show()." ".$tu->show());
        $table->endRow();
        
        // picture upload
        $form->extra = 'enctype="multipart/form-data"';
        $objUpload = $this->newObject('selectfile', 'filemanager');
        $objUpload->name = 'photo';
        $objUpload->restrictFileList = array('jpg', 'JPG', 'png', 'PNG', 'gif', 'GIF');
        $table->startRow();
        $recipepicLabel = new label($this->objLanguage->languageText('recipepic', 'recipes').'&nbsp;', 'input_photo');
        $table->addCell($recipepicLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($objUpload->show());
        $table->endRow();
        
        // recipe summary/notes
        $table->startRow();
        $summ = $this->newObject('htmlarea', 'htmlelements');
        $summ->name = 'summary';
        if(isset($editparams['summary'])) {
            $summ->value = $editparams['summary'];
        }
        else {
            $summ->value = '';
        }
        $summ->width ='80%';
        $summ->height = '20%';
        $summLabel = new label($this->objLanguage->languageText('mod_recipes_recipenotes', 'recipes').'&nbsp;', 'input_summary');
        $table->addCell($summLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $summ->toolbarSet = 'simple';
        $table->addCell($summ->show());
        $table->endRow();
        
        // nutritional information
        // dropdown of energy units
        $eu = new dropdown('energyunits');
        $eu->addOption('J', $this->objLanguage->languageText("mod_recipes_energy_joules", "recipes"));
        $eu->addOption('KJ', $this->objLanguage->languageText("mod_recipes_energy_kilojoules", "recipes"));
        $eu->addOption('Cal', $this->objLanguage->languageText("mod_recipes_energy_calories", "recipes"));
        $eu->addOption('kCal', $this->objLanguage->languageText("mod_recipes_energy_kilocaloriess", "recipes"));
        $table->startRow();
        $recipeenergy = new textinput('recipeenergy');
        if(isset($editparams['nutrition'])) {
            $recpieenergy->setValue($editparams['nutrition']);
        }
        $recipeenergyLabel = new label($this->objLanguage->languageText('recipeenergy', 'recipes').'&nbsp;', 'input_recipeenergy');
        $table->addCell($recipeenergyLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($recipeenergy->show()." ".$eu->show());
        $table->endRow();
        
        // wine pairing
        $table->startRow();
        $wine = new textinput('wine');
        $wineLabel = new label($this->objLanguage->languageText('suggestedwineorbeverage', 'recipes').'&nbsp;', 'input_wine');
        $table->addCell($wineLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($wine->show());
        $table->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = $this->objLanguage->languageText('mod_recipes_createrecipe', 'recipes');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        if(isset($editparams['cookbookname'])) {
            $button = new button ('submitform', $this->objLanguage->languageText("mod_recipes_editrecipe", "recipes"));
        }
        else {
            $button = new button ('submitform', $this->objLanguage->languageText("mod_recipes_createrecipe", "recipes"));
        }
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    
    }
    
    /**
     * Sign in block
     *
     * Used in conjunction with the welcome block as a alertbox link. The sign in simply displays the block to sign in to Chisimba
     *
     * @return string
     */
    public function showSignInBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languageText("mod_events_signin", "recipes"), $objBlocks->showBlock('login', 'security', 'none'));
    }
    
    public function ingredientsBoxen() {
        $inglist = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
        $inglist .= '<script type="text/javascript">
             jQuery(document).ready(function(){
                 var counter = 2;
                 jQuery("#addButton").click(function () {   
              	     var newIngredientsDiv = jQuery(document.createElement(\'div\')).attr("id", \'IngredientsDiv\' + counter);
	                 newIngredientsDiv.after().html(\'<label>Ingredient #\'+ counter + \' : </label>\' + \'<input type="text" name="textbox\' + counter + \'" id="textbox\' + counter + \'" value="" >\'); 
	                 newIngredientsDiv.appendTo("#IngredientsGroup");
          	         counter++;
                 });
                 jQuery("#removeButton").click(function () {
	                 if(counter==1){
                         alert("No more ingredients to remove");
                         return false;
                     }   
 	                 counter--;
                     jQuery("#IngredientsDiv" + counter).remove();
 
                 });
                 jQuery("#getButtonValue").click(function () {
  	                 var msg = \'\';
	                 for(i=1; i<counter; i++){
   	                     msg += "\n Ingredient #" + i + " : " + jQuery(\'#textbox\' + i).val();
	                 }
    	             alert(msg);
                 });
            });
        </script>';
        $this->appendArrayVar('headerParams', $inglist);
        $html = '<div id=\'TextBoxesGroup\'>
	                 <div id="IngredientsDiv1">
		                 <label>Ingredient #1 : </label><input type=\'textbox\' id=\'textbox1\' >
	                 </div>
                 </div>
                 <input type=\'button\' value=\'Add Button\' id=\'addButton\'>
                 <input type=\'button\' value=\'Remove Button\' id=\'removeButton\'>';
                 
        
        return $html;
    }

    /**
     * Sign up block
     *
     * Method to generate a sign up (register) block for the module. It uses a linked alertbox to format the response
     *
     * @return string
     */
    public function showSignUpBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languageText("mod_events_signup", "recipes"), $objBlocks->showBlock('register', 'security', 'none'));
    }
    
    public function getFbCode() {
        $fbapid = $this->objSysConfig->getValue('apid', 'facebookapps');
        $fb = "<div id=\"fb-root\"></div>
               <script>
                   window.fbAsyncInit = function() {
                       FB.init({appId: '$fbapid', status: true, cookie: true,
                       xfbml: true});
                   };
                   (function() {
                       var e = document.createElement('script'); e.async = true;
                       e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
                       document.getElementById('fb-root').appendChild(e);
                   }());
             </script>
             <fb:like action='like' colorscheme='light' layout='standard' show_faces='true' width='500'/>";
        return $fb;
    }
}
?>
