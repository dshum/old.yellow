form.factory('FormInterceptor', function ($q, $rootScope, $injector, Alert) {
	return {
		request: function (config) {
			if (config.checkForm) {
				Alert.onSubmit();
			}

			return config;
		},
		response: function (response) {
			var state = $injector.get('$state');

			if (response.data.state === 'error_element_not_found') {
				state.go('base.browse');
			} else if (response.data.state === 'error_element_access_denied') {
				state.go('base.browse');
			} else if (response.data.state === 'error_element_move_access_denied') {
				Alert.message('У вас нет прав на перемещение этого элемента.');
			} else if (response.data.state === 'error_element_delete_access_denied') {
				Alert.message('У вас нет прав на удаление этого элемента.');
			} else if (response.data.state === 'error_element_delete_restricted') {
				Alert.message('Невозможно удалить этот элемент, пока существуют связанные с ним элементы.');
			} else if (response.data.state === 'error_element_restore_access_denied') {
				Alert.message('У вас нет прав на восстановление этого элемента.');
			} else if (response.data.state === 'error_element_save_failed') {
				Alert.message('Произошла ошибка сохранения данных.');
			} else if (response.data.state === 'error_admin_access_denied') {
				state.go('base.browse');
			} else if (response.data.state === 'error_group_not_found') {
				state.go('base.users');
			} else if (response.data.state === 'error_group_access_denied') {
				state.go('base.users');
			} else if (response.data.state === 'error_user_not_found') {
				state.go('base.users');
			} else if (response.data.state === 'error_user_access_denied') {
				state.go('base.users');
			} else if (response.data.message) {
				Alert.message(response.data.message);
			} else if (response.config.checkForm) {
				Alert.onResponse(response);
			}

			if (response.data.error) {
				console.log(response.data.error);
			}

			return response;
		},
		responseError: function(rejection) {
			return $q.reject(rejection);
		}
	};
});