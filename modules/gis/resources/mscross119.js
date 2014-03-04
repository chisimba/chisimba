// 20050927 - Copyright (C) 2005-2006 Simone Manca <simone.manca@gmail.com>
// http://datacrossing.crs4.it/en_Documentation_mscross.html
// v1.1.9 20070218
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// Old code
//Object.prototype.objRef = null;

var pixel_img = new Image(); pixel_img.src = '/img/pixel.gif';
var browser = new Browser();
var dragObj = new Object();
dragObj.zIndex = 0;



// msMap class prototype
function msMap(DivTag, ControlType, p_protocol)
{
  // Private vars
  var i        = this;
  var _tagMain = DivTag;
  var _map_w   = parseInt( _tagMain.style.width );
  var _map_h   = parseInt( _tagMain.style.height );

  var _protocol = p_protocol; // 'mapservercgi', 'wms'
  if (_protocol == null) { _protocol = 'mapservercgi'; }

  // Hidden map border in in pixel. You can set map size highter respect
  // div size (visible map)
  var _map_w_bord = 0; var _map_h_bord = 0;

  //var _action  = 'pan';	// pan, zoom, none // xxx old
  var _control = ControlType;
  var _cgi     = '/cgi-bin/mapserv';
  var _mode    = 'map';
  var _layers  = '';
  var _map_file; var _args;
  var _attachedMsMap = null; var _referenceMap  = null;
  var _report = null;
  var _loading_counter = 0;
  var _read_cookie = false;

  var _ext_Xmin_orig; var _ext_Xmax_orig;
  var _ext_Ymin_orig; var _ext_Ymax_orig;
  var _ext_Xmin = null; var _ext_Xmax = null;
  var _ext_Ymin = null; var _ext_Ymax = null;
  
  var _zoombox_x_first; var _zoombox_y_first;
  var _zoombox_x_last;  var _zoombox_y_last;
  var _pixel_w; var _pixel_h;

  // Double buffering
  var _tagMap   = document.createElement('img');
  var _tagMap_B = document.createElement('img');
  this.getTagMap = function(){return _tagMap;}
  this.getTagEvent = function(){return _tagEvents;}

  var _tagZoombox   = document.createElement('div');
  var _tagReference = document.createElement('div');
  var _tagLoading   = document.createElement('img');
  var _tagOverlay   = document.createElement('div'); // Overlay Layer
  var _tagPoints    = document.createElement('div'); // Points Overlay container
  var _tagInfo      = document.createElement('div'); // Report win Overlay container
  var _tagEvents    = document.createElement('div');
//  var _Xclick; var _Yclick;

  var _toolbars = new Array(); // Array of toolbars
  this.getToolbar = function(p){return _toolbars[p];}
  var _iconLoading = '/img/button_loading.png';

  // WMS.GetMap protocol specific
  var _wms_imageformat = 'image/png';
  var _wms_projection = 'EPSG:4326';
  this.setWmsImageFormat = function(p){_wms_imageformat = p;}
  this.setWmsProjection = function(p){_wms_projection = p;}

  // Map events
/*
  var _mapEvents = new Array();
  this.addMapEvent = function(p_event, p_funct)
  {
    add_event(_tagEvents, p_event,
      function(event)
      {
        //if (_action == 'xxx coords')
        {p_funct(event);}
      }
    );
  }
*/

  // Disable text/images selection.
  if ( browser.isIE )
  {
    _tagMain.onselectstart = function(){return false;};
    _tagMain.ondrag = function(){return false;};
  } else
  { _tagMain.style.setProperty("-moz-user-select", "none", ""); }

  // Point Overlay array
  var _pointsOverlayArray = new Array();
  this.setReport = function(p)
  {
    if (_report != null)
    {
      //_tagInfo.removeChild(_tagInfo.childNodes[0]);
      i.setReportNull();
    }
    _report = p;
  }
  this.addPointOverlay = function(p_point, p_redraw)
  {
    p_point.setMap(i);
    _pointsOverlayArray.push(p_point);

    if ( (p_redraw == null) || (p_redraw == true) )
    { i.overlayPointsResort() }
  }
  this.getMainTag = function() { return _tagMain; }
  this.getInfoTag = function() { return _tagInfo; }
  this.setReportNull = function()
  {
    if (_tagInfo.childNodes[0]) {_tagInfo.removeChild(_tagInfo.childNodes[0]);}
    delete _report; _report = null;
  }

  i.show_loading_image = function(p)
  {
    if (p == true)
    {
      _loading_counter++;
      _tagLoading.style.display = '';
    }
    if (p == false)
    {
      _loading_counter--;  if (_loading_counter < 0) { _loading_counter = 0; }
      //xxx errore!!! da risolvere!!!    if (_loading_counter == 0)
      { _tagLoading.style.display = 'none'; }
    }
  }

  // Active Debug mode
  this.debug = function()
  {
    var db = document.createElement('a');
    db.oncontextmenu    = function(){return false;};
    setZindex(db, '110');
    db.style.position   = 'absolute';
    db.style.fontFamily = 'Verdana, Arial, Helvetica, sans-serif';
    db.style.fontSize   = '11px';
    db.style.left       = i.width()-72+'px';
    db.style.top        = i.height()-13+'px';
    db.style.fontWeight = 'normal';
    db.style.textDecoration = 'overline';
    db.appendChild(document.createTextNode("Debug INFO"));
    _tagMain.appendChild(db);
    add_event(db, 'click', function(){ prompt('Debug INFO',i.get_map_url()) } );
  }

  // Check if a box (x, y, width, height) is within a map
  this.isPointInMap = function( p_x, p_y, p_w, p_h )
  {
    if ( (p_x > (_ext_Xmax+i.wPixel2real(_map_w_bord))) ||
         ((p_x+p_w) < (_ext_Xmin-i.wPixel2real(_map_w_bord))) ) return false;
    if ( ((p_y-p_h) > (_ext_Ymax+i.hPixel2real(_map_h_bord))) ||
         ((p_y+p_h-p_h) < (_ext_Ymin-i.hPixel2real(_map_h_bord))) ) return false;
    return true;
  }

  // Imposta il bordo intorno alla mappa.
  this.setBorder = function(p)
  {
    _map_w_bord = p; _map_h_bord = p;

    // First image buffer (double buffer)
    _tagMap.style.width  = (i.width()+_map_w_bord+_map_w_bord)+'px';
    _tagMap.style.height = (i.height()+_map_h_bord+_map_h_bord)+'px';
    _tagMap.style.top    = (- _map_w_bord) +'px';
    _tagMap.style.left   = (- _map_h_bord) +'px';

    // Second image buffer (double buffer)
    _tagMap_B.style.width  = _tagMap.style.width;
    _tagMap_B.style.height = _tagMap.style.height;
    _tagMap_B.style.top    = _tagMap.style.top;
    _tagMap_B.style.left   = _tagMap.style.left;
  }
  this.getBorder = function() {return _map_w_bord;}

  this.addClickTool = function()
  { //xxx
    i.getToolbar(0).addMapTool('Select', i.setActionSelect, _iconSelectButton);
  }

  this.width      = function() { return _map_w; }
  this.height     = function() { return _map_h; }
//  this.action     = function() { return _action; }
  this.control    = function() { return _control; }
  this.setCgi     = function(path) { _cgi = path; }
  this.setMapFile = function(p_mapFile) { _map_file = 'map='+p_mapFile; }
  this.setMode    = function(p_mode) { _mode = p_mode; }
  this.setLayers  = function(p_layers) { _layers = p_layers }
  this.setArgs    = function(p_args) { _args = p_args; }
  this.attachMap  = function(myMap) { _attachedMsMap = myMap; }
  this.setReferenceMap = function(myMap) { _referenceMap = myMap; }
//  this.setClickCoords = function(p_x ,p_y) { _Xclick = i.xPixel2Real(p_x); _Yclick = i.yPixel2Real(p_y); }

  // Permette di disegnare un box (oggetto _tagReference) nella mappa, usato
  // internamente per impostare il reference di un'altra mappa.
  this.setReferenceBox = function(p_Xmin, p_Xmax, p_Ymin, p_Ymax)
  {
    //i.fullExtentNoRedraw();  // 20060316
    Xmin = i.xReal2pixel(p_Xmin); Ymin = i.yReal2pixel(p_Ymin);
    Xmax = i.xReal2pixel(p_Xmax); Ymax = i.yReal2pixel(p_Ymax);

    _tagReference.style.left    = Xmin +'px';
    _tagReference.style.top     = Ymax +'px';
    _tagReference.style.width   = Xmax - Xmin +'px';
    _tagReference.style.height  = Ymin - Ymax +'px';
    _tagReference.style.display = '';
  }

  // Converte una coordinata X/Y reale in pixel (rispetto al bordo
  // sinistro/superiore dell'immagine)
  this.xReal2pixel = function(p_x)
  { return Math.round( _map_w * (p_x - _ext_Xmin) / (_ext_Xmax - _ext_Xmin) ); }
  this.yReal2pixel = function(p_y)
  { return Math.round( _map_h * (_ext_Ymax - p_y) / (_ext_Ymax - _ext_Ymin) ); }
  this.wPixel2real = function(p_w) { return (p_w * _pixel_w); }
  this.hPixel2real = function(p_h) { return (p_h * _pixel_h); }
  this.wReal2pixel = function(p_w) { return (p_w / _pixel_w); }
  this.hReal2pixel = function(p_h) { return (p_h / _pixel_w); }
  this.xPixel2Real = function(p_x) { return i.wPixel2real(p_x)+_ext_Xmin; }
  this.yPixel2Real = function(p_y) { return i.hPixel2real(_map_h-p_y)+_ext_Ymin; }

  this.setExtent  = function(Xmin, Xmax, Ymin)
  {
    Xmin = parseFloat(Xmin); Xmax = parseFloat(Xmax); Ymin = parseFloat(Ymin);
    _ext_Xmin = Xmin; _ext_Xmax = Xmax; _ext_Ymin = Ymin;
    _ext_Ymax = ((_map_h/_map_w)*(_ext_Xmax-_ext_Xmin)) + _ext_Ymin;
  }

  this.setFullExtent = function(Xmin, Xmax, Ymin)
  {
    _ext_Xmin_orig = Xmin; _ext_Xmax_orig = Xmax; _ext_Ymin_orig = Ymin;
    _ext_Ymax_orig = ((_map_h/_map_w)*(_ext_Xmax_orig-_ext_Xmin_orig)) + _ext_Ymin_orig;
    if (_read_cookie == false)
    { i.fullExtentNoRedraw(); }	// 20060316 (IE bugfix)
  }

  this.setZoomboxFirst = function(x, y)
  { _zoombox_x_first = x; _zoombox_y_first = y; }

  this.setZoomboxWH = function(x, y)
  {
    _zoombox_x_last = x; _zoombox_y_last = y;
    _tagZoombox.style.left   = min(_zoombox_x_first, _zoombox_x_last) + 'px';
    _tagZoombox.style.top    = min(_zoombox_y_first, _zoombox_y_last) + 'px';
    _tagZoombox.style.width  = max(_zoombox_x_last,  _zoombox_x_first) -
                               min(_zoombox_x_last,  _zoombox_x_first) + 'px';
    _tagZoombox.style.height = max(_zoombox_y_last,  _zoombox_y_first) -
                               min(_zoombox_y_last,  _zoombox_y_first) + 'px';
    _tagZoombox.style.display = '';
  }

  this.zoomboxExtent = function()
  {
    _tagZoombox.style.display = 'none';

    var ll = min(_zoombox_x_last, _zoombox_x_first);
    var rr = max(_zoombox_x_last, _zoombox_x_first);
    var bb = max(_zoombox_y_last, _zoombox_y_first);
    var tt = min(_zoombox_y_last, _zoombox_y_first);

    _ext_Xmin += ll * _pixel_w;
    _ext_Xmax -= (_map_w - rr) * _pixel_w;
    _ext_Ymax -= tt * _pixel_h;
    _ext_Ymin += (_map_h - bb) * _pixel_h;

    i.redraw();
  }

  this.recalc_pixel_size = function()
  {
    _pixel_w  = (_ext_Xmax - _ext_Xmin) / _map_w;
    _pixel_h  = (_ext_Ymax - _ext_Ymin) / _map_h;
  }

  this.redraw = function(redrawAttached)
  {
    i.show_loading_image(true);

    //if ( _ext_Xmax == null ) { i.fullExtentNoRedraw(); }  // 20060316
    i.recalc_map_size();

    // Set second buffer map image (Double buffer)
    _tagMap_B.src = i.get_map_url();
    //prompt('', _tagMap_B.src);

    if ( (_attachedMsMap != null) && (redrawAttached != false) )
    {
      _attachedMsMap.attachMap(i);
      _attachedMsMap.setExtent(_ext_Xmin, _ext_Xmax, _ext_Ymin);
      _attachedMsMap.redraw(false);
    }

    if ( _referenceMap != null )	// Draw zoom box in the reference map
    {
      _referenceMap.setReferenceBox(_ext_Xmin, _ext_Xmax, _ext_Ymin, _ext_Ymax);
    }
  }

  this.fullExtentNoRedraw = function()
  {
    _ext_Xmin = _ext_Xmin_orig; _ext_Xmax = _ext_Xmax_orig;
    _ext_Ymin = _ext_Ymin_orig; _ext_Ymax = _ext_Ymax_orig;
  }

  this.fullExtent = function() { i.fullExtentNoRedraw(); i.redraw(); }

  this.setActionZoombox = function()
  { //_action = 'zoom';
     _tagMap.style.cursor = "crosshair"; }

  this.setActionPan = function()
  { //_action = 'pan';
    _tagMap.style.cursor = "move"; }

  this.setActionCoords = function()
  { //_action = 'coords';
    _tagMap.style.cursor = "crosshair"; }

  this.setActionNone = function()
  {
    for (j=0; j<_toolbars.length; j++){_toolbars[j].hide();}
//    _action = 'none';
    _tagMap.style.cursor = "";
  }

  this.setActionZoomIn = function()
  {
    if ( isNaN(_tagLoading.style.display) )
    { i.zoomPerc(1.40); i.redraw(); }
  }

  this.setActionZoomOut = function()
  {
    if ( isNaN(_tagLoading.style.display) )
    { i.zoomPerc(0.30); i.redraw(); }
  }

  this.zoomPerc = function(p_perc)
  {
    var wx     = _ext_Xmax - _ext_Xmin;
    var wx_new = wx * p_perc;
    var kx     = (wx_new - wx) / 2;
    var wy     = _ext_Ymax - _ext_Ymin;
    var wy_new = wy * p_perc;
    var ky     = (wy_new - wy) / 2;
    i.setExtent(_ext_Xmin + kx, _ext_Xmax - kx, _ext_Ymin + ky);
  }

  this.mapLoaded = function()
  {
    // Swap image buffer (double buffering)
    var tmp = _tagMap;
    _tagMap = _tagMap_B;
    _tagMap_B = tmp;

    // Disegna gli overlay puntuali
    _tagOverlay.style.left = '0'; _tagOverlay.style.top  = '0';
    for(var j=0; j<_pointsOverlayArray.length; j++)
    { _pointsOverlayArray[j].redraw(); }
    if ( _report != null ) { _report.redraw(); }

    _tagMap.style.cursor = _tagMap_B.style.cursor;
    _tagMap.style.left = (- _map_w_bord) +'px';
    _tagMap.style.top  = (- _map_h_bord) +'px';
    _tagMap_B.style.display = 'none';
    _tagMap.style.display = '';

    // Hide "loading" image when map is loaded
    i.show_loading_image(false);
  }

  this.get_map_url = function()
  {
    var my_url;

    if (_protocol == 'mapservercgi')
    {
      var size = 'mapsize=' + (_map_w+_map_w_bord+_map_w_bord) + '+'
                            + (_map_h+_map_h_bord+_map_h_bord);
      var ext  = 'mapext=' + (_ext_Xmin-i.wPixel2real(_map_w_bord)) + '+'
                           + (_ext_Ymin-i.hPixel2real(_map_h_bord)) + '+'
                           + (_ext_Xmax+i.wPixel2real(_map_w_bord)) + '+'
                           + (_ext_Ymax+i.hPixel2real(_map_h_bord)) ;

      my_url = _cgi + '?mode=' + _mode + '&' + _map_file + '&' +
               ext + '&' + size + '&layers=' + _layers;

      // Opera9 Bug Fix (onload event don't work if image is in cache)
      if (browser.isOP) {my_url = my_url + '&' + Math.random();}
    }

    if (_protocol == 'wms')
    {
      var size = 'width='   + (_map_w+_map_w_bord+_map_w_bord) +
                 '&height=' + (_map_h+_map_h_bord+_map_h_bord);
      var ext  = 'BBOX=' + (_ext_Xmin-i.wPixel2real(_map_w_bord)) + ','
                         + (_ext_Ymin-i.hPixel2real(_map_h_bord)) + ','
                         + (_ext_Xmax+i.wPixel2real(_map_w_bord)) + ','
                         + (_ext_Ymax+i.hPixel2real(_map_h_bord)) ;
      var imgtype = 'FORMAT=' + _wms_imageformat;
      var proj = 'SRS=' + _wms_projection;
      var lay = 'LAYERS=' + _layers.replace(/\ /g,",");

      my_url = _cgi + '?VERSION=1.1.1&REQUEST=GetMap&' + proj + '&' + lay +
                      '&STYLES=&' + ext + '&' + imgtype + '&' + size;
    }

    return my_url + '&' + _args;
  }

  this.setPan = function(x, y)
  {
    i.recalc_pixel_size();
    var x_real = x * _pixel_w; var y_real = y * _pixel_h;
    _ext_Xmin = _ext_Xmin - x_real; _ext_Xmax = _ext_Xmax - x_real;
    _ext_Ymin = _ext_Ymin + y_real; _ext_Ymax = _ext_Ymax + y_real;
    i.redraw();
  }

  this.recalc_map_size = function()
  {
    i.recalc_pixel_size();

    if ( _pixel_w > _pixel_h )
    { // Modify only Y (box width > height)
      var middle = ((_ext_Ymax - _ext_Ymin) / 2) + _ext_Ymin;
      var new_h = (_map_h / _map_w) * (_ext_Xmax - _ext_Xmin);
      _ext_Ymin = middle - (new_h / 2);
      _ext_Ymax = middle + (new_h / 2);
    } else
    { // Modify only X (box width < height)
      var middle = ((_ext_Xmax - _ext_Xmin) / 2) + _ext_Xmin;
      var new_w = (_map_w / _map_h) * (_ext_Ymax - _ext_Ymin);
      _ext_Xmin = middle - (new_w / 2);
      _ext_Xmax = middle + (new_w / 2);
    }

    i.recalc_pixel_size();
  }

  this.getCookieName = function()
  { return '_mscross_'+hex_md5(window.location.href+DivTag.id); }
  this.saveCookie = function()
  {
    var expdate = new Date();
    expdate.setTime(expdate.getTime() + (1000 * 60 * 60 * 24 * 365 * 10));
    var v = _ext_Xmin+' '+_ext_Xmax+' '+_ext_Ymin;
    setCookie(i.getCookieName(), v, expdate);
  }

  this.spatialPointQueryWMSurl = function(p_server, p_x, p_y, p_layers)
  {
    if (p_server == null) {p_server = _cgi;}
    var ext  = (_ext_Xmin-i.wPixel2real(_map_w_bord)) + ','
               + (_ext_Ymin-i.hPixel2real(_map_h_bord)) + ','
               + (_ext_Xmax+i.wPixel2real(_map_w_bord)) + ','
               + (_ext_Ymax+i.hPixel2real(_map_h_bord)) ;
    var c; if(p_server.indexOf('?')==-1){c='?';}else{c='&';}
    var url = p_server + c + 'SERVICE=WMS&VERSION=1.1.1&REQUEST=GetFeatureInfo' +
              '&INFO_FORMAT=text/plain' + //gml' +
              '&LAYERS=' + p_layers +
              '&QUERY_LAYERS=' + p_layers +
              '&x=' + p_x + '&y=' + p_y +
              '&bbox=' + ext +
              '&width=' +_map_w+ '&height=' +_map_h;
    return url;
  }

  // xxx INCOMPLETE !!!
  this.spatialPointQueryWMS = function(p_server, p_x, p_y, p_layers)
  {
    var url = i.spatialPointQueryWMSurl(p_server, p_x, p_y, p_layers);
    f = function(p_xml)
    {
      alert(p_xml);
    }
    i.getXML(url, f);
  }

  this.init = function()
  {
    // If exists, get cookie with saved extension
    var c = getCookie(i.getCookieName());
    if (c != null)
    {
      var cord = c.split(" ");
      i.setExtent(cord[0], cord[1], cord[2]);
      if (cord.length == 3) {_read_cookie = true;}
    }

    _tagMain.className = 'mscross';  // css
    _tagMain.oncontextmenu  = function(){return false;};
    _tagMain.style.width    = i.width()+'px';
    _tagMain.style.height   = i.height()+'px';
    _tagMain.style.overflow = 'hidden';
    _tagMain.style.position = 'relative';

    _tagEvents.oncontextmenu = function(){return false;};
    setZindex(_tagEvents, '0');
    _tagEvents.style.position = 'absolute';
    _tagEvents.left           = '0';
    _tagEvents.top            = '0';

    // First buffer (double buffer)
    _tagMap.objRef = i;
    _tagMap.oncontextmenu  = function(){return false;};
    _tagMap.onmousedown = function(){return false;};  // Disable drag'n drop
    add_event(_tagMap, 'load', i.mapLoaded );
    //i.tagMap.setAttribute('style', '-moz-user-select:none;');
    setZindex(_tagMap, '0');
    _tagMap.galleryImg = "no";
    _tagMap.style.width    = (i.width()+_map_w_bord+_map_w_bord)+'px';
    _tagMap.style.height   = (i.height()+_map_h_bord+_map_h_bord)+'px';
    _tagMap.style.border   = '0 none';
    _tagMap.style.margin   = '0'; _tagMap.style.padding  = '0';
    _tagMap.style.position = 'absolute';
    _tagMap.style.top      = (- _map_w_bord) +'px';
    _tagMap.style.left     = (- _map_h_bord) +'px';
    _tagMap.style.display  = 'none';

    // Second buffer (double buffer)
    _tagMap_B.objRef = _tagMap.objRef;
    _tagMap_B.oncontextmenu = _tagMap.oncontextmenu;
    _tagMap_B.onmousedown = _tagMap.onmousedown;
    add_event(_tagMap_B, 'load', i.mapLoaded );
    setZindex(_tagMap_B, '0');
    _tagMap_B.galleryImg = "no";
    _tagMap_B.style.width    = _tagMap.style.width;
    _tagMap_B.style.height   = _tagMap.style.height;
    _tagMap_B.style.border   = _tagMap.style.border;
    _tagMap_B.style.margin   = _tagMap.style.margin;
    _tagMap_B.style.padding  = _tagMap.style.padding;
    _tagMap_B.style.position = _tagMap.style.position;
    _tagMap_B.style.top      = _tagMap.style.top;
    _tagMap_B.style.left     = _tagMap.style.left;
    _tagMap_B.style.display  = 'none';

    _tagReference.className = 'mscross_reference_zoombox';  // css
    _tagReference.oncontextmenu    = function(){return false;};
    setZindex(_tagReference, '100');
    _tagReference.style.display    = 'none';
    _tagReference.style.position   = 'absolute';
    _tagReference.style.margin     = '0'; _tagReference.style.padding = '0';
    _tagReference.style.lineHeight = '0';
    _tagReference.style.border     = '1px solid #000000';
    _tagReference.style.background = '#a0a0a0';
    _tagReference.style.opacity    = '0.20';               // Gecko
    _tagReference.style.filter     = 'alpha(opacity=20)';  // Windows
    _tagReference.style.fontSize   = '1'; // 20061012 bugfix by Rodrigo

    _tagZoombox.oncontextmenu    = function(){return false;};
    setZindex(_tagZoombox, '100');
    _tagZoombox.style.position   = 'absolute';
    _tagZoombox.style.display    = 'none';
    _tagZoombox.style.border     = '1px dashed #000000';
    _tagZoombox.style.margin     = '0px'; _tagZoombox.style.padding = '0px';
    _tagZoombox.style.lineHeight = '0';
    _tagZoombox.style.background = '#606060';	         //'#f0f0f0';
    _tagZoombox.style.opacity    = '0.18';               // Gecko
    _tagZoombox.style.filter     = 'alpha(opacity=18)';  // Windows
    _tagZoombox.style.fontSize   = '1'; // 20061012 bugfix by Rodrigo

    // Overlay Layer
    _tagOverlay.oncontextmenu    = function(){return false;};
    setZindex(_tagOverlay, '30');
    _tagOverlay.style.position   = 'absolute';

    _tagPoints.oncontextmenu    = function(){return false;};
    setZindex(_tagPoints, '40');
    _tagPoints.style.position   = 'absolute';

    _tagInfo.oncontextmenu   = function(){return false;};
    setZindex(_tagInfo, '50');
    _tagInfo.style.position  = 'absolute';

    // "Loading" image tag
    _tagLoading.oncontextmenu    = function(){return false;};
    _tagLoading.onmousedown = function(){return false;};  // Disable drag'n drop
    setZindex(_tagLoading, '100');
    _tagLoading.style.position   = 'absolute';
    _tagLoading.style.display    = 'none';
    _tagLoading.style.border     = '0';
    _tagLoading.style.margin     = '0'; _tagLoading.style.padding    = '0';
    _tagLoading.style.lineHeight = '0';
    setAlphaPNG(_tagLoading, _iconLoading);
    _tagLoading.style.left = (_map_w - 130) / 2 + 'px';
    _tagLoading.style.top  = (_map_h - 122) / 2 + 'px';

    var myLink              = document.createElement('a');
    var ref                 = 'http://avoir.uwc.ac.za/';
    if (myLink.setAttribute)
    { myLink.setAttribute('href', ref); } else
    { myLink.href = ref }
    myLink.oncontextmenu    = function(){return false;};
    setZindex(myLink, '110');
    myLink.style.position   = 'absolute';
    myLink.style.fontFamily = 'Verdana, Arial, Helvetica, sans-serif';
    myLink.style.fontSize   = '11px';
    myLink.style.left       = '2px';
    myLink.style.top        = i.height()-13+'px';
    myLink.style.fontWeight = 'normal';
    myLink.style.textDecoration = 'overline';
   // myLink.appendChild(document.createTextNode("AVOIR"));

    // Double buffer
    _tagEvents.appendChild(_tagMap);
    _tagEvents.appendChild(_tagMap_B);

    _tagOverlay.appendChild(_tagPoints);
    _tagOverlay.appendChild(_tagInfo);

    _tagMain.appendChild(_tagEvents);
    _tagMain.appendChild(_tagOverlay);
    _tagMain.appendChild(_tagZoombox);
    _tagMain.appendChild(_tagReference);
    _tagMain.appendChild(myLink);
// ---> ToolBar ---------------------------------------------------------------
    if (ControlType != null)
    {
      _toolbars.push(new msToolbar(i, ControlType, true));
      _tagMain.appendChild(_toolbars[0].getTag());
    }
// <--- ToolBar ---------------------------------------------------------------
    _tagMain.appendChild(_tagLoading);

//    if (i.action() == 'zoom') {_tagMap.style.cursor = "crosshair";}
//    if (i.action() == 'pan') {_tagMap.style.cursor = "move";}
  }

  this.getClick_X = function(p_event)
  {
    var my_x;
    if (browser.isNS)
    {
      my_x = p_event.clientX + window.scrollX;
    } else
    {
      my_x = window.event.clientX + document.documentElement.scrollLeft
        + document.body.scrollLeft;
    }
    return my_x - DL_GetElementLeft(i.getTagMap()) - _map_w_bord;
  }
  this.getClick_Y = function(p_event)
  {
    var my_y;
    if (browser.isNS)
    {
      my_y = p_event.clientY + window.scrollY;
    } else
    {
      my_y = window.event.clientY + document.documentElement.scrollTop
        + document.body.scrollTop;
    }
    return my_y - DL_GetElementTop(i.getTagMap()) - _map_w_bord;
  }


  this.zoomStart = function(event)
  {
    var el;
    dragObj.elNode = _tagMap;
    var x = i.getClick_X(event);
    var y = i.getClick_Y(event);
    if ( isNaN(_tagLoading.style.display) )
    {
      if (browser.isNS)
      {
        document.addEventListener("mousemove", i.zoomGo,   true);
        document.addEventListener("mouseup",   i.zoomStop, true);
        event.preventDefault();
      } else
      {
        document.attachEvent("onmousemove", i.zoomGo);
        document.attachEvent("onmouseup",   i.zoomStop);
        window.event.cancelBubble = true;
        window.event.returnValue = false;
      }
      i.setZoomboxFirst(x, y);
    }
  }

  this.zoomGo = function(event)
  {
    var x = i.getClick_X(event);
    var y = i.getClick_Y(event);

    i.setZoomboxWH(x, y);

    if (browser.isNS)
    {event.preventDefault();} else
    {
      window.event.cancelBubble = true;
      window.event.returnValue = false;
    }
  }

  this.zoomStop = function(event)
  {
    // Stop capturing mousemove and mouseup events.
    var flag = true; if (browser.isOP){flag=false;}
    del_event(document, "mousemove", i.zoomGo, flag);
    del_event(document, "mouseup", i.zoomStop, flag);
    i.zoomboxExtent();
  }

  this.dragStart = function(event)
  {
    var el;
    dragObj.elNode = _tagMap;
    var x = i.getClick_X(event) + DL_GetElementLeft(i.getTagMap());
    var y = i.getClick_Y(event) + DL_GetElementTop(i.getTagMap());

    // Save starting positions of cursor and element.
    dragObj.cursorStartX = x; dragObj.cursorStartY = y;
    dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
    dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);
    if (isNaN(dragObj.elStartLeft)) dragObj.elStartLeft = 0;
    if (isNaN(dragObj.elStartTop))  dragObj.elStartTop  = 0;

    // Update element's z-index.
    //dragObj.elNode.style.zIndex = ++dragObj.zIndex;       // Serve???
    // Capture mousemove and mouseup events on the page.
    // xxxxxxxxxxxxxxxx yyyyyyyyyyyyyyyyyyyyyyyy
    if ( isNaN(_tagLoading.style.display) )
    {
      if (browser.isNS)
      {
        document.addEventListener("mousemove", i.dragGo,   true);
        document.addEventListener("mouseup",   i.dragStop, true);
        event.preventDefault();
      } else
      {
        document.attachEvent("onmousemove", i.dragGo);
        document.attachEvent("onmouseup",   i.dragStop);
        window.event.cancelBubble = true;
        window.event.returnValue = false;
      }
    }
  }

  this.dragGo = function(event)
  {
    var xx = i.getClick_X(event) + DL_GetElementLeft(i.getTagMap());
    var yy = i.getClick_Y(event) + DL_GetElementTop(i.getTagMap());

    // Move map by the same amount the cursor has moved.
    dragObj.elNode.style.left = (dragObj.elStartLeft + xx - dragObj.cursorStartX) + "px";
    dragObj.elNode.style.top  = (dragObj.elStartTop  + yy - dragObj.cursorStartY) + "px";
    // Move Overlay layer
    _tagOverlay.style.left = parseInt(dragObj.elNode.style.left) +_map_w_bord +'px';
    _tagOverlay.style.top  = parseInt(dragObj.elNode.style.top)  +_map_h_bord +'px';

    if (browser.isNS)
    {event.preventDefault();} else
    {
      window.event.cancelBubble = true;
      window.event.returnValue = false;
    }
  }

  this.dragStop = function(event)
  {
    // Clear the drag element global.
    //dragObj.elNode = null;

    // Stop capturing mousemove and mouseup events.
    var flag = true; if (browser.isOP){flag=false;}
    del_event(document, "mousemove", i.dragGo, flag);
    del_event(document, "mouseup", i.dragStop, flag);

    var xx, yy;
    var x = i.getClick_X(event) + DL_GetElementLeft(i.getTagMap());
    var y = i.getClick_Y(event) + DL_GetElementTop(i.getTagMap());
    // Move drag element by the same amount the cursor has moved.
    xx = (dragObj.elStartLeft + x - dragObj.cursorStartX);
    yy = (dragObj.elStartTop  + y - dragObj.cursorStartY);
    // Add buffer size
    xx += _map_w_bord;
    yy += _map_h_bord;

    if ((xx != 0) || (yy != 0)) {i.setPan(xx, yy);}
  }

  // WFS protocol
  this.loadPointsOverlayWFS = function(p_serv, p_name, p_icon, p_infoSkin)
  {
    i.show_loading_image(true);  // Show "loading" image

    var url = p_serv+
              '?SERVICE=WFS&VERSION=1.0.0&REQUEST=getfeature&TYPENAME='+
              p_name;
    f = function(p_xml)
    {
      var mydata = parsePointsFromGML(p_xml);
      i.setOverlayPoints(mydata, p_icon, p_infoSkin);
    }
    getXML(url, f);

    // Hide "loading" image when map is loaded
    i.show_loading_image(false);
  }

  this.removeOverlayPoints = function()
  {
    i.setReportNull();

    // Clean _pointsOverlayArray
    _pointsOverlayArray.splice(0, _pointsOverlayArray.length);

    // Empty _tagPoints icons
    var kids = _tagPoints.childNodes;
    for(var j=kids.length-1; j>=0; j--) {_tagPoints.removeChild(kids[j]);}
  }

  this.overlayPointsResort = function()
  {
    // Z sorting
    _pointsOverlayArray.sort(function(a,b){if (a.getY()>b.getY()){return -1;}else{return 1;}});

    // Empty _tagPoints icons
    var kids = _tagPoints.childNodes;
    for(var j=kids.length-1; j>=0; j--) {_tagPoints.removeChild(kids[j]);}

    for(var j=0; j<_pointsOverlayArray.length; j++)
    {_tagPoints.appendChild(_pointsOverlayArray[j].getShd());}
    // Non sono nello stesso ciclo perche` Explorer li sovrappone in base
    // all'ordine dell'appendChild anziche` dello z-index. Cosi` le ombre
    // si sovrapponevano alle icone.
    for(var j=0; j<_pointsOverlayArray.length; j++)
    {_tagPoints.appendChild(_pointsOverlayArray[j].getImg());}

    // Redraw...
    for(var j=0; j<_pointsOverlayArray.length; j++)
    {_pointsOverlayArray[j].redraw();}
  }

  this.setOverlayPoints = function(p, p_icon, p_infoSkin)
  {
/*
    if (p_infoSkin == null)
    {
      // Create a default Info-window icon object...
      p_infoSkin = new msInfoSkin( '/img/angolo_a.png', '/img/angolo_b.png',
                                   '/img/angolo_c.png', '/img/angolo_d.png',
                                   '/img/report_t.png', '/img/report_d.png',
                                   '/img/report_l.png', '/img/report_r.png',
                                   '/img/report_x.png', '/img/close.png',
                                   '/img/report_arrow.png' );
    }
    if (p_icon == null) {p_icon = new msIcon(null, null);}
*/

    for(var j=0; j<p[0].length; j++)
    {
      var myPoint = new pointOverlay(p_icon, p_infoSkin, 'Info', p[0][j], p[1][j], p[2][j], p[3][j]);
      i.addPointOverlay(myPoint, false);
      //myPoint.setMap(i);
      //_pointsOverlayArray.push(myPoint);
    }

    i.overlayPointsResort()

/*
    // Z sorting
    _pointsOverlayArray.sort(function(a,b){if (a.getY()>b.getY()){return -1;}else{return 1;}});

    // Empty _tagPoints icons
    var kids = _tagPoints.childNodes;
    for(var j=kids.length-1; j>=0; j--) {_tagPoints.removeChild(kids[j]);}

    for(var j=0; j<_pointsOverlayArray.length; j++)
    {_tagPoints.appendChild(_pointsOverlayArray[j].getShd());}
    // Non sono nello stesso ciclo perche` Explorer li sovrappone in base
    // all'ordine dell'appendChild anziche` dello z-index. Cosi` le ombre
    // si sovrapponevano alle icone.
    for(var j=0; j<_pointsOverlayArray.length; j++)
    {_tagPoints.appendChild(_pointsOverlayArray[j].getImg());}

    // Redraw...
    for(var j=0; j<_pointsOverlayArray.length; j++)
    {_pointsOverlayArray[j].redraw();}
*/
  }

  i.init();
}



// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
// Determining Element Page Coordinates, Part 4:
// http://www.webreference.com/dhtml/diner/realpos4/9.html
function DL_GetElementLeft(eElement)
{
   if (!eElement && this)                    // if argument is invalid
   {                                         // (not specified, is null or is 0)
      eElement = this;                       // and function is a method
   }                                         // identify the element as the method owner
   var DL_bIE = document.all ? true : false; // initialize var to identify IE
   var nLeftPos = eElement.offsetLeft;       // initialize var to store calculations
   var eParElement = eElement.offsetParent;  // identify first offset parent element

   while (eParElement != null)
   {                                         // move up through element hierarchy
      if(DL_bIE)                             // if browser is IE, then...
      {
         if( (eParElement.tagName != "TABLE") && (eParElement.tagName != "BODY") )
         {                                   // if parent is not a table or the body, then...
            nLeftPos += eParElement.clientLeft; // append cell border width to calcs
         }
      }
      else                                   // if browser is Gecko, then...
      {
         if(eParElement.tagName == "TABLE")  // if parent is a table, then...
         {                                   // get its border as a number
            var nParBorder = parseInt(eParElement.border);
            if(isNaN(nParBorder))            // if no valid border attribute, then...
            {                                // check the table's frame attribute
               var nParFrame = eParElement.getAttribute('frame');
               if(nParFrame != null)         // if frame has ANY value, then...
               {
                  nLeftPos += 1;             // append one pixel to counter
               }
            }
            else if(nParBorder > 0)          // if a border width is specified, then...
            {
               nLeftPos += nParBorder;       // append the border width to counter
            }
         }
         // sm 20051010
         if(eParElement.tagName == "DIV")
         {
           var bord = parseInt(eParElement.style.border);
           if ( bord > 0 ) { nLeftPos += bord; }
         }
      }
      nLeftPos += eParElement.offsetLeft;    // append left offset of parent
      eParElement = eParElement.offsetParent; // and move up the element hierarchy
   }                                         // until no more offset parents exist
   return nLeftPos;                          // return the number calculated
}

