function go2URL(address,target)
{
	window.open(address,target);
}
function newWindow(address)
{
	var maxW = screen.width;
	var maxH = screen.height;
	var w = 800;
	var h = 600;
	var _top = Math.floor((maxH - h) / 2);
	var _left = Math.floor((maxW - w) / 2);
	
	var win = window.open(address,'doiW',"toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes,copyhistory=yes,width="+w+",height="+h);
	win.moveTo(_left,_top);
	win.focus();
}