// ===================================================================
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
//
// NOTICE: You may use this code for any purpose, commercial or
// private, without any further permission from the author. You may
// remove this notice from your final code if you wish, however it is
// appreciated by the author if at least my web site address is kept.
//
// You may *NOT* re-distribute this code in any way except through its
// use. That means, you can include it in your product, or your web
// site, or any other form where the code is actually being used. You
// may not put the plain javascript up on your site for download or
// include it in your javascript libraries for download. 
// If you wish to share this code with others, please just point them
// to the URL instead.
// Please DO NOT link directly to my .js files from your site. Copy
// the files to your server and use them there. Thank you.
// ===================================================================

/*
sorttable.js
  Matt Kruse
  Last Modified: 1/22/03

(NOTE: SCRIPT tags are broken into two pieces in these comments so the browser 
 doesn't interpret them)
  
This javascript creates a table with client-side data sorting. It works in 4.x or
higher browsers. In IE, it uses CSS and in Netscape it uses Layers.

Instructions for use in your HTML:

Include the js file inside your HTML source:
   <SCR IPT LANGUAGE="JAVASCRIPT" SRC="sorttable.js"></SCR IPT>
Due to a Netscape browser bug, if the page you are using this on is inside a frameset,
you may need to include an empty SCRIPT tag before the included file. Like this:
	<SCR IPT></SCR IPT>
   <SCR IPT LANGUAGE="JAVASCRIPT" SRC="sorttable.js"></SCR IPT>

Insert the style sheet types in your HTML source, inside <HEAD></HEAD>:
   <STYLE type=text/css>
   .rel {POSITION: relative; width:100%;}
   .abs {POSITION: absolute; width:100%;}
   .right { text-align:right; }
   .left { text-align:left; }
   .center { text-align:center; }
   </STYLE>

Define the table somewhere in your HTML file. Preferrably this will go in the 
<HEAD> of your document, but it may also be included in-line:
     var t = new SortTable("t");
This creates a new SortTable object. You must pass the function an argument of
the name of the object you are creating. For example,
     var mytable = new SortTable("mytable");

Now define the columns of your table:
     t.AddColumn("Name","","left","");
     t.AddColumn("ID","","center","numeric");
     t.AddColumn("Bonus","BGCOLOR=ffffcc","right","money");
The first argument is the name of the column, which you can use when sorting.
The second argument is the <TD> properties. Anything in this argument will be
  added to the <TD> for each element in this column.
The third argument is the alignment of this column. left, center, right. (default left)
The last argument is the data type of the column. This is used for sorting. More data
  types may be defined manually. The default data type for sorting is alphanumeric.
  Accepted types are:
     - numeric : float data
	  - form : If this columns contains form elements, you must use this type!
	           (this will cause the table to be non-sortable in Netscape)
     - date : Date data must be in a format understood by Date.parse()!
	  				  
Now populate the table:

     t.AddLine("John Doe",12345,"$500.00");

This adds a row of data to the table.
Sometimes, the table cell will contain text other than the actual data. For example, if
your cells contain a "Work Order Number" like '032097' that are inside of a link, your
table cell would contain:
     <a href="whatever">032097</a>
Sorting based on this text data does not give the correct results, however. Instead, you
want to sort based on just the number itself - 032097. For this, you need to define the
sort data separately from the cell contents. To continue with the above example, let's 
say that "John Doe" is actually a link to his home page. Your AddLine would look like this:

     t.AddLine("<a href='http://www.johndoe.com/'>Jone Doe</a>,12345,"$500.00");

To correctly sort the first column by only his name, you would then add this command
immediately after the AddLine() command:

     t.AddLineSortData("John Doe",'','');

When blank entries are left for the sort data, the text data entered with AddLine() is
used for the sorting.

You have one more option for the line of data you just entered. You may include a string
that will be included in the <TR> for this line of the table. You can then include a
background color for the row, for example. NOTE: This applies to the row number, and does
NOT move when the rows are sorted. For example, if you put a red background in a row,
and then re-sort the table, the red background will not move. The syntax for adding an
argument to the row's <TR> is:
     
	  t.AddLineProperties(text);
For example:
     t.AddLineProperties("align=center bgcolor=#F6F6F6");
This will center the whole row and give it a light gray background color. This option is
mostly useful for giving every other row a different background color to make reading
easier.
	  
You are now done defining the data of your table. To put the table into your HTML, you
must manually define the table itself. In order for Netscape to properly show cells of
different lengths, a width of the table MUST be specified!!
      
     <table border=1 width=600>
     <tr>
     	<th><a href="javascript:SortRows(t,0)">Name</a></th>
     	<th><a href="javascript:SortRows(t,1)">ID</a></th>
     	<th><a href="javascript:SortRows(t,2)">Bonus</a></th>

The links are to the SortRows() function. This will sort and re-populate the table. It
will be most common to call this function when a user clicks a table header, but you 
could actually call this function to sort from anywhere in the page. The sort function
requires the table name and column number (starting with index 0) as arguments.

Note: If SortRows() is called on the same row as the last sort, it will inverse the 
order of the last sort.

Now, as the body of your table, simply call the WriteRows() method of the table to
output the data in its original order. Do not wrap the WriteRows() call around <TR>
or <TD> tags - it creates them:
     </tr>
     <SCR IPT>t.WriteRows()</SCR IPT>
     </table>

That's it!
*/
var use_css=false;
var use_layers=false;   
var use_dom=false;