function DL_GetElementTop(eElement)
{
   if (!eElement && this)                    // if argument is invalid
   {                                         // (not specified, is null or is 0)
      eElement = this;                       // and function is a method
   }                                         // identify the element as the method owner

   var DL_bIE = document.all ? true : false; // initialize var to identify IE

   var nTopPos = eElement.offsetTop;         // initialize var to store calculations
   var eParElement = eElement.offsetParent;  // identify first offset parent element

   while (eParElement != null)
   {                                         // move up through element hierarchy
      if(DL_bIE)                             // if browser is IE, then...
      {
         if( (eParElement.tagName != "TABLE") && (eParElement.tagName != "BODY") )
         {                                   // if parent a table cell, then...
            nTopPos += eParElement.clientTop; // append cell border width to calcs
         }
      }
      else                                   // if browser is Gecko, then...
      {
         if(eParElement.tagName == "TABLE")  // if parent is a table, then...
         {                                   // get its border as a number
            var nParBorder = parseInt(eParElement.border);
            if(isNaN(nParBorder))            // if no valid border attribute, then...
            {                                // check the table's frame attribute
               var nParFrame = eParElement.getAttribute('frame');
               if(nParFrame != null)         // if frame has ANY value, then...
               {
                  nTopPos += 1;              // append one pixel to counter
               }
            }
            else if(nParBorder > 0)          // if a border width is specified, then...
            {
               nTopPos += nParBorder;        // append the border width to counter
            }
         }
         // sm 20051010
         if(eParElement.tagName == "DIV")
         {
           var bord = parseInt(eParElement.style.border);
           if ( bord > 0 ) { nTopPos += bord; }
         }
      }
      nTopPos += eParElement.offsetTop;      // append top offset of parent
      eParElement = eParElement.offsetParent; // and move up the element hierarchy
   }                                         // until no more offset parents exist
   return nTopPos;                           // return the number calculated
}



