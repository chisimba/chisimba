/*
Function to load the Iframe and submit
the search data.
*/
function doSearch(url4iframe) 
{
	var searchterm;
	//If it is google it uses q as the name
	if ( document.websearch.q ) { //Google 
		searchterm=document.websearch.q.value
	}
	//Build the URL for the loadIFrame method
	var url;
	url = url4iframe + "&searchterm=" + searchterm;
	// Load the iframe and save results
	if ( window.frames.searchExtractor ) {
    	window.frames.searchExtractor.location = url;
	}
	//Submit the search
	//Debugging delay
	counter = 1
	while (counter < 11100) {
		counter++
	}
	document.websearch.submit();
	return true;
}