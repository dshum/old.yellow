alert.factory('Alert', function($rootScope, $injector) {
	return {
		handleKeys: function(event) {
			var code = event.keyCode || event.which;

			if (code == 83 && event.ctrlKey == true) {
				$rootScope.$broadcast('CtrlS');
				return false;
			}

			return true;
		},
		onSubmit: function() {
			$('.error').html(null).hide();

			$.blockUI();
		},
		onResponse: function(response) {
			var modal = $injector.get('$modal');

			if (errors = response.data.error) {
				var html = [];

				$('.error').each(function() {
					var name = $(this).attr('name');
					var label = $(this).attr('label');

					if (errors[name]) {
						for (var i in errors[name]) {
							var message = errors[name][i];

							html.push({
								label: label,
								message: message
							});

							$('.error[name="'+name+'"]').html(message);
						}
					}
				});

				if (html.length) {
					var modalInstance = modal.open({
						templateUrl: 'form.html',
						controller: 'ModalInstanceController',
						resolve: {
							data: function() {
								return {
									errors: html
								};
							},
						}
					});

					modalInstance.result.then(
						function() {},
						function() {
							$('.error').each(function() {
								if ($(this).html()) {
									$(this).fadeIn('fast');
								}
							});
						}
					);
				}
			}

			$.unblockUI();
		},
	};
});