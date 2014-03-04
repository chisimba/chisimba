<?php

/**
 * Description: Layout template, for displaying html list side menus, to be used by all templates
 * @since 4th May 2011
 *
 * @author - Academic Module Team
 */

 $sidemenus = '<ul type="square" id="ac_side_menu">';
 $sidemenus .= '<li><a href="?module=tzschoolacademics">Home</a></li>';
 $sidemenus .= '<li>Reports';
 $sidemenus .= '    <ul type="square">';
 $sidemenus .= '         <li><a href="?module=tzschoolacademics&action=StudentResults">Student Result</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=ClassResult">Class Result</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=SubjectResults">Subject Results</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=FailedStudents"> Failed Students</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=BestStudents"> Best Students</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=StudentReport">Student Report</a></li>';
 $sidemenus .= '     </ul>';
 $sidemenus.='</li>';
 $sidemenus .= '<li>';
 $sidemenus .= 'Results';
 $sidemenus .= '    <ul type="square">';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=upload_result">Upload</a></li>';
 $sidemenus .= '        <li>View</li>';
 $sidemenus .= '    <ul>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=view_subject">View by subject</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=search">Search</a></li>';
 $sidemenus .= '    </ul>';
 $sidemenus .= '    </ul>';
 $sidemenus .= '</li>';
 $sidemenus .= '<li>Registration';
 $sidemenus .= '    <ul type="square">';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=register_student">Students</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=register_teacher">Teachers</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=register_subject">Subjects</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=register_class">Classes</a></li>';
 $sidemenus .= '        <li><a href="?module=tzschoolacademics&action=reg_setup">Setup</a></li>';
 $sidemenus .= '    </ul>';
 $sidemenus .= '</li>';
 $sidemenus .= '<li>Setup</li>';

 $sidemenus .= '</ul>';

 $objCssLayout = $this->newObject('csslayout','htmlelements');
 $objCssLayout->numColumns = 2;
 $objCssLayout->setLeftColumnContent($sidemenus);
 $objCssLayout->setMiddleColumnContent($this->getContent());
 
 echo $objCssLayout->show();
 
?>
