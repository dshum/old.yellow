login.controller('LoginController', function(
	$rootScope, $scope, $state,
	Login, AuthToken, Favorite
) {
	$scope.message = null;

	$scope.submit = function() {
		Login.login(
			$scope.loginData,
			function(response) {
				AuthToken.setToken(response.data.token);
				$rootScope.loggedUser = response.data.user;
				$state.go('base.browse');
			},
			function(error) {
				$scope.message = error.data.message;
				console.log(error);
			}
		);
	};
});