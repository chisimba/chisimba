<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_textinput
 *
 *  \brief This class models all the content and functionality for the
 * text input form element.
 * \brief It provides functionality to insert new text input, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete text inputs from forms.
       * \bug The member function buildWYSIWYGTextInputEntity is called by through by AJAX.
 * If an input mask is chosen then its functionality will not work in the WYSIWYG
 * form editor. It will only work when the form is rendered for submission. 
 * The inputmask class inside for the htmlelements module used conflicts with the javscript used in this
 * module. There is a bug in the inputmasks class inside the htmlelements module. Chisimba craps out
     * when you insert an inputmasks object through ajax. The chisimba community will
 * have to fix this grave problem. For now this member function can be used but the
 * input mask will not work in the WYSIWYG form editor.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';
     //  $this->appendArrayVar('headerParams', $this->getJavascriptFile('1.4.2/jquery-1.4.2.min.js', 'jquery'));
//$this->appendArrayVar('headerParams', '<script type="text/javascript">jQuery.noConflict();</script>');
class form_entity_textinput extends form_entity_handler
{
    private $formNumber; 
    /*!
     * \brief Private data member that stores a text input object for the WYSIWYG
     * form editor.
     */
    private $objTextInput;

           /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $tiFormName;

            /*!
     * \brief This data member stores the html name of the text input.
     */
    private $tiName;

                /*!
     * \brief This data member stores the text displayed by default within the
             * text input.
     */
    private $tiTextValue;

                    /*!
     * \brief This data member stores the type of text input either for 'text' or
                     * for a 'password'.
     */
    private $tiType;

                    /*!
     * \brief This data member stores width of the text input.
     */
    private $tiSize;

                        /*!
     * \brief This data member stores whether or not there is a text mask and the
                         * type of text mask is set.
     */
    private $tiTextMask;

            /*!
     * \brief This data member stores the label for the text input.
     */
private $tiLabel;

            /*!
         * \brief labelOrientation A string that stores whether the form element label gets
     * put on top, bottom, left or right of the text input.
     */
private $tiLabelLayout;

        /*!
     * \brief Private data member from the class \ref dbformbuilder_textinput_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete text input form elements.
     */
    protected  $objDBtiEntity;

        /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The textinput, inputmasks and label classes are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function  init()
    {
        $this->loadClass('textinput','htmlelements');
                $this->loadClass('label','htmlelements');
         $this->loadClass('inputmasks', 'htmlelements');

        $this->tiName=Null;
        $this->tiFormName =NULL;
        $this->tiTextValue=NULL;
        $this->tiType=NULL;
        $this->tiSize=NULL;
        $this->tiTextMask=NULL;
        $this->tiLabel =NULL;
        $this->tiLabelLayout=NULL;
        $this->formNumber =NULL;
        $this->objDBtiEntity = $this->getObject('dbformbuilder_textinput_entity','formbuilder');
                }

                                    /*!
     * \brief This member function initializes some of the private data members for the
     * text input object.
     * \parm textInputFormName A string for the form element identifier.
                     * \param textInputName A string for the actual html name for
                     * the text area.
     */
    public function createFormElement($formNumber,$textInputFormName='',$textInputName='')
    {
    $this->formNumber = $formNumber;
    $this->tiFormName = $textInputFormName;
    $this->tiName=$textInputName; 
    }

