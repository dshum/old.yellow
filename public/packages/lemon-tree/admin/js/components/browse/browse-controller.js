browse.controller('BrowseController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var classId = $stateParams.classId;
	var itemList = [];

	var getElement = function(classId) {
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
	};

	var getPlugin = function(classId) {
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
	};

	var getBinds = function(classId) {
		var url = classId
			? 'api/binds/'+classId
			: 'api/binds';

		$http({
			method: 'GET',
			url: url
		}).then(
			function(response) {
				$scope.bindItemList = response.data.bindItemList;
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var getItems = function(classId) {
		var url = classId
			? 'api/browse/'+classId
			: 'api/browse';

		$http({
			method: 'GET',
			url: url
		}).then(
			function(response) {
				itemList = response.data.itemList;

				getList(0, classId);
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var getList = function(i, classId) {

		if ( ! itemList[i]) {
			if ( ! $scope.fill) {
				$scope.empty = true;
			}

			return false;
		}

		var item = itemList[i];

		var url = classId
			? 'api/list/'+item.nameId+'/'+classId
			: 'api/list/'+item.nameId

		var open = i ? false : true;

		$http({
			method: 'GET',
			url: url,
			params: {open: 1}
		}).then(
			function(response) {
				if (response.data.elementListView) {
					$scope.elementListViewList[item.nameId] = response.data.elementListView;
					$scope.fill = true;
				}

				getList(i + 1, classId);
			},
			function(error) {
				console.log(error);
				getList(i + 1, classId);
			}
		);
	};

	$rootScope.activeIcon = 'browse';

	$scope.currentElement = null;
	$scope.parentElement = null;
	$scope.plugin = null;
	$scope.parentList = [];
	$scope.bindItemList = [];
	$scope.elementListViewList = {};
	$scope.empty = false;
	$scope.fill = false;

	if (classId) {
		getElement(classId);
		getPlugin(classId);
		getBinds(classId);
		getItems(classId);
	} else {
		getBinds();
		getItems();
	}

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

	$scope.pageChanged = function(item, page) {
		var url = classId
			? 'api/list/'+item.nameId+'/'+classId
			: 'api/list/'+item.nameId;

		$.blockUI();

		$http({
			method: 'GET',
			url: url,
			params: {page: page}
		}).then(
			function(response) {
				if (response.data.elementListView) {
					$scope.elementListViewList[item.nameId] = response.data.elementListView;
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