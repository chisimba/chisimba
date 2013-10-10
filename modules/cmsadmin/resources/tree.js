jQuery.fn.SimpleTree = function(opt){
	this.each(function(){
		var TREE = this;
		var ROOT = jQuery('.root',this);
		TREE.option = {
			animate: false,		// this parameter has a value "true/false" (enable/disable animation for expanding/collapsing menu items) 
			autoclose: false,	// this parameter has a value "true/false" (enable/disable collapse of neighbor branches)
			speed: 'fast',		// speed open/close folder
			success: false,		// this parameter defines function, which executes after ajax is loaded (set to "false" by default)
			click: false		// this parameter defines function, which is executed after item clicked (set to "false" by default) 

		};
		TREE.option = jQuery.extend(TREE.option,opt);
		TREE.setAjaxNodes = function(obj)
		{
			var url = jQuery.trim(jQuery('>li', obj).text());
			if(url && url.indexOf('url:'))
			{
				url=jQuery.trim(url.replace(/.*\{url:(.*)\}/i ,'jQuery1'));
				jQuery.ajax({
					type: "GET",
					url: url,
					contentType:'html',
					cache:false,
					success: function(responce){
						if(responce)
						{
							obj.removeAttr('class');
							obj.html(responce);
							TREE.setTreeNodes(obj, true);
							if(typeof TREE.option.success == 'function')
							{
								TREE.option.success();
							}
						}else{
							var parent = obj.parent();
							var pClassName = parent.attr('class');
							pClassName = pClassName.replace('folder-open','leaf');
							parent.attr('class', pClassName);
							obj.remove();
							jQuery('.toggler',parent).remove();
						}
					}
				});
			}
		};
		TREE.closeNearby = function(obj)
		{
			jQuery(obj).siblings().filter('.folder-open, .folder-open-last').each(function(){
				var childUl = jQuery('>ul',this);
				var className = this.className;
				className = className.replace('open','close');
				jQuery(this).attr('class',className);
				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed);
				}else{
					childUl.hide();
				}
			});
		};
		TREE.setEventToggler = function (obj)
		{
			jQuery(obj).prepend('<span class="toggler"></span>');
			jQuery('>.toggler', obj).bind('click', function(){

				var childUl = jQuery('>ul',obj);
				var className = obj.className;
				if(childUl.is(':visible')){
					className = className.replace('open','close');
					jQuery(obj).attr('class',className);
					if(TREE.option.animate)
					{
						childUl.animate({height:"toggle"},TREE.option.speed);
					}else{
						childUl.hide();
					}
				}else{
					className = className.replace('close','open');
					jQuery(obj).attr('class',className);
					if(TREE.option.animate)
					{
						childUl.animate({height:"toggle"},TREE.option.speed, function(){
							if(TREE.option.autoclose)TREE.closeNearby(obj);
							if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl);
						});
					}else{
						childUl.show();
						if(TREE.option.autoclose)TREE.closeNearby(obj);
						if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl);
					}
				}
			});
		};
		TREE.setTreeNodes = function(obj, useParent)
		{
			obj = useParent? obj.parent():obj;
			jQuery('li', obj).each(function(i){
				var className = this.className;
				var open = false;
				var childNode = jQuery('>ul',this);

				if(childNode.size()>0){
					var setClassName = 'folder-';
					if(className && className.indexOf('open')>=0){
						setClassName=setClassName+'open';
						open=true;
					}else{
						setClassName=setClassName+'close';
					}
					this.className = setClassName + (jQuery(this).is(':last-child')? '-last':'');
					TREE.setEventToggler(this);
					if(!open || className.indexOf('ajax')>=0)childNode.hide();

				}else{
					var setClassName = 'leaf';
					this.className = setClassName + (jQuery(this).is(':last-child')? '-last':'');
				}
				jQuery('>.text, >.active',this).bind('click', function(){
					jQuery('.active',TREE).attr('class','text');
					jQuery(this).attr('class','active');
					if(typeof TREE.option.click == 'function')
					{
						TREE.option.click(this);
					}
				});
			});
		};


		TREE.init = function(obj)
		{
			TREE.setTreeNodes(obj);
		};
		TREE.init(ROOT);
	});
}