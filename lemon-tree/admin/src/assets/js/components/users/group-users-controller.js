users.controller('GroupUsersController', function(
	$rootScope, $scope, $http, $stateParams, $modal
) {
	var id = $stateParams.id;

	var userList = function() {
		$http({
			method: 'GET',
			url: 'api/group/'+id+'/user/list',
		}).then(
			function(response) {
				$scope.group = response.data.group;
				$scope.userList = response.data.userList;
			},
			function(error) {
				console.log(error);
			}
		);
	};

	$rootScope.activeIcon = 'users';

	$scope.group = null;
	$scope.userList = [];

	userList();

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