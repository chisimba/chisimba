<?php

/**
 * Short description for file
 *
 * Long description (if any) ...
 *
 * PHP version 3
 *
 * The license text...
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/* -------------------- security class extends module ----------------*/

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Module class to handle displaying the module list
*
* @author Sean Legassick
*
*         $Id$
*/
class htmlelements extends controller
{

    /**
     * Description for var
     * @var    object
     * @access public
     */
    var $objDBUser;

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    function init()
    {
        $this->objDBUser= $this->getObject('user','security');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('tabbedbox', 'htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('textarea','htmlelements');
        //$this->loadClass('calendar','htmlelements');
        $this->loadClass('layer','htmlelements');
        $this->loadClass('windowpop','htmlelements');
        $this->loadClass('form','htmlelements');
        $this->loadClass('multitabbedbox','htmlelements');
        $this->loadClass('mouseoverpopup','htmlelements');
  }


    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $action Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    function dispatch($action)
    {
        switch($action){
            case null:
                $this->showForms();
                return 'htmlelements_tpl.php';
                break;
            case 'valform':
                $this->valFormShow();
                return 'htmlelements_tpl.php';
                break;
            case 'other';
                return 'other_tpl.php';
                break;
            case 'test1':
                return 'test1_tpl.php';
            case 'tabcontent':
                return 'tabcontent_tpl.php';
            case 'submodalexample':
                return 'submodalexample_tpl.php';
            case 'submodalexample_content':
                return 'submodalexamplecontent_tpl.php';
            case 'composelist':
                $search = $this->getParam('_search');
                $name = $this->getParam('name');
                $params = $this->getParam('params');
                $callback_module = $this->getParam('callback_module');
                $callback_class = $this->getParam('callback_class');
                $this->composeList($search, $name, $params, $callback_module, $callback_class);
                //echo "OK";
                return;

        }

     //return 'contentmain_tpl.php';

    }


    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    function showForms(){
        //Text Input
        $objForm = new form('testform');
        $url=$this->uri(array('action'=>'add'),'htmlelements');
        $objForm->setAction($this->uri(array('action'=>'save'),'htmlelements'));
        $objForm->setDisplayType(2);

        $objElement = new textinput('textbox');
        $objElement->setValue('Some text');
        $objElement->label='Textbox\'s label';
        $text = $objElement->show().'<br />';
        $objForm->addToForm('<span class="warning">Start of Form</span><br />');
        $objForm->addToForm($objElement);


        //Calendar
        /*$objElement = new calendar('cal');
        $today = getdate();
        $objElement->setDate($today['mon'],$today['mday'],$today['year']);
        $calendar = $objElement->show().'<br />';*/

        //Radion button Group
        $objElement = new radio('sex_radio');
        $objElement->addOption('m','Male');
        $objElement->addOption('f','Female');
        $objElement->addOption('n','Seaweed');
        $objElement->setSelected('f');
        $radio= $objElement->show().'<br />';

        //Check boxes
        $objElement = new checkbox('m','Male',true);
        $check= $objElement->show();
        $objElement = new checkbox('f','Female');
        $check .= $objElement->show();
        $objElement = new checkbox('n','Seawood');
        $check.= $objElement->show().'<br />';

        //Dropdown
        $objElement = new dropdown('sex_dropdown');
        $objElement->addOption('',''); //adding a blank option
        $objElement->addOption('m','Male');
        $objElement->addOption('f','Female');
        $objElement->addOption('n','Seaweed');
        $objElement->setSelected('f');
        $dropdown= $objElement->show()."<br />";

        //Dropdown created from array
        $objElement = new dropdown('user_dropdown');
        $objElement->addFromDB($this->objDBUser->getAll(),'username','userid',$this->objDBUser->userName());
        $objElement->label='User list';
        $dropdown.= $objElement->show()."<br />";

        //Textarea
        $objElement = new textarea('text_area');
        $objElement->setRows(3);
        $objElement->setColumns('45');
        $objElement->setContent('This is some content for the textarea');
        $ta=$objElement->show().'<br />';

        //Button
        $objElement = new button('mybutton');
        $objElement->setValue('Normal Button');
        $objElement->setOnClick('alert(\'An onclick Event\')');
        $button=$objElement->show().'<br />';

        //Submit Button
        $objElement = new button('mybutton');
        $objElement->setToSubmit();
        $objElement->label='Buttons Label';
        $objElement->setValue('Submit Button');
        $submit=$objElement->show().'<br />';

        //add submit button to the form;
        $objForm->addToForm($objElement);

        $mouseoverpopup = new mouseoverpopup('this is some text');
        $mouseoverpopup=$mouseoverpopup->show();

        //Add all the above to a tabbedbox
        $objElement = new tabbedbox();
        $objElement->addTabLabel('Tabbed box 1');
        $objElement->addBoxContent($mouseoverpopup.$text.$dropdown.$button.$submit);
        $tab = '<br />'.$objElement->show().'<br />';
        //add the tab to the form
        $objForm->addToForm($objElement);
        $objForm->addToForm('<span class="warning">End of Form</span>');
        $form = $objForm->show().'<br />';

        //create a multitabbedbox
        $objElement =new multitabbedbox('100px','500px');
        $objElement->addTab(array('name'=>'First','url'=>'http://localhost','content' => $form,'default' => true));
        $objElement->addTab(array('name'=>'Second','url'=>'http://localhost','content' => $check.$radio));
        $objElement->addTab(array('name'=>'Third','url'=>'http://localhost','content' => $tab,'height' => '300px','width' => '600px'));
        //$objElement->addTab(array('name'=>'Test Validation','url'=>'http://localhost','content' => $this->valFormShow(),'height' => '300','width' => '700'));
        //set layers


        $left=$tab;
        $content='This is an example using most of the classes in the htmlelements module<br />';
        $content.='<br />'.$objElement->show();


        //this to make the centre layer strech downwards
        for($i=0;$i<10;$i++){
            $content.='<br />';
        }
        $right=$tab;
        $bottom=$ta;


        $this->setVar('left',$left);
        $this->setVar('right',$right);
        $this->setVar('content',$content);
        $this->setVar('bottom',$bottom);
        //return $str;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    function valFormShow()
    {

    //I am not using multilingualized text for the examples
        $objForm = new form('testform');
        $objForm->setAction($this->uri(array('action'=>'valform'),'htmlelements'));
        $objForm->setDisplayType(2);

        $name= new textinput('name');
        $name->label='Name(must be filled out)';
        $objForm->addRule('name','Please enter your name','required');


        $surname= new textinput('surname');
        $surname->label='Surname (must be less than 15 characters)';
        $objForm->addRule(array('name'=>'surname','length'=>15), 'Your surname is too long', 'maxlength');

        $email= new textinput('email');
        $email->label='Email (must be a valid email address)';
        $objForm->addRule('email', 'Not a valid Email', 'email');

        $pwd= new textinput('pwd');
        $pwd->label='Password ';
        $pwd->fldType='password';

        $pwd2= new textinput('pwd2');
        $pwd2->label='Retype password (must be the same as "Password" case sensitive)';
        $pwd2->fldType='password';
        $objForm->addRule(array('pwd','pwd2'),'Password did not match','compare');

        $age= new textinput('age');
        $age->label='Age (must be older than 18)';
        $objForm->addRule(array('name'=>'age','minnumber'=>18), 'You have to be older than 18', 'minnumber');

        $colour= new textinput('colour');
        $colour->label='Favourate Colour (must be between 3 and 15 characters inclusive)';
        $objForm->addRule(array('name'=>'colour','lower'=>3,'upper'=>10), 'must be between 3 and 10 characters inclusive', 'rangelength');

        $sentence= new textinput('sentence');
        $sentence->label='Sentence (must contain no punctuation) not working yet';


        $car= new textinput('car');
        $car->label='Favourate Car (must contain only alphabetic characters)';
        $objForm->addRule('car','Must contain letters of the alphabet', 'letteronly');

        $monitor= new textinput('monitor');
        $monitor->label='Favourate Monitor (must contain only alphanumeric characters)';
        $objForm->addRule('monitor','Must contain letters of the alphabet and valid numbers', 'alphanumeric');

        $birthday= new textinput('birthday');
        $birthday->label='Birthday (mm/dd/yyy) not working yet';

        $sex = new radio('sex_radio');
        $sex->addOption('m','Male');
        $sex->addOption('f','Female');
        $sex->addOption('n','Seaweed');
        //$objForm->addRule('sex_radio','Please select your sex','select');

        $save= new button('save');
        $save->setToSubmit();
        $save->setValue('Save');


        $objForm->addToForm($name);
        $objForm->addToForm($surname);
        $objForm->addToForm($email);
        $objForm->addToForm($pwd);
        $objForm->addToForm($pwd2);
        $objForm->addToForm($age);
        $objForm->addToForm($sentence);
        $objForm->addToForm($colour);
        $objForm->addToForm($car);
        $objForm->addToForm($monitor);
        $objForm->addToForm($birthday);
        //$objForm->addToForm($sex);
        $objForm->addToForm($save);



        $this->setVar('left',"");
        $this->setVar('right','');
        $this->setVar('content',$objForm->show());
        $this->setVar('bottom','');


    }

    public function composeList($search, $name, $params, $module, $class)
    {
        $response = '';
        $response .= "Search results for: '{$search}' ...<br />";
        $object = $this->newObject($class, $module);
        $arrList = $object->callback($params, $search);
        if (!empty($arrList)) {
            $response .= '<select id="input_'.$name.'"name="'.$name.'">';
            foreach ($arrList as $value=>$text) {
                $response .= '<option value="'.$value.'">';
                $response .= "$text";
                $response .= '</option>';
            }
            $response .= '</select>';
        } else {
            $response = 'No results found!';
        }
        //log_debug($response);
        echo $response;
    }

/*function Cal(){
//Calendar
        $objElement = new calendar('cal');
        $today = getdate();
        $objElement->setDate($today['mon'],$today['mday'],$today['year']);
        $calendar = $objElement->show().'<br />';
}
*/
/*
*
* $form->addElement('password', 'cmpPasswd', 'Password:');
$form->addElement('password', 'cmpRepeat', 'Repeat password:');
$form->addRule(array('cmpPasswd', 'cmpRepeat'), 'The passwords do not match', 'compare', null, 'client');
*/
}

?>