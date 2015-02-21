var helper = angular.module('HelperCtrl', []);

helper.constant('Helper', {
	templatePath: function(name) {
		return 'packages/lemon-tree/admin/js/'+name+'.html';
	},
	selectCaseForNumber: function(number, cases) {
		if (
			(number % 10) == 1
			&& (number % 100) != 11
		) {
			return cases[0];
		} else if (
			(number % 10) > 1
			&& (number % 10) < 5
			&& (number % 100 < 10 || number % 100 > 20)
		) {
			return cases[1];
		}

		return cases[2];
	},
	toDate: function(datetime) {
		if ( ! datetime) return null;

		var parts = datetime.split(' ');
		var dates = parts[0].split('-');
		var hours = parts[1].split(':');

		var date = new Date(
			dates[0], dates[1] - 1, dates[2],
			hours[0], hours[1], hours[2]
		);

		return date;
	},
	toDateString: function(date) {
		if ( ! date) return null;

		var dateString =
			date.getFullYear()
			+'-'+('0' + (date.getMonth() + 1)).slice(-2)
			+'-'+('0' + date.getDate()).slice(-2);

		return dateString;
	}
});