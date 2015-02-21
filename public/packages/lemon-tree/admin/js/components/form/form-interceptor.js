form.factory('FormInterceptor', function ($q, $rootScope, $injector, Alert) {
	return {
		request: function (config) {
			if (config.checkForm) {
				Alert.onSubmit();
			}

			return config;
		},
		response: function (response) {
			var state = $injector.get('$state');

			if (response.data.state === 'error_element_not_found') {
				state.go('base.browse');
			} else if (response.data.state === 'error_element_access_denied') {
				state.go('base.browse');
			} else if (response.data.state === 'error_admin_access_denied') {
				state.go('base.browse');
			} else if (response.data.state === 'error_group_not_found') {
				state.go('base.users');
			} else if (response.data.state === 'error_group_access_denied') {
				state.go('base.users');
			} else if (response.data.state === 'error_user_not_found') {
				state.go('base.users');
			} else if (response.data.state === 'error_user_access_denied') {
				state.go('base.users');
			} else if (response.data.message) {
				$rootScope.message = response.data.message;
			} else if (response.config.checkForm) {
				Alert.onResponse(response);
			}

			return response;
		},
		responseError: function(rejection) {
			return $q.reject(rejection);
		}
	};
});