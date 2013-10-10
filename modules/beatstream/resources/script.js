jQuery(document).ready(function(){
	
	var ul = jQuery('ul.suggestions');
	
	// Listening of a click on a UP or DOWN arrow:
	
	jQuery(document).on('click','div.vote span', function(){
		
		var elem		= jQuery(this),
			parent		= elem.parent(),
			li			= elem.closest('li'),
			ratingDiv	= li.find('.rating'),
			id			= li.attr('id').replace('s',''),
			v			= 1;

		// If the user's already voted:
		
		if(parent.hasClass('inactive')){
			return false;
		}
		
		parent.removeClass('active').addClass('inactive');
		
		if(elem.hasClass('down')){
			v = -1;
		}
		
		// Incrementing the counter on the right:
		ratingDiv.text(v + +ratingDiv.text());
		
		// Turning all the LI elements into an array
		// and sorting it on the number of votes:
		
		var arr = jQuery.makeArray(ul.find('li')).sort(function(l,r){
			return +jQuery('.rating',r).text() - +jQuery('.rating',l).text();
		});

		// Adding the sorted LIs to the UL
		ul.html(arr);
		
		// Sending an AJAX request
		jQuery.get('index.php?module=beatstream',{action:'vote',vote:v,'id':id});
	});


	jQuery('#suggest').submit(function(){
		
		var form		= jQuery(this),
			textField	= jQuery('#suggestionText');
		
		// Preventing double submits:
		if(form.hasClass('working') || textField.val().length<3){
			return false;
		}
		
		form.addClass('working');
		
		jQuery.getJSON('index.php?module=beatstream',{action:'submit',content:textField.val()},function(msg){
			textField.val('');
			form.removeClass('working');
			
			if(msg.html){
				// Appending the markup of the newly created LI to the page:
				jQuery(msg.html).hide().appendTo(ul).slideDown();
			}
		});
		
		return false;
	});
});
