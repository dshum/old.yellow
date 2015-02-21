tree.directive('subtree', function ($compile) {
	return {
		restrict: "E",
		replace: true,
		scope: {
			node: "=",
			tree: "=",
			show: "=",
		},
		template: '<div class="padding dnone"></div>',
		link: function(scope, element, attrs) {
			if (scope.tree) {
				var child = $('<tree node="'+scope.node+'"></tree>');
				element.data('tree', scope.tree);
				element.attr('node', scope.node);
				element.append(child);
				if (scope.show) {
					element.show();
				}
				$compile(element.contents())(scope);
			}
		}
	};
});