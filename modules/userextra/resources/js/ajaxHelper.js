function sendDataAsync(method, uri, data, S, F) {
    var xmlHttp = getXmlHttp();

    xmlHttp.onreadystatechange = function()
    {
        if (xmlHttp.readyState == 4) {
            if (xmlHttp.status < 300) S(xmlHttp.responseText);
            else F();
        }

    }

    xmlHttp.open(method, uri, true);
    xmlHttp.setRequestHeader("Content-Type", 'text/html');
    xmlHttp.send(data);
}

function sendDataSync(method, uri, data, S, F) {
    var xmlHttp = getXmlHttp();

    xmlHttp.open(method, uri, false);
    //xmlHttp.setRequestHeader("Content-Type", 'text/html');
    xmlHttp.send(data);

	alert(uri);
    
    if (xmlHttp.status < 300) S(xmlHttp.responseText);
    else F();
}

function getXmlHttp()
{
    var xmlHttp;
    try
    {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    }
    catch (e)
    {
        // Internet Explorer
        try
        {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            try
            {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e)
            {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }

    return xmlHttp;
}
