function openWindow(theURL,winName,features) 
{ 
    newwindow=window.open(theURL,winName,features);
    if (window.focus) {newwindow.focus()}
	return false;
}