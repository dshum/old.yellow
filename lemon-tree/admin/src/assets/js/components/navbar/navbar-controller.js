navbar.controller('NavbarController', function(
	$rootScope, $scope, $state,
	AuthToken, Favorite
) {
	$scope.toggle = function() {
		$('#toggle-button').blur();
		$('#wrapper').toggleClass('toggled');
	};

	$scope.home = function() {
		$state.go('base.browse');
	};

	$scope.refresh = function() {
		$state.reload();
	};

	$scope.search = function() {
		$state.go('base.search');
	};

	$scope.trash = function() {
		$state.go('base.trash');
	};

	$scope.users = function() {
		$state.go('base.users');
	};

	$scope.profile = function() {
		$state.go('base.profile');
	};

	$scope.logout = function() {
		AuthToken.clearToken();
		$rootScope.loggedUser = null;
		Favorite.clear();
		$state.go('simple.login');
	};
});