// items structure
// each item is the array of one or more properties:
// [text, link, settings, subitems ...]
// use the builder to export errors free structure if you experience problems with the syntax

var MENU_ITEMS = [
	['Menu Compatibility', null, null,
		['Supported Browsers', null, null,
			['Win32 Browsers', null, null, 
				['Internet Explorer 5+'],
				['Netscape 6.0+'],
				['Mozilla 0.9+'],
				['AOL 5+'],
				['Opera 5+'],
				['Safari 3+'] // there must be no comma after the last element
			],
			['Mac OS Browsers', null, null,
				['Internet Explorer 5+'],
				['Netscape 6.0+'],
				['Mozilla 0.9+'],
				['AOL 5+'],
				['Safari 1.0+']
			],
			['KDE (Linux, FreeBSD)', null, null,
				['Netscape 6.0+'],
				['Mozilla 0.9+']
			]
		],
		// this is how custom javascript code can be called from the item
		// note how apostrophes are escaped inside the string, i.e. 'Don't' must be 'Don\'t'
		['Unsupported Browsers', 'javascript:alert(\'hello world\')', null,
			['Internet Explorer 4.x'],
			['Netscape 4.x']
		],
		['Report test results', 'http://www.softcomplex.com/support.html'],
	],
	['Docs & Info', null, null,
		// this is how item scope settings are defined
		['Product Page', 'http://www.softcomplex.com/products/tigra_menu/', {'tw':'_blank'}],
		// this is how multiple item scope settings are defined
		['Welcome Page', '../ReadMeFirst.html', {'tw':'_top', 'tt':'Welcome Page', 'sb':'Test Status Bar Message'}],
		['Documentation', 'http://www.softcomplex.com/products/tigra_menu/docs/'],
		['Forums', 'http://www.softcomplex.com/forum/forumdisplay.php?fid=29'],
		['TM Comparison Table', 'http://www.softcomplex.com/products/tigra_menu/docs/compare_menus.html'],
		['Menu Builder', '../_builder/index.html'],
	],
	['Product Demos', null, null,
		['Traditional Blue', '../demo1/index.html'],
		['White Steps', '../demo2/index.html'],
		['Inner HTML', '../demo3/index.html'],
		['All Together', '../demo4/index.html'],
		['Frames Targeting', '../demo5/index.html'],
		['Accessing IDs', '../demo6/index.html']
	],
	['Contact', 'http://www.softcomplex.com/support.html']
];

