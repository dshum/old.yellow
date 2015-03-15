browse.controller('TrashController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var className = $stateParams.class;

	var getElementListView = function(className) {
		$('#element-list-container').hide();
		$('#empty-list-container').hide();
		$http({
			method: 'GET',
			url: 'api/trash/'+className
		}).then(
			function(response) {
				if (response.data.item) {
					$scope.currentItem = response.data.item;
				}

				if (response.data.elementListView) {
					$scope.empty = false;
					$scope.elementListView = response.data.elementListView;
					setTimeout(function() {
						$('#element-list-container').slideDown('fast');
					});
				} else {
					$scope.empty = true;
					setTimeout(function() {
						$('#empty-list-container').slideDown('fast');
					});
				}
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var getItems = function() {
		$http({
			method: 'GET',
			url: 'api/trash/items'
		}).then(
			function(response) {
				if (className) {
					$('#items-container').hide();
					$scope.sortItem = response.data.sortItem;
					$scope.itemList = response.data.itemList;
					$scope.emptyTrash = $scope.itemList.length ? false : true;
					$('#items-container').slideDown('fast', function() {
						setTimeout(function() {
							if ($scope.itemList.length) {
								getElementListView(className);
							}
						});
					});
				} else if (response.data.currentItem) {
					$state.go('base.trashItem', {
						class: response.data.currentItem.nameId
					});
				} else {
					$('#items-container').hide();
					$scope.sortItem = response.data.sortItem;
					$scope.itemList = response.data.itemList;
					$scope.emptyTrash = $scope.itemList.length ? false : true;
					$('#items-container').slideDown('fast', function() {});
				}
			},
			function(error) {
				console.log(error);
			}
		);
	};

	$rootScope.activeIcon = 'trash';

	$scope.itemList = [];
	$scope.currentItem = null;
	$scope.sortItem = null;
	$scope.elementListView = null;
	$scope.empty = false;
	$scope.emptyTrash = false;

	$scope.sortItems = function(sort) {
		$('#items-container').slideUp('fast', function() {
			$http({
				method: 'GET',
				url: 'api/trash/items/',
				params: {sort: sort}
			}).then(
				function(response) {
					$scope.sortItem = response.data.sortItem;
					$scope.itemList = response.data.itemList;
					$('#items-container').slideDown('fast');
				},
				function(error) {
					console.log(error);
				}
			);
		});
	};

	getItems();
});