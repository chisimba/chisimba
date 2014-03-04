<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_textarea
 *
 *  \brief This class models all the content and functionality for the
 * text area form element.
 * \brief It provides functionality to insert new text area, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete text areas from forms.
       * \bug The member function buildWYSIWYGTextAreaEntity is called by through by AJAX.
 * It works fine when you insert a simple text area but it does not work when inserting
 * an ckeditor because the javascript used conflicts with the javscript used in this
 * module.
  * There is a bug in the htmlarea class inside the htmlelements module. Chisimba craps out
     * when you insert a ckeditor object through ajax. The chisimba community will
 * have to fix this grave problem. For now this member function can be used but the
 * ckeditor will not be shown and there will be a javacript debug error.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';
//       $this->appendArrayVar('headerParams', $this->getJavascriptFile('1.4.2/jquery-1.4.2.min.js', 'jquery'));
//$this->appendArrayVar('headerParams', '<script type="text/javascript">jQuery.noConflict();</script>');

class form_entity_textarea extends form_entity_handler
{
    private $formNumber; 
    /*!
     * \brief Private data member that stores a text area object for the WYSIWYG
     * form editor.
     */
    private $objTextArea;

       /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $taFormName;

        /*!
     * \brief This data member stores the html name of the text area.
     */
    private $taName;

            /*!
     * \brief This data member stores the text displayed by default within the
             * text area.
     */
    private $taTextValue;

                /*!
     * \brief This data member stores width of the text area.
     */
    private $taColumnSize;

                    /*!
     * \brief This data member stores height of the text area.
     */
    private $taRowSize;

                        /*!
     * \brief This data member stores whether or not toolbar are added to the
                         * top of the text area. Either the 'simple' textarea or
                         * the 'advanced' htmlarea classes will be used to create
                         * a text area.
     */
    private $taSimpleorAdvancedChoice;

                        /*!
     * \brief If the 'advanced' htmlarea classes will be used to create
                         * a text area, this data member will be used to determine
                         * what type of toolbar will be on top of the text area.
     */
    private $toolbarChoice;

        /*!
     * \brief This data member stores the label for the text area.
     */
    private $taLabel;

            /*!
         * \brief labelOrientation A string that stores whether the form element label gets
     * put on top, bottom, left or right of the text area.
     */
    private $labelOrientation;

        /*!
     * \brief Private data member from the class \ref dbformbuilder_textarea_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete text area form elements.
     */
    protected  $objDBtaEntity;
  
    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The textarea, htmlarea and label classes are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function  init()
    {

        $this->loadClass('textarea','htmlelements');
      $this->loadClass('htmlarea','htmlelements');
        $this->loadClass('label', 'htmlelements');

        $this->taName=Null;
        $this->taFormName =NULL;
        $this->taRowSize=NULL;
        $this->taColumnSize=NULL;
        $this->taTextValue=NULL;
        $this->taSimpleorAdvancedChoice=NULL;
        $this->toolbarChoice =NULL;
        $this->taLabel=NULL;
        $this->labelOrientation;
        $this->formNumber = NULL;

        $this->objDBtaEntity = $this->getObject('dbformbuilder_textarea_entity','formbuilder');

                }

                    /*!
     * \brief This member function initializes some of the private data members for the
     * text area object.
     * \parm textAreaFormName A string for the form element identifier.
                     * \param textAreaName A string for the actual html name for
                     * the text area.
     */
    public function createFormElement($formNumber,$textAreaFormName='',$textAreaName='')
    {
    $this->formNumber = $formNumber;
    $this->taFormName = $textAreaFormName;
    $this->taName= $textAreaName; 
    }

