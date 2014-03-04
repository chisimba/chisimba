function addToShelf()  
{
    var a = $('loadIcon');
    var el1 = document.getElementsByName('articles');
    var len = el1.length;
    var num = 0;
    var arts = '';
    
    a.show();
    
    for(num = 0; num != len; num++){
        if(el1[num].checked == true){
            arts = arts+el1[num].value+'|';
        }
    }
    
    var url = 'index.php';
    var pars = 'module=etd&action=addeshelf&articles='+arts;
    var eshelfAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onSuccess: clearIcon});          
}

function clearIcon()
{
    var a = $('loadIcon');
    var b = $('confirm');
    a.hide();
    b.show();
    
    setTimeout("var b = $('confirm'); b.hide()", 15000);
}

function changeInput(drop)  
{
    var el1 = 'inputbox'+drop;
    var el2 = 'degreebox'+drop;
    var el3 = 'departmentbox'+drop;
    var el4 = 'facultybox'+drop;
    var el5 = 'languagebox'+drop;
    var el = document.getElementById('criteria'+drop).value;
    
    document.getElementById('inputbox'+drop).name = 'input'+drop;
    document.getElementById('degreebox'+drop).name = 'degree'+drop;
    document.getElementById('departmentbox'+drop).name = 'depart'+drop;
    document.getElementById('facultybox'+drop).name = 'fac'+drop;
    document.getElementById('languagebox'+drop).name = 'language'+drop;
    
    a = $(el1);
    b = $(el2);
    c = $(el3);
    d = $(el4);
    e = $(el5);
    
    a.hide();
    b.hide();
    c.hide();
    d.hide();
    e.hide();
    
    switch(el){
        case 'thesis_degree_name':
            document.getElementById('degreebox'+drop).name = 'box'+drop;
            b.show();
            break;
        case 'thesis_degree_discipline':
            document.getElementById('departmentbox'+drop).name = 'box'+drop;
            c.show();
            break;
        case 'thesis_degree_faculty':
            document.getElementById('facultybox'+drop).name = 'box'+drop;
            d.show();
            break;
        case 'dc_language':
            document.getElementById('languagebox'+drop).name = 'box'+drop;
            e.show();
            break;
        default:
            document.getElementById('inputbox'+drop).name = 'box'+drop;
            a.show();
    }
}