<?php

	$channels = db_get_channels($authorized);

?>
	<div class="row" id="stream-content">
		<div class="span9 content">

			<?php if ($alert = session_alert()) : ?>
			<div class="infobox">
			<div class="alert alert-<?php echo $alert['type'] ?>">
				<?php echo $alert['msg'] ?>
			</div>
			</div>
			<?php endif; ?>

			<?php if (isset($_SESSION['user'])) : ?>
			<div class="post-form">
				<div class="post-form-inner">
				<form method="post" action="<?php echo SITE_BASE_URL ?>post">
				<?php if (isset($individual_post)) : ?>
					<textarea class="span9" rows="4" name="post[content]" placeholder="What's on your mind?"><?php if (isset($individual_post)) echo $posts[0]['raw'] ?></textarea>
					<label class="checkbox inline"><input type="checkbox" name="private" value="1"> Private</label>
					<input type="hidden" name="post[id]" value="<?php echo $posts[0]['id'] ?>">
					<button type="submit" class="btn pull-right">Update</button>
				<?php else: ?>
					<textarea class="span9" rows="4" name="post[content]" placeholder="What's on your mind?"></textarea>
					<div class="clearfix"><button type="submit" class="btn pull-right">Post</button></div>
				<?php endif; ?>
				</form>
				</div>
			</div>
			<?php endif; ?>


			<?php if ($posts) : ?>
			<?php if (!empty($channel_name)): ?>
			<div class="infobox" >
			<div class="alert alert-info">
			Showing posts from the #<strong><?php echo $channel_name ?></strong> channel. <a href="<?php echo SITE_BASE_URL ?>">Show all posts</a>
			</div>
			</div>
			<?php endif; ?>
			<?php endif; ?>


			<div class="posts">
				<?php if ($posts) : ?>
				<?php foreach ($posts as $post): ?>
				<div class="post">
					<?php echo $post['content'] ?>
					<div class="post-permalink">

						<a href="<?php echo SITE_BASE_URL.$post['id'] ?>"><?php echo date('j M Y', strtotime($post['created_at'])); ?></a>

						<?php if (isset($_SESSION['user'])) : ?>
							-
							<a href="https://twitter.com/share?url=<?php echo urlencode(SITE_BASE_URL.$post['id']) ?>&text=<?php echo urlencode($post['raw']) ?>" target="_blank">Share on Twitter</a>
							-
							<a href="http://www.facebook.com/sharer.php?s=100&p[title]=<?php echo urlencode(ltrim($post['title'], '# ')) ?>&p[url]=<?php echo urlencode(SITE_BASE_URL.$post['id']) ?>&p[summary]=<?php echo urlencode($post['raw']) ?>" target="_blank">Share on Facebook</a>
							-
							<a href="https://plus.google.com/share?url=<?php echo urlencode(SITE_BASE_URL.$post['id']) ?>" target="_blank">Share on Google+</a>

						<?php endif; ?>

					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<?php if ($posts) : ?>
			<ul class="pager">
				<?php if (!isset($individual_post) and isset($pager['after'])): ?>
				<li class="previous"><a href="?after=<?php echo $pager['after'] ?>">&larr; Newer</a></li>
				<?php elseif (isset($pager['after'])): ?>
				<li class="previous"><a href="<?php echo SITE_BASE_URL.$pager['after'] ?>">&larr; Newer</a></li>
				<?php endif; ?>
				<?php if (!isset($individual_post) and isset($pager['before'])): ?>
				<li class="next"><a href="?before=<?php echo $pager['before'] ?>">Older &rarr;</a></li>
				<?php elseif (isset($pager['before'])): ?>
				<li class="next"><a href="<?php echo SITE_BASE_URL.$pager['before'] ?>">Older &rarr;</a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>

		</div>

		<div class="span3 sidebar">

			<ul class="unstyled channels">
				<li style="border-left: 10px solid #<?php echo dopplr_color("Home") ?>;"><a href="<?php echo SITE_BASE_URL ?>" class="channel <?php if (empty($channel_name) and !isset($individual_post)) echo 'active' ?>">Home</a></li>

				<?php foreach ($channels as $channel): ?>
					<?php if (!empty($channel_name) and ($channel_name == $channel['name'])): ?>
					<li style="border-left: 10px solid #<?php echo dopplr_color($channel['name']) ?>;"><a class="channel active" href="<?php echo SITE_BASE_URL.$channel['name'].'/' ?>"><span class="deem">#</span><?php echo $channel['name'] ?></a></li>
					<?php else: ?>
					<li style="border-left: 10px solid #<?php echo dopplr_color($channel['name']) ?>;"><a class="channel" href="<?php echo SITE_BASE_URL.$channel['name'].'/' ?>"><!-- i class="icon-tag"></i --> <span class="deem">#</span><?php echo $channel['name'] ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>

		</div>

	</div>