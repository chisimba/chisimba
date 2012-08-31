	/*
	* jGrow 0.2
	* 08.02.2008
	* 0.2 release: 04.03.2008
	*/
	
	(function($) {

		jQuery.fn.jGrow = function(options) {

			var opts = jQuery.extend({}, jQuery.fn.jGrow.defaults, options);

			return this.each(function() {

				jQuery(this).css({ overflow: "hidden" }).bind("keypress", function() {

					$this = jQuery(this);

					var o = jQuery.meta ? jQuery.extend({}, opts, $this.data()) : opts;

					if(o.rows == 0 && (this.scrollHeight > this.clientHeight)) {
						
						this.rows += 1;
						
					} else if((this.rows <= o.rows) && (this.scrollHeight > this.clientHeight)) {

						this.rows += 1;

					} else if(o.rows != 0 && this.rows > o.rows) {

						$this.css({ overflow: "auto" });

					}

					$this.html();

				});

			});

		}

		jQuery.fn.jGrow.defaults = { rows: 0 };

	})(jQuery);