            /*!
     * \brief This member function gets the text input form element name if the private
     * data member tiName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
public function getWYSIWYGTextInputName()
{
    return $this->tiName;
}

protected function checkIfTextInputExists($formNumber,$formElementName)
{
return $this->objDBtiEntity->checkIfEntryExists($formNumber,$formElementName);
}
    /*!
     * \brief This member function gets the text input name if it has already
     * been saved in the database.
     * \param textInputFormName A string containing the form element indentifier.
     * \return A string.
     */
protected function getTextInputName($formNumber,$textInputFormName)
{
    $tiParameters = $this->objDBtiEntity->listTextInputParameters($formNumber,$textInputFormName);
    $textInputArray = array();
foreach($tiParameters as $thistiParameter){

   $textInputFormName = $thistiParameter["textinputname"];
   $textInputArray[]=$textInputFormName;
}
return $textInputArray;
}

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the text input parameters to insert
     * a text area form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed text area insert form.
     */
    public function getWYSIWYGTextInputInsertForm($formName)
    {
      
        
       $WYSIWYGTextInputInsertForm="<b>Text Input HTML ID and Name Menu</b>";
    $WYSIWYGTextInputInsertForm.="<div id='textInputNameAndIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
        $WYSIWYGTextInputInsertForm.= $this->buildInsertIdForm('textinput',$formName,"70")."<br>";

      // $WYSIWYGTextInputInsertForm.= $this->buildInsertFormElementNameForm('text input', "70");
         $WYSIWYGTextInputInsertForm.= "</div>";

                $WYSIWYGTextInputInsertForm.="<b>Text Input Label Menu</b>";
            $WYSIWYGTextInputInsertForm.="<div id='textInputLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
         $WYSIWYGTextInputInsertForm.= $this->insertFormLabelOptions("text_input","labelOrientation",NULL,NULL);
          $WYSIWYGTextInputInsertForm.= "</div>";
          $WYSIWYGTextInputInsertForm.="<b>Text Input Size Menu</b>";
           $WYSIWYGTextInputInsertForm.="<div id='textInputSizeContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
           
           $WYSIWYGTextInputInsertForm .= $this->insertCharacterSizeForm(NULL)."";
                       $WYSIWYGTextInputInsertForm.= "</div>";
           $WYSIWYGTextInputInsertForm.="<b>Text Input Properties Menu</b>";
           $WYSIWYGTextInputInsertForm.="<div id='textInputPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
       $WYSIWYGTextInputInsertForm.= $this->insertTextInputOptionsForm(2,68,NULL,NULL,NULL)."";
         $WYSIWYGTextInputInsertForm.= "</div>";
           return $WYSIWYGTextInputInsertForm;
    }
    
    public function getWYSIWYGTextInputEditForm($formNumber, $formElementName) {
        $tiParameters = $this->objDBtiEntity->listTextInputParameters($formNumber, $formElementName);
        if (empty($tiParameters)) {
            return 0;
        } else {
            $textValue = "";
            $textType = "";
            $textSize = "";
            $maskedInputChoice = "";
            $textInputLabel = "";
            $labelOrientation = "";
            foreach ($tiParameters as $thistiParameter) {

                $textValue = $thistiParameter["textvalue"];
                $textType = $thistiParameter["texttype"];

                $textSize = $thistiParameter["textsize"];
                $maskedInputChoice = $thistiParameter["maskedinputchoice"];

                $textInputLabel = $thistiParameter["label"];
                $labelOrientation = $thistiParameter["labelorientation"];
            }
             $WYSIWYGTextInputEditForm ="<b>Edit Text Input Label Parameters</b>";
             $WYSIWYGTextInputEditForm.="<div id='textInputLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
             $WYSIWYGTextInputEditForm.= $this->insertFormLabelOptions("text_input","labelOrientation",$textInputLabel,$labelOrientation);
             $WYSIWYGTextInputEditForm.= "</div>";
             
             $WYSIWYGTextInputEditForm.="<b>Edit Text Input Size Parameters</b>";
             $WYSIWYGTextInputEditForm.="<div id='textInputSizeContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
             $WYSIWYGTextInputEditForm .= $this->insertCharacterSizeForm($textSize)."";
             $WYSIWYGTextInputEditForm.= "</div>";
             
             $WYSIWYGTextInputEditForm.="<b>Edit Text Input Properties</b>";
             $WYSIWYGTextInputEditForm.="<div id='textInputPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
             $WYSIWYGTextInputEditForm.= $this->insertTextInputOptionsForm(2,68,$textType,$maskedInputChoice,$textValue)."";
             $WYSIWYGTextInputEditForm.= "</div>";
             
             return $WYSIWYGTextInputEditForm;
        }
    }

