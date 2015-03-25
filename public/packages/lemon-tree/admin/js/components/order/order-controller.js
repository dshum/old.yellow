order.controller('OrderController', function(
	$rootScope, $scope, $http, $state, $stateParams,
	Alert
) {
	var className = $stateParams.class;
	var classId = $stateParams.classId;
	var url = classId
		? 'api/order/'+className+'/'+classId
		: 'api/order/'+className;
	var orders = {};

	$rootScope.activeIcon = 'browse';

	$scope.currentElement = null;
	$scope.parentElement = null;
	$scope.parentList = [];
	$scope.currentItem = null;
	$scope.elementList = [];
	$scope.empty = false;

	$scope.moveUp = function() {
		$('select[name=list]').each(function() {
			if(this.selectedIndex > 0) {
				var i = this.selectedIndex;

				var t = this.options[i].text;
				this.options[i].text = this.options[i - 1].text;
				this.options[i - 1].text = t;

				var v = this.options[i].value;
				this.options[i].value = this.options[i - 1].value;
				this.options[i - 1].value = v;

				this.options[i].selected = false;
				this.options[i - 1].selected = true;

				var order1 = orders[this.options[i].value];
				var order2 = orders[this.options[i - 1].value];

				orders[this.options[i].value] = order2;
				orders[this.options[i - 1].value] = order1;
			}
		});
	};

	$scope.moveDown = function() {
		$('select[name=list]').each(function() {
			if(this.selectedIndex < (this.options.length - 1) && this.selectedIndex != -1) {
				var i = this.selectedIndex;

				var t = this.options[i].text;
				this.options[i].text = this.options[i + 1].text;
				this.options[i + 1].text = t;

				var v = this.options[i].value;
				this.options[i].value = this.options[i + 1].value;
				this.options[i + 1].value = v;

				this.options[i].selected = false;
				this.options[i + 1].selected = true;

				var order1 = orders[this.options[i].value];
				var order2 = orders[this.options[i + 1].value];

				orders[this.options[i].value] = order2;
				orders[this.options[i + 1].value] = order1;
			}
		});
	};

	$scope.moveFirst = function() {
		$('select[name=list]').each(function() {
			if(this.selectedIndex > 0) {
				for(var i = this.selectedIndex; i > 0; i--) {
					$scope.moveUp();
				}
			}
		});
	};

	$scope.moveLast = function() {
		$('select[name=list]').each(function() {
			if(this.selectedIndex > -1) {
				for(var i = this.selectedIndex; i < (this.options.length - 1); i++) {
					$scope.moveDown();
				}
			}
		});
	};

	$scope.save = function() {
		$.blockUI();

		$http({
			method: 'POST',
			url: 'api/order/'+className,
			data: {orders: orders}
		}).then(
			function(response) {
				if (response.data.state == 'ok') {
					$rootScope.refreshTree();
				} else {
					Alert.message('Некорректные параметры.');
				}
				$.unblockUI();
			},
			function(error) {
				console.log(error);
				$.unblockUI();
			}
		);
	};

	$http({
		method: 'GET',
		url: url
	}).then(
		function(response) {
			if (response.data.currentElement) {
				$scope.currentElement = response.data.currentElement;
				$scope.parentElement = response.data.parentElement;
				$scope.parentList = response.data.parentList;
			}
			$scope.currentItem = response.data.currentItem;
			$scope.elementList = response.data.elementList;
			if ($scope.elementList.length) {
				var k = 0;
				for (var i in $scope.elementList) {
					var element = $scope.elementList[i];
					orders[element.classId] = k;
					k++;
				}
			} else {
				$scope.empty = true;
			}
		},
		function(error) {
			console.log(error);
		}
	);

	$scope.up = function() {
		if ($scope.currentElement) {
			$state.go('base.browseElement', {classId: $scope.currentElement.classId});
		} else {
			$state.go('base.browse');
		}
	};
});