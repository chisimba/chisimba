/*
The MIT Licence, for code from kryogenix.org

Code downloaded from the Browser Experiments section of kryogenix.org is licenced under the so-called MIT licence. The licence is below.

Copyright (c) 1997-date Stuart Langridge

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

To use within CHISIMBA
    $headerParams=$this->getJavascriptFile('new_sorttable.js','htmlelements');
    $this->appendArrayVar('headerParams',$headerParams);

    $this->loadclass('htmltable','htmlelements');

    $objTable=new htmltable();
    $objTable->id="newtable";
    $objTable->css_class="sorttable";
//    $objTable->cellspacing='2';   NB. It is imperative that this line is not used
    $objTable->cellpadding='2';
    $objTable->row_attributes='row_'.$objTable.id;

    $objTable->startRow();
    $objTable->addCell('Heading 1','','','','heading','');
    $objTable->addCell('Heading 2','','','','heading','');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell('Row 1 datum 1','','','','','');
    $objTable->addCell('Row 1 datum 2','','','','','');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell('Datum 1 of row 2','','','','','');
    $objTable->addCell('Datum 2 of row 2','','','','','');
    $objTable->endRow();

    echo $objTable->show();
*/
addEvent(window, "load", sortables_init);
var SORT_COLUMN_INDEX;

function sortables_init(){
    // Find all tables with class sortable and make them sortable
    if(!document.getElementsByTagName){
         return;
    }

    tables=document.getElementsByTagName("table");

    for(var tableIndex = 0;tableIndex < tables.length;tableIndex++){
        thisTable = tables[tableIndex];
        if(thisTable.className == 'sorttable'){
            //initTable(thisTbl.id);
            ts_makeSortable(thisTable);
        }
    }
}

function ts_makeSortable(table){
    tableRows = document.getElementsByName('row_'+table.id);

    if(tableRows && tableRows.length > 0){
        var firstRow = tableRows[0];
    }

    if(!firstRow){
        return;
    }

    // We have a first row: assume it's the header, and make its contents clickable links
    for(var columnIndex = 0;columnIndex < firstRow.cells.length;columnIndex++){
        var cell = firstRow.cells[columnIndex];
        var txt = ts_getInnerText(cell);
        if(txt == ''){
            cell.innerHTML = '';
        }else{
            cell.innerHTML = '<a href="#" class="sortheader" '+
            'onclick="ts_resortTable(this, '+columnIndex+');return false;">'+
            txt+'<span class="sortarrow"></span></a>';
        }
    }
}

function ts_getInnerText(cellElement){
    if(typeof cellElement == "string"){
        return cellElement;
    }

    if (typeof cellElement == "undefined"){
        return cellElement;
    }

    //Not needed but it is faster
    if(cellElement.innerText){
        return cellElement.innerText;
    }

    var str = "";
    var nodeList = cellElement.childNodes;
    var nodeLength = nodeList.length;

    for(var nodeIndex = 0;nodeIndex < nodeLength ;nodeIndex++){
        switch(nodeList[nodeIndex].nodeType){
            case 1: //ELEMENT_NODE
                str += ts_getInnerText(nodeList[nodeIndex]);
                break;
            case 3: //TEXT_NODE
                str += nodeList[nodeIndex].nodeValue;
                break;
        }
    }

    return str;
}

function ts_resortTable(sortLink, columnIndex){
    // get the span
    var span;
    for (var nodeIndex = 0;nodeIndex < sortLink.childNodes.length;nodeIndex++){
        if(sortLink.childNodes[nodeIndex].tagName
          && sortLink.childNodes[nodeIndex].tagName.toLowerCase() == 'span'){
            span = sortLink.childNodes[nodeIndex];
        }
    }

    var spantext = ts_getInnerText(span);
    var cellElement = sortLink.parentNode;
    var column = columnIndex || cellElement.cellIndex;
    var table = getParent(cellElement,'TABLE');

    // Work out a type for the column
    tableRows = document.getElementsByName('row_'+table.id);
    if(tableRows.length <= 1){
        return;
    }

    var itm = ts_getInnerText(tableRows[1].cells[column]);
    sortfn = ts_sort_caseinsensitive;

    if(itm.match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/)){
        sortfn = ts_sort_date;
    }

    if(itm.match(/^\d\d[\/-]\d\d[\/-]\d\d$/)){
        sortfn = ts_sort_date;
    }

    if(itm.match(/^[£$]/)){
        sortfn = ts_sort_currency;
    }

    if(itm.match(/^[\d\.]+$/)){
        sortfn = ts_sort_numeric;
    }

    SORT_COLUMN_INDEX = column;
    var firstRow = new Array();
    var newRows = new Array();

    // not used
    //for(i = 0;i < tableRows[0].length;i++){
    //    firstRow[i] = tableRows[0][i];
    //}

    for(rowIndex = 1;rowIndex < tableRows.length;rowIndex++){
        newRows[rowIndex-1] = tableRows[rowIndex];
    }

    newRows.sort(sortfn);

    if(span.getAttribute("sortdir") == 'down'){
        ARROW = '&#160;&#160;&#8593;';
        newRows.reverse();
        span.setAttribute('sortdir','up');
    }else{
        ARROW = '&#160;&#160;&#8595;';
        span.setAttribute('sortdir','down');
    }

    // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
    // don't do sortbottom rows
    cellSpacing = document.getElementById(table.id).cellspacing;
