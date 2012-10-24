$(document).ready(function() {

	var $signin_button = $('#signin');
	var $signout_button = $('#signout');
	$signin_button.hide();
	$signout_button.hide();

	$('#signin').click(function (e) {
		e.preventDefault();
		navigator.id.request({siteName: 'Converspace'});
	});

	$('#signout').click(function (e) {
		e.preventDefault();
		navigator.id.logout();
	});

	navigator.id.watch({
		onlogin: function ($assertion) {
			$.post(
				'persona-verifier',
				{assertion: $assertion},
				function(data) {
					$signin_button.hide();
					$signout_button.show();
				}
			);
		},
		onlogout: function () {
			$.post(
				'signout',
				{},
				function() {
					$signin_button.show();
					$signout_button.hide();
				}
			);
		}
	});

});