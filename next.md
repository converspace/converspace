* Properly parse microformats to extract h-entry and h-card.
* remove trailing tags implementation.
* Allow for reposting and liking posts that don't have h-card and h-entry.
* Implement webmention undo for unlike, etc. (based on source returning a 404 or not finding target there)

* Pagination rel markup
* fullscreen mode for writing posts
* Mark post as deleted (doing this directly in the db for now)
* Show post counts for tags
* Atom/RSS feed
* PuSH Publisher
* PuSH Subscriber
* Microformats/Atom/RSS reader
* Should tags be case-insensitive? How will they be stored and displayed if they are?
* HATEOAS API