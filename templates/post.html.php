
<?php if (isset($alert, $alert_type)) : ?>
<div class="alert alert-<?php echo $alert_type ?>">
	<?php echo $alert ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['user'])) : ?>
	<script>
		var $loggedInUser = "<?php echo $_SESSION['user']['email'] ?>";
	</script>
	<a id="signout" href="#" class="persona-button orange"><span>Sign out <?php echo $_SESSION['user']['email'] ?></span></a>


	<form method="post">
		<legend>New Post</legend>

		<label></label>
		<textarea class="span12" rows="16"></textarea>
		<span class="help-block"></span>

		<button type="submit" class="btn">Publish</button>
	</form>


<?php else: ?>
	<script>
		var $loggedInUser = null;
	</script>
	<a id="signin" href="#" class="persona-button"><span>Sign in with Mozilla Persona</span></a>
<?php endif; ?>

