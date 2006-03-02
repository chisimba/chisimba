/*
These are custom configurations for the FCKEditor in the KEWL.NextGen Project
 */




FCKConfig.ToolbarSets["Default"] = [
	['Source','DocProps','-','Save','NewPage','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    '/',
	['Image','Flash','Table','Rule','Smiley','SpecialChar','UniversalKey'],
	['TextColor','BGColor'],['Link','Unlink','Anchor'],
	'/',
    	['FontFormat','FontName','FontSize']
	

] ;

FCKConfig.ToolbarSets["DefaultWithoutSave"] = [
	['Source','DocProps','-','NewPage','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    '/',
	['Image','Flash','Table','Rule','Smiley','SpecialChar','UniversalKey'],
	['TextColor','BGColor'],['Link','Unlink','Anchor'],
	'/',
    	['FontFormat','FontName','FontSize']
	

] ;


FCKConfig.ImageBrowser = true ;
FCKConfig.LinkBrowser = true ;
FCKConfig.FlashBrowser = true ;

if (FCKURLParams['Context']=='No')
{
    FCKConfig.ImageBrowserURL = FCKURLParams['KNG']+'index.php?module=userview&action=list&mode=image&loadwindow=yes';
    FCKConfig.FlashBrowserURL = FCKURLParams['KNG']+'index.php?module=userview&action=list&mode=flash&loadwindow=yes';
    FCKConfig.LinkBrowserURL = FCKURLParams['KNG']+'index.php?module=userview&action=list&mode=other&loadwindow=yes';
} else {
    FCKConfig.ImageBrowserURL = FCKURLParams['KNG']+'index.php?module=contextview&action=contextlist&mode=image&loadwindow=yes';
    FCKConfig.FlashBrowserURL = FCKURLParams['KNG']+'index.php?module=contextview&action=contextlist&mode=flash&loadwindow=yes';
    FCKConfig.LinkBrowserURL = FCKURLParams['KNG']+'index.php?module=contextview&action=contextlist&mode=other&loadwindow=yes';
}
FCKConfig.ImageBrowserWindowWidth  = 500;
FCKConfig.ImageBrowserWindowHeight = 300;

FCKConfig.FlashBrowserWindowWidth  = 500;
FCKConfig.FlashBrowserWindowHeight = 300;

FCKConfig.LinkBrowserWindowWidth	= 500;
FCKConfig.LinkBrowserWindowHeight = 300;