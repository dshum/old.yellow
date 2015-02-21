users.controller('ItemPermissionsController', function(
	$rootScope, $scope, $http, $stateParams
) {
	var id = $stateParams.id;

	$rootScope.activeIcon = 'users';

	$scope.group = null;
	$scope.itemList = [];
	$scope.permission = {};

	$http({
		method: 'GET',
		url: 'api/group/'+id+'/items'
	}).then(
		function(response) {
			var defaultPermission = response.data.defaultPermission;
			var permissionList = response.data.permissionList;

			if (response.data.group) {
				$scope.group = response.data.group;
			}

			if (response.data.itemList) {
				$scope.itemList = response.data.itemList;
			}

			for (var name in $scope.itemList) {
				$scope.permission[name] =
					permissionList[name] || defaultPermission;
			}
		},
		function(error) {
			console.log(error);
		}
	);

	$scope.submit = function() {
		$http({
			method: 'POST',
			url: 'api/group/'+id+'/items',
			data: $scope.permission,
			checkForm: true,
		}).then(
			function(response) {

			},
			function(error) {
				console.log(error);
			}
		);
	};
});