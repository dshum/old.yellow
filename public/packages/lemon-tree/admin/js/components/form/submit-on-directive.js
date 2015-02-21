form.directive('submitOn', function() {
    return function(scope, element, attrs) {
		element.attr('onsubmit', 'return false');
		scope.$on(attrs.submitOn, function() {
			setTimeout(function() {
				element.trigger('submit');
			});
		});
    };
});