        /*!
     * \brief This member function gets the text area form element name if the private
     * data member taName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
public function getWYSIWYGTextAreaName()
{
    return $this->taName;
}

    /*!
     * \brief This member function gets the text area name if it has already
     * been saved in the database.
     * \param textAreaFormName A string containing the form element indentifier.
     * \return A string.
     */
protected function getTextAreaName($formNumber,$textAreaFormName)
{
       $taParameters = $this->objDBtaEntity->listTextAreaParameters($formNumber,$textAreaFormName);
    $textAreaNameArray = array();

foreach($taParameters as $thistaParameter){

   //$textareaFormName = $thistaParameter["textareaformname"];
  $textareaName = $thistaParameter["textareaname"];
  $textAreaNameArray[]=$textareaName;
}
return $textAreaNameArray;
}

public function getWYSIWYGTextAreaEditForm($formNumber, $formElementName) {
        $taParameters = $this->objDBtaEntity->listTextAreaParameters($formNumber, $formElementName);
        if (empty($taParameters)) {
            return 0;
        } else {
            $textareaValue = "";
            $columnSize = "";
            $rowSize = "";
            $textareaLabel = "";
            $labelLayout = "";
            $textareaName= "";
            foreach ($taParameters as $thistaParameter) {

                //$textareaFormName = $thistaParameter["textareaformname"];
                $textareaName = $thistaParameter["textareaname"];
                $textareaValue = $thistaParameter["textareavalue"];
                $columnSize = $thistaParameter["columnsize"];
                $rowSize = $thistaParameter["rowsize"];
                //      $simpleOrAdvancedChoice = $thistaParameter["simpleoradvancedchoice"];
                //              $toolbarChoice = $thistaParameter["toolbarchoice"];
                $textareaLabel = $thistaParameter["label"];
                $labelLayout = $thistaParameter["labelorientation"];
            }
            $WYSIWYGTextInputEditForm = "<b>Edit Text Area Label Parameters</b>";
            $WYSIWYGTextInputEditForm .="<input type='hidden' name='textAreaName' id='textAreaName' value=".$textareaName." />";
            $WYSIWYGTextInputEditForm.="<div id='textAreaLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGTextInputEditForm.= $this->insertFormLabelOptions("text_input", "labelOrientationSimple", $textareaLabel, $labelLayout);
            $WYSIWYGTextInputEditForm.= "</div>";

            $WYSIWYGTextInputEditForm.="<b>Edit Text Area Size Parameters</b>";
            $WYSIWYGTextInputEditForm.="<div id='textAreaSizeMenuContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGTextInputEditForm .= $this->insertTextAreaSizeParameters($columnSize, $rowSize);
            $WYSIWYGTextInputEditForm.= "</div>";

            $WYSIWYGTextInputEditForm.="<b>Edit Text Area Properties</b>";
            $WYSIWYGTextInputEditForm.="<div id='textAreaPropertiesContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGTextInputEditForm.= $this->insertTextForm('text area', 2, 68,$textareaValue);
            $WYSIWYGTextInputEditForm.= "</div>";
            return $WYSIWYGTextInputEditForm;
        }
    }
    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the text area parameters to insert
     * a text area form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \bug There might be a problem in IE with the divs. Sometimes the javascript
     * messes up the div arragement so some of the parameters dont get saved.
     * \param formName A string.
     * \return A constructed text area insert form.
     */
    public function getWYSIWYGTextAreaInsertForm($formName)
    {
               $WYSIWYGTextInputInsertForm="<div id='ALL'>";
        $WYSIWYGTextInputInsertForm.="<div id='simpleTAForm'>";

         $WYSIWYGTextInputInsertForm.="<b>Text Area HTML ID and Name Menu</b>";
      $WYSIWYGTextInputInsertForm.="<div id='textAreaNameAndIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
        $WYSIWYGTextInputInsertForm.= $this->buildInsertIdForm('textarea',$formName,"70")."<br>";
       $WYSIWYGTextInputInsertForm.= $this->buildInsertFormElementNameForm('text area', "70",NULL)."<br>";
                $WYSIWYGTextInputInsertForm.= "</div>";
   //     $WYSIWYGTextInputInsertForm.= "</div>";

      $WYSIWYGTextInputInsertForm.="<b>Text Area Label Menu</b>";
            $WYSIWYGTextInputInsertForm.="<div id='textAreaLabelContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
 $WYSIWYGTextInputInsertForm.= $this->insertFormLabelOptions("text_input","labelOrientationSimple",NULL,NULL);
          $WYSIWYGTextInputInsertForm.= "</div>";

                $WYSIWYGTextInputInsertForm.="<b>Text Area Size Menu</b>";
            $WYSIWYGTextInputInsertForm.="<div id='textAreaSizeMenuContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
              $WYSIWYGTextInputInsertForm .= $this->insertTextAreaSizeParameters(NULL,NULL);
                              $WYSIWYGTextInputInsertForm.= "</div>";

                              $WYSIWYGTextInputInsertForm.="<b>Text Area Properties Menu</b>";
           $WYSIWYGTextInputInsertForm.="<div id='textAreaPropertiesContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
            $WYSIWYGTextInputInsertForm.= $this->insertTextForm('text area',2,68,NULL);
                                          $WYSIWYGTextInputInsertForm.= "</div>";
                                            $WYSIWYGTextInputInsertForm.= "</div>";
             // $WYSIWYGTextInputInsertForm.="</div>";
                //$WYSIWYGTextInputInsertForm.="</div>";
                          $WYSIWYGTextInputInsertForm.="<div id='advancedTAForm'>";
                          $WYSIWYGTextInputInsertForm.="<b>This feature is Comming Soon!!!</b>";
//                           $WYSIWYGTextInputInsertForm.="<b>Text Area HTML ID and Name Menu</b>";
//      $WYSIWYGTextInputInsertForm.="<div id='textAreaNameAndIDContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
//                      $WYSIWYGTextInputInsertForm.= $this->buildInsertIdForm('textarea',$formName,"70")."<br>";
//       $WYSIWYGTextInputInsertForm.= $this->buildInsertFormElementNameForm('text area', "70")."<br>";
//                       $WYSIWYGTextInputInsertForm.= "</div>";
//                            //  $WYSIWYGTextInputInsertForm.= "</div>";
////                                              $WYSIWYGTextInputInsertForm.= "</div>";
//                             $WYSIWYGTextInputInsertForm.="<b>Text Area Label Menu</b>";
//            $WYSIWYGTextInputInsertForm.="<div id='textAreaLabelContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
// $WYSIWYGTextInputInsertForm.= $this->insertFormLabelOptions("text_input","labelOrientationAdvanced");
//          $WYSIWYGTextInputInsertForm.= "</div>";
//                        $WYSIWYGTextInputInsertForm.="<b>Text Area Size Menu</b>";
//            $WYSIWYGTextInputInsertForm.="<div id='textAreaSizeMenuContainer' class='ui-widget-content ui-corner-all' style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
//       $WYSIWYGTextInputInsertForm .= $this->insertTextAreaSizeParameters();
//                     $WYSIWYGTextInputInsertForm.="</div>";
//                      $WYSIWYGTextInputInsertForm.="<b>Text Area Properties Menu</b>";
//           $WYSIWYGTextInputInsertForm.="<div id='textAreaPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
//                       $WYSIWYGTextInputInsertForm.= $this->insertToolbarChoiceTextAreaOptions();
//              $WYSIWYGTextInputInsertForm.= $this->insertTextForm('text area',2,68);
                                                        $WYSIWYGTextInputInsertForm.= "</div>";
              $WYSIWYGTextInputInsertForm.= "</div>";
                         //$WYSIWYGTextInputInsertForm.= "</div>";
           return $WYSIWYGTextInputInsertForm;
    }

