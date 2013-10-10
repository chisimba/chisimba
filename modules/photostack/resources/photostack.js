    $(function() {
				/**
				* navR,navL are flags for controlling the albums navigation
				* first gives us the position of the album on the left
				* positions are the left positions for each of the 5 albums displayed at a time
				*/
                var navR,navL	= false;
				var first		= 1;
				var positions 	= {
					'0'		: 0,
					'1' 	: 170,
					'2' 	: 340,
					'3' 	: 510,
					'4' 	: 680
				}
				var $ps_albums 		= $('#ps_albums');
				/**
				* number of albums available
				*/
				var elems			= $ps_albums.children().length;
				var $ps_slider		= $('#ps_slider');
				
				/**
				* let's position all the albums on the right side of the window
				*/
				var hiddenRight 	= $(window).width() - $ps_albums.offset().left;
				$ps_albums.children('div').css('left',hiddenRight + 'px');
				
				/**
				* move the first 5 albums to the viewport
				*/
				$ps_albums.children('div:lt(5)').each(
					function(i){
						var $elem = $(this);
						$elem.animate({'left': positions[i] + 'px','opacity':1},800,function(){
							if(elems > 5)
								enableNavRight();
						});
					}
				);
				
				/**
				* next album
				*/
				$ps_slider.find('.next').bind('click',function(){
					if(!$ps_albums.children('div:nth-child('+parseInt(first+5)+')').length || !navR) return;
					disableNavRight();
					disableNavLeft();
					moveRight();
				});
				
				/**
				* we move the first album (the one on the left) to the left side of the window
				* the next 4 albums slide one position, and finally the next one in the list
				* slides in, to fill the space of the first one
				*/
				function moveRight () {
					var hiddenLeft 	= $ps_albums.offset().left + 163;
					
					var cnt = 0;
					$ps_albums.children('div:nth-child('+first+')').animate({'left': - hiddenLeft + 'px','opacity':0},500,function(){
							var $this = $(this);
							$ps_albums.children('div').slice(first,parseInt(first+4)).each(
								function(i){
									var $elem = $(this);
									$elem.animate({'left': positions[i] + 'px'},800,function(){
										++cnt;
										if(cnt == 4){
											$ps_albums.children('div:nth-child('+parseInt(first+5)+')').animate({'left': positions[cnt] + 'px','opacity':1},500,function(){
												//$this.hide();
												++first;
												if(parseInt(first + 4) < elems)
													enableNavRight();
												enableNavLeft();
											});
										}		
									});
								}
							);		
					});
				}
				
				/**
				* previous album
				*/
				$ps_slider.find('.prev').bind('click',function(){
					if(first==1  || !navL) return;
					disableNavRight();
					disableNavLeft();
					moveLeft();
				});
				
				/**
				* we move the last album (the one on the right) to the right side of the window
				* the previous 4 albums slide one position, and finally the previous one in the list
				* slides in, to fill the space of the last one
				*/
				function moveLeft () {
					var hiddenRight 	= $(window).width() - $ps_albums.offset().left;
				
					var cnt = 0;
					var last= first+4;
					$ps_albums.children('div:nth-child('+last+')').animate({'left': hiddenRight + 'px','opacity':0},500,function(){
							var $this = $(this);
							$ps_albums.children('div').slice(parseInt(last-5),parseInt(last-1)).each(
								function(i){
									var $elem = $(this);
									$elem.animate({'left': positions[i+1] + 'px'},800,function(){
										++cnt;
										if(cnt == 4){
											$ps_albums.children('div:nth-child('+parseInt(last-5)+')').animate({'left': positions[0] + 'px','opacity':1},500,function(){
												//$this.hide();
												--first;
												enableNavRight();
												if(first > 1)
													enableNavLeft();
											});
										}										
									});
								}
							);
					});
				}
				
				/**
				* disable or enable albums navigation
				*/
				function disableNavRight () {
					navR = false;
					$ps_slider.find('.next').addClass('disabled');
				}
				function disableNavLeft () {
					navL = false;
					$ps_slider.find('.prev').addClass('disabled');
				}
				function enableNavRight () {
					navR = true;
					$ps_slider.find('.next').removeClass('disabled');
				}
				function enableNavLeft () {
					navL = true;
					$ps_slider.find('.prev').removeClass('disabled');
				}		
				
				var $ps_container 	= $('#ps_container');
				var $ps_overlay 	= $('#ps_overlay');
				var $ps_close		= $('#ps_close');
				/**
				* when we click on an album,
				* we load with AJAX the list of pictures for that album.
				* we randomly rotate them except the last one, which is
				* the one the User sees first. We also resize and center each image.
				*/
				$ps_albums.children('div').bind('click',function(){
					var $elem = $(this);
					var divid = $elem.attr('id');
                    var album_name = divid;
                   

					var $loading 	= $('<div />',{className:'loading'});
					$elem.append($loading);
					$ps_container.find('img').remove();
					$.get('index.php?module=photostack&action=album', {puid:album_name} , function(data) {
						var items_count	= data.length;
						for(var i = 0; i < items_count; ++i){
							var item_source = data[i];
							var cnt 		= 0;
							$('<img />').load(function(){
								var $image = $(this);
								++cnt;
								resizeCenterImage($image);
								$ps_container.append($image);
								var r		= Math.floor(Math.random()*41)-20;
								if(cnt < items_count){
									$image.css({
										'-moz-transform'	:'rotate('+r+'deg)',
										'-webkit-transform'	:'rotate('+r+'deg)',
										'transform'			:'rotate('+r+'deg)'
									});
								}
								if(cnt == items_count){
									$loading.remove();
									$ps_container.show();
									$ps_close.show();
									$ps_overlay.show();
								}
							}).attr('src',item_source);
						}
					},'json');
				});
				
				/**
				* when hovering each one of the images, 
				* we show the button to navigate through them
				*/
				$ps_container.live('mouseenter',function(){
					$('#ps_next_photo').show();
				}).live('mouseleave',function(){
					$('#ps_next_photo').hide();
				});
				
				/**
				* navigate through the images: 
				* the last one (the visible one) becomes the first one.
				* we also rotate 0 degrees the new visible picture 
				*/
				$('#ps_next_photo').bind('click',function(){
					var $current 	= $ps_container.find('img:last');
					var r			= Math.floor(Math.random()*41)-20;
					
					var currentPositions = {
						marginLeft	: $current.css('margin-left'),
						marginTop	: $current.css('margin-top')
					}
					var $new_current = $current.prev();
					
					$current.animate({
						'marginLeft':'250px',
						'marginTop':'-385px'
					},250,function(){
						$(this).insertBefore($ps_container.find('img:first'))
							   .css({
									'-moz-transform'	:'rotate('+r+'deg)',
									'-webkit-transform'	:'rotate('+r+'deg)',
									'transform'			:'rotate('+r+'deg)'
								})
							   .animate({
									'marginLeft':currentPositions.marginLeft,
									'marginTop'	:currentPositions.marginTop
									},250,function(){
										$new_current.css({
											'-moz-transform'	:'rotate(0deg)',
											'-webkit-transform'	:'rotate(0deg)',
											'transform'			:'rotate(0deg)'
										});
							   });
					});
				});
				
				/**
				* close the images view, and go back to albums
				*/
				$('#ps_close').bind('click',function(){
					$ps_container.hide();
					$ps_close.hide();
					$ps_overlay.fadeOut(400);
				});
				
				/**
				* resize and center the images
				*/
				function resizeCenterImage($image){
					var theImage 	= new Image();
					theImage.src 	= $image.attr("src");
					var imgwidth 	= theImage.width;
					var imgheight 	= theImage.height;
					
					var containerwidth  = 460;
					var containerheight = 330;
					
					if(imgwidth	> containerwidth){
						var newwidth = containerwidth;
						var ratio = imgwidth / containerwidth;
						var newheight = imgheight / ratio;
						if(newheight > containerheight){
							var newnewheight = containerheight;
							var newratio = newheight/containerheight;
							var newnewwidth =newwidth/newratio;
							theImage.width = newnewwidth;
							theImage.height= newnewheight;
						}
						else{
							theImage.width = newwidth;
							theImage.height= newheight;
						}
					}
					else if(imgheight > containerheight){
						var newheight = containerheight;
						var ratio = imgheight / containerheight;
						var newwidth = imgwidth / ratio;
						if(newwidth > containerwidth){
							var newnewwidth = containerwidth;
							var newratio = newwidth/containerwidth;
							var newnewheight =newheight/newratio;
							theImage.height = newnewheight;
							theImage.width= newnewwidth;
						}
						else{
							theImage.width = newwidth;
							theImage.height= newheight;
						}
					}
					$image.css({
						'width'			:theImage.width,
						'height'		:theImage.height,
						'margin-top'	:-(theImage.height/2)-10+'px',
						'margin-left'	:-(theImage.width/2)-10+'px'	
					});
				}
            });
