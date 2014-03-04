<?php
$script = $this->getJavaScriptFile('turnitin.js', 'jturnitin');
$this->appendArrayVar('headerParams', $script);
?>

<h1> TEST </h1>

<a href="<?php echo $this->uri(array('action' => 'createassessment'));?>">
Create Assessment </a><br><br>

<form action="<?php echo $this->uri(array('action' => 'sub'));?>" method="post"  enctype="multipart/form-data">

<!--Paper Title<input type="text" value="The paper title" name="ptl"><br-->
<!--Type<input type="text" value="2" name="ptype"><br-->

Firstname<input type="text" value="Student" name="ufn"><br>
User Email<input type="text" value="student@uwc.ac.za" name="uem"><br>
Surname<input type="text" value="student" name="uln"><br>
Gmtime<input type="text" name="gmtime" value="<?php echo substr(gmdate('YmdHi'), 0, -1); ?>"><br>
<input type="file" name="paper"><br>
<input type="submit"><br><br></form>

<a href="<?php echo $this->uri(array('action' => 'submitassessment'));?>">
Submit Assessment </a><br><br>


<form action="<?php echo $this->uri(array('action' => 'apilogin'));?>" method="post">
Firstname<input type="text" name="firstname"><br>
Surname<input type="text" name="lastname"><br>
Password<input type="text" name="password"><br>
Email<input type="text" name="email"><br>

<input type="submit"><br><br>

<!--a href="#" onclick="showLoading('report'); getReport('100552689');"> Get the Report </a-->
<a href="#"
onClick="window.open('<?php echo $this->uri(array('action' => 'returnreport', 'objectid' => '100552689')) ?>','rview','height=768,width=1024,location=no,menubar=no,resizable=yes,scrollbars=yes,titlebar=no,toolbar=no,status=no');" > Get the Report </a>
<div id="report">www</div>
