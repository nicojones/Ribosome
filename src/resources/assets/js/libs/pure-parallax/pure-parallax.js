// PARALLAX EFFECT

// we only need three globals. Here declared
var _pl, _init_parallax = false, _parallax_width;

// start the parallax magic.
function init_parallax() {
    // getting some values for higher efficiency
	_pl = parallaxObj.length, _parallax_width = document.body.clientWidth;
	for (var i = 0, p; i < _pl; ++i) {
	 	p = parallaxObj[i];
        // editable object
		parallaxObj[i].obj = document.getElementById(p.id);

        // we set the background if is set from the settings
		if (p.background) {
			parallaxObj[i].obj.style.backgroundImage = "url(\"" + p.background + "\")";
		} else {
            // background set from CSS
        }

        // calculate the offset-top for the background
        var top = p.top;
        switch (typeof top) {
            // it's an object when there are responsive settings in parallaxObj.top
            case 'object':
                var size = 0;
                for (var key in top) {
                    (key > size) && (key < _parallax_width) && (size = key);
                }
                top = top[size];

            // it's a string when there's no responsive.
            case 'string':
                var px = top.match(/(.*)px/), em = top.match(/(.*)em/), pc = top.match(/(.*)%/);
                (pc || false) && (parallaxObj[i]._top = Number(pc.pop())/100 * p.obj.clientHeight);
                (px || false) && (parallaxObj[i]._top = Number(px.pop()));
                (em || false) && (parallaxObj[i]._top = 16*Number(em.pop()));
                break;

            // since .top is optional, we set it to 0 by default.
            case 'undefined':
            default:
                parallaxObj[i]._top = 0;
                break;
        }
        if (i == 1) console.log(parallaxObj[i]._top);

        // convert human-speed (0 = fixed, 1 = no effect, 0.5 = subtle) to equation-speed
        parallaxObj[i]._speed = (1 - (p.speed || 0));
	}

    // settings listeners, but ONLY the first time it runs.
    if (!_init_parallax) {
        window.onscroll = _parallaxScroll;
        window.onresize = init_parallax;
        _init_parallax = true;
	    _parallaxScroll();
    }
}


// what controls the background positions is called on .scroll() event.
function _parallaxScroll() {
    var pageY = window.pageYOffset;
    for(var i = 0; i < _pl; ++i) {
        // copying globals to locals for higher efficiency
        var p = parallaxObj[i], o = p.obj;

        // setting the background position. This makes the magic happen.
        o.style.backgroundPosition = '0 ' + ((pageY - o.offsetTop)*p._speed - p._top) + 'px';
    }
};
