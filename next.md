* Fix activity stream representation.
* Skeleton: First-person narrative for share, like and comment.
* Skeleton: New layout for content previews (share, like, comment)
* Skeleton: Fix for global h-card (now that each post doesn't have a p-author)
* Skeleton: Fix list styling.
* Migrate to Share from Repost
* Add html.head.title to post pages and lists.
* Pagination rel markup
* go back to namespaced URLs: /channels/foobar, /posts/22 with support for older routes.
* Atom/RSS feed
* Tag-based navigation
* Treat namespace of machinetags as regular tags so I also show posts with #like, #share, #in-reply-to (fix tag dedup bug)
* About page (I need this to replace my existing blogger based about page)
* Mention details UI.
** Properly parse microformats to extract h-entry and h-card
* Remove trailing tags implementation.
* Add quote UX? (See http://www.sandeep.io/53)
* Allow for reposting and liking posts that don't have h-card and h-entry (See http://www.sandeep.io/53)
* Implement webmention undo for unlike, etc. (based on source returning a 404 or not finding target there)

* prune webfonts

* fullscreen mode for writing posts
* Mark post as deleted (doing this directly in the db for now)
* Show post counts for tags
* PuSH Publisher
* PuSH Subscriber
* Microformats/Atom/RSS reader
* Should tags be case-insensitive? How will they be stored and displayed if they are?
* HATEOAS API