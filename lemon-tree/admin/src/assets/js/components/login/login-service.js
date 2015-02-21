login.factory('Login', function(
	$http
){
	return {
		login: function(credentials, onSuccess, onFailed) {
			$http({
				method: 'POST',
				url: 'api/login',
				data: credentials,
			}).then(
				onSuccess,
				onFailed
			);
		},
		user: function(onSuccess, onFailed) {
			$http({
				method: 'GET',
				url: 'api/user'
			}).then(
				onSuccess,
				onFailed
			);
		}
	};
});