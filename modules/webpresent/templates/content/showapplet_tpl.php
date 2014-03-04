<script type="text/javascript">

    function openWindow() {
        window.open(<?php echo $this->objConfig->getSiteRoot().'/index.php?module=webpresent&action=home'?>,"_self","fullscreen"); 
    } 
</script>
<?php
/*echo '<h1>Realtime is currently temporarily under maintenance.</h1>';
echo '<a href="'.$this->objConfig->getSiteRoot().'/index.php?module=webpresent&action=home">Back to presentations</a>';

    $userLevel;
    $isLoggedIn='false';

    if ($this->objUser->isAdmin())
    {
        $this->userLevel = 'admin';
    }
    elseif ($this->objUser->isLecturer())
    {
        $this->userLevel = 'lecturer';
    }
    elseif ($this->objUser->isStudent())
    {
        $this->userLevel = 'student';
    } else
    {
        $this->userLevel = 'guest';
    }
    $isLoggedIn =$this->objUser->isLoggedIn();
    $modPath=$this->objConfig->getModulePath();
    $siteRoot=$this->objConfig->getSiteRoot();
    $replacewith="";
    $docRoot=$_SERVER['DOCUMENT_ROOT'];
    $appletPath=str_replace($docRoot,$replacewith,$modPath);
    $appletCodeBase="http://" . $_SERVER['HTTP_HOST']."/".$appletPath.'/realtime/resources/';
    $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    $port=$objSysConfig->getValue('WHITEBOARDPORT', 'realtime');
    $rtpport=$objSysConfig->getValue('RTPPORT', 'realtime');
    $rtcpport=$objSysConfig->getValue('RTCPPORT', 'realtime');
    $sipport=$objSysConfig->getValue('SIP_PORT', 'realtime');

    $linuxJMFPathLib=$modPath.'/realtime/resources/jmf-linux-i586/lib/';
    $linuxJMFPathBin=$modPath.'/realtime/resources/jmf-linux-i586/bin/';
    //path to uploaded items
    $uploadURL="http://" . $_SERVER['HTTP_HOST']."/".$appletPath.'/realtime/templates/content/uploadfile.php';

    $objMkdir = $this->getObject('mkdir', 'files');
    // Path for uploaded files
    $uploadPath = $this->objConfig->getcontentBasePath().'/realtime/'.$this->contextCode.'/'.date("Y-m-d-H-i");//.'/'.time();
    $objMkdir->mkdirs($uploadPath, 0777);
    $resourcesPath =$modPath.'/realtime/resources';
    $chatLogPath = $filePath.'/chat/'.date("Y-m-d-H-i");
    $objMkdir->mkdirs($chatLogPath, 0777);

    echo '<center>';
    echo '<applet codebase="'.$appletCodeBase.'"';
    echo 'code="avoir.realtime.tcp.launcher.RealtimeLauncher" name ="Avoir Realtime Applet"';

    echo 'archive="realtime-launcher-1.0.1.jar" width="100%" height="700">';
    echo '<param name=appletCodeBase value="'.$appletCodeBase.'">';
    echo '<param name=userName value="'.$this->objUser->userName().'">';
    echo '<param name=isLocalhost value="true">';
    echo '<param name=fullName value="'.$this->objUser->fullname().'">';
    echo '<param name=userLevel value="'.$this->userLevel.'">';
    echo '<param name=linuxJMFPathLib value="'.$linuxJMFPathLib.'">';
    echo '<param name=linuxJMFPathBin value="'.$linuxJMFPathBin.'">';
    echo '<param name=uploadURL value="'.$uploadURL.'">';
    echo '<param name=chatLogPath value="'.$chatLogPath.'">';
    echo '<param name=siteRoot value="'.$siteRoot.'">';
    echo '<param name=supernodeHost value="'.$supernodeHost.'">';
    echo '<param name=supernodePort value="'.$supernodePort.'">';
    echo '<param name=isWebPresent value="true">';
    echo '<param name=isLoggedIn value="'.$isLoggedIn.'">';
    echo '<param name=slidesDir value="'.$filePath.'">';
    echo '<param name=uploadPath value="'.$uploadPath.'">';
    echo '<param name=resourcesPath value="'.$resourcesPath.'">';
    echo '<param name=port value="'.$port.'">';
    echo '<param name=rtpport value="'.$rtpport.'">';
    echo '<param name=rtcpport value="'.$rtcpport.'">';
    echo '<param name=sipport value="'.$sipport.'">';
    echo '<param name=sessionId value="'.$sessionid.'">';
    echo '<param name=sessionTitle value="'.$sessionTitle.'">';
    echo '<param name=slideServerId value="'.$slideServerId.'">';

    echo '<param name=isSessionPresenter value="'.$isPresenter.'">';
    echo '</applet>';
    echo '</center>';


   echo '   <?xml version="1.0" encoding="utf-8"?>';
  echo '<!-- JNLP File for Classroom launcher -->';

  echo '<jnlp spec="1.0+"';
  echo '      codebase="http://localhost/chisimba_modules/realtime/resources/" ';
   echo '     href="classroom-1.jnlp">';
    echo ' <information>';
    echo '    <title>Realtime Classroom</title>';
    echo '    <vendor>AVOIR</vendor>';
    echo '    <description>Realtime Classroom</description>';
    echo '    <homepage href="http://avoir.uwc.ac.za"/>';
    echo '    <description kind="short">Realtime Virtual Classroom</description>';
    echo '    <icon href="images/logo.png"/> ';
    echo '    <icon kind="splash" href="images/splash_realtime.png"/> ';
    echo '    <offline-allowed/>';
    echo ' </information>';
   echo '  <resources>     ';
     echo '	<jar href="realtime-launcher-1.0.1.jar"/>   ';
  echo '	<j2se version="1.5+"';
  echo '	      href="http://java.sun.com/products/autodl/j2se"/>';
    echo ' </resources>';
   echo '  <application-desc main-class="avoir.realtime.tcp.launcher.RealtimeLauncher">';
   echo '    <argument>localhost</argument>';
    echo '   <argument>22225</argument>';
  echo '<argument>s2</argument>';
  echo ' <argument>s2</argument>';
  echo ' <argument>false</argument>';
   echo '<argument>contextCode</argument>';
  echo '   </application-desc>';
  echo '<security>';
  echo '   <all-permissions/>';
  echo '</security> ';
echo '</jnlp>';

 */

echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
echo '<jnlp codebase="http://chameleon.uwc.ac.za/classroom" href="launch.jnlp" spec="1.0+">';
echo '    <information>';
echo '        <title>InstanceLauncher</title>';
echo '        <vendor>developer</vendor>';
echo '        <homepage href=""/>';
echo '        <description>InstanceLauncher</description>';
 echo '       <description kind="short">InstanceLauncher</description>';
 echo '   <offline-allowed/>';
echo '</information>';
echo '<security>';
echo '<all-permissions/>';
echo '</security>';
echo '    <resources>';
echo '<j2se version="1.5+"/>';
echo '<jar eager="true" href="InstanceLauncher.jar" main="true"/>';
echo '    </resources>';
echo '    <application-desc main-class="avoir.realtime.launcher.Main">';
echo '    </application-desc>';
echo '</jnlp>';


?>
 */
