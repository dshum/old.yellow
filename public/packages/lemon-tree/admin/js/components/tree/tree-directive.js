tree.directive('tree', function(
	$rootScope, $http, $window, $state, $injector,
	Helper, Favorite
) {
    return {
		restrict: 'E',
		replace: true,
		templateUrl: Helper.templatePath('components/tree/tree'),
		link: function(scope, element, attrs) {
			var node = attrs.node;
			var tree = element.parent().data('tree');

			var copy = function(classId) {
				$http({
					method: 'GET',
					url: 'api/element/'+classId
				}).then(
					function(response) {
						var currentElement = response.data.currentElement;
						var ones = response.data.ones;

						if ( ! currentElement) return false;

						var modal = $injector.get('$modal');

						var modalInstance = modal.open({
							templateUrl: 'copy.html',
							controller: 'CopyInstanceController',
							resolve: {
								data: function() {
									return {
										classId: classId,
										ones: ones,
										redirect: false
									};
								}
							}
						});
					},
					function(error) {
						console.log(error);
					}
				);
			};

			var move = function(classId) {
				$http({
					method: 'GET',
					url: 'api/element/'+classId
				}).then(
					function(response) {
						var currentElement = response.data.currentElement;
						var ones = response.data.ones;

						if ( ! currentElement) return false;

						var modal = $injector.get('$modal');

						var modalInstance = modal.open({
							templateUrl: 'move.html',
							controller: 'MoveInstanceController',
							resolve: {
								data: function() {
									return {
										classId: classId,
										ones: ones,
										reload: false
									};
								}
							}
						});
					},
					function(error) {
						console.log(error);
					}
				);
			};

			var drop = function(classId) {
				$.blockUI();

				$http({
					method: 'POST',
					url: 'api/delete/'+classId
				}).then(
					function(response) {
						if (response.data.state == 'ok') {
							$rootScope.refreshTree();
						}
						$.unblockUI();
					},
					function(error) {
						console.log(error);
						$.unblockUI();
					}
				);
			};

			scope.itemList = [];
			scope.itemElementList = [];
			scope.treeCount = [];
			scope.tree = [];
			scope.treeView = [];
			scope.subTree = [];

			scope.refreshTree = function() {
				$http({
					method: 'GET',
					url: 'api/tree'
				}).then(
					function(response) {
						scope.itemList = response.data.itemList;
						scope.itemElementList = response.data.itemElementList;
						scope.treeCount = response.data.treeCount;
						scope.subTree = response.data.subTree;
					},
					function(error) {
						console.log(error);
					}
				);
			};

			scope.isTreeView = function(classId) {
				if (typeof(scope.treeView[classId]) !== 'undefined') {
					return scope.treeView[classId];
				}
				scope.treeView[classId] =
					$window.localStorage.getItem('tree_'+classId)
					? true : false;
				return scope.treeView[classId];
			};

			scope.open = function(classId) {
				$window.localStorage.setItem('tree_'+classId, true);
				scope.treeView[classId] = true;
				$('.padding[node="'+classId+'"]').slideDown('fast');
			};

			scope.hide = function(classId) {
				$window.localStorage.removeItem('tree_'+classId);
				scope.treeView[classId] = false;
				$('.padding[node="'+classId+'"]').slideUp('fast');
			};

			scope.menuOptions = [
				['Редактировать', function ($itemScope) {
					$state.go('base.editElement', {classId: $itemScope.element.classId});
				}],
				['Открыть', function ($itemScope) {
					$state.go('base.browseElement', {classId: $itemScope.element.classId});
				}],
				null,
				['Копировать', function ($itemScope) {
					copy($itemScope.element.classId);
				}],
				['Переместить', function ($itemScope) {
					move($itemScope.element.classId);
				}],
				['Удалить', function ($itemScope) {
					drop($itemScope.element.classId);
				}],
				null,
				['Избранное', function ($itemScope) {
					Favorite.toggle($itemScope.element.classId);
				}],
			];

			if ( ! node) {
				$http({
					method: 'GET',
					url: 'api/tree'
				}).then(
					function(response) {
						scope.itemList = response.data.itemList;
						scope.itemElementList = response.data.itemElementList;
						scope.treeCount = response.data.treeCount;
						scope.subTree = response.data.subTree;
					},
					function(error) {
						console.log(error);
					}
				);
			} else if (tree) {
				scope.itemList = tree.itemList;
				scope.itemElementList = tree.itemElementList;
				scope.treeCount = tree.treeCount;
				scope.subTree = tree.subTree;
			}
		}
	};
});