        /*!
     * \brief This member function allows you to insert a text area in a form with
     * a form element identifier.
     * \brief Before a new text area gets inserted into the database,
     * duplicate entries are checked if there is another text area.
     * in this same form with the same form element identifier.
     * \param textareaformname A string for the form element identifier.
         * \param textareavalue A string for default text inside the text area.
     * \param textareaname A string for the actual html name for the text area.
     * \param columnsize An integer to specify the width of the text area.
         * \param rowsize A integer to specify the height of the text area.
     * \param simpleoradvancedchoice A strin to store whether or not toolbars will
         * be displayed. Two possibilities exist ie simple for no toolbar and
         * advanced for toolbars shown on top of the text area.
         * \param toolbarchoice If the advanced text area is chosen this string
         * determines what type of toolbar will be displayed.
     * \param formElementLabel A string for the actual label text for the entire text area
         * form element.
     * \param labelLayout A string that stores whether the form element label gets
     * put on top, bottom, left or right of the text area form element.
     * \return A boolean value on successful storage of the text area form element.
     */
public function insertTextAreaParameters($formNumber,$textareaformname,$textareaname,$textareavalue,$columnsize,$rowsize,$simpleoradvancedchoice,$toolbarchoice,$formElementLabel,$labelLayout)
{

    if ($this->objDBtaEntity->checkDuplicateTextAreaEntry($formNumber,$textareaformname,$textareaname) == TRUE)
    {
        $this->objDBtaEntity->insertSingle($formNumber,$textareaformname,$textareaname,$textareavalue,$columnsize,$rowsize,$simpleoradvancedchoice,$toolbarchoice,$formElementLabel,$labelLayout);

        $this->taName = $textareaname;
        $this->taRowSize=$rowsize;
        $this->taColumnSize=$columnsize;
        $this->taTextValue=$textareavalue;
        $this->taSimpleorAdvancedChoice=$simpleoradvancedchoice;
        $this->toolbarChoice=$toolbarchoice;
        $this->taLabel=$formElementLabel;
        $this->labelOrientation=$labelLayout;
    return TRUE;
    }
 else {
        return FALSE;
    }
}

public function updateTextAreaParameters($formNumber,$textareaformname,$textareaname,$textAreaValue,$ColumnSize,$RowSize,$simpleOrAdvancedHAChoice,$toolbarChoice,$formElementLabel,$labelLayout){
        if ($this->objDBtaEntity->checkDuplicateTextAreaEntry($formNumber,$textareaformname,$textareaname) == FALSE)
    {
        $this->objDBtaEntity->updateSingle($formNumber,$textareaformname,$textareaname,$textAreaValue,$ColumnSize,$RowSize,$simpleOrAdvancedHAChoice,$toolbarChoice,$formElementLabel,$labelLayout);

        $this->taName = $textareaname;
        $this->taRowSize=$RowSize;
        $this->taColumnSize=$ColumnSize;
        $this->taTextValue=$textAreaValue;
        $this->taSimpleorAdvancedChoice=$simpleOrAdvancedHAChoice;
        $this->toolbarChoice=$toolbarChoice;
        $this->taLabel=$formElementLabel;
        $this->labelOrientation=$labelLayout;
    return TRUE;
    }
 else {
        return FALSE;
    }
}


