<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->objLanguage = $this->getObject('language', 'language');
$this->objUser = $this->getObject('user', 'security');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_modulelist_header', 'modulelist');
$header->type = 1;

$countheader = new htmlHeading();
$countheader->str = count($moduleList)." ".$this->objLanguage->languageText('mod_modulelist_countavailable', 'modulelist');
$countheader->type = 2;

$middleColumn .= $header->show();
$middleColumn .= $countheader->show();

$packageLinkUri = $this->uri(array("moduletype" => "packages"), "modulelist");
$packageLink = new link($packageLinkUri);
$packageLink->link = "Packages";

$coreLinkUri = $this->uri(array("moduletype" => "core_modules"), "modulelist");
$coreLink = new link($coreLinkUri);
$coreLink->link = "Core modules";
$middleColumn .= $coreLink->show() . " | " . $packageLink->show();

if ($moduleList) {
    $objFb = $this->newObject('featurebox', 'navigation');
    $modIcon = $this->getObject('getIcon', 'htmlelements');
    $noIconCount = 0;
    $noIconModules = array();
    
    $stable=0;
    $beta=0;
    $marked_for_removal=0;
    $invisible = 0;
    $alpha = 0;
    $prealpha = 0;
    $deprecated = 0;
    $other = 0;
    $unset = 0;
    $illegalStatuses = array();
    $stableModules = array();
    $betaModules = array();
    foreach($moduleList as $moduleRow) {
        $noIcon = NULL;
        $header = new htmlHeading();
        $theModule = $moduleRow['modname'];
        $modIcon->setModuleIcon($theModule);
        $icon = $modIcon->show();
        $mStatus = trim(strtolower($moduleRow['status']));
        //echo "|" . $mStatus . "|<br/>";
        switch($mStatus) {
            case 'pre-alpha':
                $prealpha++;
                break;
            case 'alpha':
                $alpha++;
                break;
            case 'beta':
                $betaModules[] = $theModule;
                $beta++;
                break;
            case 'invisible':
                $invisible++;
                break;
            case 'marked_for_removal':
                $marked_for_removal++;
                break;
            case 'deprecated':
                $deprecated++;
                break;
            case 'stable':
                $stableModules[] = $theModule;
                $stable++;
                break;
            case 'unset':
                $unset++;
                break;
            default:
                $other++;
                $illegalStatuses[] = $mStatus;
                break;
        }
        if (strstr($icon, "skins/_common/icons/default.gif")) {
            // The module has no icon
            $noIcon = "<span class='noicon'>No icon for this module</span>";
            $noIconCount++;
            $noIconModules[] = $theModule;
        }
        $header->str =  $icon . " Module: ".ucwords($moduleRow['modname']);
        $header->type = 3;
        $middleColumn .= $objFb->show($header->show(), 
          "<span class='module_title'><em>Title:</em> " 
          . ucfirst(trim($moduleRow['longname'])) . "</span><br />"
          . "<span class='module_version'><em>Version:</em> " 
          . ucfirst(trim($moduleRow['version'])) . "</span><br />"
          . "<span class='module_authors'><em>Authors:</em> " 
          . ucfirst(trim($moduleRow['authors'])) . "</span>"
          . "<div class='" . trim($moduleRow['status'])
          . "'>" . $moduleRow['description'] 
          . "</div><br />" . $noIcon 
          . "<br /><b>Status</b>: " . $moduleRow['status']
          . "<br /><b>Directory size</b>: " . $moduleRow['dirsize']);
        // Unset everything 
        unset(
            $moduleRow['longname'], 
            $moduleRow['version'],
            $moduleRow['authors'],
            $moduleRow['status'],
            $moduleRow['description'],
            $moduleRow['dirsize']);
    }
    if ($noIconCount > 0) {
        // Print a warning about modules with no icons
        if ($this->getParam('moduletype', 'packages') == "core_modules") {
            //  We have core modules with no icons.
            $spanType = "error";
            $extra = " All core modules should be represented by an icon, even if they have no user interface.";
        } else {
            $spanType = "warning";
            $extra = " Developers should get into the habit of creating module icons when they create their module.";
        }
        $middleColumn .= "<br /><br />";
        $middleColumn .= "<span class='$spanType'>There are <b>$noIconCount</b> modules with no icon. $extra</span><br /><br /><br />";
    }
    $middleColumn .= "Pre-alpha: $prealpha <br />";
    $middleColumn .= "Alpha: $alpha <br />";
    $middleColumn .= "Beta: $beta <br />";
    $middleColumn .= "Stable: $stable <br />";
    $middleColumn .= "Deprecated: $deprecated <br />";
    $middleColumn .= "Marked for removal: $marked_for_removal <br />";
    $middleColumn .= "Invisible: $invisible <br />";
    $middleColumn .= "Unset: $unset <br />";
    $middleColumn .= "Other: $other <br />";
    if (count($illegalStatuses) > 0) {
        array_unique($illegalStatuses);
        $middleColumn .= "<br />The following illegal statuses are used:<br />";
        foreach ($illegalStatuses as $illegalStatus) {
            $middleColumn .= "[" . $illegalStatus . "] ";
        }
    }
}   

$stMods = "Here is your script for doing a release:<br /><br />"
  . "#! /bin/bash<br />";
foreach ($stableModules as $modCode) {
    $stMods .= "cp ~/chisimba/modules/" . $modCode . "/ ~/chisimba/releases/3.3.1/ -R <br />";
}

if($this->objUser->isLoggedIn()) {
    $leftColumn .= $this->leftMenu->show();
}

$middleColumn .= "<br /><br /><br />" . $stMods;

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
?>
