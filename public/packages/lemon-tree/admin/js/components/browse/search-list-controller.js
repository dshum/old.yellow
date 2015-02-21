browse.controller('SearchListController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
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

	var getElementListView = function(className, options) {
		$http({
			method: 'GET',
			url: 'api/search/'+className,
			params: options
		}).then(
			function(response) {
				if (response.data.elementListView) {
					$scope.elementListView = response.data.elementListView;
					$scope.empty =
						$scope.elementListView.elementList
						&& $scope.elementListView.elementList.length
						? false : true;
				} else {
					$scope.empty = true;
				}
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var className = $stateParams.class;
	var options = decodeOptions($stateParams.options);

	$scope.currentItem = null;
	$scope.elementListView = null;
	$scope.empty = false;

	if (className && options) {
		getElementListView(className, options);
	}
});