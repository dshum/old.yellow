property.directive('property', function ($http, Helper) {
	return {
		restrict: "E",
		replace: true,
		scope: {
			type: "=",
			mode: "=",
			view: "=",
		},
		template: '<ng-include src="getTemplateUrl()"></ng-include>',
		link: function(scope, element, attrs) {
			scope.Helper = Helper;

			scope.getTemplateUrl = function() {
				return Helper.templatePath(
					'components/properties/'+scope.type+'/'+attrs.mode
				);
			};

			scope.getList = function(viewValue) {
				var params = {term: viewValue};
				return $http.get('api/hint/'+scope.view.relatedClass, {params: params})
					.then(function(response) {
						return response.data;
					});
			};

			scope.toggle = function(name) {
				var container = $('div[name="'+name+'"]');

				if(container.css('display') == 'block') {
					container.hide();
					container.children('input').attr('disabled', true);
					container.children('label').children('input').attr('disabled', true);
					scope.view.open = false;
				} else {
					container.children('input').removeAttr('disabled');
					container.children('label').children('input').removeAttr('disabled');
					container.show();
					container.children('input:text:first:not([bs-datepicker]):not([bs-typeahead])').focus();
					scope.view.open = true;
				}
			};
		}
	};
});