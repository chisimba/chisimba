<?php
$mode = $fields['mode'];
//Get Context Code
$contextCode = $this->getSession('contextCode');
// objects that almost every template will use
$objSysConfig = $this->getObject('altconfig', 'config');
//Get the path to the site root
$siteRootPathAns = $objConfig->getItem("KEWL_SITE_ROOT") . "index.php?module=mcqtests&action=addsimplecalculated&id=".$id."&mode=".$mode."&test=".$fields['testid'].
        "&anscount=".$fields['anscount'];
$siteRootPathUnits = $objConfig->getItem("KEWL_SITE_ROOT") . "index.php?module=mcqtests&action=addsimplecalculatedunit&id=".$id."&mode=".$mode."&test=".$fields['testid'].
        "&unitcount=".$fields['unitcount'];
$this->appendArrayVar('headerParams', '<script language="JavaScript" type="text/javascript">
function createAnsInputs(dropdown)
{
    var baseURL  = "' . $siteRootPathAns . '";
    var anscnt = dropdown.value;

    redirectPath = baseURL+anscnt;
    window.location= redirectPath;
    return true;
}
</script>');

$this->appendArrayVar('headerParams', '<script language="JavaScript" type="text/javascript">
function createUnitInputs(dropdown)
{
    var baseURL  = "' . $siteRootPathUnits . '";
    var unitcnt = dropdown.value;

    redirectPath = baseURL+unitcnt;
    window.location= redirectPath;
    return true;
}
</script>');

$form = $this->objFormManager->createSimpleCalcQnForm($fields);
echo $form;
?>