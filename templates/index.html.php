
			<?php if ($alert = session_alert()) : ?>
			<div class="infobox">
			<div class="alert alert-<?php echo $alert['type'] ?>">
				<?php echo $alert['msg'] ?>
			</div>
			</div>
			<?php endif; ?>


			<?php if (isset($_SESSION['user'])) : ?>
			<div>
				<div class="post-box">
					<form method="post" id="editor" action="<?php echo SITE_BASE_URL ?>post">
					<?php if (isset($individual_post)) : ?>
						<textarea rows="8" name="post[content]" placeholder="What's on your mind?"><?php if (isset($individual_post)) echo $posts[0]['raw'] ?></textarea>
						<input type="hidden" name="post[id]" value="<?php echo $posts[0]['id'] ?>">
						<div class="clearfix"><button type="submit">Update</button></div>
					<?php else: ?>
						<textarea rows="8" name="post[content]" placeholder="What's on your mind?"></textarea>
						<div class="clearfix"><button type="submit">Post</button></div>
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



			<?php if ($posts) : ?>
			<?php foreach ($posts as $post): ?>

			<div class="h-entry">
				<div class="post-box">
					<div class="content">
						<?php echo $post['content'] ?>
					</div>
					<div class="p-name p-summary e-content" style="display:none"><?php echo htmlspecialchars($post['plaintext'], ENT_QUOTES); ?></div>
					<div class="post-footer">

						<a class="u-url" title="Permalink" href="<?php echo SITE_BASE_URL.$post['id'] ?>"><i class="icon-time"></i> <time class="dt-published" datetime="<?php echo date(DATE_ATOM, strtotime($post['created_at'])); ?>"><?php echo date('j M Y', strtotime($post['created_at'])); ?></time></a>

						<a title="Comments" href="<?php echo SITE_BASE_URL.$post['id'].'#comments' ?>"><i class="icon-comment-alt"></i> <?php echo isset($mention_count[$post['id']]['in-reply-to']) ? $mention_count[$post['id']]['in-reply-to'] : 0 ?> Comments</a>
						<a title="Likes" href="<?php echo SITE_BASE_URL.$post['id'].'#likes' ?>"><i class="icon-thumbs-up-alt"></i> <?php echo isset($mention_count[$post['id']]['like']) ? $mention_count[$post['id']]['like'] : 0 ?> Likes</a>
						<a title="Shares" href="<?php echo SITE_BASE_URL.$post['id'].'#reposts' ?>"><i class="icon-retweet"></i> <?php echo isset($mention_count[$post['id']]['repost']) ? $mention_count[$post['id']]['repost'] : 0 ?> Reposts</a>
						<a title="Mentions" href="<?php echo SITE_BASE_URL.$post['id'].'#mentions' ?>"><i class="icon-hand-right"></i> <?php echo isset($mention_count[$post['id']]['mention']) ? $mention_count[$post['id']]['mention'] : 0 ?> Mentions</a>

					</div>
				</div>
			</div>

			<?php endforeach; ?>
			<?php endif; ?>


			<?php if (isset($individual_post)) : ?>
			<div id="responses">
				<div class="">
					<div class="post-box">
						<div class="content">
							<h3 id="comments">Comments</h3>
							<?php foreach(get_webmentions($posts[0]['id'], 'in-reply-to') as $mention): ?>

								<div class="response" id="<?php echo "mention_{$mention['id']}" ?>">
									<a class="h-card" href="<?php echo hcard_url_fallback($mention['author_url'], $mention['source']) ?>">
										<img alt="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" title="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" width="22" src="<?php echo hcard_photo_fallback($mention['author_photo']) ?>" />
									</a>
									<?php echo hcard_author_fallback($mention['author_name'], $mention['author_url'], $mention['source']) ?> commented on this <?php echo comment_permalink_fallback($mention['published'], $mention['source']) ?>

									<div class="content">
										<?php echo htmlspecialchars($mention['content'], ENT_QUOTES); ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="">
					<div class="post-box">
						<div class="content">
							<h3 id="likes">Likes</h3>
							<?php foreach(get_webmentions($posts[0]['id'], 'like') as $mention): ?>

								<div class="response" id="<?php echo "mention_{$mention['id']}" ?>">
									<a class="h-card" href="<?php echo hcard_url_fallback($mention['author_url'], $mention['source']) ?>">
										<img alt="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" title="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" width="22" src="<?php echo hcard_photo_fallback($mention['author_photo']) ?>" />
									</a>
									<?php echo hcard_author_fallback($mention['author_name'], $mention['author_url'], $mention['source']) ?> liked this <?php echo comment_permalink_fallback($mention['published'], $mention['source']) ?>.
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="">
					<div class="post-box">
						<div class="content">
							<h3 id="reposts">Reposts</h3>
							<?php foreach(get_webmentions($posts[0]['id'], 'repost') as $mention): ?>

								<div class="response" id="<?php echo "mention_{$mention['id']}" ?>">
									<a class="h-card" href="<?php echo hcard_url_fallback($mention['author_url'], $mention['source']) ?>">
										<img alt="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" title="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" width="22" src="<?php echo hcard_photo_fallback($mention['author_photo']) ?>" />
									</a>
									<?php echo hcard_author_fallback($mention['author_name'], $mention['author_url'], $mention['source']) ?> reposted this <?php echo comment_permalink_fallback($mention['published'], $mention['source']) ?>.
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="">
					<div class="post-box">
						<div class="content">
							<h3 id="mentions">Mentions</h3>
							<?php foreach(get_webmentions($posts[0]['id'], 'mention') as $mention): ?>

								<div class="response" id="<?php echo "mention_{$mention['id']}" ?>">
									<a class="h-card" href="<?php echo hcard_url_fallback($mention['author_url'], $mention['source']) ?>">
										<img alt="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" title="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" width="22" src="<?php echo hcard_photo_fallback($mention['author_photo']) ?>" />
									</a>
									<?php echo hcard_author_fallback($mention['author_name'], $mention['author_url'], $mention['source']) ?> mentioned this <?php echo comment_permalink_fallback($mention['published'], $mention['source']) ?>.
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>