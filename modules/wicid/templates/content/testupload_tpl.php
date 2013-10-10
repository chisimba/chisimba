<h1>Upload a Presentation</h1>
<?php

$this->setVar('pageSuppressXML', TRUE);

$this->loadClass('iframe', 'htmlelements');

$objAjaxUpload = $this->newObject('ajaxuploader');

echo $objAjaxUpload->show();

?>


<script type="text/javascript">
//<![CDATA[

function loadAjaxForm(fileid) {
    window.setTimeout('loadForm("'+fileid+'");', 1000);
}

function loadForm(fileid) {

    var pars = "module=webpresent&action=ajaxprocess&id="+fileid;
    new Ajax.Request('index.php',{
            method:'get',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $('updateform').innerHTML = response;
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                //alert('Could not download module: '+response);
            }
    });
}

function processConversions() {
    window.setTimeout('doConversion();', 2000);
}

function doConversion() {

    var pars = "module=webpresent&action=ajaxprocessconversions";
    new Ajax.Request('index.php',{
            method:'get',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                //alert(response);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                //alert('Could not download module: '+response);
            }
    });
}
//]]>
</script>