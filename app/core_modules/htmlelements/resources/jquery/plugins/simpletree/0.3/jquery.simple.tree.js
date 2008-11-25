/*
* jQuery SimpleTree Drag&Drop plugin
* Update on 22th May 2008
* Version 0.3
*
* Licensed under BSD <http://en.wikipedia.org/wiki/BSD_License>
* Copyright (c) 2008, Peter Panov <panov@elcat.kg>, IKEEN Group http://www.ikeen.com
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*     * Neither the name of the Peter Panov, IKEEN Group nor the
*       names of its contributors may be used to endorse or promote products
*       derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY Peter Panov, IKEEN Group ``AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL Peter Panov, IKEEN Group BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


jQuery.fn.simpleTree = function(opt){
	return this.each(function(){
		var TREE = this;
		var ROOT = jQuery('.root',this);
		var mousePressed = false;
		var mouseMoved = false;
		var dragMoveType = false;
		var dragNode_destination = false;
		var dragNode_source = false;
		var dragDropTimer = false;
		var ajaxCache = Array();

		TREE.option = {
			drag:		true,
			animate:	false,
			autoclose:	false,
			speed:		'fast',
			afterAjax:	false,
			afterMove:	false,
			afterClick:	true,
			afterDblClick:	false,
			// added by Erik Dohmen (2BinBusiness.nl) to make context menu cliks available
			afterContextMenu:	false,
			docToFolderConvert:false
		};
		TREE.option = jQuery.extend(TREE.option,opt);
		jQuery.extend(this, {getSelected: function(){
			return jQuery('span.active', this).parent();
		}});
		TREE.closeNearby = function(obj)
		{
			jQuery(obj).siblings().filter('.folder-open, .folder-open-last').each(function(){
				var childUl = jQuery('>ul',this);
				var className = this.className;
				this.className = className.replace('open','close');
				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed);
				}else{
					childUl.hide();
				}
			});
		};
		TREE.nodeToggle = function(obj)
		{
			var childUl = jQuery('>ul',obj);
			if(childUl.is(':visible')){
				obj.className = obj.className.replace('open','close');

				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed);
				}else{
					childUl.hide();
				}
			}else{
				obj.className = obj.className.replace('close','open');
				if(TREE.option.animate)
				{
					childUl.animate({height:"toggle"},TREE.option.speed, function(){
						if(TREE.option.autoclose)TREE.closeNearby(obj);
						if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl, obj.id);
					});
				}else{
					childUl.show();
					if(TREE.option.autoclose)TREE.closeNearby(obj);
					if(childUl.is('.ajax'))TREE.setAjaxNodes(childUl, obj.id);
				}
			}
		};
		TREE.setAjaxNodes = function(node, parentId, callback)
		{
			if(jQuery.inArray(parentId,ajaxCache) == -1){
				ajaxCache[ajaxCache.length]=parentId;
				var url = jQuery.trim(jQuery('>li', node).text());
				if(url && url.indexOf('url:'))
				{
					url=jQuery.trim(url.replace(/.*\{url:(.*)\}/i ,'jQuery1'));
					jQuery.ajax({
						type: "GET",
						url: url,
						contentType:'html',
						cache:false,
						success: function(responce){
							node.removeAttr('class');
							node.html(responce);
							jQuery.extend(node,{url:url});
							TREE.setTreeNodes(node, true);
							if(typeof TREE.option.afterAjax == 'function')
							{
								TREE.option.afterAjax(node);
							}
							if(typeof callback == 'function')
							{
								callback(node);
							}
						}
					});
				}
				
			}
		};
		TREE.setTreeNodes = function(obj, useParent){
			obj = useParent? obj.parent():obj;
			jQuery('li>span', obj).addClass('text')
			.bind('selectstart', function() {
				return false;
			}).click(function(){
				jQuery('.active',TREE).attr('class','text');
				if(this.className=='text')
				{
					this.className='active';
				}
				if(typeof TREE.option.afterClick == 'function')
				{
					TREE.option.afterClick(jQuery(this).parent());
				}
				return false;
			}).dblclick(function(){
				mousePressed = false;
				TREE.nodeToggle(jQuery(this).parent().get(0));
				if(typeof TREE.option.afterDblClick == 'function')
				{
					TREE.option.afterDblClick(jQuery(this).parent());
				}
				return false;
				// added by Erik Dohmen (2BinBusiness.nl) to make context menu actions
				// available
			}).bind("contextmenu",function(){
				jQuery('.active',TREE).attr('class','text');
				if(this.className=='text')
				{
					this.className='active';
				}
				if(typeof TREE.option.afterContextMenu == 'function')
				{
					TREE.option.afterContextMenu(jQuery(this).parent());
				}
				return false;
			}).mousedown(function(event){
				mousePressed = true;
				cloneNode = jQuery(this).parent().clone();
				var LI = jQuery(this).parent();
				if(TREE.option.drag)
				{
					jQuery('>ul', cloneNode).hide();
					jQuery('body').append('<div id="drag_container"><ul></ul></div>');
					jQuery('#drag_container').hide().css({opacity:'0.8'});
					jQuery('#drag_container >ul').append(cloneNode);
					jQuery("<img>").attr({id	: "tree_plus",src	: "images/plus.gif"}).css({width: "7px",display: "block",position: "absolute",left	: "5px",top: "5px", display:'none'}).appendTo("body");
					jQuery(document).bind("mousemove", {LI:LI}, TREE.dragStart).bind("mouseup",TREE.dragEnd);
				}
				return false;
			}).mouseup(function(){
				if(mousePressed && mouseMoved && dragNode_source)
				{
					TREE.moveNodeToFolder(jQuery(this).parent());
				}
				TREE.eventDestroy();
			});
			jQuery('li', obj).each(function(i){
				var className = this.className;
				var open = false;
				var cloneNode=false;
				var LI = this;
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

					if(!open || className.indexOf('ajax')>=0)childNode.hide();

					TREE.setTrigger(this);
				}else{
					var setClassName = 'doc';
					this.className = setClassName + (jQuery(this).is(':last-child')? '-last':'');
				}
			}).before('<li class="line">&nbsp;</li>')
			.filter(':last-child').after('<li class="line-last"></li>');
			TREE.setEventLine(jQuery('.line, .line-last', obj));
		};
		TREE.setTrigger = function(node){
			jQuery('>span',node).before('<img class="trigger" src="images/spacer.gif" border=0>');
			var trigger = jQuery('>.trigger', node);
			trigger.click(function(event){
				TREE.nodeToggle(node);
			});
			if(!jQuery.browser.msie)
			{
				trigger.css('float','left');
			}
		};
		TREE.dragStart = function(event){
			var LI = jQuery(event.data.LI);
			if(mousePressed)
			{
				mouseMoved = true;
				if(dragDropTimer) clearTimeout(dragDropTimer);
				if(jQuery('#drag_container:not(:visible)')){
					jQuery('#drag_container').show();
					LI.prev('.line').hide();
					dragNode_source = LI;
				}
				jQuery('#drag_container').css({position:'absolute', "left" : (event.pageX + 5), "top": (event.pageY + 15) });
				if(LI.is(':visible'))LI.hide();
				var temp_move = false;
				if(event.target.tagName.toLowerCase()=='span' && jQuery.inArray(event.target.className, Array('text','active','trigger'))!= -1)
				{
					var parent = event.target.parentNode;
					var offs = jQuery(parent).offset({scroll:false});
					var screenScroll = {x : (offs.left - 3),y : event.pageY - offs.top};
					var isrc = jQuery("#tree_plus").attr('src');
					var ajaxChildSize = jQuery('>ul.ajax',parent).size();
					var ajaxChild = jQuery('>ul.ajax',parent);
					screenScroll.x += 19;
					screenScroll.y = event.pageY - screenScroll.y + 5;

					if(parent.className.indexOf('folder-close')>=0 && ajaxChildSize==0)
					{
						if(isrc.indexOf('minus')!=-1)jQuery("#tree_plus").attr('src','images/plus.gif');
						jQuery("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
						dragDropTimer = setTimeout(function(){
							parent.className = parent.className.replace('close','open');
							jQuery('>ul',parent).show();
						}, 700);
					}else if(parent.className.indexOf('folder')>=0 && ajaxChildSize==0){
						if(isrc.indexOf('minus')!=-1)jQuery("#tree_plus").attr('src','images/plus.gif');
						jQuery("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
					}else if(parent.className.indexOf('folder-close')>=0 && ajaxChildSize>0)
					{
						mouseMoved = false;
						jQuery("#tree_plus").attr('src','images/minus.gif');
						jQuery("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();

						jQuery('>ul',parent).show();
						/*
							Thanks for the idea of Erik Dohmen
						*/
						TREE.setAjaxNodes(ajaxChild,parent.id, function(){
							parent.className = parent.className.replace('close','open');
							mouseMoved = true;
							jQuery("#tree_plus").attr('src','images/plus.gif');
							jQuery("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
						});

					}else{
						if(TREE.option.docToFolderConvert)
						{
							jQuery("#tree_plus").css({"left": screenScroll.x, "top": screenScroll.y}).show();
						}else{
							jQuery("#tree_plus").hide();
						}
					}
				}else{
					jQuery("#tree_plus").hide();
				}
				return false;
			}
			return true;
		};
		TREE.dragEnd = function(){
			if(dragDropTimer) clearTimeout(dragDropTimer);
			TREE.eventDestroy();
		};
		TREE.setEventLine = function(obj){
			obj.mouseover(function(){
				if(this.className.indexOf('over')<0 && mousePressed && mouseMoved)
				{
					this.className = this.className.replace('line','line-over');
				}
			}).mouseout(function(){
				if(this.className.indexOf('over')>=0)
				{
					this.className = this.className.replace('-over','');
				}
			}).mouseup(function(){
				if(mousePressed && dragNode_source && mouseMoved)
				{
					dragNode_destination = jQuery(this).parents('li:first');
					TREE.moveNodeToLine(this);
					TREE.eventDestroy();
				}
			});
		};
		TREE.checkNodeIsLast = function(node)
		{
			if(node.className.indexOf('last')>=0)
			{
				var prev_source = dragNode_source.prev().prev();
				if(prev_source.size()>0)
				{
					prev_source[0].className+='-last';
				}
				node.className = node.className.replace('-last','');
			}
		};
		TREE.checkLineIsLast = function(line)
		{
			if(line.className.indexOf('last')>=0)
			{
				var prev = jQuery(line).prev();
				if(prev.size()>0)
				{
					prev[0].className = prev[0].className.replace('-last','');
				}
				dragNode_source[0].className+='-last';
			}
		};
		TREE.eventDestroy = function()
		{
			// added by Erik Dohmen (2BinBusiness.nl), the unbind mousemove TREE.dragStart action
			// like this other mousemove actions binded through other actions ain't removed (use it myself
			// to determine location for context menu)
			jQuery(document).unbind('mousemove',TREE.dragStart).unbind('mouseup').unbind('mousedown');
			jQuery('#drag_container, #tree_plus').remove();
			if(dragNode_source)
			{
				jQuery(dragNode_source).show().prev('.line').show();
			}
			dragNode_destination = dragNode_source = mousePressed = mouseMoved = false;
			//ajaxCache = Array();
		};
		TREE.convertToFolder = function(node){
			node[0].className = node[0].className.replace('doc','folder-open');
			node.append('<ul><li class="line-last"></li></ul>');
			TREE.setTrigger(node[0]);
			TREE.setEventLine(jQuery('.line, .line-last', node));
		};
		TREE.convertToDoc = function(node){
			jQuery('>ul', node).remove();
			jQuery('img', node).remove();
			node[0].className = node[0].className.replace(/folder-(open|close)/gi , 'doc');
		};
		TREE.moveNodeToFolder = function(node)
		{
			if(!TREE.option.docToFolderConvert && node[0].className.indexOf('doc')!=-1)
			{
				return true;
			}else if(TREE.option.docToFolderConvert && node[0].className.indexOf('doc')!=-1){
				TREE.convertToFolder(node);
			}
			TREE.checkNodeIsLast(dragNode_source[0]);
			var lastLine = jQuery('>ul >.line-last', node);
			if(lastLine.size()>0)
			{
				TREE.moveNodeToLine(lastLine[0]);
			}
		};
		TREE.moveNodeToLine = function(node){
			TREE.checkNodeIsLast(dragNode_source[0]);
			TREE.checkLineIsLast(node);
			var parent = jQuery(dragNode_source).parents('li:first');
			var line = jQuery(dragNode_source).prev('.line');
			jQuery(node).before(dragNode_source);
			jQuery(dragNode_source).before(line);
			node.className = node.className.replace('-over','');
			var nodeSize = jQuery('>ul >li', parent).not('.line, .line-last').filter(':visible').size();
			if(TREE.option.docToFolderConvert && nodeSize==0)
			{
				TREE.convertToDoc(parent);
			}else if(nodeSize==0)
			{
				parent[0].className=parent[0].className.replace('open','close');
				jQuery('>ul',parent).hide();
			}

			// added by Erik Dohmen (2BinBusiness.nl) select node
			if(jQuery('span:first',dragNode_source).attr('class')=='text')
			{
				jQuery('.active',TREE).attr('class','text');
				jQuery('span:first',dragNode_source).attr('class','active');
			}

			if(typeof(TREE.option.afterMove) == 'function')
			{
				var pos = jQuery(dragNode_source).prevAll(':not(.line)').size();
				TREE.option.afterMove(jQuery(node).parents('li:first'), jQuery(dragNode_source), pos);
			}
		};

		TREE.addNode = function(id, text, callback)
		{
			var temp_node = jQuery('<li><ul><li id="'+id+'"><span>'+text+'</span></li></ul></li>');
			TREE.setTreeNodes(temp_node);
			dragNode_destination = TREE.getSelected();
			dragNode_source = jQuery('.doc-last',temp_node);
			TREE.moveNodeToFolder(dragNode_destination);
			temp_node.remove();
			if(typeof(callback) == 'function')
			{
				callback(dragNode_destination, dragNode_source);
			}
		};
		TREE.delNode = function(callback)
		{
			dragNode_source = TREE.getSelected();
			TREE.checkNodeIsLast(dragNode_source[0]);
			dragNode_source.prev().remove();
			dragNode_source.remove();
			if(typeof(callback) == 'function')
			{
				callback(dragNode_destination);
			}
		};

		TREE.init = function(obj)
		{
			TREE.setTreeNodes(obj, false);
		};
		TREE.init(ROOT);
	});
}