/**
 * Atlassian JS animation framework. Simple, but not overly so.
 *
 * TODO: Document this, make it an object
 */
var AJS = AJS || {};

AJS.animation = {
    running: [],
    queue: [],
    timer: null,
    duration: 300,
    period: 20,
    add: function(item) {
        this.queue.push(item);
    },
    start: function() {
        if (this.timer != null) return;
        this.running = this.queue;
        this.queue = [];
        jQuery.each(this.running, function () {
            if (this.onStart) {
                this.onStart();
            }
        });
        var animation = this;
        var startTime = new Date().getTime();
        var endTime = startTime + this.duration;
        this.timer = setInterval(function() {
            var time = new Date().getTime();
            var pos = (time - startTime) / (endTime - startTime);
            if (pos <= 1)
                animation.animate(pos);
            if (pos >= 1 && animation.timer != null)
                animation.finish();
        }, this.period);
        return this.timer;
    },
    finish: function() {
        clearInterval(this.timer);
        jQuery.each(this.running, function () {
            if (this.onFinish) {
                this.onFinish();
            }
        });
        this.running = [];
        this.timer = null; // must be last because it's the lock to prevent concurrent executions
        if (this.queue.length > 0) this.start();
    },
    animate: function(pos) {
        jQuery.each(this.running, function () {
            if (this.animate) {
                this.animate(AJS.animation.interpolate(pos, this.start, this.end, this.reverse));
            }
        });
    },
    interpolate: function(pos, start, end, reverse) {
        if (typeof start != "undefined" && typeof end != "undefined") {
            if (reverse) {
                return end + pos * (start - end);
            } else {
                return start + pos * (end - start);
            }
        }
        return pos;
    },
    combine: function(list) {
        return {
            animations: list,
            append: function(animation) {
                this.animations.push(animation);
                return this;
            },
            onStart: function() {
                jQuery.each(this.animations, function () {
                    if (this.onStart) {
                        this.onStart();
                    }
                });
            },
            onFinish: function() {
                jQuery.each(this.animations, function () {
                    if (this.onFinish) {
                        this.onFinish();
                    }
                });
            },
            animate: function(pos) {
                jQuery.each(this.animations, function () {
                    if (this.animate) {
                        this.animate(AJS.animation.interpolate(pos, this.start, this.end, this.reverse));
                    }
                });
            }
        };
    }
};