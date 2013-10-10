function modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, url, loadingImage){

    var containerid = "innerModalPopupDiv";
		
    var popupDiv = document.createElement('div');
    var popupMessage = document.createElement('div');
    var blockDiv = document.createElement('div');
	
    popupDiv.setAttribute('id', 'outerModalPopupDiv');
    popupDiv.setAttribute('class', 'outerModalPopupDiv');
	
    popupMessage.setAttribute('id', 'innerModalPopupDiv');
    popupMessage.setAttribute('class', 'innerModalPopupDiv');
	
    blockDiv.setAttribute('id', 'blockModalPopupDiv');
    blockDiv.setAttribute('class', 'blockModalPopupDiv');
    blockDiv.setAttribute('onClick', 'closePopup(' + fadeOutTime + ')');
	
    jQuery("body").append(popupDiv);
    jQuery("#outerModalPopupDiv").append(popupMessage);
    jQuery("body").append(blockDiv);
	
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
        var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
        if(ieversion>6) {
            getScrollHeight(top);
        }
    } else {
        getScrollHeight(top);
    }
	
    jQuery('#outerModalPopupDiv').css('display','block');
    jQuery('#outerModalPopupDiv').css('width', width + 'px');
    jQuery('#outerModalPopupDiv').css('padding', borderWeight + 'px');
    jQuery('#outerModalPopupDiv').css('background', borderColor);
    jQuery('#outerModalPopupDiv').css('borderRadius', borderRadius + 'px');
    jQuery('#outerModalPopupDiv').css('MozBorderRadius', borderRadius + 'px');
    jQuery('#outerModalPopupDiv').css('WebkitBorderRadius', borderRadius + 'px');
    jQuery('#outerModalPopupDiv').css('borderWidth', 0 + 'px');
    jQuery('#outerModalPopupDiv').css('position', 'absolute');
    jQuery('#outerModalPopupDiv').css('zIndex', 100);
	
    jQuery('#innerModalPopupDiv').css('padding', padding + 'px');
    jQuery('#innerModalPopupDiv').css('background',backgroundColor);
    jQuery('#innerModalPopupDiv').css('borderRadius',(borderRadius - 3) + 'px');
    jQuery('#innerModalPopupDiv').css('MozBorderRadius',(borderRadius - 3) + 'px');
    jQuery('#innerModalPopupDiv').css('WebkitBorderRadius',(borderRadius - 3) + 'px');
	
    jQuery('#blockModalPopupDiv').css('width', 100 + '%');
    jQuery('#blockModalPopupDiv').css('border', 0 + 'px');
    jQuery('#blockModalPopupDiv').css('padding', 0 + 'px');
    jQuery('#blockModalPopupDiv').css('margin', 0 + 'px');
    jQuery('#blockModalPopupDiv').css('background', disableColor);
    jQuery('#blockModalPopupDiv').css('opacity', (disableOpacity / 100));
    jQuery('#blockModalPopupDiv').css('filter', 'alpha(Opacity=' + disableOpacity + ')');
    jQuery('#blockModalPopupDiv').css('zIndex', 99);
    jQuery('#blockModalPopupDiv').css('position', 'fixed');
    jQuery('#blockModalPopupDiv').css('top', 0 + 'px');
    jQuery('#blockModalPopupDiv').css('left', 0 + 'px');
	
    if(align=="center") {
        jQuery('#outerModalPopupDiv').css('marginLeft', (-1 * (width / 2)) + 'px');
        jQuery('#outerModalPopupDiv').css('left', 50 + '%');
    } else if(align=="left") {
        jQuery('#outerModalPopupDiv').css('marginLeft', 0 + 'px');
        jQuery('#outerModalPopupDiv').css('left', 10 + 'px');
    } else if(align=="right") {
        jQuery('#outerModalPopupDiv').css('marginRight', 0 + 'px');
        jQuery('#outerModalPopupDiv').css('right', 10 + 'px');
    } else {
        jQuery('#outerModalPopupDiv').css('marginLeft', (-1 * (width / 2)) + 'px');
        jQuery('#outerModalPopupDiv').css('left',  50 + '%');
    }
    blockPage();
 
    jQuery("#"+containerid).html('<div align="center"><img src="' + loadingImage + '" border="0" /></div>');

    jQuery.ajax({
        type: "POST",
        url: "index.php?module=mynotes&action=ajaxGetShare",
        success: function(source) {
            jQuery("#"+containerid).html(source);
        }
    });
	
}

function imageloader(url, containerid, loadingImage) {
	
    document.getElementById(containerid).innerHTML = '<div align="center"><img src="' + loadingImage + '" border="0" /></div>';
    document.getElementById(containerid).innerHTML='<div align="center"><img src="' + url + '" border="0" /></div>';
	
}

function blockPage() {
	
    var blockdiv = jQuery('#blockModalPopupDiv');
    var height = screen.height;
	
    blockdiv.css('height', height + 'px');
    blockdiv.css('display', 'block');

}

function getScrollHeight(top) {
   
    var h = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
           
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
		
        var ieversion=new Number(RegExp.$1);
		
        if(ieversion>6) {
            jQuery('#outerModalPopupDiv').css('top', h + top + 'px');
        } else {
            jQuery('#outerModalPopupDiv').css('top', top + 'px');
        }
		
    } else {
        jQuery('#outerModalPopupDiv').css('top', h + top + 'px');
    }
	
}

function closePopup(fadeOutTime) {
	
    fade('outerModalPopupDiv', fadeOutTime);
    jQuery('#blockModalPopupDiv').css('display', 'none');

}

function fade(id, fadeOutTime) {
	
    var el = document.getElementById(id);
	
    if(el == null) {
        return;
    }
	
    if(el.FadeState == null) {
		
        if(el.style.opacity == null || el.style.opacity == '' || el.style.opacity == '1') {
            el.FadeState = 2;
        } else {
            el.FadeState = -2;
        }
	
    }
	
    if(el.FadeState == 1 || el.FadeState == -1) {
		
        el.FadeState = el.FadeState == 1 ? -1 : 1;
        el.fadeTimeLeft = fadeOutTime - el.fadeTimeLeft;
		
    } else {
		
        el.FadeState = el.FadeState == 2 ? -1 : 1;
        el.fadeTimeLeft = fadeOutTime;
        setTimeout("animateFade(" + new Date().getTime() + ",'" + id + "','" + fadeOutTime + "')", 33);
	
    }  
  
}

function animateFade(lastTick, id, fadeOutTime) {
	  
    var currentTick = new Date().getTime();
    var totalTicks = currentTick - lastTick;
	
    var el = document.getElementById(id);
	
    if(el.fadeTimeLeft <= totalTicks) {
	
        el.style.opacity = el.FadeState == 1 ? '1' : '0';
        el.style.filter = 'alpha(opacity = ' + (el.FadeState == 1 ? '100' : '0') + ')';
        el.FadeState = el.FadeState == 1 ? 2 : -2;
        document.body.removeChild(el);
        return;
	
    }
	
    el.fadeTimeLeft -= totalTicks;
    var newOpVal = el.fadeTimeLeft / fadeOutTime;
	
    if(el.FadeState == 1) {
        newOpVal = 1 - newOpVal;
    }
	
    el.style.opacity = newOpVal;
    el.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';
	
    setTimeout("animateFade(" + currentTick + ",'" + id + "','" + fadeOutTime + "')", 33);
  
}