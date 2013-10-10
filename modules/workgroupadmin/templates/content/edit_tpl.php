<?php 
    // Display heading.
	echo "<h1>".$objLanguage->languageText("mod_workgroupadmin_workgroup")." : ".$workgroup['description']."</h1>";
    // Display members.
	echo "<h1>".$objLanguage->languageText('mod_workgroupadmin_members')."</h1>";
	echo "<table>";
	$index = 0;
	foreach ($members as $member) {
		if (($index%2)==0) {
			$class = "even";
		}
		else {
			$class = "odd";
		}
		echo "<tr>";
		echo "<td class=\"".$class."\">";
		echo $member['firstName'] . "&nbsp;" . $member['surname'];
		echo "</td>";
		echo "<td class=\"".$class."\">";
        // Remove student.
		echo "<a href=\"".
			$this->uri(array(
		    	'module'=>'workgroups',
				'action'=>'removeUser',
				'workgroupId'=>$workgroup['id'],
				'userId'=>$member['userId']
			))
		."\">".$objLanguage->languageText("mod_workgroupadmin_remove")."</a>"."<br/>";
		echo "</td>";
		echo "</tr>";
		$index++;
	}
	echo "</table>";
    if (empty($members)) {
        echo "<span class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound') . "</span>";
    }
    echo("<br/>");
    echo "<table>";
    echo "<tr>";
    // Show lecturers.
    echo "<td valign=\"top\">";
	echo "<h1>".$objLanguage->languageText('mod_workgroupadmin_lecturers')."</h1>";
	echo "<table>";
	$index = 0;
	foreach ($lecturers as $user) {
		if (($index%2)==0) {
			$class = "even";
		}
		else {
			$class = "odd";
		}
		echo "<tr>";
		echo "<td class=\"".$class."\">";
		echo $user['firstName'] . "&nbsp;" . $user['surname'];
		echo "</td>";
		echo "<td class=\"".$class."\">";
        // Add student.
		echo "<a href=\"".
			$this->uri(array(
		    	'module'=>'workgroups',
				'action'=>'addUser',
				'workgroupId'=>$workgroup['id'],
				'userId'=>$user['userId']
			))
		."\">".$objLanguage->languageText("word_add")."</a>"."<br/>";
		echo "</td>";
		echo "</tr>";
		$index++;
	}
	echo "</table>";
    if (empty($lecturers)) {
        echo "<span class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound') . "</span>";
    }
    echo("<br/>");
    echo "</td>";
    echo "<td valign=\"top\">";
    echo "&nbsp;";
    echo "</td>";
    // Show students.
    echo "<td valign=\"top\">";
	echo "<h1>".$objLanguage->languageText('mod_workgroupadmin_students')."</h1>";
	echo "<table>";
	$index = 0;
	foreach ($students as $user) {
		if (($index%2)==0) {
			$class = "even";
		}
		else {
			$class = "odd";
		}
		echo "<tr>";
		echo "<td class=\"".$class."\">";
		echo $user['firstName'] . "&nbsp;" . $user['surname'];
		echo "</td>";
		echo "<td class=\"".$class."\">";
        // Add student.
		echo "<a href=\"".
			$this->uri(array(
		    	'module'=>'workgroups',
				'action'=>'addUser',
				'workgroupId'=>$workgroup['id'],
				'userId'=>$user['userId']
			))
		."\">".$objLanguage->languageText("word_add")."</a>"."<br/>";
		echo "</td>";
		echo "</tr>";
		$index++;
	}
	echo "</table>";
    if (empty($students)) {
        echo "<span class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound') . "</span>";
    }
    echo("<br/>");
    echo "</td>";
    echo "<td valign=\"top\">";
    echo "&nbsp;";
    echo "</td>";
    // Show guests.
    echo "<td valign=\"top\">";
	echo "<h1>".$objLanguage->languageText('mod_workgroupadmin_guests')."</h1>";
	echo "<table>";
	$index = 0;
	foreach ($guests as $user) {
		if (($index%2)==0) {
			$class = "even";
		}
		else {
			$class = "odd";
		}
		echo "<tr>";
		echo "<td class=\"".$class."\">";
		echo $user['firstName'] . "&nbsp;" . $user['surname'];
		echo "</td>";
		echo "<td class=\"".$class."\">";
        // Add student.
		echo "<a href=\"".
			$this->uri(array(
		    	'module'=>'workgroups',
				'action'=>'addUser',
				'workgroupId'=>$workgroup['id'],
				'userId'=>$user['userId']
			))
		."\">".$objLanguage->languageText("word_add")."</a>"."<br/>";
		echo "</td>";
		echo "</tr>";
		$index++;
	}
	echo "</table>";
    if (empty($guests)) {
        echo "<span class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound') . "</span>";
    }
    echo("<br/>");
    echo "</td>";
    // Show back link.
    echo "</tr>";
    echo "</table>";
    echo("<br/>");
	echo "<a href=\"".
		$this->uri(array(
	    	'module'=>'workgroups',
		))
	."\">".$objLanguage->languageText("word_back")."</a>"."<br/>"; //wg_return
?>