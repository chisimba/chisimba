/*======================================================================
 *  Dispatcher
 *======================================================================
 */

Runway.Dispatcher = {
};

Runway.Dispatcher.wrap = function(f) {
    var id = "f" + new Date().getTime() + Math.round(Math.random() * 1000000);
    f.__dispid = id;
    
    window[id] = f;
    
    return id;
};

Runway.Dispatcher.release = function(f) {
    if ("__dispid" in f) {
        try {
            delete window[f.__dispid];
        } catch (e) {
            window[f.__dispid] = undefined;
        }
        
        delete f.__dispid;
    }
};
