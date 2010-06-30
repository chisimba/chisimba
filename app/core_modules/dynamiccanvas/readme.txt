This module allows for block based templates.
Blocks are inserted using JSON. A typical JSON
block will contain some or all of the following.

{
    "display" : "block",
    "module" : "modulename",
    "block" : "blockname",
    "blocktype" : "blocktype",
    "titleLength" : "titlelength",
    "wrapStr" : 0|1,
    "showToggle" : 0|1,
    "hidden" : "value,
    "showTitle" : 0|1,
    "cssClass" : "cssClass",
    "cssId " : "cssId"
}

Only the first three parameter:value pairs are
required. Example block insertion JSON includes:

{
    "display" : "block",
    "module" : "dynamiccanvas",
    "block" : "thirdtest",
    "showToggle" : 0

}

{
    "display" : "block",
    "module" : "userdetails",
    "block" : "userdetails"
}

Note that "display" : "block" is required. Note that
parameter names are case sensitive so showTitle is correct
while showtitle is not.