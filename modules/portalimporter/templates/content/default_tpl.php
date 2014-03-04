<?php
set_time_limit(0);
$xmlGenLink = $this->uri(
  array(
    "action"=>"genxml"
  ), "portalimporter"
);

$xmlReadLink = $this->uri(
  array(
    "action"=>"readportal"
  ), "portalimporter"
);

$dtaFixPageRef = $this->uri(
  array(
    "action"=>"fixPageRef"
  ), "portalimporter"
);


$dtaFixDocRef = $this->uri(
  array(
    "action"=>"fixDocumentRef"
  ), "portalimporter"
);

$dtaStoreLink = $this->uri(
  array(
    "action"=>"goportal"
  ), "portalimporter"
);

$showStructured = $this->uri(
  array(
    "action"=>"showstructured"
  ), "portalimporter"
);

$showFiles = $this->uri(
  array(
    "action"=>"showfiles"
  ), "portalimporter"
);
$showDirs = $this->uri(
  array(
    "action"=>"showdirs"
  ), "portalimporter"
);
$configure = $this->uri(
  array(
    "action"=>"step2",
    "pmodule_id" => "portalimporter"
  ), "sysconfig"
);
$imagemove = $this->uri(
  array(
    "action"=>"imagemove"
  ), "portalimporter"
);
$flashmove = $this->uri(
  array(
    "action"=>"flashmove"
  ), "portalimporter"
);
$pdfmove = $this->uri(
  array(
    "action"=>"pdfmove"
  ), "portalimporter"
);
$mediamove = $this->uri(
  array(
    "action"=>"mediamove"
  ), "portalimporter"
);
$docmove = $this->uri(
  array(
    "action"=>"docmove"
  ), "portalimporter"
);
$findwordcrud = $this->uri(
  array(
    "action"=>"findwordcrud"
  ), "portalimporter"
);
$dummy = $this->uri(
  array(
    "action"=>"dummy"
  ), "portalimporter"
);
$hideDudsLk = $this->uri(
  array(
    "action"=>"showstructured",
    "hideduds"=>"TRUE"
  ), "portalimporter"
);
$hideLegacyLk = $this->uri(
  array(
    "action"=>"showstructured",
    "hidelegacy"=>"TRUE"
  ), "portalimporter"
);
$hideStructLk = $this->uri(
  array(
    "action"=>"showstructured",
    "hidestructured"=>"TRUE"
  ), "portalimporter"
);
$showOnlyLegacy = $this->uri(
  array(
    "action"=>"showstructured",
    "hidestructured"=>"TRUE",
    "hideduds"=>"TRUE"
  ), "portalimporter"
);

?>
&nbsp;<br />
&nbsp;<br />
<h3>Options</h3>

&nbsp;Please note that these are all actions that may take a long time to complete.<br /> 
&nbsp;Half an hour for a large site would not be unusual.  
<br /><br />
<table>
<tr>
  <th valign="top">
    <h2>Import portal content</h2>
  </th>
  <th valign="top">
    <h2>Utilities</h2>
  </th>
</tr>
<tr>
  <td valign="top">
    <h4>Data migration</h4>
    <ul>
      <li><a href="<?php echo $dtaStoreLink;?>">Read portal content and store in database</a></li>
    </ul>
     <h4>This will do the following : (images, documents, media)</h4>
    <ol>
      <li>Move image assets to repository</li>
      <li>Move Adobe Flash assets to repository</li>
      <li>Move PDF assets to repository</li>
      <li>Move other document assets to repository</li>
      <li>Move media assets (sound, video) to repository</li>
    </ol>
   
    <ul>
      <li><a href="<?php echo $dtaFixPageRef;?>">Fix anchors to reference imported content</a></li>
    </ul>
   
    <ul>
      <li><a href="<?php echo $dtaFixDocRef;?>">Fix anchors that reference Documents and Media to point to imported resources</a></li>
    </ul>
 </td>
  <td valign="top">
    <h4>Content and raw data</h4>
    <ul>
      <li><a href="<?php echo $showFiles;?>">Read portal content and show all files</a></li>
      <li><a href="<?php echo $showDirs;?>">Read portal content and show all directories</a></li>
      <li><a href="<?php echo $showStructured;?>">Read portal content and show which files are structured and which raw</a></li>
        <ul>
            <li><a href="<?php echo $showOnlyLegacy;?>">Show only Legacy</a></li>
            <li><a href="<?php echo $hideDudsLk;?>">Hide duds</a></li>
            <li><a href="<?php echo $hideStructLk;?>">Hide structured</a></li>
            <li><a href="<?php echo $hideLegacyLk;?>">Hide legacy</a></li>
        </ul>
      <li><a href="<?php echo $findwordcrud;?>">Read portal content and identify files with Word crud</a></li>
      <li><a href="<?php echo $xmlReadLink;?>">Read portal content and show XML</a></li>
      <li><a href="<?php echo $xmlGenLink;?>">Read portal content and generate XML to file</a></li>
    </ul>
    <h4>Settings</h4>
    <ul>
      <li><a href="<?php echo $configure;?>">Configure import settings</a></li>
      <li><a href="<?php echo $dummy;?>">Dummy link for testing whatever is being written now</a></li>
    </ul>
  </td>
</tr>
</table>
&nbsp;<br />
One of the "features" of Firefox is a pop-up dialog that appears whenever a page takes too long to 
load that reads, "<font color="brown">Warning: Unresponsive script. A script on this page may be busy, or it 
may have stopped responding. You can stop the script now, or you can continue to see if 
the script will complete.</font>" You will almost certainly get this popup when working with 
this module because some of the actions take a long time to produce output. To fix this:<br />
<br />
<ol>
<li>Type about:config in Firefox's address bar.
<li>Filter down to the value for dom.max_script_run_time.
<li>Change the value to something higher than the default (which is 20) I set mine to 200 wile running this module and then set it back again after.
<li>Go ahead and use Portal importer!
</ol>
&nbsp;<br />
