<?php
echo "<h3>" . $this->objLanguage->languageText("mod_userstats_title","userstats") . "</h3>";
//----Output the overall stats
//Total logins for site
echo $this->objLanguage->languageText("mod_userstats_totallogins","userstats") 
  . ": " . $totalLogins . "<br />";
//Number of unique users logging in
echo $this->objLanguage->languageText("mod_userstats_uniquelogins","userstats") 
  . ": " . $uniqueLogins . "<br />";
  
//Number of males
echo "Total Number of Males"
  . ": " . $males . "<br />";

//Number of females
echo "Total Number of females"
  . ": " . $females . "<br />";



//Output the login history array
if (isset($ar)) {
    $link = $this->newObject('link', 'htmlelements');
    
    //Add the table header linked for logins
    $loginsLabel = $this->objLanguage->languageText("word_logins");
    if ($this->getParam('order', 'surname') != 'logins') {
        $loginsLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'logins'), 'userstats');
        //Make it a link
        $link->href = $loginsLabel;
        $link->link = $this->objLanguage->languageText("word_logins");
        $loginsLabel = $link->show();
    }
    
    //Add the table header linked for title
    $tlLabel = $this->objLanguage->languageText("word_title");
    if ($this->getParam('order', 'surname') != 'title') {
        $tlLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'title'), 'userstats');
        //Make it a link
        $link->href = $tlLabel;
        $link->link = $this->objLanguage->languageText("word_title");
        $tlLabel = $link->show();
    }
    
    //Add the table header linked for firstname
    $fnLabel = $this->objLanguage->languageText("phrase_firstName");
    if ($this->getParam('order', 'surname') != 'firstName') {
        $fnLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'firstName'), 'userstats');
        //Make it a link
        $link->href = $fnLabel;
        $link->link = $this->objLanguage->languageText("phrase_firstName");
        $fnLabel = $link->show();
    }
    
    //Add the table header linked for surname
    $dnLabel = $this->objLanguage->languageText("word_surname");
    if ($this->getParam('order', 'surname') != 'surname') {
        $dnLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'surname'), 'userstats');
        //Make it a link
        $link->href = $dnLabel;
        $link->link = $this->objLanguage->languageText("word_surname");
        $dnLabel = $link->show();
    }
    
    //Add the table header linked for country
    $cnLabel = $this->objLanguage->languageText("word_country");
    if ($this->getParam('order', 'surname') != 'country') {
        $cnLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'country'), 'userstats');
        //Make it a link
        $link->href = $cnLabel;
        $link->link = $this->objLanguage->languageText("word_country");
        $cnLabel = $link->show();
    }

    //Add the table header linked for email
    $emailLabel = $this->objLanguage->languageText("phrase_emailAddress");
    if ($this->getParam('order', 'surname') != 'emailAddress') {
        $emailLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'emailaddress'), 'userstats');
        //Make it a link
        $link->href = $emailLabel;
        $link->link = $this->objLanguage->languageText("phrase_emailAddress");
        $emailLabel = $link->show();
    }


//Add the table header linked for sex
$this->sex = "Sex";
$sexLabel = $this->sex;
//$this->objLanguage->languageText("word_country");
    if ($this->getParam('order', 'surname') != 'sex') {
        $sexLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'sex'), 'userstats');
        //Make it a link
        $link->href = $sexLabel;
        $link->link = $this->sex;
        $sexLabel = $link->show();
    }





    //Add the table header linked for last on
    $lonLabel = $this->objLanguage->languageText("phrase_laston");
    if ($this->getParam('order', 'surname') != 'lastOn') {
        $lonLabel = $this->uri(array(
           'action' => 'viewloginhistory',
           'order' => 'lastOn'), 'userstats');
        //Make it a link
        $link->href = $lonLabel;
        $link->link = $this->objLanguage->languageText("phrase_laston");
        $lonLabel = $link->show();
    }
    
    
    //Create an instance of the table object    
    $objTable = $this->newObject('htmltable', 'htmlelements');
    //Add the table headings into an array
    $tableHd[] = $tlLabel;
    $tableHd[] = $fnLabel;
    $tableHd[] = $dnLabel;
    $tableHd[] = $cnLabel;
    $tableHd[] = $emailLabel;
    $tableHd[] = $lonLabel;
    $tableHd[] = $loginsLabel;
    $tableHd[] = $sexLabel; 
    //Create the table header for display
    $objTable->addHeader($tableHd, "heading");
    //Initialize the odd/even counter
    $rowcount = 0;
    //Output the data
    
    foreach ($ar as $line) {
        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
        $tableRow[]=$line['title'];
        $tableRow[]=$line['firstname'];
        $tableRow[]=$line['surname'];
        $tableRow[]=$line['country'];
        $tableRow[]=$line['emailaddress'];
        $tableRow[]=$line['laston'];
        $tableRow[]=$line['logins'];
	$tableRow[]=$line['sex'];
        //Add the row to the table for output
        $objTable->addRow($tableRow, $oddOrEven);
        // Set rowcount for bitwise determination of odd or even
        $rowcount = ($rowcount == 0) ? 1 : 0;
        //Clear the array
        $tableRow=array();
    }
    
    //Show the table    
    echo $objTable->show();
}
?>