   /*!
     * \brief This member function coverts an integer for the row size into a
    * px format and returns it.
     * \param rowSize An intger for the row size. The integer will specify how
    * many lines of text can be visible at one time.
     * \return A string for the pixel format of the row size.
     */
private function convertTextAreaRowSizeToHTMLRowSize($rowSize)
{
    return 130 + $rowSize."px";
}

   /*!
     * \brief This member function coverts an integer for the column size into a
    * percentage format and returns it.
     * \param columnSize An intger for the column size. The integer will specify how
    * many characters of text can be visible at one time.
     * \return A string for the percantage format of the whole screen of the column size.
     */
private function convertTextAreaColumnSizeToHTMLColumnSize($columnSize)
{
    return 0.7*$columnSize."%";
}

    /*!
     * \brief This member function deletes an existing text area form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a text area or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
protected function deleteTextAreaEntity($formNumber,$formElementName)
{
    $deleteSuccess = $this->objDBtaEntity->deleteFormElement($formNumber,$formElementName);
    return $deleteSuccess;
}

    /**
     * \brief This builds the html content for build text area.
     * \param id This string sets the unique id for the build text area.
     * \param default_text This string specifies default text in the text area.
     * \param class This string specifies class build area input belongs to.
     * \param with_label_bool This string specify whether to make it with a label '
     * or without a lable specified by True or false.
     * \param label_text This string specifies the label text for the text area.
     * \param label_orientation This string specifies the orientation of the
     *  orientation.
     * \param rows This integer specifies the number of rows needed for the build
     *  text area.
     * \param column This integer specifies the number of columns needed for the
     * build text area.
     * \return This returns the build text area.
     */
    public function buildTextArea($id, $default_text, $class, $with_label_bool, $label_text, $label_orientation, $rows='5', $columns='35') {

        if ($id == null) {
            return "Error. Text Input Parameters not defined.";
        }

        if ($with_label_bool == true) {
            if ($class == null) {
                $textAreaUnderConstruction = "<textarea id=\"$id\" name=\"$id\" rows=$rows cols=$columns>$default_text</textarea>";
            } else {
                $textAreaUnderConstruction = "<textarea id=\"$id\" name=\"$id\" class=$class rows=$rows cols=$columns>$default_text</textarea>";
            }
            $labelUnderConstruction = "<label for='".$id."'>".$label_text."</label>";
            
                 switch ($label_orientation) {
                case 'top':
return $currentConstructedta = "<div class='textAreaLabelContainer' style='clear:both;'> ".$labelUnderConstruction."</div>"
        ."<div class='textAreaContainer'style='clear:left;'> ".$textAreaUnderConstruction."</div>";
break;
                case 'bottom':
return $currentConstructedta = "<div class='textAreaContainer'style='clear:both;'> ".$textAreaUnderConstruction."</div>".
                        "<div class='textAreaLabelContainer' style='clear:both;'> ".$labelUnderConstruction."</div>";
break;
                case 'left':
return $currentConstructedta ="<div style='clear:both;overflow:auto;'>"."<div class='textAreaLabelContainer' style='float:left;clear:left;'> ".$labelUnderConstruction."</div>"
        ."<div class='textAreaContainer'style='float:left; clear:right;'> ".$textAreaUnderConstruction."</div></div>";
break;
                case 'right':
return $currentConstructedta = "<div style='clear:both;overflow:auto;'>"."<div class='textAreaContainer'style='float:left;clear:left;'> ".$textAreaUnderConstruction."</div>".
                        "<div class='textAreaLabelContainer' style='float:left;clear:right;'> ".$labelUnderConstruction."</div></div>";
break;
                 }
            
        } else {
            if ($class == null) {
                return "<textarea id=\"$id\" name=\"$id\" rows=$rows cols=$columns>$default_text</textarea>";
            } else {
                return "<textarea id=\"$id\" name=\"$id\" class=$class rows=$rows cols=$columns>$default_text</textarea>";
            }
        }
    }