// Cookie functions
// from  http://www.webreference.com/javascript/961125/part01.html
function getCookie(p_name)
{
  var dcookie = document.cookie; 
  var cname = p_name + "=";
  var clen = dcookie.length;
  var cbegin = 0;
  while (cbegin < clen)
  {
    var vbegin = cbegin + cname.length;
    if (dcookie.substring(cbegin, vbegin) == cname)
    { 
      var vend = dcookie.indexOf(";", vbegin);
      if (vend == -1) vend = clen;
      return unescape(dcookie.substring(vbegin, vend));
    }
    cbegin = dcookie.indexOf(" ", cbegin) + 1;
    if (cbegin == 0) break;
  }
  return null;
}

function delCookie(p_name)
{
  var expireNow = new Date();
  document.cookie = p_name + "=" +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT" +  "; path=/";
}

function setCookie(p_name, p_value, p_expires)
{
  if (!p_expires) p_expires = new Date();
  document.cookie = p_name + "=" + escape(p_value) +
    "; expires=" + p_expires.toGMTString() +  "; path=/";
}



/* A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info. */
var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase      */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode    */
function str_md5(s) {return binl2str(core_md5(str2binl(s), s.length * chrsz));}
function hex_md5(s) {return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}
function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}
function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}
function bit_rol(num, cnt) {return (num << cnt) | (num >>> (32 - cnt));}
function core_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);
}
function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}



