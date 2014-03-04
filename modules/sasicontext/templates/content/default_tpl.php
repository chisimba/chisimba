<script type="text/javascript">
    function loadData(url){
        jQuery.facebox(function() {
            jQuery.get(url, function(data) {
                jQuery.facebox(data);
            })
        })
    }

</script>
<?php
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressSkin', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressGoogleAnalytics', TRUE);

$alertBox = $this->getObject('alertbox', 'htmlelements');
$alertBox->putJs();

echo $this->objSasicontext->buildLinks($mydata);
?>
