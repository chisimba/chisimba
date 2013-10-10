<?php
/*
* @author Emmanuel Natalis
* @Software developer university of dar es salaam (University Computing Center)
* @copyright (c) 2008 GNU GPL
* @package happyBirthDay
* @version 1
*/
$this->objLangu=$this->getObject('language','language');
$this->deleteSuccess=$this->objLangu->languageText('mod_happybirthday_deletesuccess','happybirthday');
$this->notAvailable=$this->objLangu->languageText('mod_happybirthday_useravailable','happybirthday');
$this->happybirthday=$this->objLangu->languageText('mod_happybirthday_happybirthday','happybirthday');
$this->objEnterdate=$this->getObject('enterdate','happybirthday');

/*
The table coded below displays three columns just for layout
*/
?>
 <table style="text-align: left; width: 100%;" border="0"
 cellpadding="2" cellspacing="2">
  <tbody>
    <tr>
      <td style="width: 468px;">
<?php
/*
This is the function which display's the main menu's of the happybirthday module
*/
$this->objEnterdate->display_main_menu();
?></td>
      <td style="width: 10px;"></td>


<?php
if(isset($remove)) //This checks if the user has clicked the 'birthdate remove menu'
{
 $obj=$this->getObject('dbhappybirthday','happybirthday');
 $status=$obj->deleteBirthdate();
 if($status=='deleted')//birthdate is succesifully deleted
 {
  
   ?>
    <td style="width: 430px;">
    <?php
   echo("<font color='green'>$this->deleteSuccess </font>");
  ?>
 </td>
    </tr>
  </tbody>
</table>
 <?php
 
 } else
     if($status=='not_exist')//birthdate not available
  
   {
   
   ?> 
    <td style="width: 430px;">
    <?php
   echo "<font color='red'>$this->notAvailable </font>";
  ?>
 </td>
    </tr>
  </tbody>
</table>
 <?php
   
 }
  
} else

if(isset($view_users)) //Displaying users celebrating their birthdate today
{
  ?>
    <td style="width: 2400px;" valign='top'>
    <?php
   $obj=$this->getObject('dbhappybirthday','happybirthday');
  echo("<h1><font color='green'>".$this->happybirthday."</font></h1>");
  $obj->displayUser();
  ?>
    </td>
    </tr>
  </tbody>
</table>
 <?php
  
} 
else
if(isset($dat))
{
  ?>

      <td style="width: 430px;">
 <?php
   $this->objLangu=$this->getObject('language','language');
    $this->success=$this->objLangu->languageText('mod_happybirthday_entersuccess','happybirthday');
    $this->available=$this->objLangu->languageText('mod_happybirthday_enteravailable','happybirthday');
    $this->problem=$this->objLangu->languageText('mod_happybirthday_enterproblem','happybirthday');
    $obj=$this->getObject('dbhappybirthday','happybirthday');
    $status=$obj->insert_birthdate($dat);
    if($status=='exist')
      {
       echo "<font color='red'>$this->available </font>";
      }    else
    if($status=='inserted')
     {
      echo "<font color='green'>$this->success </font>";
     }
     else
     if($status=='error')
      {
        echo "<font color='red'>$this->problem </font>";
      }
 ?></td>
    </tr>
  </tbody>
</table>
<?php
}
 else

{
//get the happybirthday object and instantiate it
$this->obj=$this->getObject('enterdate','happybirthday');



?>

      <td style="width: 430px;">
 <?php
 //displaying the contents
  echo $this->obj->displayMsg();
  echo $this->obj->show();
 ?></td>
    </tr>
  </tbody>
</table>
<br>
<?php }?>
