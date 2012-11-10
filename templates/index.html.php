
	<div class="row" id="stream-content">
		<div class="span9 content">

			<?php if (!empty($_SESSION['alert'])) : ?>
			<div class="infobox">
			<div class="alert alert-<?php echo $_SESSION['alert']['type'] ?>">
				<button type="button" class="close" data-dismiss="alert">x</button>
				<?php echo $_SESSION['alert']['msg'] ?>
			</div>
			</div>
			<?php unset($_SESSION['alert']) ?>
			<?php endif; ?>

			<?php if (isset($_SESSION['user'])) : ?>
			<div class="post-form">
				<div class="post-form-inner">
				<form method="post" action="<?php echo SITE_BASE_URL ?>post">
				<?php if (isset($post_edit)) : ?>
					<textarea class="span9" rows="4" name="post[content]" placeholder="What's on your mind?"><?php if (isset($post_edit)) echo $posts[0]['raw'] ?></textarea>
					<label class="checkbox inline"><input type="checkbox" name="private" value="1"> Private</label>
					<input type="hidden" name="post[id]" value="<?php echo $posts[0]['id'] ?>">
					<button type="submit" class="btn pull-right">Update</button>
				<?php else: ?>
					<textarea class="span9" rows="4" name="post[content]" placeholder="What's on your mind?"></textarea>
					<label class="checkbox inline"><input type="checkbox" name="private" value="1"> Private</label>
					<button type="submit" class="btn pull-right">Post</button>
				<?php endif; ?>
				</form>
				</div>
			</div>
			<?php endif; ?>


			<?php if (isset($channel_name)): ?>
			<div class="infobox" >
			<div class="alert alert-info">
			Showing posts from the #<strong><?php echo $channel_name ?></strong> channel. <a href="<?php echo SITE_BASE_URL ?>">Show all posts</a>
			</div>
			</div>
			<?php endif; ?>


			<div class="posts">
				<?php foreach ($posts as $post): ?>
				<div class="post">
					<?php echo $post['content'] ?>
					<div class="post-permalink">

						<a href="<?php echo SITE_BASE_URL ?>posts/<?php echo $post['id'] ?>"><?php echo $post['created_at'] ?></a>

						<?php if (isset($_SESSION['user'])) : ?>
							-
							<a href="https://twitter.com/share?url=<?php echo urlencode(SITE_BASE_URL."posts/{$post['id']}") ?>&text=<?php echo urlencode($post['raw']) ?>" target="_blank">Share on Twitter</a>
							-
							<a href="http://www.facebook.com/sharer.php?s=100&p[title]=<?php echo urlencode(ltrim($post['title'], '# ')) ?>&p[url]=<?php echo urlencode(SITE_BASE_URL."posts/{$post['id']}") ?>&p[summary]=<?php echo urlencode($post['raw']) ?>" target="_blank">Share on Facebook</a>
							-
							<a href="https://plus.google.com/share?url=<?php echo urlencode(SITE_BASE_URL."posts/{$post['id']}") ?>" target="_blank">Share on Google+</a>

						<?php endif; ?>

					</div>
				</div>
				<?php endforeach; ?>
			</div>

		</div>

		<div class="span3 sidebar">

			<ul class="unstyled channels">
				<li style="border-left: 10px solid #<?php echo dopplr_color("Home") ?>;"><a href="<?php echo SITE_BASE_URL ?>" class="channel <?php if (!isset($channel_name) and !isset($post_edit)) echo 'active' ?>"><!-- i class="icon-home <?php if (!isset($channel_name) and !isset($post_edit)) echo 'icon-white' ?>"></i --> Home</a></li>
				<?php foreach ($channels as $channel): ?>

					<?php if (isset($channel_name) and ($channel_name == $channel['name'])): ?>
					<li style="border-left: 10px solid #<?php echo dopplr_color($channel['name']) ?>;"><a class="channel active" href="<?php echo SITE_BASE_URL ?>channels/<?php echo $channel['name'] ?>"><!-- i class="icon-chevron-left  icon-white"></i --> <span class="hash">#</span><?php echo $channel['name'] ?></a></li>
					<?php else: ?>
					<li style="border-left: 10px solid #<?php echo dopplr_color($channel['name']) ?>;"><a class="channel" href="<?php echo SITE_BASE_URL ?>channels/<?php echo $channel['name'] ?>"><!-- i class="icon-tag"></i --> <span class="hash">#</span><?php echo $channel['name'] ?></a></li>
					<?php endif; ?>

				<?php endforeach; ?>
			</ul>

		</div>

	</div>