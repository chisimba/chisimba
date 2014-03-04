<?php
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

  class view_bysubject extends object
  {

   public $loadupload;

   var $subjectname;
   var $academicyear;
   var $term;

    public function  init() {
        $this->loadelements = $this->newObject('reportdisplay','tzschoolacademics');
        $this->loadupload = $this->newObject('upload_marks', 'tzschoolacademics');
        $this->db_access = $this->newObject('marksdb', 'tzschoolacademics');
        $this->dbchange_access = $this->newObject('dbsubjectmodule','tzschoolacademics');
    }


    public function view_form()
    {
       $this->loadupload->loadElements();

       // create new form instance
        $objform = new form('viewbysubject');
        $objform->action=$this->uri(array('action' =>'display'),'tzschoolacademics');
        $objform->setDisplayType(2);

   //---------------------------------------------------------------------------------------------
       // this sets subject dropdown
        $label = new label('choose subject');
        $objdropdown = new dropdown('subject');
        $displaysubject = $this->db_access->load_subjects();
        $objdropdown->addFromDB( $displaysubject, 'subject_name', 'puid');

   //--------------------------------------------------------------------------------------------

      // this sets academic year dropdown
      $acalabel= new label('academic year');
      $objacademicdrop = new dropdown('aca_year');
      $displayacademicyear = $this->db_access->load_academic_year(); 
      $objacademicdrop->addFromDB($displayacademicyear, 'year_name', 'puid');

  //----------------------------------------------------------------------------------------------

      // this sets term in a dropdown
      $termlabel = new label('term');
      $objtermdrop = new dropdown('term');
      $displayterm = $this->db_access->load_term();
      $objtermdrop->addFromDB($displayterm, 'term_name', 'puid');

   //---------------------------------------------------------------------------------------------
      //this set submit button
        $objsubmit = new button('display', 'display');
        $objsubmit->setToSubmit();
        $objsubmit->setToReset();
  //------------------------------------------------------------
        $objform->addToForm($label->show());

        $objform->addToForm($objdropdown->show());

         $objform->addToForm( $acalabel->show());
         $objform->addToForm( $objacademicdrop->show());

        $objform->addToForm($termlabel->show());
        $objform->addToForm($objtermdrop->show());

        $objform->addToForm($objsubmit->show());

      return $objform->show();
  }

  
  public function  change_results($subject,$academic_year,$term)
  {
    $this->loadelements->get_html_elements();
   $output = $this->dbchange_access->view_bysub($subject,$academic_year,$term);
   if ($output != 0)
   {


   $objHref = $this->newObject('href','htmlelements');
        //initiating the table for carrying and formating output result
        $data_table = $this->newObject('htmltable', 'htmlelements');
        $data_table->border = 0;
        $data_table->width = '90%';
        $data_table->cellPadding = '2px';
        //table header
         $objHref_edit = $this->newObject('href','htmlelements');
         $objHref_edit->text = 'edit';
         
                $data_table->startHeaderRow();
                $data_table->addHeaderCell('No');
                $data_table->addHeaderCell('Registration no');
                $data_table->addHeaderCell('First Name');
                $data_table->addHeaderCell('Othername');
                $data_table->addHeaderCell('Last Name');
                $data_table->addHeaderCell('Marks');
                $data_table->addHeaderCell('edit');

                $data_table->endHeaderRow();

       $sn = 0;
  foreach($output as $row)
  {
      $regno = $row['tbl_student_reg_no'];
             $sn++;
          
                    $data_table->startRow();
                    $data_table->addCell($sn);
                    $data_table->addCell($row['tbl_student_reg_no']);
                    $data_table->addCell($row['firstname']);
                    $data_table->addCell($row['lastname']);
                    $data_table->addCell($row['othernames']);
                    $data_table->addCell($row['score']);
                    $objHref_edit->link ='?module=tzschoolacademics &action=editresults &id='.$row['tbl_student_reg_no'] .'&subject='.$subject .'&aca_year='.$academic_year .'&term='.$term ;
                    $data_table->addCell($objHref_edit->show());
                    $data_table->endRow();
      
  }
       $subjectname  =  $this->dbchange_access->return_subjectname($subject);
       $academicyear =  $this->dbchange_access->return_academicyear($academic_year);
       $term   =        $this->dbchange_access->return_term($term);


  $heading = "<h4><ul> RESULTS FOR ".$subjectname." ACADEMIC YEAR". $academicyear . " TERM". $term;

 return $heading.$data_table->show();
      
  }
  else
  {
   $message = "no results found matching that criteria";
   return $message;
  }
  }
  public function edit_resultform($regno,$subj,$acayear,$term)
  {
      
   $this->loadelements->get_html_elements();
   $output = $this->dbchange_access->edit_results($regno,$subj,$acayear,$term);

   foreach ( $output as $row)
   {
      $firstname = $row['firstname'];

      $surname = $row['lastname'];

      $othername = $row['othernames'];

      $score = $row['score'];

      $regno = $row['tbl_student_reg_no'];
       
   }

  // create new form instance
   $objform = new form('editbysubject');
   $objform->action=$this->uri(array('action' =>'editbysubject'),'tzschoolacademics');
   $objform->setDisplayType(2);

//------------------------------------------------------------------------------------------------
    // this sets the registration number as hidden
    $objregno = new textinput($name='regno', $value=null, $type='hidden', $size=null);
    $objregno->setvalue($regno);
    //---------------------------------------------------------------------------------------------
       // this sets student first name
         $fnlabel = new label('first name');
         $objFname = new textinput('fname');
         $objFname->setValue($firstname);
    //-----------------------------------------------------------------------------------------------
       // this sets student surname
        $snlabel = new label('surname');
        $objsurname = new textinput('surname');
         $objsurname->setValue($surname);
  //-------------------------------------------------------------------------------------------------
      //this sets othername

       $otlabel = new label('othername');
       $objothername = new textinput('othername');
        $objothername->setValue($othername);
   //-----------------------------------------------------------------------------------------------
     // this sets the score
        $sclabel = new label('score');
        $objscore = new textinput('score');
        $objscore->setValue($score);

   //-------------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
      //this set submit button
        $objsubmit = new button('edit', 'edit');
        $objsubmit->setToSubmit();
        $objsubmit->setToReset();
  //------------------------------------------------------------
        $objform->addToForm($fnlabel->show());

        $objform->addToForm($objFname->show());
        
        $objform->addToForm($snlabel->show());
        $objform->addToForm($objsurname->show());


        $objform->addToForm($otlabel ->show());
        $objform->addToForm($objothername->show());

        $objform->addToForm($sclabel ->show());
        $objform->addToForm( $objscore->show());

        $objform->addToForm($objsubmit->show());

        $objform->addToForm($objregno->show());


   //------------------------------------------------------------------------------------------------
         return $objform->show();
  }

   

  }
?>
