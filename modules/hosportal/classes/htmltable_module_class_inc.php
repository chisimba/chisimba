<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'chisimba_modules_handler_class_inc.php';

class htmltable_module extends chisimba_modules_handler
{

private $objTable;

    public function init()
    {
        // $this->objform= $this->loadClass('form','htmlelements');
//          $this->$objForm = new form($name_of_form, $form_action);
       // $this->objLanguage = $this->getObject('language','language');
    }
    public function createNewObjectFromModule($name_of_class = "htmltable" , $name_of_module = "htmlelements")
    {
// return   $this->objForm = new form($name_of_form, $form_action);
 return $this->objTable = $this->newObject($name_of_class, $name_of_module);
}

public function EditModule()
{
}
public function setBorderThickness($border_thickness = 0)
{
    return $this->objTable->border = $border_thickness;
}
public function setCellPadding($border_spacing = 12)
{
    return $this->objTable->cellspacing = $border_spacing;
}

public function setCellWidth($width_percentage_to_text = "40%")
{
    return $this->objTable->width = $width_percentage_to_text;
}
public function addLabelsToHeader($name_of_array = NULL,$label_for_object = "NoName")
        {
    return $this->objTable->addHeader($name_of_array, $label_for_object);
        }
public function beginTableRow()
        {
  return $this->objTable->startRow();
        }
        public function beginHeaderTableRow()
        {
         return $this->objTable->startHeaderRow();
        }
        
      public function addHeaderCellWithObject($name_of_object="   ",$width = NULL, $vertical_alignment = "top", $text_alignment = null, $class = null, $atributes = null)
              {
        return $this->objTable->addHeaderCell($name_of_object,$width, $vertical_alignment, $text_alignment, $class, $atributes);
              } 

                public function endHeaderTableRow()
        {
         return $this->objTable->endHeaderRow();
        }
    //    ($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
public function addCellwithObject($name_of_object="   ",$width = NULL, $vertical_alignment = "top", $text_alignment = null, $class = null, $attributes=null,$border_thickness = '0')
        {
  return $this->objTable->addCell($name_of_object,$width,$vertical_alignment,$text_alignment,$class,$attributes,$border_thickness);
        }
        public function endTableRow()
        {
      return $this->objTable->endRow();
        }
        public function setAlternateColorsForRows($bolean_value = false)
        {
        return $this->objTable->active_rows = $bolean_value;
        }
public function showBuiltTable()
        {
       return $this->objTable->show();
        }
    //    $commentsTable->show()
   //$commentsTable->startRow();
//   $commentsTable->addCell($title);
//   $commentsTable->addCell($commenttxt);
//   $commentsTable->addCell($linkEdManage);
//   $commentsTable->addCell($objConfirm->show());
//   $commentsTable->endRow();
//  $commentsTable = $this->newObject("htmltable", "htmlelements");
//  //Define the table border
//  $commentsTable->border = 0;
//  //Set the table spacing
//  $commentsTable->cellspacing = '12';
//  //Set the table width
//  $commentsTable->width = "40%";
 //$commentsTable->addHeader($tableHeader, "heading");




}


?>