            /*!
     * \brief This member function allows you to insert a text input in a form with
     * a form element identifier.
     * \brief Before a new text input gets inserted into the database,
     * duplicate entries are checked if there is another text area.
     * in this same form with the same form element identifier.
     * \param textinputformname A string for the form element identifier.
     * \param textinputname A string for the actual html name for the text area.
          * \param textvalue A string for default text inside the text input.
             * \param A string to determine whether the text input is for 'text'
             * or a 'password'.
     * \param textsize An integer to specify the width of the text input.
         * \param maskinputchoice A string to determine whether or not there is a text mask and the
                         * type of text mask is set.
     * \param formElementLabel A string for the actual label text for the entire text input
         * form element.
     * \param labelLayout A string that stores whether the form element label gets
     * put on top, bottom, left or right of the text input form element.
     * \return A boolean value on successful storage of the text input form element.
     */
public function insertTextInputParameters($formNumber,$textinputformname,$textinputname,$textvalue,$texttype,$textsize,$maskedinputchoice,$formElementLabel,$formElementLabelLayout)
{
     // return   $textInputArrayForThisForm = $this->getFormElementIdentifierArray('text_input', $textinputformname)."gtgtgtgtg<br>";
    if ($this->objDBtiEntity->checkDuplicateTextInputEntry($formNumber,$textinputformname,$textinputname) == TRUE)
    {
  

        $this->objDBtiEntity->insertSingle($formNumber,$textinputformname,$textinputformname,$textvalue,$texttype,$textsize,$maskedinputchoice,$formElementLabel,$formElementLabelLayout);

        $this->tiName = $textinputname;
        $this->tiTextValue=$textvalue;
        $this->tiType=$texttype;
        $this->tiSize=$textsize;
        $this->tiTextMask=$maskedinputchoice;
        $this->formNumber=$formNumber;
        $this->tiLabel =$formElementLabel;
        $this->tiLabelLayout=$formElementLabelLayout;
    return TRUE;
    }
 else {
        return FALSE;
    }
}

public function updateTextInputParameters($formNumber,$textinputformname,$textinputname,$textvalue,$texttype,$textsize,$maskedinputchoice,$formElementLabel,$formElementLabelLayout){
       if ($this->objDBtiEntity->checkDuplicateTextInputEntry($formNumber,$textinputformname,$textinputname) == FALSE)
    {
  
        $this->objDBtiEntity->updateSingle($formNumber,$textinputformname,$textinputformname,$textvalue,$texttype,$textsize,$maskedinputchoice,$formElementLabel,$formElementLabelLayout);

        $this->tiName = $textinputname;
        $this->tiTextValue=$textvalue;
        $this->tiType=$texttype;
        $this->tiSize=$textsize;
        $this->tiTextMask=$maskedinputchoice;
        $this->formNumber=$formNumber;
        $this->tiLabel =$formElementLabel;
        $this->tiLabelLayout=$formElementLabelLayout;
    return TRUE;
    }
 else {
        return FALSE;
    } 
}

