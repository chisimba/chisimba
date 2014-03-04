Take a look at the uncompressed source code for all the options. We'll cover the main ones here.

url
string
Default: [empty]
Required. The URL to your server-side script that will handle the logic.

images
string
Default: images
Required. The relative path to the Compass Grid images folder.




hover
true/false
Default: true
When set to true, hovering over table cells will activate the .hover css class for css styling. The CSS classname for hover can be changed by changing the hoverClass option.

selectable
true/false
Default: true
When set to true, clicking on a cell "selects" it. We combine this in Compass with a checkbox for each row. The CSS classname for this can be changed by changing the selectableClass option.

sort
true/false
Default: true
Allows users to sort columns (although logic for this must be incorporated into server-side script).

striping
true/false
Default: true
Striping will alternate classes for table rows (default classes .odd and .even). CSS classes can be changed by altering oddClass and evenClass options.

resizable
true/false
Default: true
Setting to true allows users to resize columns. This is currently a little buggy, so use at your own risk. Request jQuery UI / Resizable to work.

toggle
true/false
Default: true
Setting to true allows users to toggle columns on or off. A "Show/Hide Columns" box is added automatically.

pager
true/false
Default: true
Whether you're using pagination or not.

pagerBefore
true/false
Default: true
Set to true to show the "pager bar" above (before) the table.

pagerAfter
true/false
Default: true
Set to true to show the "pager bar" after the table.

paramsStart
string
Default: ?
A starting character for passing your server-side script information. Compass Grid defaults to ?foo=bar&this=that

paramsSeparator
string
Default: &
A character for separating your server-side key/value pairs. Compass Grid defaults to ?foo=bar&this=that

paramsSeparator
string
Default: =
A character placed between a key and a value in the url. Compass Grid defaults to ?foo=bar&this=that



Server-Side Code

Your server side code should check for various data being sent by the plugin. A sample request might look like this:

pageData.php/?page=3&sortField=title&sortOrder=asc&show10;

Here's what this means:

Page: The page the user is requesting

sortField: The table column that the user is sorting by. The value here will be the id for the column that you specified in your server-side code.

sortOrder: The order the user wants to sort. Either asc or desc.

show: The number of results to return to the user (i.e 10 at a time)
