<?php
/* 
 * The contents of this file are subject to the Meeting Manager Public license you may not use or change this file except in
 * compliance with the License. You may obtain a copy of the License by emailing this address udsmmeetingmanager@googlegroups.com
 *  @author victor katemana
 *  @email princevickatg@gmail.com


 */
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


  class marksdb  extends dbtable
  {



 public function init()
   {
//Set the table in the parent class
parent::init('tbl_subjects');
parent::init('tbl_class');
parent::init('tbl_term');
parent::init('tbl_academic_year');
parent::init('tbl_exam');
   }

 public function load_subjects()
 {
  $this->_tableName = 'tbl_subjects';

  $result = $this->getAll();
  if($result)
  {
      return $result;
  }
  else
  {
      return FALSE;
  }
 }

  public function load_classes()
  {
    $this->_tableName = 'tbl_class';
    $result = $this->getAll();
    if($result)
    {
        return $result;
    }
    else
    {
        return False;
    }



  }

  public function load_term ()
  {
   $this->_tableName = 'tbl_term';
   $result = $this->getAll();
   if($result)
   {
       return $result;
   }
   else
   {
       return FALSE;
   }

  }



   public function get_term_name ($term_id)
  {
   $this->_tableName = 'tbl_term';
   $filter="WHERE term_name='$term_id' ";
   $result = $this->getAll($filter);
   if($result)
   {
       return $result;
   }
   else
   {
       return FALSE;
   }

  }



  public function load_exam_type()
  {
      $this->_tableName = 'tbl_exam';
    $result = $this->getAll();
    if($result)
    {
        return $result;
    }
    else
    {
        return FALSE;
    }
  }


  public function load_academic_year()
  {
      $this->_tableName = 'tbl_academic_year';

      $result = $this->getAll();
      if($result)
      {
        return $result;
      }
      else
      {

       return FALSE;
      }



      
  }


  

  }


?>