    /*!
     * \brief This member function deletes an existing text input form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a text input or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
protected function deleteTextInputEntity($formNumber,$formElementName)
{
    $deleteSuccess = $this->objDBtiEntity->deleteFormElement($formNumber,$formElementName);
    return $deleteSuccess;
}

    public function buildTextInput($id,$type, $default_text, $class, $with_label_bool, $label_text, $label_orientation, $size='35') {

        if ($id == null) {
            return "Error. Text Input Parameters not defined.";
        }

        if ($with_label_bool == true) {
            if ($class == null) {
                $textInputUnderConstruction = "<input type=\"$type\" name=\"$id\" id=\"$id\" size=$size value=\"$default_text\" />";
            } else {
                $textInputUnderConstruction = "<input type=\"$type\" name=\"$id\" class=\"$class\" size=$size id=\"$id\" value=\"$default_text\" />";
            }
            $labelUnderConstruction = "<label for='".$id."'>".$label_text."</label>";
switch ($label_orientation) {
                case 'top':
$currentConstructedti= "<div id='textInputLabelContainer' style='clear:both;'> ".$labelUnderConstruction."</div>"
        ."<div id='textInputContainer'style='clear:left;'> ".$textInputUnderConstruction."</div>";
                    return $currentConstructedti;
break;
                case 'bottom':
$currentConstructedti= "<div id='textInputContainer'style='clear:both;'> ".$textInputUnderConstruction."</div>".
                        "<div id='textInputLabelContainer' style='clear:both;'> ".$labelUnderConstruction."</div>";
                        return $currentConstructedti;
break;
                case 'left':
$currentConstructedti= "<div style='clear:both;overflow:auto;'><div id='textInputLabelContainer' style='float:left;clear:left;'> ".$labelUnderConstruction."</div>"
        ."<div id='textInputContainer'style='float:left; clear:right;'> ".$textInputUnderConstruction."</div></div>";
                        return $currentConstructedti;
break;
                case 'right':
$currentConstructedti= "<div style='clear:both;overflow:auto;'><div id='textInputContainer'style='float:left;clear:left;'> ".$textInputUnderConstruction."</div>".
                        "<div id='textInputLabelContainer' style='float:left;clear:right;'> ".$labelUnderConstruction."</div></div>";
                        return $currentConstructedti;
break;
default:
   $currentConstructedti= "<div style='clear:both;overflow:auto;'><div id='textInputLabelContainer' style='float:left;clear:left;'> ".$labelUnderConstruction."</div>"
        ."<div id='textInputContainer'style='float:left; clear:right;'> ".$textInputUnderConstruction."</div></div>";
        return $currentConstructedti;
break; 
                 }
                 
                 
        } else {
            if ($class == null) {
                return "<input type=\"$type\" name=\"$id\" id=\"$id\" size=$size value=\"$default_text\" />";
            } else {
                return "<input type=\"$type\" name=\"$id\" class=\"$class\" size=$size id=\"$id\" value=\"$default_text\" />";
            }
        }
    }

    /*!
     * \brief This member function constructs a text input form element for a the actual form
     * rendering from the database.
     * \param textInputName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed text input object.
     */
protected function constructTextInputEntity($textInputName,$formNumber)
{

    $tiParameters = $this->objDBtiEntity->listTextInputParameters($formNumber,$textInputName);
$constructedti= "";
foreach($tiParameters as $thistiParameter){

//   $textInputFormName = $thistiParameter["textinputformname"];
  $textInputName = $thistiParameter["textinputname"];
  $textValue = $thistiParameter["textvalue"];
      $textType = $thistiParameter["texttype"];

      $textSize = $thistiParameter["textsize"];
      $maskedInputChoice = $thistiParameter["maskedinputchoice"];

      $textInputLabel = $thistiParameter["label"];
      $labelOrientation = $thistiParameter["labelorientation"];


      if ($textInputLabel == NULL){
          $with_label_bool =false;
      } else {
         $with_label_bool =true; 
      }
      $currentConstructedti= $this->buildTextInput($textInputName,$textType, $textValue, $maskedInputChoice, $with_label_bool, $textInputLabel, $labelOrientation, $textSize);
      $constructedti .= $currentConstructedti;
      
//      if ($textInputLabel == NULL)
//      {
//$tiUnderConstruction = new textinput($textInputName, $textValue, $textType, $textSize);
//    if ($maskedInputChoice != "default")
//    {
//        $tiUnderConstruction->setCss($maskedInputChoice);
//         $inputMasksUnderConstruction = $this->getObject('inputmasks', 'htmlelements');
//    } else {
//        $inputMasksUnderConstruction = $this->getObject('inputmasks', 'htmlelements');
//    }
//
//   
//
//$currentConstructedti = "<div style='clear:both;'>".$inputMasksUnderConstruction->show().$tiUnderConstruction->show()."</div>";
//
//$constructedti .= $currentConstructedti;
//      }
// else {
//  $textInputLabelUnderConstruction = new label ($textInputLabel, $textInputName);
//     $tiUnderConstruction = new textinput($textInputName, $textValue, $textType, $textSize);
//
//    if ($maskedInputChoice != "default")
//    {
//        $tiUnderConstruction->setCss($maskedInputChoice);
//        $inputMasksUnderConstruction = $this->getObject('inputmasks', 'htmlelements');
//    } else {
//        $inputMasksUnderConstruction = null;
//    }
//
//    
//
//switch ($labelOrientation) {
//                case 'top':
//$currentConstructedti= $inputMasksUnderConstruction->show()."<div id='textInputLabelContainer' style='clear:both;'> ".$textInputLabelUnderConstruction->show()."</div>"
//        ."<div id='textInputContainer'style='clear:left;'> ".$tiUnderConstruction->show()."</div>";
//break;
//                case 'bottom':
//$currentConstructedti= $inputMasksUnderConstruction->show()."<div id='textInputContainer'style='clear:both;'> ".$tiUnderConstruction->show()."</div>".
//                        "<div id='textInputLabelContainer' style='clear:both;'> ".$textInputLabelUnderConstruction->show()."</div>";
//break;
//                case 'left':
//$currentConstructedti= "<div style='clear:both;overflow:auto;'>".$inputMasksUnderConstruction->show()."<div id='textInputLabelContainer' style='float:left;clear:left;'> ".$textInputLabelUnderConstruction->show()."</div>"
//        ."<div id='textInputContainer'style='float:left; clear:right;'> ".$tiUnderConstruction->show()."</div></div>";
//break;
//                case 'right':
//$currentConstructedti= "<div style='clear:both;overflow:auto;'>".$inputMasksUnderConstruction->show()."<div id='textInputContainer'style='float:left;clear:left;'> ".$tiUnderConstruction->show()."</div>".
//                        "<div id='textInputLabelContainer' style='float:left;clear:right;'> ".$textInputLabelUnderConstruction->show()."</div></div>";
//break;
//                 }
//
// $constructedti .= $currentConstructedti;
//      }

}

  return $constructedti;
}

