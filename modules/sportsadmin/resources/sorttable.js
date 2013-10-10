//javascript file to move teams from the pool of available teams 
function moveSelectedAOption(from,to,hiddenvalue){

//check if there are some options selected
if(from !=null && from.options !=null){

	//check if there are some values already selected
	if(to.value == ""){
		
		
		//move the elements
	var o = from.options[from.selectedIndex]; 
	to.value= from.options[from.selectedIndex].text;
	
	tovalue = from.options[from.selectedIndex].value;
	
	//delete the selected /moved option from the previous list
	from.options[from.selectedIndex] =  null;
	hiddenvalue.value = tovalue;			
document.getElementById("valuea").innerHTML = "<input type='hidden' name='selectedi' value="+ tovalue+">";	
	}
	
	
	else {return false;}	
}

}


function moveSelectedBOption(from,to,hiddenvalue){

//check if there are some options selected
if(from !=null && from.options !=null){
   
   //check if there are any elements in the textbox
   if(to.value == ""){

	//move the elements
	var o = from.options[from.selectedIndex]; 
	to.value= from.options[from.selectedIndex].text;
	
	bright = from.options[from.selectedIndex].value;
	
	//delete the selected /moved option from the previous list
	from.options[from.selectedIndex] =  null;
	
	hiddenvalue.value = bright;
	var content = "";
	content +="<input type='hidden' name='bright' value="+ bright+">";		
	document.getElementById("valueb").innerHTML = content;	
  	
}
	else {return false;} 


}

}
	

//function to move the option to left
function moveOptionToLeft(from,field,to){
	
	
if(from.value !=""){
	
	var v = from.value;	
	var t = field.value;
	var array_size = to.options.length;	
    to.options[array_size] = new Option(t,v,false, false);

//make the value null
field.value = null;
	
}
else {return false;}
	
	
}//closing the function