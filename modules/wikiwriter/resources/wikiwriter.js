
	/*******
	 Loads the urls from a textarea and saves them in an hidden variable array for sending to the webserver
	***************************/
	function addURLs(oForm){
		// Grab the URLS from the textarea
		var sURLList = oForm.URLLoader.value;
		// Split into an array using newlines (NOTE: Maybe parse for commas?)
		var aNewURLs = new Array(sURLList.split("\n"));
		// Does the hidden value URLList exist?
		var aOldURLs;
		if(oForm.input_URLList.value != ''){
			// yes?  Load the old urls
			aOldURLs = oForm.input_URLList.value;
			aOldURLs = new Array(aOldURLs.split(","));
			// Add the New URLs to the old list
			aOldURLs.concat(aNewURLs);
			// Now store in the new URLs list
			aNewURLs = aOldURLs;
		} 

		// Now add the URLs to the URLList element
		var sFormattedURLList = "";
		for(i = 0; i < aNewURLs.length; i++){
			sFormattedURLList += aNewURLs[i];
			if((i + 1) < aNewURLs.length){
				sFormattedURLList += ",";
			}
		}
		oForm.input_URLList.value = sFormattedURLList;
		// Now clear the textarea as we've added the urls
		oForm.URLLoader.value = "";
	}