    /*!
     * \brief This member function constructs a text input for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement and the insertTextInputParameters member
     *  functions which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed text input form element.
     */
private function buildWYSIWYGTextInputEntity()
{
 
          if ($this->tiLabel  == NULL){
          $with_label_bool =false;
      } else {
         $with_label_bool =true; 
      }
      
      return $currentConstructedti= $this->buildTextInput($this->tiName,$this->tiType, $this->tiTextValue, $this->tiTextMask, $with_label_bool, $this->tiLabel, $this->tiLabelLayout, $this->tiSize);
//      $constructedti .= $currentConstructedti;
//    if ($this->tiLabel == NULL)
//  {
//      $this->objTextInput = new textinput($this->tiName,  $this->tiTextValue, $this->tiType, $this->tiSize);
//    if ($this->tiTextMask != "default")
//    {
//        $this->objTextInput->setCss($this->tiTextMask);
//    }
//
//$objInputMasks = $this->getObject('inputmasks', 'htmlelements');
//return "<div style='clear:both;>".$objInputMasks->show().$this->objTextInput->show()."</div>";
// }
// else {
//                                 $textInputLabel = new label ($this->tiLabel,  $this->tiName);
//    $this->objTextInput = new textinput($this->tiName,  $this->tiTextValue, $this->tiType, $this->tiSize);
//   
//    if ($this->tiTextMask != "default")
//    {
//        $this->objTextInput->setCss($this->tiTextMask);
//    }
//$objInputMasks = $this->getObject('inputmasks', 'htmlelements');
//
//     switch ($this->tiLabelLayout) {
//                case 'top':
//return $objInputMasks->show()."<div class='textInputLabelContainer' style='clear:both;'> ".$textInputLabel->show()."</div>"
//        ."<div class='textInputContainer'style='clear:left;'> ".$this->objTextInput->show()."</div>";
//break;
//                case 'bottom':
//return $objInputMasks->show()."<div class='textInputContainer'style='clear:both;'> ".$this->objTextInput->show()."</div>".
//                        "<div class='textInputLabelContainer' style='clear:both;'> ".$textInputLabel->show()."</div>";
//break;
//                case 'left':
//return "<div style='clear:both;overflow:auto;'>".$objInputMasks->show()."<div class='textInputLabelContainer' style='float:left;clear:left;'> ".$textInputLabel->show()."</div>"
//        ."<div class='textInputContainer'style='float:left; clear:right;'> ".$this->objTextInput->show()."</div></div>";
//break;
//                case 'right':
//return "<div style='clear:both;overflow:auto;'>".$objInputMasks->show()."<div class='textInputContainer'style='float:left;clear:left;'> ".$this->objTextInput->show()."</div>".
//                        "<div class='textInputLabelContainer' style='float:left;clear:right;'> ".$textInputLabel->show()."</div></div>";
//break;
//                 }
// }
}

    /*!
     * \brief This member function allows you to get a text input for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed text input.
     */
public function showWYSIWYGTextInputEntity()
{
    return $this->buildWYSIWYGTextInputEntity();
}




}
?>
