/*
   jQuery Text Annotate Plugin 0.9
   Copyright (C) 2011  Koos van der Kolk (koosvdkolk [at] gmail.com)

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
(function($){
	function textAnnotate(element, options) {
		/* init some variables */		
		options = options || {};
		var global_annotations = {};
		var global_annotationsCounter = 0;
		var textAnnotaterOn = false;
		var textHiglighterOnAction;
		var totalWordCounter=0;
		var indexesOfAnnotationsWithFocus;
		var beingAnnotatedElementIdArray = [];	
		var formElement;

		/* options */

		//css and ids
		options.annotatedClassName       = options.annotatedClassName || "annotated";
		options.beingAnnotatedClassName  = options.beingAnnotatedClassName || "beingAnnotated";
		options.wrapperClassName         = options.wrapperClassName || "textAnnotateWrapper";
		options.elementClassName         = options.elementClassName || "annotatableElement";
		options.annotationFocusClassName = options.annotationFocusClassName || "annotationFocus";
		options.elementIdPrefix          = options.elementIdPrefix  || "textAnnotate_";
		options.annotations              = options.annotations || false;

		options.form                     = options.form || {};
		//form		
		options.formDeleteAnnotationButton = options.formDeleteAnnotationButton || '<a href="javascript:void(0)" class="jQueryTextAnnotaterDialogRemoveButton">Remove annotation</a>';
		
		//events
		options.onInit             = options.onInit || function(){};
		options.onAddAnnotation    = options.onAddAnnotation || function(){};
		options.onRemoveAnnotation = options.onRemoveAnnotation || function(){};
		options.onSaveAnnotation   = options.onSaveAnnotation || function(){};

		/* add event to body, ending all annotating activities */
		$('body').mouseup(function(){
			onAnnotateEnd();
		});

		/* create user dialog */
		var userDialog                    = $('<div class="jQueryTextAnnotaterDialog"></div>');

		//set annotatin focus when hovering (probably user does not want the annotation focus to be gone when hovering the dialog)
		userDialog.hover(function(){
			var element = userDialog.data('textAnnotate.element');
			if (element){
				setAnnotationFocus(element);
			}
		});

		//remove annotation focus when leaving the dialog
		userDialog.mouseleave(function(){
			userDialog.removeData('element');
			unsetAnnotationFocus();
		})

		//add button to dialog
		var userDialogRemoveAnnotationButton = $(options.formDeleteAnnotationButton);
		userDialogRemoveAnnotationButton.click(function(){
			textHiglighterOnAction = '';
			removeAnnotationWithFocus();
		});

		userDialog.append(userDialogRemoveAnnotationButton);
	
		//add form to dialog
		if (options.form){
			formElement = $('<form class="jQueryTextAnnotaterDialogForm"></form>');
			formElement.html(options.form);

			//when form element value changes, save the form
			formElement.find(':input').click(function(){
				saveAnnotationFormWithFocus();
			}).keyup(function(){
				saveAnnotationFormWithFocus();
			});
		}
		userDialog.append(formElement);
		userDialog.enableTextSelect();
		
		$('body').append(userDialog);
	
		function _init(jQueryElement) {
			var i;
	
			/* put all elements to which this function is called into _initElements function */
			for (i=0; i<jQueryElement.length; i++){
				_initElements(jQueryElement[i]);

				$(jQueryElement[i]).mousedown(function(){
					onAnnotateStart(this);
				});
			}
	
			$("."+options.elementClassName, jQueryElement).mousedown(function() {
				if($(this).attr('class').indexOf(options.annotatedClassName)===-1){
					textHiglighterOnAction = 'add';
					addHighlight(this);
				}
			});
		
			$("."+options.elementClassName, jQueryElement).mouseover(function() {
				var element = $(this);			
				if (textAnnotaterOn){
					if (textHiglighterOnAction==='add'){
						addHighlight(element);
					} else if (textHiglighterOnAction==='remove') {
						removeElementAnnotation(element, indexesOfAnnotationsWithFocus);
					}
				} else if(element.data('textAnnotate.annotationIndexes') && element.data('textAnnotate.annotationIndexes').length>0){
					setAnnotationFocus(element);
				}
			});
		
			$("."+options.elementClassName, jQueryElement).mouseout(function() {
				unsetAnnotationFocus();
			});

			
			/* load annotations in options */
			if (options.annotations){
				loadAnnotations(options.annotations);
			}

			//call listener
			options.onInit.call(undefined, global_annotations);
		}

		/**
		 * Loads annotations
		 * @param array annotationDefinitions {annotationId : [{"elementId":id, "formValues":dataObj}, ...], ...}, in which dataObj = {key: value, ...}
		 **/
		function loadAnnotations(annotationDefinitions, useAnnotationIndex){
			var annotationId, elements;
			useAnnotationIndex = useAnnotationIndex || false;

			for (annotationId in annotationDefinitions){			
				elements = annotationDefinitions[annotationId];
				
				if (useAnnotationIndex){
					addAnnotation(elements, annotationId);
				}else{
					addAnnotation(elements);
				}				
			}
		}

		/**
		 * Adds an annotation
		 * @param {object}  elements         A jQuery array-like object with elements OR an array with annotation definitions (see function loadAnnotations)
		 * @param {integer} annotationId  (optional) the annotationId
		 **/
		function addAnnotation(elements, annotationId){
			var element, elementDefinitionsProvided = false, newAnnotateLevel, currentAnnotateLevel;
			var elementDefinition, elementId, elementData, dataKey, elementIndex, dataToStore, key, pos;
			if ($.isArray(elements)){
				/* we have an array of element definitions, see function loadAnnotations */
				elementDefinitionsProvided = true;
			}

			/* create new annotation */
			var annotation = {};			

			//store annotation			
			annotation = [];
			annotationId = annotationId || global_annotationsCounter++;
			global_annotations[annotationId] = annotation;
			
			/* add elements to annotation */
			$(elements).each(function(index, value){
				if (elementDefinitionsProvided){
					/* we have an array of element definitions, see function loadAnnotations */
					elementDefinition = value;

					element = $('#'+elementDefinition.elementId);
					element.data('textAnnotate.formValues', elementDefinition.formValues);
				}else{
					/* we have an element collection */
					element = $(value);
				}

				/* remove beingAnnotated flag (if present) */
				element.data('textAnnotate.beingAnnotated', false);

				/* give it the correct class */
				element.removeClass(options.beingAnnotatedClassName);
				element.addClass(options.annotatedClassName);

				//add annotation level & class
				currentAnnotateLevel = element.attr('annotateLevel');
				if (currentAnnotateLevel===undefined) {
					newAnnotateLevel = 0;
				} else {
					newAnnotateLevel = parseInt(currentAnnotateLevel, 10) + 1;
				}
				
				element.attr('annotateLevel', newAnnotateLevel);


				/* keep reference to annotation */
				if (element.data('textAnnotate.annotationIndexes')===undefined){
					element.data('textAnnotate.annotationIndexes', []);
				}
				$.merge(element.data('textAnnotate.annotationIndexes'), [annotationId]);

				/* add the element to annotation */
				elementIndex = annotation.length
				annotation[elementIndex] = {
					"elementId": element.attr('id'),
					"formValues": element.data('textAnnotate.formValues')
				};
			});
			

			//call listener
			options.onAddAnnotation.call(undefined, annotation, global_annotations);
			
			return annotation;
		}
	
		/**
		 * This function prepares an element for annotating
		 * @param object currentElement A DOM node
		 * @credits based on JQuery Plugin by Simon Chong (http://www.opensource.csimon.info/JQueryTextHighlighter/index.php)
		 **/
		function _initElements(currentElement) {
			var i, wordCounter, newImage, textnode, newElement, wrapperElement, words;

			/* put all words within childnodes of currentElement in <span>-tags (assumption: words are separated by whitespaces) */
			for ( i = 0; i < currentElement.childNodes.length; i++) {
				var currentChildnode = currentElement.childNodes[i];

				if (currentChildnode.nodeType === 3) {
					
					var currentChildnodeData = currentChildnode.data;
					
					//skip empty childnodes
					if (trim(currentChildnodeData).length < 1) {
						continue;
					}
				
					//get words within childnode
					words = currentChildnodeData.split(/\s/);
				
					//create span wrapping
					wrapperElement = $('<span class="'+options.wrapperClassName+'"></span>');

					//loop over words and put them in another span
					for (wordCounter = 0; wordCounter < words.length; wordCounter++) {
						textnode = document.createTextNode(words[wordCounter]);
						if (textnode.length==0) continue;
						
						newElement = $('<span class="'+options.elementClassName+'"></span>');
						newElement.attr('id', options.elementIdPrefix+totalWordCounter);
						newElement.data('textAnnotate.annotaterId', totalWordCounter);
					
						var whiteSpaceTextNode = document.createTextNode(" ");
						newElement.append(textnode);
						wrapperElement.append(newElement);
					
						//keep whitespaces in text
						if (wordCounter + 1 !== words.length || currentChildnodeData[currentChildnodeData.length - 1] === " ") {
							wrapperElement.append(whiteSpaceTextNode);
						}
						totalWordCounter++;
					}
					$(currentChildnode).replaceWith(wrapperElement);
				} else if (currentChildnode.nodeType ===1 && currentChildnode.nodeName==='IMG') {
					/* images */
					newImage = $(currentChildnode).clone();

					newElement = $('<span class="'+options.elementClassName+'"></span>');
					newElement.attr('id', options.elementIdPrefix+totalWordCounter);
					newElement.data('textAnnotate.annotaterId', totalWordCounter);
					newElement.append(newImage);					
					$(currentChildnode).replaceWith(newElement);
					
					totalWordCounter++;
				} else if (currentChildnode.nodeType === 1 && currentChildnode.childNodes	&& !/(script|style)/i.test(currentChildnode.tagName)) {
					/* other nodes with childnodes */
					_initElements(currentChildnode);
				}
			}
			return;
		}
	

	
		/* this function is called when annotating starts */
		function onAnnotateStart() {
			textAnnotaterOn = true;
		}

		/* this function is called when annotating ends */

		function onAnnotateEnd() {
			if (beingAnnotatedElementIdArray.length==0) return;

			var lastElement;

			if (textHiglighterOnAction==='add'){
				//fetch collection of elements currently being annotated
				var beingAnnotatedElements = $("."+options.beingAnnotatedClassName);

				if (beingAnnotatedElements.size()>0) {
					addAnnotation(beingAnnotatedElements);
				}
			}

			textAnnotaterOn = false;
			
			lastElement = jQuery('#'+options.elementIdPrefix+beingAnnotatedElementIdArray[beingAnnotatedElementIdArray.length-1]);
			setAnnotationFocus(lastElement);
			
			
			beingAnnotatedElementIdArray = [];

		}

		function addHighlight(element, autoFill){
			var firstAnnotatedElementId, numberOfBeingAnnotatedElements;

			/* init */
			element  = $(element);
			autoFill = autoFill==undefined ? true : false;

			var highlighterElements, startIndex, endIndex, i

			/* should we also highlight the elements between the first highlighted element and this one? */
			if (autoFill==true && beingAnnotatedElementIdArray.length>0){
				highlighterElements = $('.'+options.elementClassName);

				firstAnnotatedElementId = beingAnnotatedElementIdArray[0];

				if (element.data('textAnnotate.annotaterId')>firstAnnotatedElementId){
					startIndex = firstAnnotatedElementId;
					endIndex   = element.data('textAnnotate.annotaterId');
				}else{
					startIndex = element.data('textAnnotate.annotaterId');
					endIndex   = firstAnnotatedElementId;
				}

				for (i=startIndex; i<endIndex; i++){
					addHighlight(highlighterElements.get(i), false);
				}

				numberOfBeingAnnotatedElements = beingAnnotatedElementIdArray.length;
				for (i=0; i<numberOfBeingAnnotatedElements; i++){
					if (beingAnnotatedElementIdArray[i]<startIndex || beingAnnotatedElementIdArray[i]>endIndex){
						removeHighlight($('#'+options.elementIdPrefix+beingAnnotatedElementIdArray[i]));
						i--;
					}
				}
			}
			
			if (element.data('textAnnotate.beingAnnotated')!=true) {
				element.addClass(options.beingAnnotatedClassName );
				element.data('textAnnotate.beingAnnotated', true);
				beingAnnotatedElementIdArray.push(element.data('textAnnotate.annotaterId'));
				
			}
		}

		function removeHighlight(element){
			var newAnnotateLevel;
			var currentAnnotateLevel = parseInt(element.attr('annotateLevel'));
		
			element = $(element);
			element.removeClass(options.beingAnnotatedClassName );
		
			if (currentAnnotateLevel>0){
				newAnnotateLevel = currentAnnotateLevel>0 ? currentAnnotateLevel-1 : 0;
				element.attr('annotateLevel', newAnnotateLevel);
			}else{
				element.removeAttr('annotateLevel');
			}
			element.data('textAnnotate.beingAnnotated', false);
	
			beingAnnotatedElementIdArray.splice($.inArray(element.data('textAnnotate.annotaterId'), beingAnnotatedElementIdArray),1);
		}



		function removeElementAnnotation(element, annotationIndexToRemoveFrom) {
			element = $(element);

			var annotateLevel = parseInt(element.attr('annotateLevel'));
		
			/* remove element from annotation */
			removeElementFromAnnotation(element, annotationIndexToRemoveFrom);

			/* remove annotation focus class */
			element.removeClass(options.annotationFocusClassName);

			/* remove annotate	*/
			annotateLevel = annotateLevel-1;
			if (annotateLevel<0){
				element.removeAttr('annotateLevel');
				element.removeClass(options.annotatedClassName);
			}else{
				element.attr('annotateLevel', annotateLevel);
			}

			/* remove data */
			element.removeData('beingAnnotated');
			element.removeData('formValues');

		}

		function _getAnnotationElementIds(annotationId){
			var annotationElementIds = [], key, i=0;
			
			for (key in global_annotations[annotationId]){
				annotationElementIds[i] = global_annotations[annotationId][key].elementId;
				i++;
			}
			
			return annotationElementIds;
		}

		function removeElementFromAnnotation(element, annotationId){
			var annotationIndexInElement;
			var annotationElementIds = _getAnnotationElementIds(annotationId);
			var elementIdIndex    = $.inArray(element.attr('id'), annotationElementIds);

			/* remove element id from annotation */
			if (elementIdIndex!=-1) {
				annotationElementIds.splice(elementIdIndex, 1);
			}

			/* remove annotation index from element */
			annotationIndexInElement = $.inArray(annotationId, element.data('textAnnotate.annotationIndexes'));
			if (annotationIndexInElement!=-1){
				element.data('textAnnotate.annotationIndexes').splice(annotationIndexInElement, 1);
			}
		}
	
		function consolelog(msg){
			if (typeof console!='undefined'){
				console.log(msg);
			} else{
				$('#consolelog').prepend(msg);
			}
		}

		function _removeEmptyAnnotations() {
			$(global_annotations).each(function(annotationId, annotation){
				if (annotation.length===0) {
					delete global_annotations[annotationId];
				}
			});
		}
	
		function removeAllElementsFromAnnotation(annotationId) {
			var elementIds = _getAnnotationElementIds(annotationId)
			
			$(elementIds).each(function(index, value){
				removeElementAnnotation('#'+value, annotationId);
			});
		}
	
		function setAnnotationFocus(elementInAnnotation){
			var annotation, annotationsElementIds, elementAnnotationIndexes;
			
			/* user userDialog */
	
			//show the dialog and position it
			userDialog.show();
			userDialog.offset({
				"left":parseInt(elementInAnnotation.offset().left, 10),
				"top":(parseInt(elementInAnnotation.offset().top,10)+parseInt(elementInAnnotation.height(), 10))
			});

			loadElementForm(elementInAnnotation);
			
			userDialog.data('textAnnotate.element', elementInAnnotation);
		
			elementAnnotationIndexes           = elementInAnnotation.data('textAnnotate.annotationIndexes');
			
			indexesOfAnnotationsWithFocus = elementAnnotationIndexes;
			
			$(elementAnnotationIndexes).each(function(index, value){
				annotation = global_annotations[value]
				annotationsElementIds = _getAnnotationElementIds(value);
								
				$(annotationsElementIds).each(function(index, value){
					element = $('#'+value);
					$(element).addClass(options.annotationFocusClassName);
				});
			});
		}


		function unsetAnnotationFocus(){
			var annotationsElementIds;
			if (textAnnotaterOn===true || indexesOfAnnotationsWithFocus===undefined) {
				return;
			}
			
			userDialog.hide();
		
			$(indexesOfAnnotationsWithFocus).each(function(){
				var annotation = global_annotations[this];
				annotationsElementIds = _getAnnotationElementIds(this);
				$(annotationsElementIds).each(function(index, value){
					var element = $('#'+value);
					$(element).removeClass(options.annotationFocusClassName);
				});
			});
		
			indexesOfAnnotationsWithFocus = undefined;
		}
	
		function removeAnnotationWithFocus(){
			var i, j;

			/* remove annotation focus and dialog */
			$('.'+options.annotationFocusClassName).removeClass(options.annotationFocusClassName);
			userDialog.hide();
		
			/* remove annotates (and thus annotations) */
			$(indexesOfAnnotationsWithFocus).each(function(index, value){
				removeAnnotation(value);
			});
		}

		function removeAnnotation(annotationId){

			var annotationToRemove = $.extend(true, {}, global_annotations[annotationId]);

			removeAllElementsFromAnnotation(annotationId);
			
			delete global_annotations[annotationId];
						
			//call listener
			options.onRemoveAnnotation.call(undefined, annotationToRemove, global_annotations);
		}

		function saveAnnotationFormWithFocus(){
			var annotationElementIds, annotationId, index;
			var serializedForm = formElement.serializeArray();

			/* loop over all annotations having a focus */
			$(indexesOfAnnotationsWithFocus).each(function(index, annotationId){
				/* get all elements within annotation */
				annotationElementIds = _getAnnotationElementIds(annotationId);
				$(annotationElementIds).each(function(index, elementId){
					saveElementForm(annotationId, $('#'+elementId), serializedForm)
				});

				//call listener
				options.onSaveAnnotation.call(undefined, global_annotations[this], serializedForm);
			});
		}

		function saveElementForm(annotationId, element, serializedForm){
			var annotationElements, numberOfElements, i;
			element.data('textAnnotate.formValues', serializedForm);
			
			annotationElements = global_annotations[annotationId];
			
			numberOfElements = annotationElements.length;
			for (i=0; i<numberOfElements; i++){
				if (annotationElements[i].elementId==element.attr('id')){
					annotationElements[i].formValues = serializedForm;
				}
			}
		//[element.attr('id')]);
		}

		function loadElementForm(element){
			var key, data = {};
			var formValues = $(element).data('textAnnotate.formValues');
			
			/* reset the form */
			formElement.each(function(){
				this.reset();
			});

			/* set new values */
			if (formValues) {
				$(formValues).each(function(index, value){
					key   = this.name;
					value = this.value;


					if (data[key]==undefined){
						data[key] = [value];
					}else{
						$.merge(data[key], [value]);
					}
				});

				for (key in data){
					formElement.find('[name='+key+']').val(data[key]);
				}
			}
		}
		
		/**
		 * Removes whitespaces from the beginning and ending of a string
		 * @param string value
		 * @return string
		 **/		 		 		 		
		function trim(value) {
  		value = value.replace(/^\s+/,'');
  		value = value.replace(/\s+$/,'');
  		return value;
		}


		/* init */
		_init(element);
	}

	/* initialize */
	$.fn.textAnnotate = function(options){
		textAnnotate(this, options);
		return;
	}

})(jQuery);
