users.controller('ElementPermissionsController', function(
	$rootScope, $scope, $http, $stateParams
) {
	var id = $stateParams.id;

	$rootScope.activeIcon = 'users';

	$scope.group = null;
	$scope.itemList = [];
	$scope.itemElementList = [];
	$scope.permission = {};

	$http({
		method: 'GET',
		url: 'api/group/'+id+'/elements'
	}).then(
		function(response) {
			var defaultPermission = response.data.defaultPermission;
			var permissionList = response.data.permissionList;

			$scope.group = response.data.group;
			$scope.itemList = response.data.itemList;
			$scope.itemElementList = response.data.itemElementList;

			for (var itemName in $scope.itemElementList) {
				for (var classId in $scope.itemElementList[itemName]) {
					$scope.permission[classId] =
						permissionList[classId]
						|| permissionList[itemName]
						|| defaultPermission;
				}
			}
		},
		function(error) {
			console.log(error);
		}
	);

	$scope.submit = function() {
		$http({
			method: 'POST',
			url: 'api/group/'+id+'/elements',
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