    /*!
     * \brief This member function constructs a text area form element for a the actual form
     * rendering from the database.
     * \param textareaName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed textarea object.
     */
protected function constructTextAreaEntity($textareaName,$formNumber)
{
    $taParameters = $this->objDBtaEntity->listTextAreaParameters($formNumber,$textareaName);
$constructedta = "";
foreach($taParameters as $thistaParameter){

 //  $textareaFormName = $thistaParameter["textareaformname"];
  $textareaName = $thistaParameter["textareaname"];
      $textareaValue = $thistaParameter["textareavalue"];
      $columnSize = $thistaParameter["columnsize"];
      $rowSize = $thistaParameter["rowsize"];
            $simpleOrAdvancedChoice = $thistaParameter["simpleoradvancedchoice"];
          //        $toolbarChoice = $thistaParameter["toolbarchoice"];
                  $textareaLabel= $thistaParameter["label"];
                  $labelLayout = $thistaParameter["labelorientation"];
                  
  if ($textareaLabel == NULL){
      $with_label_bool = false;
  }  else {
      $with_label_bool = true;
  }
  
  $currentConstructedta = $this->buildTextArea($textareaName, $textareaValue, $simpleOrAdvancedChoice, $with_label_bool, $textareaLabel, $labelLayout, $rowSize, $columnSize);
  $constructedta .= $currentConstructedta;
//if ($textareaLabel == NULL)
//{
//    if ($simpleOrAdvancedChoice == "textarea")
//    {
//$taUnderConstruction= new textarea($textareaName,$textareaValue,$rowSize,$columnSize);
//    $currentConstructedta = $taUnderConstruction->show();
//
//    }
// else {
//$taUnderConstruction = $this->newObject('htmlarea','htmlelements');
//$taUnderConstruction->setName($textareaName);
//$taUnderConstruction->setContent($textareaValue);
//$taUnderConstruction->width = $this->convertTextAreaColumnSizeToHTMLColumnSize($columnSize);
//$taUnderConstruction->height =$this->convertTextAreaRowSizeToHTMLRowSize($rowSize);
//$taUnderConstruction->toolbarSet= $toolbarChoice;
//    $currentConstructedta = $taUnderConstruction->show();
//    }
//
// $constructedta .= $currentConstructedta;
//}
// else {
//  $textAreaLabel = new label ($textareaLabel, $textareaName);
//     if ($simpleOrAdvancedChoice == "textarea")
//    {
//$taUnderConstruction= new textarea($textareaName,$textareaValue,$rowSize,$columnSize);
//    $currentConstructedta = $taUnderConstruction->show();
//     switch ($labelLayout) {
//                case 'top':
//$currentConstructedta = "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='clear:left;'> ".$taUnderConstruction->show()."</div>";
//break;
//                case 'bottom':
//$currentConstructedta = "<div class='textAreaContainer'style='clear:both;'> ".$taUnderConstruction->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>";
//break;
//                case 'left':
//$currentConstructedta ="<div style='clear:both;overflow:auto;'>"."<div class='textAreaLabelContainer' style='float:left;clear:left;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='float:left; clear:right;'> ".$taUnderConstruction->show()."</div></div>";
//break;
//                case 'right':
//$currentConstructedta = "<div style='clear:both;overflow:auto;'>"."<div class='textAreaContainer'style='float:left;clear:left;'> ".$taUnderConstruction->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='float:left;clear:right;'> ".$textAreaLabel->show()."</div></div>";
//break;
//                 }
//       $constructedta .= $currentConstructedta;
//    }
// else {
//
//     $taUnderConstruction = $this->newObject('htmlarea','htmlelements');
//$taUnderConstruction->setName($textareaName);
//$taUnderConstruction->setContent($textareaValue);
//$taUnderConstruction->width = $this->convertTextAreaColumnSizeToHTMLColumnSize($columnSize);
//$taUnderConstruction->height =$this->convertTextAreaRowSizeToHTMLRowSize($rowSize);
//$taUnderConstruction->toolbarSet= $toolbarChoice;
//
//     switch ($labelLayout) {
//                case 'top':
//$currentConstructedta = "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='clear:left;'> ".$taUnderConstruction->show()."</div>";
//break;
//                case 'bottom':
//$currentConstructedta = "<div class='textAreaContainer'style='clear:both;'> ".$taUnderConstruction->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>";
//break;
//                case 'left':
//$currentConstructedta ="<div style='clear:both;overflow:auto;'>"."<div class='textAreaLabelContainer' style='float:left;clear:left;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='float:left; clear:right;'> ".$taUnderConstruction->show()."</div></div>";
//break;
//                case 'right':
//$currentConstructedta = "<div style='clear:both;overflow:auto;'>"."<div class='textAreaContainer'style='float:left;clear:left;'> ".$taUnderConstruction->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='float:left;clear:right;'> ".$textAreaLabel->show()."</div></div>";
//break;
//                 }
//                  $constructedta .= $currentConstructedta;
//    }
//}
}


  return $constructedta;
}

