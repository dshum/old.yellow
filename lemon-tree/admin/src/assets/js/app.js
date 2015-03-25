var app = angular.module('adminApp', [
	'ngAnimate',
	'ui.router', 'ui.bootstrap',
	'mgcrea.ngStrap.typeahead',
	'mgcrea.ngStrap.datepicker', 'mgcrea.ngStrap.timepicker',
	'HelperCtrl', 'uiTinymceCtrl',
	'ModalCtrl', 'PropertyCtrl', 'ContextMenuCtrl',
	'AlertCtrl', 'AuthCtrl', 'FormCtrl',
	'NavbarCtrl', 'TreeCtrl', 'FavoritesCtrl',
	'LoginCtrl', 'UsersCtrl',
	'BrowseCtrl', 'OrderCtrl', 'EditCtrl',
	'FilemanagerCtrl', 'PluginCtrl',
]);

app.run(function(
	$rootScope, $state, $document, $window,
	AuthToken, Login, Alert, Favorite, Helper
){
	$rootScope.$on('$stateChangeStart', function(
		event, toState, toParams, fromState, fromParams
	){
		$rootScope.currentState = toState;
		$rootScope.locationSearch = null;

		if (AuthToken.isAuthenticated()) {
			Login.user(
				function(response) {
					$rootScope.loggedUser = response.data.user;
					$rootScope.$broadcast('loggedUser', response.data.user);
					setTimeout(function() {
						Favorite.getList();
					}, 1000);
				}
			);
			if (toState.name == 'simple.login') {
				event.preventDefault();
				$state.go('base.browse');
			}
		} else {
			if (toState.name != 'simple.login') {
				Favorite.clear();
				event.preventDefault();
				$state.go('simple.login');
			}
		}
	});

	$document
		.on('keypress', function(event){
			return Alert.handleKeys(event);
		})
		.on('keydown', function(event){
			return Alert.handleKeys(event);
		});

	$rootScope.Helper = Helper;
});

app.config([
	'$stateProvider', '$urlRouterProvider', '$httpProvider',
	'Helper',
	function(
		$stateProvider, $urlRouterProvider, $httpProvider,
		Helper
	) {
		var templatePath = Helper.templatePath;

		$httpProvider.interceptors.push('AuthInterceptor');
		$httpProvider.interceptors.push('FormInterceptor');

		$urlRouterProvider.otherwise('/');

		$stateProvider
		.state('simple', {
			templateUrl: templatePath('components/simple/layout')
		})
		.state('base', {
			templateUrl: templatePath('components/base/layout')
		})
		.state('simple.login', {
			url: '/login',
			templateUrl: templatePath('components/login/login'),
			controller: 'LoginController'
		})
		.state('base.browse', {
			url: '/',
			templateUrl: templatePath('components/browse/browse'),
			controller: 'BrowseController'
		})
		.state('base.browseElement', {
			url: '/browse/{classId:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/browse/browse'),
			controller: 'BrowseController'
		})
		.state('base.order', {
			url: '/order/{class:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/order/order'),
			controller: 'OrderController'
		})
		.state('base.orderElement', {
			url: '/order/{class:[A-Za-z\.0-9]+}/{classId:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/order/order'),
			controller: 'OrderController'
		})
		.state('base.editElement', {
			url: '/edit/{classId:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/edit/edit'),
			controller: 'EditController',
			data: {
				trashed: false
			}
		})
		.state('base.trashedElement', {
			url: '/trashed/{classId:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/edit/edit'),
			controller: 'EditController',
			data: {
				trashed: true
			}
		})
		.state('base.addElement', {
			url: '/add/{class:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/edit/edit'),
			controller: 'EditController'
		})
		.state('filemanager', {
			url: '/filemanager',
			templateUrl: templatePath('components/filemanager/filemanager'),
			controller: 'FilemanagerController'
		})
		.state('base.search', {
			url: '/search',
			templateUrl: templatePath('components/browse/search'),
			controller: 'SearchController'
		})
		.state('base.searchItem', {
			url: '/search/{class:[A-Za-z\.0-9]+}?options',
			templateUrl: templatePath('components/browse/search'),
			controller: 'SearchController'
		})
		.state('base.trash', {
			url: '/trash',
			templateUrl: templatePath('components/browse/trash'),
			controller: 'TrashController'
		})
		.state('base.trashItem', {
			url: '/trash/{class:[A-Za-z\.0-9]+}',
			templateUrl: templatePath('components/browse/trash'),
			controller: 'TrashController'
		})
		.state('base.users', {
			url: '/users',
			templateUrl: templatePath('components/users/users'),
			controller: 'UsersController'
		})
		.state('base.log', {
			url: '/log?id',
			templateUrl: templatePath('components/users/log'),
			controller: 'LogController'
		})
		.state('base.group', {
			url: '/group/{id:[0-9]+}',
			templateUrl: templatePath('components/users/group'),
			controller: 'GroupController'
		})
		.state('base.groupAdd', {
			url: '/group/add',
			templateUrl: templatePath('components/users/group'),
			controller: 'GroupController'
		})
		.state('base.groupUsers', {
			url: '/group/{id:[0-9]+}/users',
			templateUrl: templatePath('components/users/group-users'),
			controller: 'GroupUsersController'
		})
		.state('base.groupItems', {
			url: '/group/{id:[0-9]+}/items',
			templateUrl: templatePath('components/users/item-permissions'),
			controller: 'ItemPermissionsController'
		})
		.state('base.groupElements', {
			url: '/group/{id:[0-9]+}/elements',
			templateUrl: templatePath('components/users/element-permissions'),
			controller: 'ElementPermissionsController'
		})
		.state('base.user', {
			url: '/user/{id:[0-9]+}',
			templateUrl: templatePath('components/users/user'),
			controller: 'UserController'
		})
		.state('base.userAdd', {
			url: '/user/add',
			templateUrl: templatePath('components/users/user'),
			controller: 'UserController'
		})
		.state('base.profile', {
			url: '/profile',
			templateUrl: templatePath('components/users/profile'),
			controller: 'ProfileController'
		});

		$.blockUI.defaults.message = '<img src="packages/lemon-tree/admin/img/loader.gif" />';
		$.blockUI.defaults.css.border = 'none';
		$.blockUI.defaults.css.background = 'none';
		$.blockUI.defaults.css.zIndex = 10001;
		$.blockUI.defaults.overlayCSS.zIndex = 10000;
		$.blockUI.defaults.overlayCSS.opacity = 0.2;
		$.blockUI.defaults.fadeIn = 50;

	}
]);