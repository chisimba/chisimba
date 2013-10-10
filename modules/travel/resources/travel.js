var childrenPerRoom=new Array();
var adultsPerRoom=new Array();

function adjustRooms(index,word_room) {
    numRooms = index+1;
    var table = '<table border="0" cellspacing="2" cellpadding="2">\n'
    for (i = 0; i < numRooms; i++) {
        if (adultsPerRoom[i] == null) {
            adultsPerRoom[i] = 2;
        }
        if (childrenPerRoom[i] == null) {
            childrenPerRoom[i] = 0;
        }
        table += '<tr><td>';
        if (numRooms >1) {
            roomNo = i+1;
            table += word_room+" "+roomNo+": </td><td>"; 
        }
        table += '<select id="input_searchAdults_'+i+'" class="WCHhider" name="searchAdults_'+i+'"><option value="1">1</option><option selected="selected" value="2">2</option><option value="3">3</option><option value="4">4</option></select></td><td>';
        table += '<select id="input_searchChildren_'+i+'" class="WCHhider" name="searchChildren_'+i+'"><option selected="selected" value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option></select></td></tr>';
    }
    table += "</table>";
    document.getElementById('roomsDiv').innerHTML = table;
    //alert(index+1);
}

function windowLoad(url) {
    new Ajax.Autocompleter("input_searchStr", "autocomplete_choices", url, {minChars: 3});
}