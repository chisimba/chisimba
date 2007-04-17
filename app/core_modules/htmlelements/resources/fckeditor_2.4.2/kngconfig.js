/*
These are custom configurations for the FCKEditor in the Chisimba Project
 */
 
FCKConfig.ToolbarSets["Basic"] = [
	['Source','Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-','About','FitWindow']
] ;

FCKConfig.ToolbarSets["simple"] = FCKConfig.ToolbarSets["Basic"];

FCKConfig.ToolbarSets["advanced"] = [
	['Source','-','Save','NewPage','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    '/',
	['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
	['TextColor','BGColor'],['Link','Unlink','Anchor','FitWindow'],
	'/',
    	['FontFormat','FontName','FontSize']
	

] ;


FCKConfig.ToolbarSets["Default"] = FCKConfig.ToolbarSets["advanced"];

FCKConfig.ToolbarSets["DefaultWithoutSave"] = [
	['Source','-','NewPage','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    '/',
	['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
	['TextColor','BGColor'],['Link','Unlink','Anchor','FitWindow'],
	'/',
    	['FontFormat','FontName','FontSize']
	

] ;


FCKConfig.ImageBrowser = true ;
FCKConfig.LinkBrowser = true ;
FCKConfig.FlashBrowser = true ;

if (FCKURLParams['Context']=='No')
{
    FCKConfig.ImageBrowserURL = FCKURLParams['KNG']+'index.php?module=filemanager&action=fckimage&context=no&loadwindow=yes';
    FCKConfig.FlashBrowserURL = FCKURLParams['KNG']+'index.php?module=filemanager&action=fckflash&context=no&loadwindow=yes';
    FCKConfig.LinkBrowserURL = FCKURLParams['KNG']+'index.php?module=filemanager&action=fcklink&context=no&loadwindow=yes';
} else {
    FCKConfig.ImageBrowserURL = FCKURLParams['KNG']+'index.php?module=filemanager&action=fckimage&context=yes&loadwindow=yes';
    FCKConfig.FlashBrowserURL = FCKURLParams['KNG']+'index.php?module=filemanager&action=fckflash&context=yes&loadwindow=yes';
    FCKConfig.LinkBrowserURL = FCKURLParams['KNG']+'index.php?module=filemanager&action=fcklink&context=yes&loadwindow=yes';
}
FCKConfig.ImageBrowserWindowWidth  = 500;
FCKConfig.ImageBrowserWindowHeight = 300;

FCKConfig.FlashBrowserWindowWidth  = 500;
FCKConfig.FlashBrowserWindowHeight = 300;

FCKConfig.LinkBrowserWindowWidth	= 500;
FCKConfig.LinkBrowserWindowHeight = 300;

FCKConfig.LinkUpload = false ;
FCKConfig.ImageUpload = false ;
FCKConfig.FlashUpload = false ;