tree.directive('tree', function(
	$http, $window, $state, Helper, Favorite
) {
    return {
		restrict: 'E',
		replace: true,
		templateUrl: Helper.templatePath('components/tree/tree'),
		link: function(scope, element, attrs) {
			var node = attrs.node;
			var tree = element.parent().data('tree');

			scope.itemList = [];
			scope.itemElementList = [];
			scope.treeCount = [];
			scope.tree = [];
			scope.treeView = [];
			scope.subTree = [];

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
				['Переместить', function ($itemScope) {

				}],
				['Удалить', function ($itemScope) {

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