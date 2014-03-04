function showdetails(fieldname) {
	if(fieldname!="") {
		alert('good');
		var Content;
		profContent='<?=$objLanguage->languageText('mod_sports')?> ';
		profContent+='<?=$objDropProfileFrom->show()?> ';
		profContent+='<?=$objLanguage->languageText('mod_sportsadmin')?> ';
		profContent+='<?=$objDropProfileTo->show()?> ';

		document.getElementById('name').innerHTML='<?=$objLanguage->languageText('word_sports')?>';
		
	}
}
