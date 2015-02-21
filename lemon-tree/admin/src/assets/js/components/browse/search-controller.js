browse.controller('SearchController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var encodeOptions = function(options) {
		var params = [];

		for (var name in options) {
			var value = encodeURIComponent(options[name]);
			params[params.length] = name+':'+value;
		}

		return params.join(';');
	};

	var decodeOptions = function(encoded) {
		var options = [];

		var params = encoded ? encoded.split(';') : [];

		for (var i in params) {
			var param = params[i].split(':');
			var name = param[0];
			var value = decodeURIComponent(param[1]);
			options[name] = value;
		}

		return options;
	};

	var getElementListView = function() {
		$('#element-list-container').hide();
		$('#empty-list-container').hide();
		$http({
			method: 'GET',
			url: 'api/search/'+className,
			params: options
		}).then(
			function(response) {
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

	var selectItem = function() {
		$('#item-container').hide();
		$http({
			method: 'GET',
			url: 'api/search/item/'+className
		}).then(
			function(response) {
				$scope.currentItem = response.data.item;
				$scope.sortProperty = response.data.sortProperty;
				$scope.propertyList = response.data.propertyList;
				setTimeout(function() {
					$('#item-container').slideDown('fast', function() {
						if (options.action) {
							getElementListView();
						}
					});
				});
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var getItems = function() {
		$http({
			method: 'GET',
			url: 'api/search/items'
		}).then(
			function(response) {
				if (className) {
					$('#items-container').hide();
					$scope.sortItem = response.data.sortItem;
					$scope.itemList = response.data.itemList;
					$('#items-container').slideDown('fast', function() {
						setTimeout(function() {
							selectItem();
						});
					});
				} else if (response.data.currentItem) {
					$state.go('base.searchItem', {
						class: response.data.currentItem.nameId,
						options: null
					});
				} else {
					$('#items-container').hide();
					$scope.sortItem = response.data.sortItem;
					$scope.itemList = response.data.itemList;
					$('#items-container').slideDown('fast', function() {});
				}
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var className = $stateParams.class;
	var options = decodeOptions($stateParams.options);

	$rootScope.activeIcon = 'search';

	$scope.itemList = [];
	$scope.propertyList = [];
	$scope.currentItem = null;
	$scope.sortItem = null;
	$scope.sortProperty = null;
	$scope.elementListView = null;
	$scope.empty = false;

	$scope.sortItems = function(sort) {
		$('#items-container').slideUp('fast', function() {
			$http({
				method: 'GET',
				url: 'api/search/items/',
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

	$scope.sortProperties = function(sort) {
		if ( ! $scope.currentItem) return false;

		$('#properties-container').slideUp('fast', function() {
			$http({
				method: 'GET',
				url: 'api/search/item/'+$scope.currentItem.nameId,
				params: {sort: sort}
			}).then(
				function(response) {
					$scope.sortProperty = response.data.sortProperty;
					$scope.propertyList = response.data.propertyList;
					$('#properties-container').slideDown('fast');
				},
				function(error) {
					console.log(error);
				}
			);
		});
	};

	$scope.search = function() {
		var propertyList = $scope.propertyList;

		var options = {
			action: 'search',
		};

		for (var i in propertyList) {
			var name = propertyList[i].searchView.name;
			var value = propertyList[i].searchView.value;
			var open = propertyList[i].searchView.open;

			if (name && value && open) {
				if (typeof value === 'object') {
					for (var i in value) {
						options[name+'_'+i] = value[i];
					}
				} else {
					options[name] = value;
				}
			}
		}

		options = encodeOptions(options);

		$state.go('base.searchItem', {
			class: $scope.currentItem.nameId,
			options: options
		});
	};

	getItems();
});