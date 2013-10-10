/**
 * Ingrid : JQuery Datagrid Control
 *
 * Copyright (c) 2007-2009 Matthew Knight (http://www.reconstrukt.com http://slu.sh)
 * 
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * @requires jQuery v1.2+
 * @version 0.9.3
 * @todo load JSON data, etc.
 * 
 * Revision: 0.9.3.0 2009/06/26 Patrice Blanchardie
 * - bug fixes: selection behaviour,
 *              hscroll width,
 *              attribute selector,
 *              result error handler,
 *              header auto-resize
 * - feature: new param: unsortable columns
 *
 */

jQuery.fn.ingrid = function(o){

	var cfg = {
		height: 200, 							// height of our datagrid (scrolling body area)
		
		savedStateLoad : false,					// when Ingrid is initialized, should it load data from a previously saved state?
		initialLoad : false,					// when Ingrid is initialized, should it load data immediately?

		colWidths: [225,225,225,225],			// width of each column
		minColWidth: 60,						// minimum column width
		headerHeight: 30,						// height of our header
		headerClass: 'grid-header-bg',			// header bg
		resizableCols: true,					// make columns resizable via drag + drop
		
		gridClass: 'datagrid',					// class of head & body
		rowClasses: [],							// list of row classes (selected by cursor)
		colClasses: [],							// array of classes : i.e. ['','grid-col-2','','']
		rowHoverClass: 'grid-row-hover',		// hovering over a row? use this class
		rowSelection: true,						// allow row selection?
		rowSelectedClass: 'grid-row-sel',		// selecting a row? use this class
		onRowSelect: function(tr, selected){},	// function to call when row is clicked
		
		/* sorting */
		sorting: true,
		colSortParams: [],						// value to pass as sort param when header clicked (i.e. '&sort=param') ex: ['col1','col2','col3','col4']
		sortAscParam: 'asc',					// param passed on ascending sort (i.e. '&dir=asc)
		sortDescParam: 'desc',					// param passed on ascending sort (i.e. '&dir=desc)
		sortedCol: 'col1',						// current data's sorted column (can be a key from 'colSortParams', or an int 0-n (for n columns)
		sortedColDir: 'desc',					// current data's sorted directions
		sortDefaultDir: 'desc',					// on 1st click, sort tihs direction
		sortAscClass: 'grid-sort-asc',			// class for ascending sorted col
		sortDescClass: 'grid-sort-desc',		// class for descending sorted col
		sortNoneClass: 'grid-sort-none',		// ... not sorted? use this class
		unsortableCols: [],						// do not make theses columns sortable
		
		/* paging */
		paging: true,							// create a paging toolbar
		pageNumber: 1,
		recordsPerPage: 0,
		totalRecords: 0,
		pageToolbarHeight: 25,
		pageToolbarClass: 'grid-page-toolbar',
		pageStartClass: 'grid-page-start',
		pagePrevClass: 'grid-page-prev',
		pageInfoClass: 'grid-page-info',
		pageInputClass: 'grid-page-input',
		pageNextClass: 'grid-page-next',
		pageEndClass: 'grid-page-end',
		pageLoadingClass: 'grid-page-loading',
		pageLoadingDoneClass: 'grid-page-loading-done',
		pageViewingRecordsInfoClass: 'grid-page-viewing-records-info',

		/* ajax stuff */
		url: 'remote.php',						// url to fetch data
		type: 'GET',							// 'POST' or 'GET'
		dataType: 'html',						// 'html' or 'json' - expected dataType returned
		extraParams: {},						// a map of extra params to send to the server 				
		loadingClass: 'grid-loading',			// loading modalmask div
		loadingHtml: '<div>&nbsp;</div>',			
		
		/* should seldom change */
		resizeHandleHtml: '',					// resize handle html + css
		resizeHandleClass: 'grid-col-resize',
		scrollbarW: 22,							// width allocated for scrollbar
		columnIDAttr: '_colid',					// attribute name used to groups TDs in columns
		ingridIDPrefix: '_ingrid',				// prefix used to create unique IDs for Ingrid
		
		/* cookie, for saving state */
		cookieExpiresDays: 360,
		cookiePath: '/',
		
		/* not yet implemented */
		minHeight: 100,
		resizableGrid: true,
		dragDropCols: true,
		sortType: 'server|client|none',
		
		/* cfg functions */
		isSortableCol : function(colIndex) {
			if (cfg.unsortableCols.length==0 || jQuery.inArray(colIndex, cfg.unsortableCols)==-1) {
				return true;
			}
			return false;
		}
		
	};
	jQuery.extend(cfg, o);

	// break into 2 tables: header, body.
	// create header table
	var cols = new Array();
	var h = jQuery('<table cellpadding="0" cellspacing="0"></table>')
					.html(this.find('thead'))
					.addClass(cfg.gridClass)
					.addClass(cfg.headerClass)
					.height(cfg.headerHeight)
					.extend({
						cols : cols
					});
	// initialize columns
	h.find('th').each(function(i){
														 
		// init width
		jQuery(this).width(cfg.colWidths[i]);
		
		// put column text in a div, make unselectable
		var col_label = jQuery('<div />')
										.html(jQuery(this).html())
										.css({float: 'left', display: 'block'})
										.css('-moz-user-select', 'none')
										.css('-khtml-user-select', 'none')
										.css('user-select', 'none')
										.attr('unselectable', 'on');

		// column sorting?
		if (cfg.sorting && cfg.isSortableCol(i)) {
			
			var key = cfg.colSortParams[i] ? cfg.colSortParams[i] : i;
			// is this column the default sorted column?
			var cls = (key == cfg.sortedCol || i == cfg.sortedCol) ? 
									( cfg.sortedColDir == cfg.sortAscParam ? cfg.sortAscClass : cfg.sortDescClass ) :
									( cfg.sortNoneClass );

			col_label.addClass(cls).click(function(){
				var dir = col_label.hasClass(cfg.sortNoneClass) ? 
										cfg.sortDefaultDir : ( col_label.hasClass(cfg.sortAscClass) ? cfg.sortDescParam : cfg.sortAscParam );

				var params = { sort : key, dir : dir };					
				if (p) jQuery.extend(params, { page : p.getPage() } );
				
				g.load( params, function(){						
					var cls = col_label.hasClass(cfg.sortNoneClass) ? 
											( cfg.sortDefaultDir == cfg.sortAscParam ? cfg.sortAscClass : cfg.sortDescClass ) :
											( col_label.hasClass(cfg.sortAscClass) ? cfg.sortDescClass : cfg.sortAscClass );

					// re-init sortable cols
					var i2 = 0;
					g.getHeaders(function(col){
						col.find('div:first').each(function() {
							if(cfg.isSortableCol(i2++))
								jQuery(this).addClass(cfg.sortNoneClass).removeClass(cfg.sortAscClass).removeClass(cfg.sortDescClass);
						});
					});
					col_label.removeClass(cfg.sortAscClass).removeClass(cfg.sortDescClass).addClass(cls).removeClass(cfg.sortNoneClass);

				});
			});
		}
		
		// replace contents of <th>
		jQuery(this).html(col_label);
		
		// bind an event to easily resize columns
		jQuery(this).bind('resizeColumn', {col_num : i}, function(e, w){
			
			jQuery(this).width(w);	
			
			// auto enlarge while header is > headerHeight
			while(jQuery(this).parent().height()>cfg.headerHeight) {
				jQuery(this).width(++w);
			}
			
			// set body cells to this width
			g.resize();	
			g.getColumn(e.data.col_num).each(function(){
				jQuery(this).width(w);
			});
		});
		
		// append resize handle?
		if (cfg.resizableCols) {
			// make column headers resizable
			var handle = jQuery('<div />').html(cfg.resizeHandleHtml == '' ? '-' : cfg.resizeHandleHtml).addClass(cfg.resizeHandleClass);
			handle.bind('mousedown', function(e){
				// start resize drag
				var th 		= jQuery(this).parent();
				var left  = e.clientX;
				z.resizeStart(th, left);
			});
			jQuery(this).append(handle);
		}
	});
	
	// create body table. surround body with container div for scrolling
	// setting width on first row keeps it from "blinking"
	var row = this.find('tr:first')
	jQuery(row).find('td').each(function(i){
		jQuery(this).width( cfg.colWidths[i] )								
	});
	var b = jQuery('<div />')
					.html( jQuery('<table cellpadding="0" cellspacing="0"></table>').html( this.find('tbody') ).width( h.width() ).addClass(cfg.gridClass) )
					.css('overflow', 'auto')
					.height(cfg.height);
			
	
	// resizable cols?
	// if so create a vertical resize divider, with unique ID
	if (cfg.resizableCols) {
		var z_sel = 'vertical-resize-divider' + new Date().getTime();
		var z	= jQuery('<div id="' + z_sel + '"></div>')
						.css({
							backgroundColor: '#ababab', 
							height: (cfg.headerHeight + cfg.height),
							width: '4px',
							position: 'absolute',
							zIndex: '10',
							display: 'block'
						})
						.extend({
							resizeStart : function(th, eventX){
								// this is fired onmousedown of the column's resize handle 						
								var pos	= th.offset();
								jQuery(this).show().css({
									top: pos.top,
									left: eventX
								})
								// when resizing, bind some listeners for mousemove & mouseup events
								jQuery('body').bind('mousemove', {col : th}, function(e){		
									// on mousemove, move the vertical-resize-divider
									var th 		= e.data.col;
									var pos		= th.offset();
									var col_w	= e.clientX - pos.left;
									// make sure cursor isn't trying to make column smaller than minimum
									if (col_w > cfg.minColWidth) {
										jQuery('#' + z_sel).css('left', e.clientX);										
									}																		
								})
								jQuery('body').bind('mouseup', {col : th}, function(e){
									// on mouseup, 
									// 1.) unbind resize listener events from body
									// 2.) hide the vertical-resize-divider
									// 3.) trigger the resize event on the column
									jQuery(this).unbind('mousemove').unbind('mouseup');
									jQuery('#' + z_sel).hide();
									var th 		= e.data.col;
									var pos		= th.offset();
									var col_w	= e.clientX - pos.left;
									if (col_w > cfg.minColWidth) {
										th.trigger('resizeColumn', [col_w]);
									} else {
										th.trigger('resizeColumn', [cfg.minColWidth]);
									}
								})
							}
						});
	}
	// paging?
	// if so create a paging toolbar
	if (cfg.paging) {
	
		// create a paging toolbar
		var totr  = cfg.recordsPerPage > 0 ? cfg.recordsPerPage : b.find('tr').length;
		
		// total records viewing message (if we know total records)
		// total record count might not be passed in config, it's sometimes an expensive hit to the DB
		var pv;
		if (cfg.totalRecords > 0) {
			pv = jQuery('<div />')
						.addClass(cfg.pageViewingRecordsInfoClass)
						.extend({
							updateViewInfo : function(loaded_rows, page){
								var _start = ( (loaded_rows * (page - 1) + 1) );
								var _end   = ( (loaded_rows * page) > cfg.totalRecords ? cfg.totalRecords : loaded_rows * page );
								this.html('Viewing Rows ' + _start + ' - ' + _end + ' of ' + cfg.totalRecords);
								return this;
							}
						});
			// update the "viewing x of y" record info
			pv.updateViewInfo(totr, cfg.pageNumber);
		}
		
		// create our paging control container
		var p 		= jQuery('<div />')
								.addClass(cfg.pageToolbarClass)
								.height(cfg.pageToolbarHeight)
								.width(b.width())
								.extend({										
										setPage : function(p){
											var input = this.find('input.' + cfg.pageInputClass);
											pload.removeClass(cfg.pageLoadingDoneClass);
											g.load( {page : p}, function(){
												input.val(p);
												if (cfg.totalRecords > 0) {
													var totr = b.find('tr').length;
													pv.updateViewInfo(totr, p);
												}
												pload.addClass(cfg.pageLoadingDoneClass);
											});
											return this;
										},
										getPage : function(){
											var p = Number(this.find('input.' + cfg.pageInputClass).val());
											return p;
										}
								});
	
		// start page button
		var pb1		= jQuery('<a href="#">&laquo;</a>').addClass(cfg.pageStartClass).click(function(){
									p.setPage(1);
								});
	
		// prev page button
		var pb2		= jQuery('<a href="#">&lt;</a>').addClass(cfg.pagePrevClass).click(function(){
									var _p = p.getPage();																															
									if (_p > 1) {
										_p--;
										p.setPage(_p);
									}										
								});
	
		// next page button
		if (cfg.totalRecords > 0) {
			var totp = Math.ceil(cfg.totalRecords / totr);
		}
		var pb3		= jQuery('<a href="#">&gt;</a>').addClass(cfg.pageNextClass).click(function(){
									var _p = p.getPage(); _p++;
									if (totp) {
										if (_p <= totp) p.setPage(_p);
									} else {
										p.setPage(_p);
									}
								});
		
		// loading indicator
		var pload	= jQuery('<div />').addClass(cfg.pageLoadingClass).addClass(cfg.pageLoadingDoneClass);
		
		// page field & form
		var pfld  = jQuery('<input type="text" value="' + cfg.pageNumber + '"/>').addClass(cfg.pageInputClass);
		var pinfo = jQuery('<div />')
								.addClass(cfg.pageInfoClass)
								.append(pfld);
		var pform = jQuery('<form></form>').append(pinfo).submit(function(){
									var _p = parseInt(p.getPage());
									if (_p) {
										if (totp) {
											if (_p <= totp) p.setPage(_p);
										} else if (_p > 0) {
											p.setPage(_p);
										}
									} else {
										alert('Please Enter a Valid Page Number.');
									}
									return false;
								});
		
		// last page button & info (if we know total records)
		var pb4;
		if (cfg.totalRecords > 0) {
			pinfo.html('Page ' + pinfo.html() + ' of ' + totp);
			var pb4		= jQuery('<a href="#">&raquo;</a>').addClass(cfg.pageEndClass).click(function(){
										var _p = p.getPage(); _p++;
										if (totp) {
											 if (_p < totp) p.setPage(totp);
										}
									});
		} else {
			pinfo.html('Page ' + pinfo.html());
		}
		
		p.append(pb1).append(pb2).append(pform).append(pb3).append(pb4).append(pload).append(pv);
	}

	// create a container div to for our main grid object
	// append & extend grid {g} with header {h}, body {b}, paging {p}, resize handle {z}
	var g = jQuery('<div />').append(h).append(b).extend({
		h : h,
		b : b
	});
	if (cfg.paging) {
		g.append(p).extend({ p : p });
	}
	if (cfg.resizableCols) {
		g.append(z.hide()).extend({ z : z });
	}

	// create some other piece-parts, like
	// ...a gap filler to fill gap over scrollport		
	var gap = jQuery('<div />').width(cfg.scrollbarW).addClass(cfg.headerClass).height(cfg.headerHeight).css({
		position: 'absolute',
		zIndex: '0'
	}).appendTo(g);
	// ...a loading modal mask
	var modalmask = jQuery('<div />').html(cfg.loadingHtml).addClass(cfg.loadingClass).css({
		position: 'absolute',		
		zIndex: '1000'
	}).appendTo(g).hide();

	// create methods on our grid object
	g.extend({
		load : function(params, cb) {
			var data = jQuery.extend(cfg.extraParams, params);
			
			/*
			alert(this + ' ...is jQuery')
			alert(this[0] + ' ...is the div, id="' + this.attr('id') + '"')
			*/
			
			// show loading canvas
			modalmask.width(b.width()).show();
			
			// save selected rows
			g.saveSelectedRows();
			
			jQuery.ajax({
				type: cfg.type.toUpperCase(),
				url: cfg.url,
				data: data,
				success: function(result){
					if(result == "") {
						alert('Error: Empty result.');
						return;
					}
					// for JSON return type
					if (cfg.dataType == 'json') {
						var rows  = eval( '(' + result + ')' );
						alert('json = ' + rows);
					}
					// for HTML (Table) return type
					if (cfg.dataType == 'html') {
						var $tbl = jQuery(result);
						var row  = $tbl.find('tr:first');
						if ( jQuery(row).find('td').length == cfg.colWidths.length ) {
							// setting width on first row keeps it from "blinking"
							jQuery(row).find('td').each(function(i){
								// don't use width() - makes column headers jitter																									 
								// g.getHeader(i).width()
								jQuery(this).width( g.getHeader(i).css('width') )								
							});
							// now swap the tbody's
							b.find('tbody').html($tbl.find('tbody').html());
							g.initStylesAndWidths();
							
							// remember the last loaded state for this grid?
							g.saveState(data);
							
						} else if (row.length < 1) {
							// no rows returned
							alert('Error: No Rows Returned.');
						} else {
							// inconsistent results... too many (or too few) columns returned
							alert('Error: Total columns returned [' + $tbl.find('tbody tr:first td').length + '] do not match Ingrid ['+ cfg.colWidths.length +'].');
						}
					}
					if (cb) cb();
				},
				error: function(){
					alert('Error: Could not load "' + cfg.url + '". Please check the URL and try again. ');
				},
				complete: function(){
					modalmask.hide();
				}
			});
			return this;
		},
		
		// returns JSON
		getState : function() {
			
			/*
			alert(this + ' ...is jQuery')
			alert(this[0] + ' ...is the div, id="' + this.attr('id') + '"')
			*/
			var props = {
				url : 'nothing'				
			}
			return props;
		},
		
		saveState : function(data){

			// how can we deserialize the 'data' object from JSON, to a string, like: "{page:3}"
			//   we could then save this JSON string into a cookie, 
			//   and eval() it back out again when initStylesAndWidths() is called
			
			/*
			I think I need the JSON lib?
			JSON.toString(json_object)
			
			so, like
			JSON.toString(props)
			
			would be nice to combine JSON & jQuery's cookie plugin, call it something like "cache"
			which would let you serialize JSON objects as strings, for storage in cookies, and eval() them back out from a cookie later
			
			so you could call like: 
			
				jQuery.toCache(json_object, 'key')
				json_object = eval( jQuery.fromCache('key') );
				jQuery.clearCache('key')
				
			...u could get creative and call it "save", "remember", "recall", "read", "store", "forget" or whatever
			*/
			
			if (jQuery.cookie) {
				// save page #, column sort & dir
				var g_id  		= this.attr('id');
				var param_str = 'page=' + data.page + ',sort=' + data.sort + ',dir=' + data.dir;
				jQuery.cookie(g_id, param_str, {expires: cfg.cookieExpiresDays, path: cfg.cookiePath});
			}
			
			/*
			props.url = data;
			alert( data.toString() );
			alert( props.toString() );
			*/
			
		},
		
		saveSelectedRows : function() {
			if (jQuery.cookie) {
				var row_ids		= g.getSelectedRowIds();
				if (row_ids.length > 0) {
					jQuery.cookie( this.attr('id') + '_rows', row_ids.join(','), {expires: cfg.cookieExpiresDays, path: cfg.cookiePath});
				}				
			}
		},
		
		// returns <th> els
		getHeaders : function(cb) {
			var ths = this.find('th');
			if (cb) {
				ths.each(function(){
					cb(jQuery(this));							 
				});
				return this;
			} else {
				return ths;
			}
		},
		
		// returns single <th> el
		getHeader : function(i, cb) {
			var th = this.find('th').slice(i, i+1);
			if (cb) {
				cb(jQuery(this));
				return this;
			} else {
				return th;
			}
		},
		
		// returns <td> els in column i
		getColumn : function(i, cb) {
			var tds = this.find("tbody td[" + cfg.columnIDAttr + "='" + i + "']");
			if (cb) {
				tds.each(function(){
					cb(jQuery(this));							 
				});
				return this;
			} else {
				return tds;							 
			}
		},
		
		// returns <tr> els
		getRows : function(cb) {
			var trs = this.find("tbody tr");
			if (cb) {
				trs.each(function(){
					cb(jQuery(this));							 
				});
				return this;
			} else {
				return trs;							 
			}
		},
				
		// returns <tr> els
		getSelectedRows : function() {
			return this.find("tbody tr[_selected='true']");
		},
		
		// returns an array of IDs (current view)
		getSelectedRowIds : function() {
			var rows 			= g.getSelectedRows();
			var row_ids		= [];
			for (i=0; i<rows.length; i++) {
				if ( jQuery(rows[i]).attr('id') ) row_ids.push( jQuery(rows[i]).attr('id') );
			}
			return row_ids;
		},
		
		// returns an array of IDs (saved in cookie)		
		getSavedRowIds : function() {
			var row_ids = [];
			if (jQuery.cookie) {
				var str_ids = jQuery.cookie( this.attr('id') + '_rows' );
				if (str_ids) row_ids = str_ids.split(',');
			}			
			return row_ids;
		},
		
		resize : function() {
			// set body table width based on header table 
			// minimize calls to width() and offset()
			var outer_w = h.width() + cfg.scrollbarW;
			b.width(outer_w);

			if (p) p.width(outer_w);
			
			if (gap) {
				var pos = h.offset();
				gap.css('left', outer_w - cfg.scrollbarW + pos.left).css('top', pos.top);
			}
		},
		
		initStylesAndWidths : function() {
			
			// alert('setting styles and widths')
			
			var colWidths = new Array();
			this.getHeaders().each(function(i){
				// don't use width() - makes column headers jitter
				// colWidths[i] = jQuery(this).width();
				colWidths[i] = jQuery(this).css('width');
			});

			// make one pass of the grid, 
			// initialize properties on rows & columns
			var str_ids = '|' + g.getSavedRowIds().join('|') + '|';
			
			this.getRows().each(function(r){
				
				// custom row styles (striping, etc) & hover
				if (cfg.rowClasses.length > 0) {
					var cursor = (r == 0 ? 0 : r % cfg.rowClasses.length);
					if (cfg.rowClasses[cursor] != '') {
						// custom row class
						jQuery(this).addClass(cfg.rowClasses[cursor]);							
					}
					if (cfg.rowHoverClass != '') {
						// hover class
						jQuery(this).hover(
							function() { if (jQuery(this).attr('_selected') != 'true') jQuery(this).removeClass(cfg.rowClasses[cursor]).addClass(cfg.rowHoverClass); },
							function() { if (jQuery(this).attr('_selected') != 'true') jQuery(this).removeClass(cfg.rowHoverClass).addClass(cfg.rowClasses[cursor]); }
						);
					}
				}
				
				// selection behaviour
				if (cfg.rowSelection == true) {
					jQuery(this).click(function(){
						if (jQuery(this).attr('_selected')) {
							jQuery(this).attr('_selected') == 'true' ?
								jQuery(this).attr('_selected', 'false').removeClass(cfg.rowSelectedClass) :
								jQuery(this).attr('_selected', 'true').addClass(cfg.rowSelectedClass);
							
						} else {
							jQuery(this).attr('_selected', 'true').addClass(cfg.rowSelectedClass);
						}
						if (cfg.onRowSelect) {
							cfg.onRowSelect(this, (jQuery(this).attr('_selected') == 'true' ? true : false) );
						}
					});
					
					// previously selected rows
					if (jQuery(this).attr('id') && str_ids.indexOf( '|' + jQuery(this).attr('id') + '|' ) != -1) {
						jQuery(this).attr('_selected', 'true').addClass(cfg.rowSelectedClass);
					}
				}
				
				// setup column IDs & classes on row's cells
				jQuery(this).find('td').each(function(i){
					// column IDs & width
					// wrap the cell text in a div with overflow hidden, so cells aren't stretched wider by long text
					var txt = jQuery(this).html();
					jQuery(this).attr(cfg.columnIDAttr, i)
											.width(colWidths[i])
											.html( jQuery('<div />').html(txt).css('overflow', 'hidden') );
					// column colors
					if (cfg.colClasses.length > 0) {
						if (cfg.colClasses[i] != '') {
							jQuery(this).addClass(cfg.colClasses[i]);
						}
					}
				});
			});
		}			
	});
	
	// don't break the chain
	// return a modified & extended jQ table object.
	// here,
	// 	this     ...is jQuery
	// 	this[0]  ...is a table
	
	/*
			alert(this + ' ...is jQuery')
			alert(this[0] + ' ...is a table')
			alert(this.length + ' = total tables matched to selector')
	*/

	return this.each(function(tblIter){
		// fires for each table[tblIter].
		// for each one,
		// 	this     ...is a table

		/*
		alert(this + ' ...is a table [' + tblIter + '] , id="' + jQuery(this).attr('id') + '"')
		alert(g[0] + ' ...is the grid div html');
		*/

		// append to doc
		var g_id = 	cfg.ingridIDPrefix + '_' + jQuery( this ).attr('id') + '_' + tblIter;
		g.attr( 'id', g_id );
		jQuery( this ).replaceWith( g[0] )

		// init grid styles, etc
		g.initStylesAndWidths();
		
		// sync grid size to headers
		g.resize();
		
		// place the mask accordingly
		modalmask.width( h.width() + cfg.scrollbarW ).height( b.height()).css({
			top: b.offset().top,
			left: b.offset().left
		});

		// load it up?
		if (cfg.savedStateLoad && jQuery.cookie) {
			var param_str = jQuery.cookie(g_id);
			if (!param_str) {
				g.load();
				cfg.initialLoad = false;
			} else {
				// we have a saved state for this grid_id
				var pairs  	= param_str.split(',');
				var params 	= {};
				var hash  	= [];
				for (i=0; i<pairs.length; i++) {
					var prop = pairs[i].split('=');
					hash[prop[0]] = prop[1];
				}
				if (hash['page'].toLowerCase() != 'undefined' && cfg.paging) {
					params.page = hash['page'];
					p.find('input.' + cfg.pageInputClass).val(params.page);
				}
				if (hash['sort'].toLowerCase() != 'undefined' && 
						hash['dir'].toLowerCase() != 'undefined') {
					
					params.sort = hash['sort'];
					params.dir 	= hash['dir'];
					var colid = params.sort;
					// perhaps the sort param is a key, if so, get the id for that key
					if (cfg.colSortParams.length > 0) {
						for (i=0; i<cfg.colSortParams; i++) {
							if (cfg.colSortParams[i] == params.sort) {
								colid = i;
								break;
							}
						}
					}
					
					// set appropriate style on sorted column
					// perhaps we should bind an event to the column <th>, like setSort()?
					// (re-init sortable cols)
					var i2 = 0;
					g.getHeaders(function(col){
						col.find('div:first').each(function() {
							if(cfg.isSortableCol(i2++))
								g.getHeaders(function(th){
									jQuery(this).addClass(cfg.sortNoneClass).removeClass(cfg.sortAscClass).removeClass(cfg.sortDescClass);
								})
						})
					});
					// all this prevents the column from being style-less (and blinking)
					g.getHeader(parseInt(colid)).find('div:first').addClass(cfg.sortNoneClass).removeClass(cfg.sortAscClass).removeClass(cfg.sortDescClass)
																							  .addClass( params.dir == cfg.sortAscParam ? cfg.sortAscClass : cfg.sortDescClass )
																							  .removeClass(cfg.sortNoneClass);
				}					
				if ( params.page || params.sort || params.dir ) {
					g.load(params);
					cfg.initialLoad = false;
				}
			}
		}
		
		if (cfg.initialLoad) {
			g.load();
		}

	}).extend({

		g : g

	});

};

