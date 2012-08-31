yahooweatherbadge = function(){
  var container,units,style;
  function init(){
    var sc = document.getElementsByTagName('script');
    for(var i=0;i<sc.length;i++){
      if(sc[i].src.indexOf('weather.js')!=-1){
        units = 'f';
        var elm = sc[i];
        var content = elm.innerHTML.split(',');
        var city = content[0].split(':')[1];
        if(city && typeof city === 'string'){
          var loc = encodeURIComponent(city);
        }
        var u = content[1].split(':')[1];
        units = (u==='c') ? 'c' : 'f';
        style = content[2].split(':')[1];
        break;
      };
    }
    container = document.createElement('div');
    container.className = 'yahooweather';
    container.appendChild(document.createTextNode('Weather loading...'));
    elm.parentNode.insertBefore(container,elm);
    elm.parentNode.removeChild(elm);
    var apisrc = 'http://query.yahooapis.com/v1/public/yql?q=use%20\'http%3A%2F%2Fisithackday.com%2Fweatherbadge%2Fweather.bylocation.xml\'%20as%20we%3Bselect%20*%20from%20we%20where%20location%3D%22'+loc+'%22%20and%20unit%3D\''+units+'\'&format=json&callback=yahooweatherbadge.weather';
    var s = document.createElement('script');
    s.src=apisrc;
    document.getElementsByTagName('head')[0].appendChild(s);
  }
  function weather(o){
    var wdata = o.query.results.weather.rss.channel;
    if(wdata.title.indexOf('Error')==-1){
    var elm = document.getElementById('weather');
    var title = wdata.title;
    var link = wdata.link;
    var temperature = wdata.item.condition.temp;
    var weather = wdata.item.condition.text;
    var pic = wdata.item.description.match(/src="([^"]+)".*/)[1];
    var forecast = '';
    for(var i=0,j=wdata.item.forecast.length;i<j;i++){
      var cur = wdata.item.forecast[i];
      forecast += '<li><strong>'+cur.date+'</strong>: '+cur.text+', low: '+
                  cur.low+' high: '+cur.high+'</li>';
    }
    var badge = '<h5>'+wdata.title+'</h5>'+
                '<p class="condition">'+
                '<img src="'+pic+'" alt="">'+
                '<span>'+temperature+' degrees,  '+weather+'</span>'+
                '</p>'+
                '<h6>Forecast:</h6>'+
                '<ul>'+forecast+'</ul>'+
                '<p class="byline">'+
                'Get the full weather forecast on '+
                '<a href="'+link+'">Yahoo Weather</a> '+
                'powered by '+
                '<a href="http://weather.com">The Weather Channel</a></p>';
    } else {
     var badge = '<h5>Cannot find weather data for this location.</h5>';
    }

    var styles = 'div.yahooweather * {font-size:12px;margin:0;padding:0;}'+
                 'div.yahooweather{border:2px solid #ccc;'+
                 '-moz-border-radius:5px;background:#eee;padding:5px;}'+
                 'div.yahooweather h5{margin:2px 0;font-size:14px;}'+
                 'div.yahooweather ul{margin:5px 0;list-style:none;}'+
                 'div.yahooweather li{list-style:none;}'+
                 'div p.byline{margin:5px 0;color:#999;font-size:10px;'+
                              'text-align:right}'+
                 'div p.byline a{color:#666;font-size:10px;}';
    if(style==='false'){
      container.innerHTML = badge;
    } else {
      container.innerHTML = '<style>'+styles+'</style>'+badge;
    }           
  }
  function addEvent(elm, evType, fn, useCapture){
    if (elm.addEventListener){
      elm.addEventListener(evType, fn, useCapture);
      return true;
    } else if (elm.attachEvent) {
      var r = elm.attachEvent('on' + evType, fn);
      return r;
    } else {
      elm['on' + evType] = fn;
    }
  }
  return{addEvent:addEvent,init:init,weather:weather}
}();
yahooweatherbadge.addEvent(window,'load',yahooweatherbadge.init,false);