// Determine browser and version
function Browser()
{
  var ua, s, i;

  this.isIE    = false;
  this.isNS    = false;
  this.isOP    = false;
  this.name    = navigator.appName;
  this.version = null;

  ua = navigator.userAgent;
  //alert(navigator.vendor);

  // Firefox:
  // Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6

  // Explorer:
  // Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)

  // Opera:
  // Mozilla/4.0 (compatibile; MSIE 6.0; Windows NT 5.1; en) Opera 8.50

  if ((navigator.userAgent).indexOf("Opera")!=-1)
  {
    this.isOP = true;
  } else
  if (navigator.appName=="Netscape")
  {
    this.isNS = true;
    //s = "Netscape6/";
    //this.version = parseFloat(ua.substr(i + s.length));
  } else
  if ( (navigator.appName).indexOf("Microsoft") != -1 )
  {
    this.isIE = true;
    //s = "MSIE";
    //this.version = parseFloat(ua.substr(i + s.length));
  }

  return;
}

function min(a, b) { if ( a < b ) { return a; } else { return b; } }
function max(a, b) { if ( a > b ) { return a; } else { return b; } }

// http://www.quirksmode.org/js/events_advanced.html
function add_event(obj, event_id, func)
{
  if (obj.addEventListener)
  {
    obj.addEventListener(event_id, func, false)
  } else if(obj.attachEvent)
  {
    event_id = 'on'+event_id;
    obj.attachEvent(event_id, func)
  } else
  { obj[event_id] = func; }
}
function del_event(obj, event_id, funct, flag)
{
  if (obj.removeEventListener)
  {
    obj.removeEventListener(event_id, funct, flag);
  } else if(obj.detachEvent)
  {
    obj.detachEvent(event_id, funct);
    obj.detachEvent('on'+event_id, funct);
  }
}

