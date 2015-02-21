favorites.directive('favorite', function (Favorite, Helper) {
	return {
		restrict: "E",
		replace: true,
		scope: {
			classId: "=",
		},
		templateUrl: Helper.templatePath('components/favorites/favorite'),
		link: function (scope, element, attrs) {
			scope.isFavorite = function() {
				return Favorite.isFavorite(scope.classId);
			};

			scope.toggle = function() {
				Favorite.toggle(scope.classId);
			};
		}
	};
});