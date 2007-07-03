
/**
* Ajax method to retrieve module zip
* @param string module the module to retrieve
*/
function downloadModule(module) {
    var target = "download_"+module;
    $(target).innerHTML='Downloading...';
    var pars = "module=modulecatalogue&action=ajaxdownload&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                unzipModule(module);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not download module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function unzipModule(module) {
    var target = "download_"+module;
    var pars = "module=modulecatalogue&action=ajaxunzip&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                installModule(module);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not unzip module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function installModule(module) {
    var target = "download_"+module;
    var pars = "module=modulecatalogue&action=ajaxinstall&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not install module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}