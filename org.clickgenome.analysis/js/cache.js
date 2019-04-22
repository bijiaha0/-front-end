var Cache = new Object({
	init: function() {
		if(window.localStorage) { //或者 window.sessionStorage     
			return true;
		}
		return false;
	},
	save: function(key, value) {
		window.localStorage.setItem(key, value);
	},
	get: function(key) {
		return window.localStorage.getItem(key);
	}
});