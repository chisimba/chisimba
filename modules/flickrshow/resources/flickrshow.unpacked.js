var flickrshowCache = new Array();
var flickrshow = Class.create();
flickrshow.prototype = {
	cs: {
		images: 0,
		loaded: 0,
		viewed: 0
	},
	fs: {
		buttons: 0,
		htmlLoad: 0,
		id: Math.floor(Math.random() * 999999),
		jsonLoad: 0,
		playing: 0
	},
	os: {
		debug: 0,
		flickr_mode: "-",
		flickr_user: "-",
		flickr_vars: "-",
		size: "_",
		speed: 3,
		target: "-",
		skindir: "",
		autostart: 0,
		url: ""
	},
	es: new Object(),
	is: new Object(),
	tp: new Object(),
	initialize: function (a, b) {
		this.os.target = (a) ? a: '';
		this.os.url = (b.url) ? b.url : "";
		this.os.debug = (b.debug) ? b.debug: 0;
		this.os.size = (b.size) ? b.size: "";
		this.os.speed = (b.speed) ? b.speed: 3;
		this.os.skindir = (b.skindir) ? b.skindir: "";
		this.os.autostart = (b.autostart) ? b.autostart: 0;
		this.os.flickr_user = (b.flickr_user) ? b.flickr_user: "-";
		if (b.flickr_user) {
			this.os.flickr_user = b.flickr_user
		}
		if (b.flickr_group) {
			this.os.flickr_mode = "group";
			this.os.flickr_vars = b.flickr_group
		}
		if (b.flickr_photoset) {
			this.os.flickr_mode = "photoset";
			this.os.flickr_vars = b.flickr_photoset
		}
		if (b.flickr_tags) {
			this.os.flickr_mode = "tags";
			this.os.flickr_vars = b.flickr_tags
		}
		this.start()
	},
	start: function () {
		if ((this.os.flickr_mode == "-") && (this.os.flickr_user == "-")) {
			this.stop(1)
		}
		var a = document.createElement("script");
		a.setAttribute("language", "javascript");
		a.setAttribute("type", "text/javascript");
		a.setAttribute("src", this.os.url + "/index.php?module=flickrshow&action=getphotoset&id=" + this.fs.id + "&photoset=" + this.os.flickr_vars + "&size=" + this.os.size);
		document.getElementsByTagName("head").item(0).appendChild(a);
		var b = document.createElement("link");
		b.setAttribute("media", "screen");
		b.setAttribute("rel", "stylesheet");
		b.setAttribute("type", "text/css");
		b.setAttribute("href", this.os.skindir + "screen.css");
		document.getElementsByTagName("head").item(0).appendChild(b);
		Event.observe(window, "load", function (e) {
			this.onLoadHtml();
			new PeriodicalExecuter(this.monLoadJson.bind(this), 0.001);
			new PeriodicalExecuter(this.monLoadPage.bind(this), 0.001)
		}.bindAsEventListener(this))
	},
	stop: function (e) {
		if (this.os.debug == 1) {
			switch (e) {
			case 1:
				alert("Flickrshow could not load - You have not specified a method/username with which to retrieve photos.");
				break;
			case 2:
				alert("Flickrshow could not load - The specified target element could not be found");
				break;
			case 3:
				alert("Flickrshow could not load - The Flickr photo feed could not be found or it contained no images.");
				break
			}
		}
		Event.unloadCache();
		return false
	},
	activate: function (i) {
		this.fs.buttons = (i == 0) ? 0 : 1;
		if (i == 0) {
			this.es.wrapper.addClassName("fsNoButtons");
			this.sl.setDisabled()
		} else {
			this.es.wrapper.removeClassName("fsNoButtons");
			this.sl.setEnabled()
		}
	},
	clickPlay: function (e) {
		if (this.fs.buttons != 1) {
			return
		}
		if (this.fs.playing == 0) {
			this.es.wrapper.addClassName("fsPlaying");
			this.fs.playing = 1;
			new PeriodicalExecuter(this.animPlay.bind(this), this.os.speed)
		} else {
			this.es.wrapper.removeClassName("fsPlaying");
			this.fs.playing = 0
		}
	},
	clickLeft: function (e) {
		if (this.fs.buttons != 1) {
			return
		}
		this.activate(0);
		if ((this.cs.viewed) > 0) {
			this.cs.viewed--;
			this.sl.rX = this.es.wrapper.getWidth() * (this.cs.viewed + 1);
			this.sl.sX = this.es.wrapper.getWidth() * (this.cs.viewed);
			new PeriodicalExecuter(this.animSlideLeft.bind(this), 0.05)
		} else {
			this.cs.viewed = this.cs.images - 1;
			this.sl.rX = this.es.wrapper.getWidth() * 0;
			this.sl.sX = this.es.wrapper.getWidth() * (this.cs.viewed);
			new PeriodicalExecuter(this.animSlideRight.bind(this), 0.05)
		}
	},
	clickRight: function (e) {
		if (this.fs.buttons != 1) {
			return
		}
		this.activate(0);
		if ((this.cs.viewed + 1) < this.cs.images) {
			this.cs.viewed++;
			this.sl.rX = this.es.wrapper.getWidth() * (this.cs.viewed - 1);
			this.sl.sX = this.es.wrapper.getWidth() * (this.cs.viewed);
			new PeriodicalExecuter(this.animSlideRight.bind(this), 0.05)
		} else {
			this.cs.viewed = 0;
			this.sl.rX = this.es.wrapper.getWidth() * (this.cs.images - 1);
			this.sl.sX = this.es.wrapper.getWidth() * (this.cs.viewed);
			new PeriodicalExecuter(this.animSlideLeft.bind(this), 0.05)
		}
	},
	monLoadJson: function (a) {
		if (flickrshowCache[this.fs.id]) {
			a.stop();
			this.fs.jsonLoad++;
			this.onLoadJson()
		}
	},
	monLoadPage: function (a) {
		if ((this.fs.htmlLoad == 1) && (this.fs.jsonLoad == 2)) {
			a.stop();
			this.onLoadPage()
		}
	},
	tmrLoadImgs: function (a) {
		var p = Math.round((this.cs.loaded / this.cs.images) * 10);
		this.es.wrapper.removeClassName("fsLoaded-0");
		this.es.wrapper.removeClassName("fsLoaded-1");
		this.es.wrapper.removeClassName("fsLoaded-2");
		this.es.wrapper.removeClassName("fsLoaded-3");
		this.es.wrapper.removeClassName("fsLoaded-4");
		this.es.wrapper.removeClassName("fsLoaded-5");
		this.es.wrapper.removeClassName("fsLoaded-6");
		this.es.wrapper.removeClassName("fsLoaded-7");
		this.es.wrapper.removeClassName("fsLoaded-8");
		this.es.wrapper.removeClassName("fsLoaded-9");
		this.es.wrapper.removeClassName("fsLoaded-10");
		this.es.wrapper.addClassName("fsLoaded-" + p);
		if (this.cs.loaded == this.cs.images) {
			a.stop();
			this.onLoadImgs()
		}
	},
	animPlay: function (a) {
		if (this.fs.playing) {
			this.clickRight()
		} else {
			a.stop()
		}
	},
	animSlideLeft: function (a) {
		if (this.sl.sX < this.sl.rX) {
			this.sl.rX = Math.round(this.sl.rX - this.getEase(this.sl.rX - this.sl.sX));
			this.es.imgs.style.left = "-" + this.sl.rX + "px"
		} else {
			this.animSlideTidy(a)
		}
	},
	animSlideRight: function (a) {
		if (this.sl.rX < this.sl.sX) {
			this.sl.rX = Math.round(this.sl.rX + this.getEase(this.sl.sX - this.sl.rX));
			this.es.imgs.style.left = "-" + this.sl.rX + "px"
		} else {
			this.animSlideTidy(a)
		}
	},
	animSlideTidy: function (a) {
		a.stop();
		this.sl.setValue(this.cs.viewed);
		this.activate(1)
	},
	onLoadHtml: function () {
		if (!$(this.os.target)) {
			return this.stop(2)
		}
		$(this.os.target).style.position = "relative";
		$(this.os.target).descendants().invoke("remove");
		new Insertion.Top($(this.os.target), "<div class=\"fs fsNoButtons\" id=\"fs" + this.fs.id + "\" style=\"height:" + $(this.os.target).getHeight() + "px;left:0;overflow:hidden;position:absolute;top:0;width:100%;\"></div>");
		this.es.wrapper = $("fs" + this.fs.id);
		new Insertion.Top(this.es.wrapper, "<ol class=\"fsButtons\" id=\"fs" + this.fs.id + "Buttons\" style=\"height:32px;right:0;list-style:none;margin:0;padding:0;position:absolute;text-indent:0;bottom:8px;width:100%;\"><li class=\"fsButtonA\" id=\"fs" + this.fs.id + "ButtonA\" style=\"height:32px;left:0;position:absolute;top:0;width:28px;\">Play/Pause</li><li class=\"fsButtonB\" id=\"fs" + this.fs.id + "ButtonB\" style=\"height:32px;right:28px;position:absolute;top:0;width:24px;\">Previous</li><li class=\"fsButtonC\" id=\"fs" + this.fs.id + "ButtonC\" style=\"height:32px;right:0;position:absolute;top:0;width:28px;\">Next</li></ol>");
		this.es.buttons = $("fs" + this.fs.id + "Buttons");
		$("fs" + this.fs.id + "ButtonA").observe("click", this.clickPlay.bindAsEventListener(this));
		$("fs" + this.fs.id + "ButtonB").observe("click", this.clickLeft.bindAsEventListener(this));
		$("fs" + this.fs.id + "ButtonC").observe("click", this.clickRight.bindAsEventListener(this));
		new Insertion.Top(this.es.wrapper, "<div class=\"fsSlider\" id=\"fs" + this.fs.id + "Slider\" style=\"height:8px;left:0;position:absolute;bottom:0;width:100%;\"><div class=\"fsSliderT\" id=\"fs" + this.fs.id + "SliderT\" style=\"height:8px;left:0;position:absolute;bottom:0;width:100%;\"></div><div class=\"fsSliderH\" id=\"fs" + this.fs.id + "SliderH\" style=\"height:8px;left:0;position:absolute;bottom:0;\"></div></div>");
		this.es.slider = $("fs" + this.fs.id + "Slider");
		this.es.sliderT = $("fs" + this.fs.id + "SliderT");
		this.es.sliderH = $("fs" + this.fs.id + "SliderH");
		new Insertion.Top(this.es.wrapper, "<ol class=\"fsImages\" id=\"fs" + this.fs.id + "Images\" style=\"height:" + ($(this.os.target).getHeight() ) + "px;left:0;list-style:none;margin:0;padding:0;position:absolute;text-indent:0;top:0;width:100%;\"></ol>");
		this.es.imgs = $("fs" + this.fs.id + "Images");
		new Insertion.Bottom(this.es.wrapper, "<div class=\"fsLoading\" id=\"fs" + this.fs.id + "Loading\" style=\"height:" + $(this.os.target).getHeight() + "px;left:0;overflow:hidden;position:absolute;top:0;width:100%;\"></div>");
		this.es.loading = $("fs" + this.fs.id + "Loading");
		this.fs.htmlLoad++;
		return 
	},
	onLoadJson: function () {
		var b = eval(flickrshowCache[this.fs.id]);
		if ((!b) || (!b.images) || (b.images.length < 1)) {
			return this.stop(3)
		}
		this.tp.images = "";
		b.images.each(function (a) {
			a.title = a.title.unescapeHTML();
			this.tp.images += "<li id=\"fsImages-" + a.id + "\" style=\"float:left;height:" + this.es.imgs.getHeight() + "px;position:relative;width:" + this.es.imgs.getWidth() + "px;\"><h2 style=\"color:inherit;font-size:14px;left:24px;line-height:32px;margin:0;position:absolute;right:56px;text-align:center;top:" + this.es.imgs.getHeight() + "px;\">" + a.title + "</h2><a href=\"" + a.url + "\" style=\"height:" + this.es.imgs.getHeight() + "px;left:0;overflow:hidden;position:absolute;top:0;width:100%;\"><img src=\"" + a.src + "\" alt=\"" + a.title + "\" style=\"border:none;left:50%;position:absolute;top:50%;\" /></a></li>"
		}.bind(this));
		this.fs.jsonLoad++;
		return 
	},
	onLoadPage: function () {
		var b = $(this.os.target);
		new Insertion.Top(this.es.imgs, this.tp.images);
		this.cs.images = this.es.imgs.immediateDescendants().size();
		this.es.imgs.style.width = (this.es.wrapper.getWidth() * this.cs.images) + "px";
		this.es.sliderH.style.width = (this.es.wrapper.getWidth() / this.cs.images) + "px";
		this.sl = new Control.Slider(this.es.sliderH, this.es.sliderT, {
			range: $R(0, (this.cs.images - 1)),
			onSlide: this.slideManual.bind(this),
			onChange: this.slideFull.bind(this)
		});
		this.sl.oX = 0;
		this.sl.setDisabled();
		$$("#fs" + this.fs.id + " ol.fsImages li a img").each(function (a) {
			a.observe("load", function (e) {
				this.cs.loaded++
			}.bindAsEventListener(this))
		}.bind(this));
		new PeriodicalExecuter(this.tmrLoadImgs.bind(this), 0.25);
		return
	},
	onLoadImgs: function () {
		this.es.wrapper.removeClassName("fsLoaded-10");
		this.es.loading.remove();
		$$("#fs" + this.fs.id + " ol.fsImages li a img").each(function (a) {
			if (a.getWidth() > a.getHeight()) {
				var w = Math.round(a.up("a").getWidth());
				var h = Math.round(a.getHeight() * (a.up("a").getWidth() / a.getWidth()))
			} else {
				var h = Math.round(a.up("a").getHeight());
				var w = Math.round(a.getWidth() * (a.up("a").getHeight() / a.getHeight()))
			}
			a.style.height = h + "px";
			a.style.width = w + "px";
			a.style.marginLeft = "-" + (w / 2) + "px";
			a.style.marginTop = "-" + (h / 2) + "px"
		}.bind(this));
		this.activate(1)
		if (this.os.autostart == 1) {
			this.clickPlay();
		}
	},
	slideManual: function (x) {
		this.es.imgs.style.left = "-" + (this.es.wrapper.getWidth() * x) + "px";
		this.cs.viewed = Math.round(x)
	},
	slideFull: function (x) {
		if (x == Math.round(x)) {
			return
		}
		this.cs.viewed = Math.round(x);
		this.activate(0);
		this.sl.rX = x * this.es.wrapper.getWidth();
		this.sl.sX = Math.round(x) * this.es.wrapper.getWidth();
		if (this.sl.sX < this.sl.rX) {
			new PeriodicalExecuter(this.animSlideLeft.bind(this), 0.05)
		} else {
			new PeriodicalExecuter(this.animSlideRight.bind(this), 0.05)
		}
	},
	getEase: function (x) {
		x = x / 2;
		if (x < 1) {
			return 1
		}
		return x
	}
}