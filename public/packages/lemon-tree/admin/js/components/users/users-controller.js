users.controller('UsersController', function(
	$rootScope, $scope, $http, $modal
) {
	var groupList = function() {
		$http({
			method: 'GET',
			url: 'api/group/list'
		}).then(
			function(response) {
				$scope.groupList = response.data.groupList;
			},
			function(error) {
				console.log(error);
			}
		);
	};

	var userList = function() {
		$http({
			method: 'GET',
			url: 'api/user/list'
		}).then(
			function(response) {
				$scope.userList = response.data.userList;
			},
			function(error) {
				console.log(error);
			}
		);
	};

	$rootScope.activeIcon = 'users';

	$scope.groupList = [];
	$scope.userList = [];

	groupList();
	userList();

	$scope.deleteGroup = function(group) {
		var modalInstance = $modal.open({
			templateUrl: 'modal.html',
			controller: 'ModalInstanceController',
			size: 'sm',
			resolve: {
				data: function() {
					return {
						message: 'Удалить группу «'+group.name+'»?',
						textOk: 'Удалить',
					};
				}
			}
		});

		modalInstance.result.then(
			function() {
				$.blockUI();
				$http({
					method: 'DELETE',
					url: 'api/group/'+group.id,
				}).then(
					function(response) {
						groupList();
						userList();
						$.unblockUI();
					},
					function(error) {
						console.log(error);
					}
				);
			},
			function() {}
		);
	};

	$scope.deleteUser = function(user) {
		var modalInstance = $modal.open({
			templateUrl: 'modal.html',
			controller: 'ModalInstanceController',
			size: 'sm',
			resolve: {
				data: function() {
					return {
						message: 'Удалить пользователя «'+user.login+'»?',
						textOk: 'Удалить',
					};
				}
			}
		});

		modalInstance.result.then(
			function() {
				$.blockUI();
				$http({
					method: 'DELETE',
					url: 'api/user/'+user.id,
				}).then(
					function(response) {
						userList();
						$.unblockUI();
					},
					function(error) {
						console.log(error);
					}
				);
			},
			function() {}
		);
	};
});