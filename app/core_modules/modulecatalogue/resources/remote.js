
/**
* Ajax method to retrieve module zip
* @param string module the module to retrieve
*/
function downloadModule(module,name) {
    var target = "download_"+module;
    $(target).innerHTML='Downloading...';
    var pars = "module=modulecatalogue&action=ajaxdownload&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                unzipModule(module,name);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not download module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function downloadModuleUpgrade(module,name) {
    var target = "download_"+module;
    $(target).innerHTML='Downloading...';
    var pars = "module=modulecatalogue&action=ajaxdownload&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                unzipModuleUpgrade(module,name);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not download module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function unzipModule(module,name) {
    var target = "download_"+module;
    var pars = "module=modulecatalogue&action=ajaxunzip&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                installModule(module,name);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not unzip module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}


function unzipModuleUpgrade(module,name) {
    var target = "download_"+module;
    var pars = "module=modulecatalogue&action=ajaxunzip&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                upgradeModule(module,name);
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not unzip module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function installModule(module,name) {
    var target = "download_"+module;
    var link = "link_"+module;
    var pars = "module=modulecatalogue&action=ajaxinstall&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                $(link).innerHTML = "<a href='index.php?module="+module+"'><b>"+name+"</b></a>";
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not install module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function upgradeModule(module,name) {
    var target = "download_"+module;
    var link = "link_"+module;
    var pars = "module=modulecatalogue&action=ajaxupgrade&moduleId="+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                $(target).innerHTML = response;
                $(link).innerHTML = "<a href='index.php?module="+module+"'><b>"+name+"</b></a>";
            },
            onFailure: function(transport){
                var response = transport.responseText || "no response text";
                alert('Could not upgrade module: '+response);
                $(target).innerHTML = '<b>Failed</b>';
            }
    });
}

function uploadArchive(filename,module) {
    var target = "download_"+module;
    new Ajax.Request('index.php',{
            method:'post',
            parameters:'module=modulecatalogue&action=ajaxupload&filename='+filename,
            onSuccess: function(transport) {
                var response = transport.responseText || "no response text";
                $(target).innerHTML = "<b>"+response+"</b>";
            },
            onFailure: function(transport) {
                var response = transport.responseText || "no response text";
                alert(response);
            }
    });

}


function toggleChecked(oElement) {
    oForm = oElement.form;
    oElement = oForm.elements[oElement.name];
    if(oElement.length) {
        bChecked = true;
        nChecked = 0;
        for(i = 1; i < oElement.length; i++) {
            if(oElement[i].checked) {
                nChecked++;
            }
        }
        if(nChecked < oElement.length - 1) {
            bChecked = false;
        } else {
            bChecked = true;
        }
        oElement[0].checked = bChecked;
    }
}

function baseChecked(oElement) {
    oForm = oElement.form;
    oElement = oForm.elements[oElement.name];
    if(oElement.length) {
        bChecked = oElement[0].checked;
        for(i = 1; i < oElement.length; i++) {
            oElement[i].checked = bChecked;
        }
    }
}
