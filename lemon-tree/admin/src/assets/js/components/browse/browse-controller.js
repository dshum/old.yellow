browse.controller('BrowseController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var classId = $stateParams.classId;

	$rootScope.activeIcon = 'browse';

	$scope.currentElement = null;
	$scope.parentElement = null;
	$scope.plugin = null;
	$scope.parentList = [];
	$scope.bindItemList = [];
	$scope.elementListViewList = [];
	$scope.empty = false;

	if (classId) {
		$http({
			method: 'GET',
			url: 'api/element/'+classId
		}).then(
			function(response) {
				$scope.currentElement = response.data.currentElement;
				$scope.parentElement = response.data.parentElement;
				$scope.parentList = response.data.parentList;
			},
			function(error) {
				console.log(error);
			}
		);

		$http({
			method: 'GET',
			url: 'api/plugin/browse/'+classId
		}).then(
			function(response) {
				if (response.data.plugin) {
					$scope.plugin = 'plugins/'+response.data.plugin;
				}
			},
			function(error) {
				console.log(error);
			}
		);
	}

	$http({
		method: 'GET',
		url: (classId ? 'api/binds/'+classId : 'api/binds')
	}).then(
		function(response) {
			$scope.bindItemList = response.data.bindItemList;
		},
		function(error) {
			console.log(error);
		}
	);

	$http({
		method: 'GET',
		url: (classId ? 'api/browse/'+classId : 'api/browse')
	}).then(
		function(response) {
			$scope.elementListViewList = response.data.elementListViewList;
			$scope.empty = $scope.elementListViewList.length ? false : true;
		},
		function(error) {
			console.log(error);
		}
	);

	$scope.up = function() {
		if ($scope.parentElement) {
			$state.go('base.browseElement', {classId: $scope.parentElement.classId});
		} else if ($scope.currentElement) {
			$state.go('base.browse');
		}
	};

	$scope.edit = function() {
		if ($scope.currentElement) {
			$state.go('base.editElement', {classId: $scope.currentElement.classId});
		}
	};
});