// Non e` molto elegante come soluzione... ma sembra funzionare...
function ChiamaEvento(e)
{
  var i;
  if (e.srcElement) {i = e.srcElement.objRef}
  if (e.target)     {i = e.target.objRef}
  i.dragStart(e);
}

function setPos(p_obj, p_x, p_y)
{
  p_obj.style.left    = p_x+'px';
  p_obj.style.top     = p_y+'px';
  p_obj.style.display = '';
}





// pointOverlay class prototype
function pointOverlay( p_icon, p_infoSkin, p_title, p_x, p_y, p_item_name, p_item_value )
{
  var _msMap = null;
  var _img   = null;	//document.createElement('img');
  var _shd   = null;	//document.createElement('img');
  var _x     = parseFloat(p_x);	// Real coord X
  var _y     = parseFloat(p_y);	// Real coord Y
  var _title = p_title;
  var _icon  = p_icon;
  var _infoSkin = p_infoSkin;
  var _item_name = p_item_name; var _item_value = p_item_value;
  var _offsetX = 0;  var _offsetY = 0;

  if (_title == null) { _title = 'Info'; }
  if (_infoSkin == null)
  {
    // Create a default Info-window icon object...
    _infoSkin = new msInfoSkin( '/img/angolo_a.png', '/img/angolo_b.png',
                                '/img/angolo_c.png', '/img/angolo_d.png',
                                '/img/report_t.png', '/img/report_d.png',
                                '/img/report_l.png', '/img/report_r.png',
                                '/img/report_x.png', '/img/close.png',
                                '/img/report_arrow.png' );
  }
  if (_icon == null) {_icon = new msIcon(null, null);}


  // Functions...
  this.setMap = function(m) { _msMap = m; }
  this.getMap = function()  { return _msMap; }
  this.getImg = function()  { return _img; }
  this.getShd = function()  { return _shd; }
  this.getX   = function()  { return _x; }
  this.getY   = function()  { return _y; }
  this.getHtmlAttributes = function()
  {
    var ret = "<table>";
    for (var j=0; j<_item_name.length; j++)
    {
      // css
      ret += "<tr><td class=\"mscross_report_attr_name\">"+ _item_name[j] +
             "</td><td class=\"mscross_report_attr_value\" "+
             "style='padding-left: 8px;'>"+ _item_value[j] +"</td></tr>";
    }
    ret += "</table>";
    return ret;
  }
  this.getInfoX = function() { return _msMap.xReal2pixel(_x); }
  this.getInfoY = function() { return Math.round(_msMap.yReal2pixel(_y) - (parseInt(_img.offsetHeight)/2) ); }
  this.getInfoSkin = function() { return _infoSkin; }
  this.getWidth  = function() { return parseInt(_img.style.width); }
  this.getHeight = function() { return parseInt(_img.style.height); }

  this.redraw = function()
  {
    // se e` visibile (coordinate del punto interne al box della mappa)...
    if ( _msMap.isPointInMap( _x - _msMap.wPixel2real(_offsetX),
                              _y + _msMap.hPixel2real(_offsetY),
                              _msMap.wPixel2real(_offsetX),
                              _msMap.hPixel2real(_offsetY) ) )
    {
      setPos(_img, _msMap.xReal2pixel(_x) - _offsetX,
                   _msMap.yReal2pixel(_y) - _offsetY);
      setPos(_shd, _msMap.xReal2pixel(_x) - _offsetX,
                   _msMap.yReal2pixel(_y) - _offsetY);
    } else
    { this.setVisible(false); }
  }

  this.setVisible = function(p_bool)
  {
    var str = null;
    if (p_bool) {str = '';} else {str = 'none';}
    _img.style.display = str;
    _shd.style.display = str;
  }

  this.showReport = function()
  {
    var pnt = new msReport(this, _title);
    _msMap.setReport(pnt);
  }

  // Initialization...
  _img = _icon.getImage(); _shd = _icon.getShadow();
  _offsetX = _icon.getShiftX() -1;
  _offsetY = _icon.getShiftY() -1;
//////////////////////////////////////////////////////////////////
// xxx Etichetta del punto, visualizzata al passaggio del mouse...
//  if (p_title != null) { _img.title = p_title; }
//////////////////////////////////////////////////////////////////
  _img.objRef = this;

  add_event(_img, 'click', function(event){pointOverlayEvent(event);});
}



// Imposta un'immagine PNG con trasparenza
// (risolve il limite di Internet Explorer)
function setAlphaPNG(p_imgTag, p_src)
{
  if (browser.isIE)
  {
    p_imgTag.src = pixel_img.src;
    p_imgTag.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader"+
                            "(src='"+p_src+"',sizingMethod='image')";
      //# "image": Keep the original size of the image.
      //# "scale": Stretch or compress the image to the container boundaries.
      //# "crop": Crop the image to the container dimensions.
/*
    // Trucco per ricavare le dimensioni dell'immagine...
    // Tanto l'immagine p_src dovrebbe essere caricata solo una volta.
    var tmp = new Image();
    tmp.onload=function()
    {
      p_imgTag.style.width  = tmp.width+'px';
      p_imgTag.style.height = tmp.height+'px';
    }
    tmp.src = p_src;
*/
  } else
  { p_imgTag.src = p_src; }
}



function setAlphaBackgroundPNG( p_Tag, p_src )
{
  if ( browser.isIE )
  {
    p_Tag.style.backgroundImage = 'none';
    p_Tag.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader"+
                         "(src='"+p_src+"',sizingMethod='scale')";
  } else
  { p_Tag.style.backgroundImage = "url('"+p_src+"')"; }
}



function setZindex(p_tag, p)
{
  if (p_tag.setAttribute)
  { p_tag.setAttribute('style', 'z-index:'+p+';'); } else
  { p_tag.style.zIndex = p; }
}



function msInfoSkin( p_corner_a, p_corner_b, p_corner_c, p_corner_d,
                     p_top, p_bottom, p_left, p_right,
                     p_fill, p_close, p_arrow)
{
  var _corner_a = new Image(); _corner_a.src = p_corner_a;
  var _corner_b = new Image(); _corner_b.src = p_corner_b;
  var _corner_c = new Image(); _corner_c.src = p_corner_c;
  var _corner_d = new Image(); _corner_d.src = p_corner_d;

  var _bord_top    = new Image(); _bord_top.src = p_top;
  var _bord_bottom = new Image(); _bord_bottom.src = p_bottom;
  var _bord_left   = new Image(); _bord_left.src = p_left;
  var _bord_right  = new Image(); _bord_right.src = p_right;

  var _fill  = new Image(); _fill.src = p_fill;
  var _close = new Image(); _close.src = p_close;
  var _arrow = new Image(); _arrow.src = p_arrow;

  this.getCornerA = function() { return _corner_a.src; }
  this.getCornerB = function() { return _corner_b.src; }
  this.getCornerC = function() { return _corner_c.src; }
  this.getCornerD = function() { return _corner_d.src; }
  this.getFill = function() { return _fill.src; }
  this.getLeft = function() { return _bord_left.src; }
  this.getRight = function() { return _bord_right.src; }
  this.getTop = function() { return _bord_top.src; }
  this.getBottom = function() { return _bord_bottom.src; }

this.getClose = function() { return _close.src; }
this.getArrow = function() { return _arrow.src; }
}



