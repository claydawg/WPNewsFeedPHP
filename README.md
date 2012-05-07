WPNewsFeedPHP
=============

WPNewsFeedPHP was built based on this RSS Parser:  http://www.suttree.com/code/rss/rss.phps
It utilizes an XML cache based on a segment of the GCalPHP script:  https://github.com/media-uk/GCalPHP

Using WPNewsFeedPHP, you can provide a URL to any RSS feed (I built this to pull from a WordPress site).  It will spit out the title and a link (it can do more, but that's just what I needed).
The cache is checked on page-load.  If it's older than 1 hour (3600*H where H is the number of hours you wish to update the cache.