/*
These are custom configurations for the FCKEditor in the Chisimba Project
 */
 
FCKConfig.ToolbarSets["Basic"] = [
	['Source','Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-','About','FitWindow']
] ;

FCKConfig.ToolbarSets["simple"] = FCKConfig.ToolbarSets["Basic"];

FCKConfig.ToolbarSets["advanced"] = [
	['Source','-','Save','Preview'],
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

/* removed 'NewPage', */

FCKConfig.ToolbarSets["DefaultWithoutSave"] = [
	['Source','-','Preview'],
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

FCKConfig.ToolbarSets["cms"] = [
	['Source','-','Save','Preview', 'Templates'],
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

FCKConfig.ToolbarSets["forms"] = [
	['Source','-','Save','Preview', 'Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
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
    FCKConfig.ImageBrowserURL = FCKURLParams['CHISIMBA_BASE']+'index.php?module=filemanager&action=fckimage&context=no&loadwindow=yes';
    FCKConfig.FlashBrowserURL = FCKURLParams['CHISIMBA_BASE']+'index.php?module=filemanager&action=fckflash&context=no&loadwindow=yes';
    FCKConfig.LinkBrowserURL = FCKURLParams['CHISIMBA_BASE']+'index.php?module=filemanager&action=fcklink&context=no&loadwindow=yes';
} else {
    FCKConfig.ImageBrowserURL = FCKURLParams['CHISIMBA_BASE']+'index.php?module=filemanager&action=fckimage&context=yes&loadwindow=yes';
    FCKConfig.FlashBrowserURL = FCKURLParams['CHISIMBA_BASE']+'index.php?module=filemanager&action=fckflash&context=yes&loadwindow=yes';
    FCKConfig.LinkBrowserURL = FCKURLParams['CHISIMBA_BASE']+'index.php?module=filemanager&action=fcklink&context=yes&loadwindow=yes';
}
FCKConfig.ImageBrowserWindowWidth  = 750;
FCKConfig.ImageBrowserWindowHeight = 550;

FCKConfig.FlashBrowserWindowWidth  = 750;
FCKConfig.FlashBrowserWindowHeight = 550;

FCKConfig.LinkBrowserWindowWidth	= 750;
FCKConfig.LinkBrowserWindowHeight = 550;

FCKConfig.LinkUpload = false ;
FCKConfig.ImageUpload = false ;
FCKConfig.FlashUpload = false ;
