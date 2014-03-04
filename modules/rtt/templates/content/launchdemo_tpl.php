<?php


$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link','htmlelements');
$jnlpPath = "packages/rtt/resources/$username.jnlp";
$this->appendArrayVar('headerParams', '
    <script src="http://www.java.com/js/deployJava.js"></script>');

$javaVersion='
    <script>
var minVersion="1.6";
var javaVersionDiv = document.getElementById("javaversion");

if(deployJava.isWebStartInstalled(minVersion)){
javaVersionDiv.appendChild(document.createTextNode("You have the required version of java installed."));

}else{
var answer = confirm("You need latest java installed to run Chisimba Realtime Tools Demo. Install latest java now?")
if (answer){
deployJava.installLatestJRE();
}

}
</script>
';

$redirectJS =
        '
<script type="text/javascript">
var jnlpPath="' . $jnlpPath . '";
function redirect() {
document.location.href = jnlpPath;
}
</script>
';


$this->appendArrayVar('headerParams', $redirectJS);
$params = 'onload="javascript: redirect()"';
$this->setVar("bodyParams", $params);
echo '<div id="javaversion"><div/></br>';
$objWashout=$this->getObject('washout','utilities');

$link=new link($this->uri(array("action"=>"demo")));
$link->link=$this->objLanguage->languageText('mod_rtt_demoback','rtt','Back to home page');

echo $javaVersion;

// Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(3);
$title=$this->objLanguage->languageText('mod_rtt_launching','rtt','Chisimba Realtime Tools Demo. Launching ...');
$heading = new htmlHeading();
$heading->type = 1;
$heading->str = $title;

$cssLayout->setLeftColumnContent($this->objDbRtt->getDownloadsStory());

// Add Right Column
$cssLayout->setMiddleColumnContent( $heading->show().$this->objDbRtt->getPostDemoContent().$link->show());

//Output the content to the page
echo $cssLayout->show();

?>
