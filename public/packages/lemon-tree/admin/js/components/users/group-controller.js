users.controller('GroupController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var id = $stateParams.id;

	$rootScope.activeIcon = 'users';

	$scope.id = id;
	$scope.group = null;

	if (id) {
		$http({
			method: 'GET',
			url: 'api/group/'+id
		}).then(
			function(response) {
				$scope.group = response.data.group;
			},
			function(error) {
				console.log(error);
			}
		);
	}

	$scope.submit = function() {
		$http({
			method: 'POST',
			url: (id ? 'api/group/'+id : 'api/group/add'),
			data: $scope.group,
			checkForm: true,
		}).then(
			function(response) {
				if (response.data.group) {
					if (id) {
						$scope.group = response.data.group;
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