if (document.all)    { use_css    = true; }
if (document.layers) { use_layers = true; }
if (document.getElementById) { use_dom=true; }

var sort_object;
var sort_column;
var reverse=0;

// Constructor for SortTable object
function SortTable(name) {
	// Properties
	this.name = name;
	this.sortcolumn="";
	this.dosort=true;
	this.tablecontainsforms=false;
	// Methods
	this.AddLine = AddLine;
	this.AddColumn = AddColumn;
	this.WriteRows = WriteRows;
	this.SortRows = SortRows;
	this.AddLineProperties = AddLineProperties;
	this.AddLineSortData = AddLineSortData;
	// Structure
	this.Columns = new Array();
	this.Lines = new Array();
	this.LineProperties = new Array();
	}
// Add a line to the grid
function AddLine() {
	var index = this.Lines.length;
	this.Lines[index] = new Array();
	for (var i=0; i<arguments.length; i++) {
		this.Lines[index][i] = new Object();
		this.Lines[index][i].text = arguments[i];
		this.Lines[index][i].data = arguments[i];
		}
	}
// Define properties for the <TR> of the last line added
function AddLineProperties(prop) {
	var index = this.Lines.length-1;
	this.LineProperties[index] = prop;
	}
// Define sorting data for the last line added
function AddLineSortData() {
	var index = this.Lines.length-1;
	for (var i=0; i<arguments.length; i++) {
		if (arguments[i] != '') {
			this.Lines[index][i].data = arguments[i];
			}
		}
	}

// Add a column definition to the table
// Arguments:
//   name = name of the column
//   td   = any arguments to go into the <TD> tag for this column (ex: BGCOLOR="red")
//   align= Alignment of data in cells
//   type = type of data in this column (numeric, money, etc) - default alphanumeric
function AddColumn(name,td,align,type) {
	var index = this.Columns.length;
	this.Columns[index] = new Object;
	this.Columns[index].name = name;
	this.Columns[index].td   = td;
	this.Columns[index].align=align;
	this.Columns[index].type = type;
	if (type == "form") {
		 this.tablecontainsforms=true; 
		 if (use_layers) { 
		 	this.dosort=false;
			}
		}
	}
// Print out the original set of rows in the grid
function WriteRows() {
	var open_div = "";
	var close_div = "";
	for (var i=0; i<this.Lines.length; i++) {
	document.write("<TR "+this.LineProperties[i]+">");
		for (var j=0; j<this.Columns.length; j++) {
			var div_name = "d"+this.name+"-"+i+"-"+j;
			if (use_css || use_dom) {
				if (this.Columns[j].align != '') {
					var align = " ALIGN="+this.Columns[j].align;
					}
				else {
					var align = "";
					}
				open_div = "<DIV ID=\""+div_name+"\" "+align+">";
				close_div= "</DIV>";
				}
			else if (use_layers) {
				// If the table contains form elements, don't use <LAYER> tags or the
				// form will be forced closed.
				if (!this.dosort) {
					if (this.Columns[j].align != '') {
						open_div="<SPAN CLASS=\""+this.Columns[j].align+"\">";
						}
					}
				else {
					open_div = "<ILAYER NAME=\""+div_name+"\" WIDTH=100%>";
					open_div+= "<LAYER NAME=\""+div_name+"x\" WIDTH=100%>";
					if (this.Columns[j].align != '') {
						open_div+= "<SPAN CLASS=\""+this.Columns[j].align+"\">";
						}
					}
				if (this.Columns[j].align != '') {
	 				close_div = "</SPAN>";
					}
				if (this.dosort) {
					close_div += "</LAYER></ILAYER>";
					}
				}
			document.write("<TD "+this.Columns[j].td+">"+open_div+this.Lines[i][j].text+close_div+"</TD>");
			}
		document.write("</TR>");
		}
	}
