users.controller('UserController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var id = $stateParams.id;

	$rootScope.activeIcon = 'users';

	$scope.id = id;
	$scope.user = null;
	$scope.groupList = [];

	$http({
		method: 'GET',
		url: 'api/user/form'
	}).then(
		function(response) {
			$scope.groupList = response.data.groupList;
		},
		function(error) {
			console.log(error);
		}
	);

	if (id) {
		$http({
			method: 'GET',
			url: 'api/user/'+id
		}).then(
			function(response) {
				$scope.user = response.data.user;
			},
			function(error) {
				console.log(error);
			}
		);
	}

	$scope.submit = function() {
		$http({
			method: 'POST',
			url: (id ? 'api/user/'+id : 'api/user/add'),
			data: $scope.user,
			checkForm: true,
		}).then(
			function(response) {
				if (response.data.user) {
					if (id) {
						$scope.user = response.data.user;
					} else {
						$state.go('base.users');
					}
				}
			},
			function(error) {
				console.log(error);
			}
		);
	};
});