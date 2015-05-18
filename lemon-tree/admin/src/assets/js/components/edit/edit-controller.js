edit.controller('EditController', function(
	$rootScope, $scope, $http, $state, $stateParams, $injector,
    Alert
) {
	var classId = $stateParams.classId;

	$rootScope.activeIcon = $state.current.data.trashed ? 'trash' : 'browse';

	$scope.trashed = $state.current.data.trashed;

	$scope.currentElement = null;
	$scope.parentElement = null;
	$scope.parentList = [];
	$scope.currentItem = null;
	$scope.propertyList = [];
	$scope.ones = [];
	$scope.files = {};

	$scope.$on('fileSelected', function (event, args) {
		$scope.$apply(function () {
			$scope.files[args.name] = args.file;
		});
	});

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
			$scope.ones = response.data.ones;

			if (
				$scope.currentElement
				&& $scope.currentElement.trashed
				&& $state.current.name == 'base.editElement'
			) {
				$state.go('base.trashedElement', {classId: classId});
			}

			if (
				$scope.currentElement
				&& ! $scope.currentElement.trashed
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
		if ($scope.trashed && $scope.currentItem) {
			$state.go('base.trashItem', {class: $scope.currentItem.nameId});
		} else if ($scope.parentElement) {
			$state.go('base.browseElement', {classId: $scope.parentElement.classId});
		} else {
			$state.go('base.browse');
		}
	};

	$scope.save = function() {
		var data = new FormData();

		for (var i in $scope.propertyList) {
			var property = $scope.propertyList[i];
			var name = property.editView.name;
			var value = property.editView.value;
			var drop = property.editView.drop;

			if (value && value.id) {
				data[name] = value.id;
				data.append(name, value.id);
			} else if (drop) {
				data.append(name+'_drop', 1);
			} else if ($scope.files[name]) {
				data.append(name, $scope.files[name]);
			} else if (value && value.value) {
				data.append(name, value.value);
			} else if (value) {
				data.append(name, value);
			}
		}

		$http({
			method: 'POST',
			url: 'api/element/'+classId,
			headers: {'Content-Type': undefined},
			data: data,
			checkForm: true
		}).then(
			function(response) {
				if (response.data.state == 'ok') {
					$scope.currentElement = response.data.currentElement;
					$scope.propertyList = response.data.propertyList;
					$scope.ones = response.data.ones;
				}
			},
			function(error) {
				console.log(error);
			}
		);
	};

	$scope.copy = function() {
		var modal = $injector.get('$modal');

		var modalInstance = modal.open({
			templateUrl: 'copy.html',
			controller: 'CopyInstanceController',
			resolve: {
				data: function() {
					return {
						classId: $scope.currentElement.classId,
						ones: $scope.ones,
						redirect: true
					};
				}
			}
		});
	};

	$scope.move = function() {
		var modal = $injector.get('$modal');

		var modalInstance = modal.open({
			templateUrl: 'move.html',
			controller: 'MoveInstanceController',
			resolve: {
				data: function() {
					return {
						classId: $scope.currentElement.classId,
						ones: $scope.ones,
						reload: true
					};
				}
			}
		});
	};

	$scope.drop = function() {
		$.blockUI();

		$http({
			method: 'POST',
			url: 'api/delete/'+classId
		}).then(
			function(response) {
				if (response.data.state == 'ok') {
					$scope.up();
					$rootScope.refreshTree();
				}
				$.unblockUI();
			},
			function(error) {
				console.log(error);
				$.unblockUI();
			}
		);
	};

	$scope.restore = function() {
		$.blockUI();

		$http({
			method: 'POST',
			url: 'api/restore/'+classId
		}).then(
			function(response) {
				if (response.data.state == 'ok') {
					$state.go('base.editElement', {classId: classId});
					$rootScope.refreshTree();
				}
				$.unblockUI();
			},
			function(error) {
				console.log(error);
				$.unblockUI();
			}
		);
	};
});