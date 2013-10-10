<?php
echo "<h1>" . $this->objLanguage->languageText("mod_sitestats_title","sitestats") . "</h1>";
//----Output the overall stats

//Total contexts for site
$this->objLink =& $this->newObject('link','htmlelements');
$this->objLink->href = $this->uri(array('action'=>'getContextStats'));

$arrOfRep = array();

$linkLabel = $this->objLanguage->code2Txt("mod_contextstats_linkcontexts","sitestats",$arrOfRep);
$this->objLink->link = $linkLabel;



echo $this->objLanguage->languageText("mod_userstats_totalcontexts","sitestats") 
  . ": " . $tContexts . "&nbsp;" . $this->objLink->show() . "<br />";
  
//Total files in all contexts for site
echo $this->objLanguage->languageText("mod_userstats_totalfiles","sitestats") 
  . ": " . $tFiles . "<br />";
  
//Total file space used in all contexts for site
echo $this->objLanguage->languageText("mod_userstats_totalfilespace","sitestats") 
  . ": " . round($tFlSp/1000000, 2) . " Mb<br />";

echo "<hr />";

//Total users for the site
echo $this->objLanguage->languageText("mod_userstats_users","sitestats") 
  . ": " . $users . "<br />";
//How the users were created
echo $this->objLanguage->languageText("mod_userstats_created","sitestats") 
  . ": " . $created . "<br />";
  
//Total female users
echo $this->objLanguage->languageText("mod_userstats_females","sitestats") 
  . ": " . $females . "<br />";
//Total male users
echo $this->objLanguage->languageText("mod_userstats_males","sitestats") 
  . ": " . $males . "<br />";

//Total countries represented by all users
echo $this->objLanguage->languageText("mod_userstats_totalcountries","sitestats") 
  . ": " . $tCntry;
  
//Total countries represented by all users
echo $flags . "<br />";
  
?>
