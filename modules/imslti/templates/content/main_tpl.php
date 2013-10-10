<h1><?php echo $title ?></h1>
<?php
$url1="https://pilotfish.ucompass.com/lti.cgi?id=ucompass@gmail.com/MichaelSakai/assessment.xml";
$secret ="yourkiddingright";
/*$myXml = '<?xml version="1.0" encoding="UTF-8"?>
<launchRequest>
  <launchData>
    <group>
      <available>true</available>
      <groupType>course</groupType>
      <shortDescription>Test</shortDescription>
    </group>
    <membership>
      <groupRoles>
        <groupRole>
          <value>Instructor</value>
        </groupRole>
      </groupRoles>
    </membership>
    <user>
      <email>admin@umich.edu</email>
      <firstName>Sakai</firstName>
      <fullName>Sakai Administrator</fullName>
      <lastName>Administrator</lastName>
      <systemRole>User</systemRole>
    </user>
  </launchData>
  <launchDefinition>
    <displayTarget>IFrame</displayTarget>
    <launchLink>groupview</launchLink>
    <pageId>2</pageId>
    <toolConsumerId>moodle_0a1b_2</toolConsumerId>
    <toolId>wimbaclassroomlti</toolId>
    <userToken>admin</userToken>
  </launchDefinition>
</launchRequest>';*/

$objMsg = $this->getObject("ltixmlmsg", "imslti");
$myXml = $objMsg->show();


$url2 = "http://ltihost.wimba.com:8080/TITool-0.0.1/rest/launchws";
$url3 = "https://pilotfish.ucompass.com/lti.cgi?id=ucompass@gmail.com/SampleForPaul/TL_ARTT1.zip";

$choice=$this->getParam("choice", "1");
$url=$url1;

$objFetcher = $this->getObject("ltifetcher", "imslti");
$objFetcher->set("xmlPacket", $myXml);

$gotBack = $objFetcher->getUrl($url);

$objWrapper = $this->getObject("ltiwrapper", "imslti");
echo $objWrapper->show($gotBack);
?>