// Sort the table and re-write the results to the existing table
function SortRows(table,column) {
	sort_object = table;
	if (!sort_object.dosort) { return; }
	if (sort_column == column) { reverse=1-reverse; }
	else { reverse=0; }
	sort_column = column;

	// Save all form column contents into a temporary object
	// This is a nasty hack to keep the current values of form elements intact
	if (table.tablecontainsforms) {
		var iname="1";
		var tempcolumns = new Object();
		for (var i=0; i<table.Lines.length; i++) {
			for (var j=0; j<table.Columns.length; j++) {
				if(table.Columns[j].type == "form") {
					var cell_name = "d"+table.name+"-"+i+"-"+j;
					if (use_css) {
						tempcolumns[iname] = document.all[cell_name].innerHTML;
					}
					else {
						tempcolumns[iname] = document.getElementById(cell_name).innerHTML;
					}
					table.Lines[i][j].text = iname;
					iname++;
					}
				}
			}
		}
	
	if (table.Columns[column].type == "numeric") {
		// Sort by Float
		table.Lines.sort(	function by_name(a,b) {
									if (parseFloat(a[column].data) < parseFloat(b[column].data) ) { return -1; }
									if (parseFloat(a[column].data) > parseFloat(b[column].data) ) { return 1; }
									return 0;
									}
								);
		}
	else if (table.Columns[column].type == "money") {
		// Sort by Money
		table.Lines.sort(	function by_name(a,b) {
									if (parseFloat(a[column].data.substring(1)) < parseFloat(b[column].data.substring(1)) ) { return -1; }
									if (parseFloat(a[column].data.substring(1)) > parseFloat(b[column].data.substring(1)) ) { return 1; }
									return 0;
									}
								);
		}
	else if (table.Columns[column].type == "date") {
		// Sort by Date
		table.Lines.sort(	function by_name(a,b) {
									if (Date.parse(a[column].data) < Date.parse(b[column].data) ) { return -1; }
									if (Date.parse(a[column].data) > Date.parse(b[column].data) ) { return 1; }
									return 0;
									}
								);
		}

	else {
		// Sort by alphanumeric
		table.Lines.sort(	function by_name(a,b) {
									if (a[column].data+"" < b[column].data+"") { return -1; }
									if (a[column].data+"" > b[column].data+"") { return 1; }
									return 0;
									}
								);
		}

	if (reverse) { table.Lines.reverse(); }
	for (var i=0; i<table.Lines.length; i++) {
		for (var j=0; j<table.Columns.length; j++) {
			var cell_name = "d"+table.name+"-"+i+"-"+j;
			if (use_dom) {
				if(table.Columns[j].type == "form") {
					var iname = table.Lines[i][j].text;
					document.getElementById(cell_name).innerHTML = tempcolumns[iname];
					}
				else {
					document.getElementById(cell_name).innerHTML = table.Lines[i][j].text;
					}
				}
			else if (use_css) {
				if(table.Columns[j].type == "form") {
					var iname = table.Lines[i][j].text;
					document.all[cell_name].innerHTML = tempcolumns[iname];
					}
				else {
					document.all[cell_name].innerHTML = table.Lines[i][j].text;
					}
				}
			else if (use_layers) {
				var cell_namex= "d"+table.name+"-"+i+"-"+j+"x";
				if (table.Columns[j].align != '') {
					document.layers[cell_name].document.layers[cell_namex].document.write("<SPAN CLASS=\""+table.Columns[j].align+"\">");
					}
				document.layers[cell_name].document.layers[cell_namex].document.write(table.Lines[i][j].text);
				if (table.Columns[j].align != '') {
					document.layers[cell_name].document.layers[cell_namex].document.write("</SPAN>");
					}
				document.layers[cell_name].document.layers[cell_namex].document.close();
				}
			}
		}
	}
