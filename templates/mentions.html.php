				<div class="">
					<div class="post-box">
						<div class="content">
							<h3 id="comments">Recent Mentions</h3>
							<?php foreach($mentions as $mention): ?>

								<div class="response">
									<a class="h-card" href="<?php echo hcard_url_fallback($mention['author_url'], $mention['source']) ?>">
										<img alt="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" title="<?php echo hcard_author_name_fallback($mention['author_name']) ?>" width="22" src="<?php echo hcard_photo_fallback($mention['author_photo']) ?>" />
									</a>
									<?php echo hcard_author_name_fallback($mention['author_name']) ?> <?php echo mention_type_past_tense_linked($mention['type'], $mention['target'], $mention['id']) ?> a post <?php echo comment_permalink_fallback($mention['published'], $mention['source']) ?>.
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>