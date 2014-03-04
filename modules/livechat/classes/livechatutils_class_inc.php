<?php

class livechatutils extends object {

    function init() {
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $siteRoot = $this->objAltConfig->getsiteRootPath();
        $moduleUri = $this->objAltConfig->getModuleURI();
        $this->objUser = $this->getObject("user", "security");
        $this->jnlpPath = $siteRoot . "/" . $moduleUri . '/livechat/resources/launch' . $this->objUser->userid() . '.jnlp';
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        $this->objContext = $this->getObject("dbcontext", "context");
        
        $this->isInstructor = $this->objUser->isCourseAdmin($this->objContext->getContextCode()) ? "yes" : "no";
        $this->roomDesc = $this->objContext->getTitle($this->objContext->getContextCode());
        $this->roomName='livechat';//$this->objContext->getContextCode() == '' ?"lobby": $this->objContext->getContextCode();
    }

    function generateJNLP() {
        $stringData = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<jnlp href="launch' . $this->objUser->userid() . '.jnlp" spec="1.0+">
    <information>
        <title>Who is online plugin for KEWL3</title>
        <vendor>eLearning Support and Innovation Unit</vendor>
        <homepage href="https://elearn.wits.ac.za"/>
        <description>Support Live chats within the course</description>
        <description kind="short">Support Live chats within the course</description>
    </information>
    <update check="always"/>
    <security>
        <all-permissions/>
    </security>
    <resources>
        <j2se version="1.5+"/>
        <jar href="LiveChat.jar" main="true"/>
        <jar href="smack.jar"/>
        <jar href="smackx.jar"/>
    </resources>
    <applet-desc height="250" main-class="livechat.gui.CompactChatFrame" name="LiveChat" width="250">
        <param name="serverHost" value="' . $this->objSysConfig->getValue('SERVER_HOST', 'livechat') . '"/>
        <param name="serverPort" value="' . $this->objSysConfig->getValue('SERVER_PORT', 'livechat') . '"/>
        <param name="username" value="' . $this->objUser->username() . '"/>
        <param name="roomname" value="' . $this->roomName . '"/>
        <param name="email" value="' . $this->objUser->email() . '"/>
        <param name="names" value="' . $this->objUser->fullname() . '"/>
        <param name="isinstructor" value="' . $this->isInstructor . '"/>
        <param name="roomdesc" value="' . $this->roomDesc . '"/>
    </applet-desc>
</jnlp>
          
';
        $fh = fopen($this->jnlpPath, 'w') or die("can't open file");
        fwrite($fh, $stringData);
        fclose($fh);
    }

}

?>
