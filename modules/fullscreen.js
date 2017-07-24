angular.module('toggle-fullscreen', []).factory('fullscreen', function() {
	
	function fullscreen() {
		
		var self = this;

 		function isFullScreen() {
			return (document.fullScreenElement && document.fullScreenElement !== null)
				 || document.mozFullScreen
				 || document.webkitIsFullScreen;
		};

		function requestFullScreen(element) {
			var elem = document.querySelector(element);		
			if (elem.requestFullscreen)
				elem.requestFullscreen();
			else if (elem.msRequestFullscreen)
				elem.msRequestFullscreen();
			else if (elem.mozRequestFullScreen)
				elem.mozRequestFullScreen();
			else if (elem.webkitRequestFullscreen)
				elem.webkitRequestFullscreen();
		};

		function exitFullScreen() {
			if (document.exitFullscreen)
				document.exitFullscreen();
			else if (document.msExitFullscreen)
				document.msExitFullscreen();
			else if (document.mozCancelFullScreen)
				document.mozCancelFullScreen();
			else if (document.webkitExitFullscreen)
				document.webkitExitFullscreen();
		};

		self.toggleFullScreen = function(element) {
			if (isFullScreen())
				exitFullScreen();
			else
				requestFullScreen(element || document.documentElement);
		};
	
	};

	return new fullscreen();
	
});