//alert(document.getElementById(table.id));

    for(rowIndex = 0;rowIndex < newRows.length;rowIndex++){
        if(!newRows[rowIndex].className || (newRows[rowIndex].className && (newRows[rowIndex].className.indexOf('sortbottom') == -1))){
            table.appendChild(newRows[rowIndex]);
        }
    }

    // do sortbottom rows only
    for(rowIndex = 0;rowIndex < newRows.length;rowIndex++){
        if(newRows[rowIndex].className && (newRows[rowIndex].className.indexOf('sortbottom') != -1)){
            table.appendChild(newRows[rowIndex]);
        }
    }

    if(cellSpacing){
alert(cellSpacing);
        table.cellspacing = cellSpacing;
    }

    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for(var spanIndex = 0;spanIndex < allspans.length;spanIndex++){
        if(allspans[spanIndex].className == 'sortarrow'){
            if(getParent(allspans[spanIndex],"table") == getParent(sortLink,"table")){ // in the same table as us?
                allspans[spanIndex].innerHTML = '';
            }
        }
    }

    if(document.getElementById('input_'+table.id)){
        if(span.getAttribute("sortdir") == 'down'){
            sort_order = 'ASC';
        }else{
            sort_order = 'DESC';
        }
        document.getElementById('input_'+table.id).value = table.id+'|'+SORT_COLUMN_INDEX+'|'+sort_order;
    }
    span.innerHTML = ARROW;
}

function getParent(cellElement, parentTagName){
    if(cellElement == null){
        return null;
    }else if(cellElement.nodeType == 1 && cellElement.tagName.toLowerCase() == parentTagName.toLowerCase()){
        // Gecko bug, supposed to be uppercase
        return cellElement;
    }else{
        return getParent(cellElement.parentNode, parentTagName);
    }
}

function ts_sort_date(a,b){
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);

    if(aa.length == 10){
        dt1 = aa.substr(6,4)+aa.substr(3,2)+aa.substr(0,2);
    }else{
        yr = aa.substr(6,2);
        if(parseInt(yr) < 50){
            yr = '20'+yr;
        }else{
            yr = '19'+yr;
        }
        dt1 = yr+aa.substr(3,2)+aa.substr(0,2);
    }

    if(bb.length == 10){
        dt2 = bb.substr(6,4)+bb.substr(3,2)+bb.substr(0,2);
    }else{
        yr = bb.substr(6,2);
        if(parseInt(yr) < 50){
            yr = '20'+yr;
        }else{
            yr = '19'+yr;
        }
        dt2 = yr+bb.substr(3,2)+bb.substr(0,2);
    }

    if(dt1==dt2){
        return 0;
    }

    if(dt1 < dt2){
        return -1;
    }

    return 1;
}

function ts_sort_currency(a,b){
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');

    return parseFloat(aa) - parseFloat(bb);
}

function ts_sort_numeric(a,b){
    aa = parseFloat(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
    if(isNaN(aa)){
        aa = 0;
    }

    bb = parseFloat(ts_getInnerText(b.cells[SORT_COLUMN_INDEX]));
    if(isNaN(bb)){
        bb = 0;
    }

    return aa-bb;
}

function ts_sort_caseinsensitive(a,b){
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();

    if(aa==bb){
        return 0;
    }

    if(aa < bb){
        return -1;
    }

    return 1;
}

function ts_sort_default(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);

    if(aa==bb){
        return 0;
    }

    if(aa < bb){
        return -1;
    }

    return 1;
}

function addEvent(elm, evType, fn, useCapture){
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
    if (elm.addEventListener){
        elm.addEventListener(evType, fn, useCapture);
        return true;
    }else if(elm.attachEvent){
        var r = elm.attachEvent("on"+evType, fn);
        return r;
    }else{
        alert("Handler could not be removed");
    }
}