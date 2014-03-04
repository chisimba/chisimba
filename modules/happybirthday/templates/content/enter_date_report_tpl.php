<?php
    $this->objLangu=$this->getObject('language','language');
    $this->success=$this->objLangu->languageText('mod_happybirthday_entersuccess','happybirthday');
    $this->available=$this->objLangu->languageText('mod_happybirthday_enteravailable','happybirthday');
    $this->problem=$this->objLangu->languageText('mod_happybirthday_enterproblem','happybirthday');
    $obj=$this->getObject('dbhappybirthday','happybirthday');
    $status=$obj->insert_birthdate($dat);
    if($status=='exist')
      {
       echo $this->available;
      }    else
    if($status=='inserted')
     {
      echo $this->success;
     }
     else
     if($status=='error')
      {
        echo $this->problem;
      }
?>
