; <?php /*

[blog/archives]

label = "Blog: Archives Sidebar"
icon = rss

limit[label] = "Hide older than (months)"
limit[type] = numeric
limit[initial] = 24

[blog/headlines]

label = "Blog: Headlines"
icon = rss

limit[label] = "Number of Posts"
limit[type] = numeric
limit[initial] = 10

tag[label] = "Tag (optional)"
tag[type] = select
tag[require] = "apps/blog/lib/Functions.php"
tag[callback] = "blog_get_tags"

dates[label] = "Show dates"
dates[type] = select
dates[require] = "apps/blog/lib/Functions.php"
dates[callback] = "blog_yes_no"

[blog/thumbnail-sidebar]

label = "Blog: Thumbnail Sidebar"
icon = rss

[blog/bymonth]

label = "Blog: Headlines by Month"
icon = rss

tag[label] = "Tag (optional)"
tag[type] = select
tag[require] = "apps/blog/lib/Functions.php"
tag[callback] = "blog_get_tags"

[blog/tags]

label = "Blog: Tag Cloud"
icon = tags

[blog/rssviewer]

label = "Blog: RSS Viewer"
icon = rss

url[label] = RSS Link
url[type] = text
url[url] = 1
url[message] = Please enter a valid URL.

[blog/postsfeed]

label = "Blog: Latest Posts"
icon = clock-o

number[label] = "Number of Posts"
number[type] = numeric
number[initial] = 5

tag[label] = "Tag (optional)"
tag[type] = select
tag[require] = "apps/blog/lib/Functions.php"
tag[callback] = "blog_get_tags"

; */ ?>
