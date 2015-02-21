favorites.factory('Favorite', function ($rootScope, $http) {
	$rootScope.favoriteList = [];

	return {
		clear: function() {
			$rootScope.favoriteList = [];
		},
		getList: function() {
			if ($rootScope.favoriteList.length) {
				return false;
			}

			$http({
				method: 'GET',
				url: 'api/favorites'
			}).then(
				function(response) {
					$rootScope.favoriteList = response.data.favoriteList;
				},
				function(error) {
					console.log(error);
				}
			);
		},
		isFavorite: function(classId) {
			for (var i in $rootScope.favoriteList) {
				var favorite = $rootScope.favoriteList[i];

				if (classId === favorite.classId) {
					return true;
				}
			}

			return false;
		},
		toggle: function(classId) {
			$http({
				method: 'POST',
				url: 'api/favorites/'+classId
			}).then(
				function(response) {
					var result = response.data.result;
					var favorite = response.data.favorite;

					if (result === 'add') {
						$rootScope.favoriteList.push(favorite);
					} else {
						for (var i in $rootScope.favoriteList) {
							var classId = $rootScope.favoriteList[i].classId;
							if (classId === favorite.classId) {
								$rootScope.favoriteList.splice(i, 1);
							}
						}
					}
				},
				function(error) {
					console.log(error);
				}
			);
		}
	};
});