// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2007 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Mediawiki Wiki tags example
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
mySettings = {
	previewParserPath:	"", // path to your Wiki parser
	onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
	markupSet:  [
		{name:'Heading 1', key:'1', openWith:'+ ', closeWith:' +', placeHolder:'Your title here...' },
		{name:'Heading 2', key:'2', openWith:'++ ', closeWith:' ++', placeHolder:'Your title here...' },
		{name:'Heading 3', key:'3', openWith:'+++ ', closeWith:' +++', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'4', openWith:'++++ ', closeWith:' ++++', placeHolder:'Your title here...' },
		{name:'Heading 5', key:'5', openWith:'+++++ ', closeWith:' +++++', placeHolder:'Your title here...' },
        {name:'Heading 6', key:'6', openWith:'++++++ ', closeWith:' ++++++', placeHolder:'Your title here...' },
		{separator:'---------------' },		
		{name:'Bold', key:'B', openWith:"**", closeWith:"**"}, 
		{name:'Italic', key:'I', openWith:"//", closeWith:"//"}, 
		{name:'Stroke through', key:'S', openWith:'@@---', closeWith:'@@'}, 
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
		{name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
		{separator:'---------------' },
		{name:'Picture', key:"P", replaceWith:"[[![Image url:!:http://]!] [![Caption]!]]"}, 
		{name:'Link', key:"L", replaceWith:"[[![Link:!:http://]!]]"}, 
		{name:'Url', key:"U", replaceWith:"[[![Url:!:http://]!] [![Link text]!]]"}, 
		{separator:'---------------' },
		{name:'Quotes', openWith:'(!(> |!|>)!)', placeHolder:''},
		{name:'Code', openWith:'(!([CODE lang="[![Language:!:php]!]"]|!|<pre>)!)', closeWith:'(!([/CODE]|!|</pre>)!)'}, 
		{separator:'---------------' },
		{name:'Preview', call:'preview', className:'preview'}
	]
}