plugin.controller('MoneyStatController', function(
	$rootScope, $scope, $http, $state, $stateParams
) {
	var getList = function() {
		$('button').blur();

		$http({
			method: 'GET',
			url: 'plugins/moneyStat/list',
			params: $scope.filter
		}).then(
			function(response) {
				$scope.goodList = response.data.goodList;
				$.unblockUI();
			},
			function(error) {
				console.log(error);
				$.unblockUI();
			}
		);
	};

	$scope.filter = {
		name: null,
		priceFrom: null,
		priceTo: null,
	};

	$scope.goodList = [];

	$scope.search = function() {
		$.blockUI();
		getList();
	};

	getList();
});