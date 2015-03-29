users.controller('LogController', function(
	$rootScope, $scope, $http, $stateParams, $window,
	Helper
) {
	var id = $stateParams.id;
	var currentPage = $window.localStorage.getItem('log_current_page') || 1;
	var blocked = false;

	var getForm = function() {
		$http({
			method: 'GET',
			url: 'api/log/form',
			params: {
				user: id
			}
		}).then(
			function(response) {
				$scope.activeUser = response.data.activeUser;
				$scope.userList = response.data.userList;
				$scope.actionTypeList = response.data.actionTypeList;

				getList();
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var getList = function(stop) {
		blocked = true;

		var userId = $scope.activeUser
			? $scope.activeUser.id
			: null;

		var dateFromString = $scope.filter.dateFrom
			? Helper.toDateString($scope.filter.dateFrom)
			: null;

		var dateToString = $scope.filter.dateTo
			? Helper.toDateString($scope.filter.dateTo)
			: null;

		$http({
			method: 'GET',
			url: 'api/log',
			params: {
				user: userId,
				actionType: $scope.filter.actionType,
				comments: $scope.filter.comments,
				dateFrom: dateFromString,
				dateTo: dateToString,
				page: currentPage,
				perPage: $scope.perPage
			}
		}).then(
			function(response) {
				$scope.userActionList = response.data.userActionList;
				$scope.count = response.data.count;
				$scope.currentPage = response.data.currentPage;

				if (
					currentPage !== response.data.currentPage
					&& ! stop
				) {
					currentPage = response.data.currentPage;
					getList(true);
					return false;
				}

				$window.localStorage.setItem('log_current_page', currentPage);

				$scope.empty = $scope.userActionList.length ? false : true;

				blocked = false;

				$.unblockUI();
			},
			function(error) {
				console.log(error);
			}
		);
	};

	$rootScope.activeIcon = 'users';

	$scope.activeUser = null;
	$scope.userList = [];
	$scope.actionTypeList = [];
	$scope.userActionList = [];
	$scope.empty = false;

	$scope.filter = {
		actionType: null,
		comments: null,
		dateFrom:  null,
		dateTo:  null
	};

	$scope.perPage = 10;

	getForm();

	$scope.submit = function() {
		$.blockUI();
		currentPage = 1;
		getList();
	};

	$scope.pageChanged = function() {
		$.blockUI();
		currentPage = $scope.currentPage;
		getList();
	};
});