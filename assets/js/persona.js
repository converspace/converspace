$(document).ready(function() {

	$('#signin').click(function (e) {
		e.preventDefault();
		navigator.id.request({siteName: 'Converspace'});
	});

	$('#signout').click(function (e) {
		e.preventDefault();
		navigator.id.logout();
	});

	navigator.id.watch({
		loggedInUser: $loggedInUser,
		onlogin: function ($assertion) {
			$.post(
				'persona-verifier',
				{assertion: $assertion},
				function(data) {
					window.location.reload();
				}
			);
		},
		onlogout: function () {
			$.post(
				'signout',
				{},
				function() {
					window.location.reload();
				}
			);
		}
	});

});