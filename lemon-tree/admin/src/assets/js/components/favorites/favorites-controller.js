favorites.controller('FavoritesController', function(
	$scope, $state, Favorite
) {
	$scope.menuOptions = [
		['Редактировать', function ($itemScope) {
			$state.go('base.editElement', {classId: $itemScope.favorite.classId});
		}],
		['Открыть', function ($itemScope) {
			$state.go('base.browseElement', {classId: $itemScope.favorite.classId});
		}],
		null,
		['Переместить', function ($itemScope) {

		}],
		['Удалить', function ($itemScope) {

		}],
		null,
		['Избранное', function ($itemScope) {
			Favorite.toggle($itemScope.favorite.classId);
		}],
	];
});