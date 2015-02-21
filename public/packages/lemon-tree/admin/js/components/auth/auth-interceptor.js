auth.factory('AuthInterceptor', function ($q, $injector, AuthToken) {
	return {
		request: function (config) {
			var token = AuthToken.getToken();

			if (token) {
				config.headers = config.headers || {};
				config.headers.Authorization = 'Bearer ' + token;
			}

			return config;
		},
		response: function (response) {
			return response;
		},
		responseError: function(rejection) {
			var state = $injector.get('$state');

			if (rejection.status === 401) {
				state.go('simple.login');
			}

			return $q.reject(rejection);
		}
	};
});