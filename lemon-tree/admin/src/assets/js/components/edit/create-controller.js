edit.controller('CreateController', function(
	$rootScope, $scope, $http, $state, $stateParams, $injector,
	Alert
) {
	var parentId = $stateParams.parentId;
	var className = $stateParams.class;

	var url = parentId
		? 'api/create/'+className+'/'+parentId
		: 'api/create/'+className;

	$rootScope.activeIcon = 'browse';

	$scope.currentElement = null;
	$scope.parentElement = null;
	$scope.parentList = [];
	$scope.currentItem = null;
	$scope.propertyList = [];
	$scope.ones = [];
	$scope.files = {};

	$http({
		method: 'GET',
		url: url
	}).then(
		function(response) {
			$scope.currentElement = response.data.currentElement;
			$scope.parentElement = response.data.parentElement;
			$scope.parentList = response.data.parentList;
			$scope.currentItem = response.data.currentItem;
			$scope.propertyList = response.data.propertyList;
			$scope.ones = response.data.ones;
		},
		function(error) {
			console.log(error);
		}
	);

	$scope.$on('fileSelected', function (event, args) {
		$scope.$apply(function () {
			$scope.files[args.name] = args.file;
		});
	});

	$scope.up = function() {
		if ($scope.parentElement) {
			$state.go('base.browseElement', {classId: $scope.parentElement.classId});
		} else {
			$state.go('base.browse');
		}
	};

	$scope.add = function() {
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
});