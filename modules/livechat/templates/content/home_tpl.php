
<?php

$this->objAltConfig = $this->getObject('altconfig', 'config');
$siteRoot = $this->objAltConfig->getsiteRoot();
$moduleUri = $this->objAltConfig->getModuleURI();
$objUtil = $this->getObject("livechatutils");
$objUtil->generateJNLP();
$this->objUser = $this->getObject("user", "security");
$jnlpPath = $siteRoot . "/" . $moduleUri . '/livechat/resources/launch' . $this->objUser->userid() . '.jnlp';
$jarPath = $siteRoot . "/" . $moduleUri . '/livechat/resources/LiveChat.jar';
$this->appendArrayVar('headerParams', '
    <script src="http://www.java.com/js/deployJava.js"></script>');
$this->appendArrayVar('headerParams', "<script>
    <!-- applet id can be used to get a reference to the applet object -->
    var attributes = { id:'livechatApplet', code:'livechat.gui.CompactChatFrame',  width:300, height:450} ;
    var parameters = {jnlp_href: '" . $jnlpPath . "'} ;
    deployJava.runApplet(attributes, parameters, '1.6');
</script>");
$this->appendArrayVar('headerParams', "<script>
    function showWarning(){
         alert('Closing this window means you cant have access to live chat');
  }
</script>");
$params = 'onunload="showWarning();"';
$this->setVar("bodyParams", $params);
?>
