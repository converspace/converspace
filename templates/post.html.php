

<?php if (isset($_SESSION['user'])) : ?>
	<script>
		var $loggedInUser = "<?php echo $_SESSION['user']['email'] ?>";
	</script>
	<a id="signout" href="#" class="persona-button orange"><span>Sign out <?php echo $_SESSION['user']['email'] ?></span></a>
<?php else: ?>
	<script>
		var $loggedInUser = null;
	</script>
	<a id="signin" href="#" class="persona-button"><span>Sign in with Mozilla Persona</span></a>
<?php endif; ?>

