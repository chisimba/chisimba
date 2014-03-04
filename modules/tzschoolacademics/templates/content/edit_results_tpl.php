<?php
switch ($option)
{
 case 'change_result':
 echo "data successful edited";
 break;

 case 'editresults':

$displayObj=$this->newObject('view_bysubject', 'tzschoolacademics');
echo $displayObj->edit_resultform($regno,$subj,$acayear,$term);
}



?>

