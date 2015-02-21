modal.controller('ModalInstanceController', function(
	$scope, $modalInstance, data
) {
	$scope.data = data;
	$scope.textOk = data.textOk;
	$scope.textCancel = data.textCancel;

	$scope.ok = function () {
		$modalInstance.close();
	};

	$scope.cancel = function () {
		$modalInstance.dismiss();
	};
});