    /*!
     * \brief This member function constructs a text area for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement and the insertTextAreaParameters member
     *  functions which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed text area form element.
     */
private function buildWYSIWYGTextAreaEntity()
{
   
      if ($this->taLabel == NULL){
      $with_label_bool = false;
  }  else {
      $with_label_bool = true;
  }
  
  return $currentConstructedta = $this->buildTextArea($this->taName, $this->taTextValue, $this->taSimpleorAdvancedChoice, $with_label_bool, $this->taLabel, $this->labelOrientation, $this->taRowSize, $this->taColumnSize);
//  $constructedta .= $currentConstructedta;
  
//    if ($this->taLabel==NULL)
//   {
//    if ($this->taSimpleorAdvancedChoice == "textarea")
//    {
//        $this->objTextArea= new textarea($this->taName,$this->taTextValue,$this->taRowSize,$this->taColumnSize);
//       return $this->objTextArea->show();
//    }
// else {
//  $this->objTextArea = $this->newObject('htmlarea','htmlelements');
//  $this->objTextArea->setName($this->taName);
//     $this->objTextArea->setContent($this->taTextValue);
//     $this->objTextArea->width =$this->convertTextAreaColumnSizeToHTMLColumnSize($this->taColumnSize);
//     $this->objTextArea->height =$this->convertTextAreaRowSizeToHTMLRowSize($this->taRowSize);
//       $this->objTextArea->toolbarSet= $this->toolbarChoice;
//return $this->objTextArea->show();
//    }
//   }
// else {
//        $textAreaLabel = new label ($this->taLabel, $this->taName);
//        if ($this->taSimpleorAdvancedChoice == "textarea")
//    {
//        $this->objTextArea= new textarea($this->taName,$this->taTextValue,$this->taRowSize,$this->taColumnSize);
//    
//             switch ($this->labelOrientation) {
//                case 'top':
//return "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='clear:left;'> ".$this->objTextArea->show()."</div>";
//break;
//                case 'bottom':
//return "<div class='textAreaContainer'style='clear:both;'> ".$this->objTextArea->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>";
//break;
//                case 'left':
//return "<div style='clear:both;overflow:auto;'>"."<div class='textAreaLabelContainer' style='float:left;clear:left;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='float:left; clear:right;'> ".$this->objTextArea->show()."</div></div>";
//break;
//                case 'right':
//return "<div style='clear:both;overflow:auto;'>"."<div class='textAreaContainer'style='float:left;clear:left;'> ".$this->objTextArea->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='float:left;clear:right;'> ".$textAreaLabel->show()."</div></div>";
//break;
//                 }
//    }
// else {
//      $textAreaLabel = new label ($this->taLabel, $this->taName);
//  $this->objTextArea = $this->newObject('htmlarea','htmlelements');
//  $this->objTextArea->setName($this->taName);
//     $this->objTextArea->setContent($this->taTextValue);
//     $this->objTextArea->width =$this->convertTextAreaColumnSizeToHTMLColumnSize($this->taColumnSize);
//     $this->objTextArea->height =$this->convertTextAreaRowSizeToHTMLRowSize($this->taRowSize);
//       $this->objTextArea->toolbarSet= $this->toolbarChoice;
//
//       
//   switch ($this->labelOrientation) {
//                case 'top':
//return "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='clear:left;'> ".$this->objTextArea->show()."</div>";
//break;
//                case 'bottom':
//return "<div class='textAreaContainer'style='clear:both;'> ".$this->objTextArea->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='clear:both;'> ".$textAreaLabel->show()."</div>";
//break;
//                case 'left':
//return "<div style='clear:both;overflow:auto;'>"."<div class='textAreaLabelContainer' style='float:left;clear:left;'> ".$textAreaLabel->show()."</div>"
//        ."<div class='textAreaContainer'style='float:left; clear:right;'> ".$this->objTextArea->show()."</div></div>";
//break;
//                case 'right':
//return "<div style='clear:both;overflow:auto;'>"."<div class='textAreaContainer'style='float:left;clear:left;'> ".$this->objTextArea->show()."</div>".
//                        "<div class='textAreaLabelContainer' style='float:left;clear:right;'> ".$textAreaLabel->show()."</div></div>";
//break;
//                 }
//    }
//
//   }
}

    /*!
     * \brief This member function allows you to get a text area for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed text area.
     */
public function showWYSIWYGTextAreaEntity()
{
    return $this->buildWYSIWYGTextAreaEntity();
}




}
?>
