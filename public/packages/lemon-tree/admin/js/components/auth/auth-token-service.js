auth.factory('AuthToken', function($window) {
	var tokenKey = 'token';

	return {
		isAuthenticated: isAuthenticated,
		setToken: setToken,
		getToken: getToken,
		clearToken: clearToken
	};

	function setToken(token) {
		$window.localStorage.setItem(tokenKey, token);
	}

	function getToken() {
		return $window.localStorage.getItem(tokenKey);
	}

	function clearToken() {
		$window.localStorage.removeItem(tokenKey);
	}

	function isAuthenticated() {
		return !! getToken();
	}
});