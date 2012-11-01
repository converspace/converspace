
<?php if (isset($alert, $alert_type)) : ?>
<div style="padding: 20px 0;">
<div class="alert alert-<?php echo $alert_type ?>">
	<?php echo $alert ?>
</div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['persona'])) : ?>
	<script>
		var $loggedInUser = "<?php echo $_SESSION['persona']['email'] ?>";
	</script>
	<div style="padding: 20px 0;">
	<a id="signout" href="#" class="persona-button orange"><span>Sign out <?php echo $_SESSION['persona']['email'] ?></span></a>
	</div>
	<div>
	<?php if (isset($_SESSION['user'])) : ?>
		<form method="post">
			<textarea class="span12" rows="16" name="post" placeholder="What's on your mind?"></textarea>
			<span class="help-block"></span>

			<label class="checkbox inline">
				<input type="checkbox" name="private" value="1"> Private
			</label>
			<button type="submit" class="btn pull-right">Post</button>
		</form>
	<?php endif; ?>
	</div>

<?php else: ?>
	<script>
		var $loggedInUser = null;
	</script>
	<a id="signin" href="#" class="persona-button"><span>Sign in with Mozilla Persona</span></a>
<?php endif; ?>

