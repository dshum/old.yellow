edit.controller('EditController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var classId = $stateParams.classId;

	$rootScope.activeIcon = $state.current.data.trashed ? 'trash' : 'browse';

	$scope.trashed = $state.current.data.trashed;

	$scope.currentElement = null;
	$scope.parentElement = null;
	$scope.parentList = [];
	$scope.currentItem = null;
	$scope.propertyList = [];

	$http({
		method: 'GET',
		url: 'api/element/'+classId
	}).then(
		function(response) {
			$scope.currentElement = response.data.currentElement;
			$scope.parentElement = response.data.parentElement;
			$scope.parentList = response.data.parentList;
			$scope.currentItem = response.data.currentItem;
			$scope.propertyList = response.data.propertyList;

			if (
				$scope.currentElement.trashed
				&& $state.current.name == 'base.editElement'
			) {
				$state.go('base.trashedElement', {classId: classId});
			}

			if (
				! $scope.currentElement.trashed
				&& $state.current.name == 'base.trashedElement'
			) {
				$state.go('base.editElement', {classId: classId});
			}
		},
		function(error) {
			console.log(error);
		}
	);

	$scope.up = function() {
		if ($scope.parentElement) {
			$state.go('base.browseElement', {classId: $scope.parentElement.classId});
		} else {
			$state.go('base.browse');
		}
	};

	$scope.submit = function() {
		console.log($scope);
	};
});