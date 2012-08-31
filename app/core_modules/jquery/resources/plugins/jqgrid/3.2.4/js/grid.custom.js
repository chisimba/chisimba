;(function($){
/*
**
 * jqGrid extension for custom methods
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/ 

$.fn.extend({
    getColProp : function(colname){
        var ret ={}, $t = this[0];
        if (!$t.grid) return;
        var cM = $t.p.colModel;
        for(var i =0;i<cM.length;i++) {
            if(cM[i].name == colname) {
                ret = cM[i];
                break;
            };
        };
        return ret;
    },
    setColProp : function(colname, obj){
        // do not set width will not work
        return this.each(function(){
        if (this.grid) 
            if( obj ) {
                var cM = this.p.colModel;
                for(var i =0;i<cM.length;i++) {
                    if(cM[i].name == colname) {
                        $.extend(this.p.colModel[i],obj);
	                    break;
                    }
                }
            };
        });
    },
	sortGrid : function(colname,reload){
		return this.each(function(){
			var $t=this,idx=-1;
			if (!$t.grid ) return;
			if(!colname) colname = $t.p.sortname;
			for(var i=0;i<$t.p.colModel.length;i++) {
				if($t.p.colModel[i].index == colname || $t.p.colModel[i].name==colname) {
					idx = i;
					break;
				}
			}
			if(idx!=-1){
				var sort = $t.p.colModel[idx].sortable;
				if( typeof sort !== 'boolean') sort =  true;
				if( typeof reload !=='boolean') reload = false;
				if(sort) $t.sortData(colname, idx, reload);
			};
		});
	},
	
	GridDestroy : function () {
		return this.each(function(){
			if (this.p.pager) {
				$(this.p.pager).remove();
			}
			$("#lui_"+this.id).remove();
			$(this.grid.bDiv).remove();
			$(this.grid.hDiv).remove();
			$(this.grid.cDiv).remove();
			if(this.p.toolbar[0]) $(this.grid.uDiv).remove();
			this.p = null;
			this.grid =null;
		});
	},
    
	GridUnload : function(){
		return this.each(function(){
			var defgrid = {id: $(this).attr('id'),cl: $(this).attr('class'),cellSpacing: $(this).attr('cellspacing') || '0',cellPadding:$(this).attr('cellpadding') || '0'};
			if (this.p.pager) {
				$(this.p.pager).empty();
			}
			var newtable = document.createElement('table');
			$(newtable).attr({id:defgrid['id'],cellSpacing:defgrid['cellSpacing'], cellPadding:defgrid['cellPadding']});
			newtable.className = defgrid['cl'];
			$("#lui_"+this.id).remove();
			if(this.p.toolbar[0]) $(this.grid.uDiv).remove();
			$(this.grid.cDiv).remove();
			$(this.grid.bDiv).remove();
			$(this.grid.hDiv).before(newtable).remove();
			this.p = null;
			this.grid =null;
		});
	}    
});
})(jQuery);