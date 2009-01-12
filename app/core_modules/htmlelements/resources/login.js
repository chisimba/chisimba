// method that sets up a cross-browser XMLHttpRequest object
function getHTTPObject() {
    var http_object;

    // MSIE Proprietary method

    /*@cc_on
    @if (@_jscript_version >= 5)
        try {
            http_object = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            try {
                http_object = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (E) {
                http_object = false;
            }
        }
    @else
        xmlhttp = http_object;
    @end @*/


    // Mozilla and others method

    if (!http_object && typeof XMLHttpRequest != 'undefined') {
        try {
            http_object = new XMLHttpRequest();
        }
        catch (e) {
            http_object = false;
        }
    }

    return http_object;
}

xmlhttp = getHTTPObject(); // We create the HTTP Object

function doRequest(url)
{

    if (xmlhttp)
    {
        xmlhttp.open("GET",url,false);
        xmlhttp.send(null);
        
        return xmlhttp.responseText;
/*
        document.getElementById('A1').innerHTML=xmlhttp.status;
        document.getElementById('A2').innerHTML=xmlhttp.statusText;
        document.getElementById('A3').innerHTML=xmlhttp.responseText;
*/
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
}

function tryLogin(frm)
{
    var element = document.getElementById("loginresponse"); 
    element.innerHTML = '<p><img src="skins/_common/icons/ajax-loader.gif"></p>'; 
    
    url = frm.action + "&password="+frm.input_password.value+"&username="+frm.input_username.value+"&remember="+frm.input_remember.checked;
    response = doRequest(url);
    element.innerHTML = response;
    setTimeout("resetMessage()", 5000);
}

function resetMessage()
{
    var element = document.getElementById("loginresponse");
    element.innerHTML = "";
}