// msIcon class prototype
function msIcon( p_img, p_shd, p_offsetX, p_offsetY )
{
  var _img_name = p_img;
  var _shd_name = p_shd;
  var _offsetX = 1;	// Distanza del target dall'angolo
  var _offsetY = 1;	// alto-sinistra.

  if ( p_offsetX != null ) { _offsetX = p_offsetX; }
  if ( p_offsetY != null ) { _offsetY = p_offsetY; }
  if ( _img_name == null )
  {
    _img_name = '/img/mm_20_red.png';
    _shd_name = '/img/mm_20_shadow.png';
    _offsetX  = 6; _offsetY = 19;
  }
  if (_img_name == '') {_img_name='/img/pixel.gif';}
  if (_shd_name == '') {_shd_name='/img/pixel.gif';}

  this.getShiftX = function() { return _offsetX; }
  this.getShiftY = function() { return _offsetY; }
  this.getImage = function()
  {
    var tmp = document.createElement('img');
    tmp.oncontextmenu  = function(){return false;};
    tmp.onmousedown = function(){return false;};  // Disable drag'n drop
    setZindex(tmp, '110');
    tmp.style.position = 'absolute';
    tmp.style.cursor   = 'pointer';
    setAlphaPNG(tmp, _img_name);
    return tmp;
  }
  this.getShadow = function()
  {
    var tmp = document.createElement('img');
    tmp.oncontextmenu  = function(){return false;};
    tmp.onmousedown = function(){return false;};
    setZindex(tmp, '109');
    tmp.style.position = 'absolute';
    setAlphaPNG(tmp, _shd_name);
    return tmp;
  }
}



// msReport class prototype
function msReport(p_pnt, p_title)
{
  var _pointOverlay = p_pnt;
  var d = document.createElement('div'); p_pnt.getMap().getInfoTag().appendChild(d);
  var _content = document.createElement('div');
  var _scrollX = 16;  // Gli offset devono essere impostati dinamicamente
  var _scrollY = 0;
  var _title = p_title;
  var j = this;
  var _infoSkin = p_pnt.getInfoSkin();

  // Set _content style
  _content.style.paddingTop = '6px';
  _content.style.fontSize = '80%';

  this.redraw = function()
  {
    var h = parseInt(d.offsetHeight);
    var os_x = _scrollX;
    var os_y = _scrollY +h;

    d.style.left = p_pnt.getInfoX() -os_x +'px';
    d.style.top  = p_pnt.getInfoY() -os_y +'px';
  }

  // Chiude la finestra
  this.close = function()
  {
    //d.removeChild( d.childNodes[0] );
    var taginfo = p_pnt.getMap().getInfoTag();
    taginfo.removeChild( taginfo.childNodes[0] );
    p_pnt.getMap().setReportNull();
    delete j;
  }

  // Imposta il contenuto
  this.setContent = function(p_html) {_content.innerHTML = p_html;}

  this.init = function()
  {
    // Main DIV container
    d.oncontextmenu  = function(){return false;};
    d.style.position = 'absolute';

    // External table (borders)
    var t_b  = document.createElement('table');
    t_b.cellSpacing = '0'; t_b.cellPadding = '0';
    var tb_b = document.createElement('tbody'); t_b.appendChild(tb_b);
    var tr_a = document.createElement('tr'); tb_b.appendChild(tr_a);
    var tr_w = document.createElement('tr'); tb_b.appendChild(tr_w);
    var tr_b = document.createElement('tr'); tb_b.appendChild(tr_b);
    var tr_c = document.createElement('tr'); tb_b.appendChild(tr_c);
    var tr_d = document.createElement('tr'); tb_b.appendChild(tr_d);

    var td_a1 = document.createElement('td'); tr_a.appendChild(td_a1);
    var td_a2 = document.createElement('td'); tr_a.appendChild(td_a2);
    var td_a3 = document.createElement('td'); tr_a.appendChild(td_a3);

    // Close button
    var td_w1 = document.createElement('td'); tr_w.appendChild(td_w1);
    var td_w2 = document.createElement('td'); tr_w.appendChild(td_w2);
    var td_w3 = document.createElement('td'); tr_w.appendChild(td_w3);

    var td_b1 = document.createElement('td'); tr_b.appendChild(td_b1);
    var td_b2 = document.createElement('td'); tr_b.appendChild(td_b2);
    var td_b3 = document.createElement('td'); tr_b.appendChild(td_b3);

    var td_c1 = document.createElement('td'); tr_c.appendChild(td_c1);
    var td_c2 = document.createElement('td'); tr_c.appendChild(td_c2);
    var td_c3 = document.createElement('td'); tr_c.appendChild(td_c3);

    var td_d1 = document.createElement('td'); tr_d.appendChild(td_d1);
    var td_d2 = document.createElement('td'); tr_d.appendChild(td_d2);
    var td_d3 = document.createElement('td'); tr_d.appendChild(td_d3);

    var ang_a = document.createElement('img'); setAlphaPNG(ang_a, _infoSkin.getCornerA());
    ang_a.onmousedown = function(){return false;};
    var ang_b = document.createElement('img'); setAlphaPNG(ang_b, _infoSkin.getCornerB());
    ang_b.onmousedown = function(){return false;};
    var ang_c = document.createElement('img'); setAlphaPNG(ang_c, _infoSkin.getCornerC());
    ang_c.onmousedown = function(){return false;};
    var ang_d = document.createElement('img'); setAlphaPNG(ang_d, _infoSkin.getCornerD());
    ang_d.onmousedown = function(){return false;};
    var arrow = document.createElement('img'); setAlphaPNG(arrow, _infoSkin.getArrow());
    arrow.onmousedown = function(){return false;};

    td_a1.appendChild(ang_a); td_a3.appendChild(ang_b);
    td_c1.appendChild(ang_d); td_c3.appendChild(ang_c);
    td_d2.appendChild(arrow);
    td_b2.appendChild(_content);

    setAlphaBackgroundPNG(td_b2, _infoSkin.getFill());
    setAlphaBackgroundPNG(td_b1, _infoSkin.getLeft());
    setAlphaBackgroundPNG(td_b3, _infoSkin.getRight());
    setAlphaBackgroundPNG(td_a2, _infoSkin.getTop());
    setAlphaBackgroundPNG(td_c2, _infoSkin.getBottom());
    setAlphaBackgroundPNG(td_w1, _infoSkin.getLeft());
    setAlphaBackgroundPNG(td_w2, _infoSkin.getFill());
    setAlphaBackgroundPNG(td_w3, _infoSkin.getRight());

    var close = document.createElement('img'); setAlphaPNG(close, _infoSkin.getClose());
    add_event(close, 'click', function(){ j.close(); } );

    // Info window Title
    var tt = document.createElement('table'); tt.style.width = "100%";
    var tt_b = document.createElement('tbody'); tt.appendChild(tt_b);
    var tt_tr = document.createElement('tr'); tt_b.appendChild(tt_tr);
    var tt_td1 = document.createElement('td'); tt_tr.appendChild(tt_td1);
    var tt_td2 = document.createElement('td'); tt_tr.appendChild(tt_td2);
    var title = document.createTextNode(_title);

    tt_td1.className = 'mscross_report_title';  // css

    tt_td1.style.fontWeight = 'bold'; tt.cellSpacing = '0'; tt.cellPadding = '0';
    tt_td1.style.borderBottom = '1px dashed #d0d0d0';
    tt_td1.appendChild(title); tt_td2.appendChild(close);
    tt_td2.style.textAlign = 'right';
    td_w2.appendChild(tt);

    d.appendChild(t_b);

// BUG Firefox 1.0.7 ??? ////////
    if (browser.isNS)
    {
      d.style.display = 'table';
//      t.style.display = 'table-cell';
      //d.style.setProperty("-moz-box-align", "stretch", "");
      //d.style.setProperty("-moz-box-sizing", "padding-box", "");
      // -moz-box-align stretch
      // -moz-box-sizing
    }
/////////////////////////////////
  }

  this.init();
  this.setContent(p_pnt.getHtmlAttributes());
  this.redraw();
}



// pointOverlay Click Event
function pointOverlayEvent(e)
{
  var p;
  if (e.srcElement) { p = e.srcElement.objRef; }
  if (e.target)     { p = e.target.objRef; }
  p.showReport();
}



// .......................................

function getXML(p_url, p_funct)
{
  http_request = false;
  if (window.XMLHttpRequest) // Mozilla, Safari,...
  {
    http_request = new XMLHttpRequest();
    if (http_request.overrideMimeType)
    { http_request.overrideMimeType('text/xml'); }
  } else if (window.ActiveXObject) // IE
  {
    try
    {
      http_request = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e)
    {
      try
      {
        http_request = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e) {}
    }
  }
  if (!http_request)
  {
    alert('Giving up :( Cannot create an XMLHTTP instance');
    return false;
  }
  http_request.onreadystatechange = function()
  {
    if (http_request.readyState == 4)
    {
      if (http_request.status == 200)
      {
        var xml = http_request.responseXML;
        p_funct(xml);
      } else
      { alert('There was a problem with the request.'); }
    }
  }
  http_request.open('GET', p_url, true);
  http_request.send(null);
}

