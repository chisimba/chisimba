/*!
 * jQuery UI Stars v3.0.1
 * http://plugins.jquery.com/project/Star_Rating_widget
 *
 * Copyright (c) 2010 Marek "Orkan" Zajac (orkans@gmail.com)
 * Dual licensed under the MIT and GPL licenses.
 * http://docs.jquery.com/License
 *
 * jQueryRev: 164 jQuery
 * jQueryDate:: 2010-05-01 #jQuery
 * jQueryBuild: 35 (2010-05-01)
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *
 */
(function(jQuery) {

jQuery.widget('ui.stars', {
	options: {
		inputType: 'radio', // [radio|select]
		split: 0, // decrease number of stars by splitting each star into pieces [2|3|4|...]
		disabled: false, // set to [true] to make the stars initially disabled
		cancelTitle: 'Cancel Rating',
		cancelValue: 0, // default value of Cancel btn.
		cancelShow: true,
		disableValue: true, // set to [false] to not disable the hidden input when Cancel btn is clicked, so the value will present in POST data.
		oneVoteOnly: false,
		showTitles: false,
		captionEl: null, // jQuery object - target for text captions 
		callback: null, // function(ui, type, value, event)

		/*
		 * CSS classes
		 */
		starWidth: 16, // width of the star image
		cancelClass: 'ui-stars-cancel',
		starClass: 'ui-stars-star',
		starOnClass: 'ui-stars-star-on',
		starHoverClass: 'ui-stars-star-hover',
		starDisabledClass: 'ui-stars-star-disabled',
		cancelHoverClass: 'ui-stars-cancel-hover',
		cancelDisabledClass: 'ui-stars-cancel-disabled'
	},
	
	_create: function() {
		var self = this, o = this.options, starId = 0;
		this.element.data('former.stars', this.element.html());

		o.isSelect = o.inputType == 'select';
		this.jQueryform = jQuery(this.element).closest('form');
		this.jQueryselec = o.isSelect ? jQuery('select', this.element)  : null;
		this.jQueryrboxs = o.isSelect ? jQuery('option', this.jQueryselec)   : jQuery(':radio', this.element);

		/*
		 * Map all inputs from jQueryrboxs array to Stars elements
		 */
		this.jQuerystars = this.jQueryrboxs.map(function(i)
		{
			var el = {
				value:      this.value,
				title:      (o.isSelect ? this.text : this.title) || this.value,
				isDefault:  (o.isSelect && this.defaultSelected) || this.defaultChecked
			};

			if(i==0) {
				o.split = typeof o.split != 'number' ? 0 : o.split;
				o.val2id = [];
				o.id2val = [];
				o.id2title = [];
				o.name = o.isSelect ? self.jQueryselec.get(0).name : this.name;
				o.disabled = o.disabled || (o.isSelect ? jQuery(self.jQueryselec).attr('disabled') : jQuery(this).attr('disabled'));
			}

			/*
			 * Consider it as a Cancel button?
			 */
			if(el.value == o.cancelValue) {
				o.cancelTitle = el.title;
				return null;
			}

			o.val2id[el.value] = starId;
			o.id2val[starId] = el.value;
			o.id2title[starId] = el.title;

			if(el.isDefault) {
				o.checked = starId;
				o.value = o.defaultValue = el.value;
				o.title = el.title;
			}

			var jQuerys = jQuery('<div/>').addClass(o.starClass);
			var jQuerya = jQuery('<a/>').attr('title', o.showTitles ? el.title : '').text(el.value);

			/*
			 * Prepare division settings
			 */
			if(o.split) {
				var oddeven = (starId % o.split);
				var stwidth = Math.floor(o.starWidth / o.split);
				jQuerys.width(stwidth);
				jQuerya.css('margin-left', '-' + (oddeven * stwidth) + 'px');
			}

			starId++;
			return jQuerys.append(jQuerya).get(0);
		});

		/*
		 * How many Stars?
		 */
		o.items = starId;

		/*
		 * Remove old content
		 */
		o.isSelect ? this.jQueryselec.remove() : this.jQueryrboxs.remove();

		/*
		 * Append Stars interface
		 */
		this.jQuerycancel = jQuery('<div/>').addClass(o.cancelClass).append( jQuery('<a/>').attr('title', o.showTitles ? o.cancelTitle : '').text(o.cancelValue) );
		o.cancelShow &= !o.disabled && !o.oneVoteOnly;
		o.cancelShow && this.element.append(this.jQuerycancel);
		this.element.append(this.jQuerystars);

		/*
		 * Initial selection
		 */
		if(o.checked === undefined) {
			o.checked = -1;
			o.value = o.defaultValue = o.cancelValue;
			o.title = '';
		}
		
		/*
		 * The only FORM element, that has been linked to the stars control. The value field is updated on each Star click event
		 */
		this.jQueryvalue = jQuery("<input type='hidden' name='"+o.name+"' value='"+o.value+"' />");
		this.element.append(this.jQueryvalue);


		/*
		 * Attach stars event handler
		 */
		this.jQuerystars.bind('click.stars', function(e) {
			if(!o.forceSelect && o.disabled) return false;

			var i = self.jQuerystars.index(this);
			o.checked = i;
			o.value = o.id2val[i];
			o.title = o.id2title[i];
			self.jQueryvalue.attr({disabled: o.disabled ? 'disabled' : '', value: o.value});

			fillTo(i, false);
			self._disableCancel();

			!o.forceSelect && self.callback(e, 'star');
		})
		.bind('mouseover.stars', function() {
			if(o.disabled) return false;
			var i = self.jQuerystars.index(this);
			fillTo(i, true);
		})
		.bind('mouseout.stars', function() {
			if(o.disabled) return false;
			fillTo(self.options.checked, false);
		});


		/*
		 * Attach cancel event handler
		 */
		this.jQuerycancel.bind('click.stars', function(e) {
			if(!o.forceSelect && (o.disabled || o.value == o.cancelValue)) return false;

			o.checked = -1;
			o.value = o.cancelValue;
			o.title = '';
			
			self.jQueryvalue.val(o.value);
			o.disableValue && self.jQueryvalue.attr({disabled: 'disabled'});

			fillNone();
			self._disableCancel();

			!o.forceSelect && self.callback(e, 'cancel');
		})
		.bind('mouseover.stars', function() {
			if(self._disableCancel()) return false;
			self.jQuerycancel.addClass(o.cancelHoverClass);
			fillNone();
			self._showCap(o.cancelTitle);
		})
		.bind('mouseout.stars', function() {
			if(self._disableCancel()) return false;
			self.jQuerycancel.removeClass(o.cancelHoverClass);
			self.jQuerystars.triggerHandler('mouseout.stars');
		});


		/*
		 * Attach onReset event handler to the parent FORM
		 */
		this.jQueryform.bind('reset.stars', function(){
			!o.disabled && self.select(o.defaultValue);
		});


		/*
		 * Clean up to avoid memory leaks in certain versions of IE 6
		 */
		jQuery(window).unload(function(){
			self.jQuerycancel.unbind('.stars');
			self.jQuerystars.unbind('.stars');
			self.jQueryform.unbind('.stars');
			self.jQueryselec = self.jQueryrboxs = self.jQuerystars = self.jQueryvalue = self.jQuerycancel = self.jQueryform = null;
		});


		/*
		 * Star selection helpers
		 */
		function fillTo(index, hover) {
			if(index != -1) {
				var addClass = hover ? o.starHoverClass : o.starOnClass;
				var remClass = hover ? o.starOnClass    : o.starHoverClass;
				self.jQuerystars.eq(index).prevAll('.' + o.starClass).andSelf().removeClass(remClass).addClass(addClass);
				self.jQuerystars.eq(index).nextAll('.' + o.starClass).removeClass(o.starHoverClass + ' ' + o.starOnClass);
				self._showCap(o.id2title[index]);
			}
			else fillNone();
		};
		function fillNone() {
			self.jQuerystars.removeClass(o.starOnClass + ' ' + o.starHoverClass);
			self._showCap('');
		};


		/*
		 * Finally, set up the Stars
		 */
		this.select(o.value);
		o.disabled && this.disable();

	},

	/*
	 * Private functions
	 */
	_disableCancel: function() {
		var o = this.options, disabled = o.disabled || o.oneVoteOnly || (o.value == o.cancelValue);
		if(disabled)  this.jQuerycancel.removeClass(o.cancelHoverClass).addClass(o.cancelDisabledClass);
		else          this.jQuerycancel.removeClass(o.cancelDisabledClass);
		this.jQuerycancel.css('opacity', disabled ? 0.5 : 1);
		return disabled;
	},
	_disableAll: function() {
		var o = this.options;
		this._disableCancel();
		if(o.disabled)  this.jQuerystars.filter('div').addClass(o.starDisabledClass);
		else            this.jQuerystars.filter('div').removeClass(o.starDisabledClass);
	},
	_showCap: function(s) {
		var o = this.options;
		if(o.captionEl) o.captionEl.text(s);
	},

	/*
	 * Public functions
	 */
	value: function() {
		return this.options.value;
	},
	select: function(val) {
		var o = this.options, e = (val == o.cancelValue) ? this.jQuerycancel : this.jQuerystars.eq(o.val2id[val]);
		o.forceSelect = true;
		e.triggerHandler('click.stars');
		o.forceSelect = false;
	},
	selectID: function(id) {
		var o = this.options, e = (id == -1) ? this.jQuerycancel : this.jQuerystars.eq(id);
		o.forceSelect = true;
		e.triggerHandler('click.stars');
		o.forceSelect = false;
	},
	enable: function() {
		this.options.disabled = false;
		this._disableAll();
	},
	disable: function() {
		this.options.disabled = true;
		this._disableAll();
	},
	destroy: function() {
		this.jQueryform.unbind('.stars');
		this.jQuerycancel.unbind('.stars').remove();
		this.jQuerystars.unbind('.stars').remove();
		this.jQueryvalue.remove();
		this.element.unbind('.stars').html(this.element.data('former.stars')).removeData('stars');
		return this;
	},
	callback: function(e, type) {
		var o = this.options;
		o.callback && o.callback(this, type, o.value, e);
		o.oneVoteOnly && !o.disabled && this.disable();
	}
});

jQuery.extend(jQuery.ui.stars, {
	version: '3.0.1'
});

})(jQuery);
