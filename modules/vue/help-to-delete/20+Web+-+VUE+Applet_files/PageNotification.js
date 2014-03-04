
function PageNotification() { }
PageNotification._path = '/confluence/dwr';

PageNotification.setPermissionManager = function(p0, callback) {
    DWREngine._execute(PageNotification._path, 'PageNotification', 'setPermissionManager', p0, callback);
}

PageNotification.setNotificationManager = function(p0, callback) {
    DWREngine._execute(PageNotification._path, 'PageNotification', 'setNotificationManager', p0, callback);
}

PageNotification.setPageManager = function(p0, callback) {
    DWREngine._execute(PageNotification._path, 'PageNotification', 'setPageManager', p0, callback);
}

PageNotification.startWatching = function(p0, callback) {
    DWREngine._execute(PageNotification._path, 'PageNotification', 'startWatching', p0, callback);
}

PageNotification.stopWatching = function(p0, callback) {
    DWREngine._execute(PageNotification._path, 'PageNotification', 'stopWatching', p0, callback);
}
