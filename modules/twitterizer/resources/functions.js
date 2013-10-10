 
var dts = new Date();

var lastTimeCheck = dts.format("U");



function doTwitUpdates(data){
    	
		if(data.hasUpdates == 'yes'){
			ds.load({
					add:true, 
					params:{start:0, limit:20, lastTimeCheck: lastTimeCheck}
			});
			ds.sort('tstamp','ASC');
			dv.refresh();
			
		}
    	
    }