function parsePointsFromGML(myxml)
{
  var _coords = null;
  var _name = null;
  var prefix = "";
  var featureMember_Name = "featureMember";
  //var msGeometry_Name    = "msGeometry";
  var Point_Name         = "Point";
  var _add               = 0; // Mozilla utilizza gli indici di "childNodes" + 1.
  var _molt              = 1; // Mozilla moltiplica per 2

  if (browser.isIE)   // IE
  {
    featureMember_Name = "gml:"+featureMember_Name;
    // msGeometry_Name    = "ms:"+msGeometry_Name;
    Point_Name         = "gml:"+Point_Name;
  } else
  if (window.XMLHttpRequest)  // Mozilla, Safari,...
  {
    _add  = 1;
    _molt = 2;
  } 

  var _data = new Array();
  _data[0] = new Array();	// X
  _data[1] = new Array();	// Y
  _data[2] = new Array();	// Name
  _data[3] = new Array();	// Value

  // For each point in GML file...
  var count = myxml.getElementsByTagName(featureMember_Name).length;
  for(var i=0; i<count; i++)
  {
    _coords =  myxml.getElementsByTagName(featureMember_Name)[i].
               childNodes[0+_add].childNodes[0+_add].
               childNodes[0+_add].
               childNodes[0+_add].childNodes[0].nodeValue;
    // Prima usavo questo, ma 'msGeometry_Name' varia a seconda della versione
    /* _coords = myxml.getElementsByTagName(featureMember_Name)[i].
                    getElementsByTagName(msGeometry_Name)[0].
                    childNodes[0+_add].
                    childNodes[0+_add].childNodes[0].nodeValue;  */
    var tmp = new Array(); tmp = _coords.split(',');
    var names = new Array(); var values = new Array();

    // Per ogni attributo alfanumerico...
    var size = (myxml.getElementsByTagName(featureMember_Name)[0].
               childNodes[0+_add].childNodes.length - _add) / _molt;

    for (var j=2; j<size; j++)
    {
      nam = myxml.getElementsByTagName(featureMember_Name)[i].
                  childNodes[0+_add].childNodes[(j * _molt) +_add].tagName;
      var nam = nam.split(":");

      val = myxml.getElementsByTagName(featureMember_Name)[i].
                 childNodes[0+_add].childNodes[(j * _molt) +_add].
                 childNodes[0].nodeValue;

      names.push(nam[1]);
      values.push(val);
    }
/////////////////////////////////////

    _data[0][i] = tmp[0];	// X
    _data[1][i] = tmp[1];	// Y

    _data[2][i] = names;	// Attributes Name
    _data[3][i] = values;	// and Values
  }

  return _data;
}



// msTool class prototype
function msTool(p_title, p_event_button, p_icon, p_event_map)
{
  var _tag = document.createElement('img');
  var _map; var _toolbar;

  var _event_button = null;	//p_event_button;
  if (p_event_button != null)
  { _event_button = function(e){p_event_button(e, _map);} }

  var _event_map = null;
  if (p_event_map != null)
  {
    _event_map = function(e)
    {
      var xx = _map.getClick_X(e); var yy = _map.getClick_Y(e);
      p_event_map(e, _map, xx, yy, _map.xPixel2Real(xx), _map.yPixel2Real(yy));
    }
  }

  this.eventClick = function(e)
    {
      if (_event_map != null)
      {
        _toolbar.removeMapEvents();  // Remove all events
        add_event(_map.getTagEvent(), 'mousedown', _event_map);
      }
      if (_event_button != null) {_event_button();}
    }

  _tag.className = 'mscross_tool';
  _tag.oncontextmenu = function(){return false;};
  _tag.onmousedown = function(){return false;};  // Disable drag'n drop
  add_event(_tag, 'click', this.eventClick);
  setAlphaPNG(_tag, p_icon);
  _tag.title = p_title;
  setZindex(_tag, '200');
  _tag.style.margin = '0'; _tag.style.padding = '0';
  _tag.style.position = 'absolute';
  _tag.style.cursor = 'pointer';
  _tag.style.display = 'none';

  this.getTag = function(){return _tag;}
  this.setMap = function(p){_map=p;}
  this.setToolbar = function(p){_toolbar=p;}
  this.haveMapEvent = function(){if (_event_map == null){return false;} return true;}
  this.removeMapEvent = function()
  {
    if (_event_map != null)
    { del_event(_map.getTagEvent(), "mousedown", _event_map, false); }
  }
}

// msToolbar class prototype
function msToolbar(p_msMap, _control, _default)
{
  var _tagToolbar = document.createElement('div');
  var _toolbarArray = new Array();
  var _msMap = p_msMap;
  var _tagMap = _msMap.getTagMap();

  // Toolbar Default Icons...
  var _iconFullExtentButton = '/img/alpha_button_fullExtent.png';
  var _iconZoomboxButton    = '/img/alpha_button_zoombox.png';
  var _iconPanButton        = '/img/alpha_button_pan.png';
  var _iconZoominButton     = '/img/alpha_button_zoomIn.png';
  var _iconZoomoutButton    = '/img/alpha_button_zoomOut.png';

  this.getTag = function(){return _tagToolbar;}
  this.hide = function(){_tagToolbar.style.display = 'none';}

  this.removeMapEvents = function()
  { for (i=0; i<_toolbarArray.length; i++){_toolbarArray[i].removeMapEvent();} }

  this.addMapTool = function(p_tool)
  {
    p_tool.setMap(_msMap);
    p_tool.setToolbar(this);
    _toolbarArray.push(p_tool);
    _tagToolbar.appendChild(p_tool.getTag());
    this.redraw();
  }

  this.redraw = function()
  {
    if ( (_control == 'standard')      ||
         (_control == 'standardRight') ||
         (_control == 'standardCornerRight') )
    {
      box.style.left   = (parseInt(_tagMap.style.width)-(40+_msMap.getBorder()*2)) +'px';
      box.style.top    = '0px';
      box.style.width  = '40px';
      box.style.height = _tagMap.style.height;
      for (i=0; i<_toolbarArray.length; i++)
      { setPos(_toolbarArray[i].getTag(), parseInt(box.style.left)+5, (i*40)+5); }
    }
    if ( (_control == 'standardLeft') || (_control == 'standardCornerLeft') )
    {
      for (i=0; i<_toolbarArray.length; i++){setPos(_toolbarArray[i].getTag(), 3, (i*40)+5 );}
      box.style.left   = '0px';
      box.style.top    = '0px';
      box.style.width  = '40px';
      box.style.height = _tagMap.style.height;
    }
    if (_control == 'standardUp')
    {
      for (i=0; i<_toolbarArray.length; i++){setPos(_toolbarArray[i].getTag(), (i*40)+5, 5 );}
      box.style.left   = '0px';
      box.style.top    = '0px';
      box.style.width  = _tagMap.style.width;
      box.style.height = '40px';
    }
  }

  _tagToolbar.oncontextmenu = function(){return false;};
  setZindex(_tagToolbar, '100');
  _tagToolbar.style.position = 'absolute';

  box = document.createElement('div');
  box.oncontextmenu  = function(){return false;};
  setZindex(box, '100');
  box.style.position = 'absolute';
  box.style.display  = '';
  box.style.border     = '0px';
  box.style.margin     = '0px';
  box.style.padding    = '0px';
  box.style.background = '#404040';
  box.style.lineHeight = '0';
  box.style.opacity    = '0.20';               // Gecko
  box.style.filter     = 'alpha(opacity=20)';  // Windows
  _tagToolbar.appendChild(box);

  if (_default == true)
  {
    var t_fullext = new msTool('Full Extent', _msMap.fullExtent, _iconFullExtentButton);
    var t_pan     = new msTool('Pan', _msMap.setActionPan, _iconPanButton, function(e, map, x, y){map.dragStart(e);});
    var t_zoom    = new msTool('Zoom', _msMap.setActionZoombox, _iconZoomboxButton, function(e, map, x, y){map.zoomStart(e);});
    var t_zoomin  = new msTool('Zoom In', _msMap.setActionZoomIn, _iconZoominButton);
    var t_zoomout = new msTool('Zoom Out', _msMap.setActionZoomOut, _iconZoomoutButton);
    this.addMapTool(t_fullext);
    this.addMapTool(t_pan);
    this.addMapTool(t_zoom);
    this.addMapTool(t_zoomin);
    this.addMapTool(t_zoomout);
  }

  this.redraw();

  // Activate first button with map function
  for (i=0; i<_toolbarArray.length; i++)
  {
    if (_toolbarArray[i].haveMapEvent() == true)
    {
      _toolbarArray[i].eventClick();
      break;
    }
  }
}

/*
// msEvent class prototype
function msEvent(p_action, p_function)
{
  var _action = p_action;
  var _function = p_function;
}
*/
