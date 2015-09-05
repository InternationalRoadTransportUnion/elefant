<?php

/**
 * Displays a breadcrumb menu using a bulleted list that you can
 * apply CSS to with the `breadcrumb` class.
 *
 * In PHP code, call it like this:
 *
 *     echo $this->run ('navigation/breadcrumb');
 *
 * In a template, call it like this:
 *
 *     {! navigation/breadcrumb !}
 *
 * Also available in the dynamic objects menu as "Navigation: Breadcrumb".
 *
 * A CSS styling example:
 *
 *     .breadcrumb {
 *         list-style-type: none;
 *         margin: 0;
 *         padding: 0;
 *     }
 *
 *     .breadcrumb li {
 *         list-style-type: none;
 *         margin: 0;
 *         padding: 0;
 *         display: inline;
 *     }
 *
 *     .breadcrumb li:before {
 *         content: " / ";
 *     }
 *
 *     .breadcrumb li:first-child:before {
 *         content: "";
 *     }
 */

$n = Link::nav ();
$path = $n->path ($page->id, true);
$home_id = conf ('I18n', 'multilingual') ? $i18n->language : 'index';
$home = array ($home_id => __ ('Home'));
$path = ($path) ? $path : $home;
if (isset ($path[$home_id])) {
	$path[$home_id] = __ ('Home');
} else {
	$path = array_merge ($home, $path);
}

echo "<ul class=\"breadcrumb\">\n";
foreach ($path as $id => $title) {
	if ($id != $page->id) {
		printf ("<li>%s <span class=\"divider\">/</span></li>\n", Link::make ($id, $title));
	} else {
		printf ("<li class=\"current\">%s</li>\n", $title);
	}
}
echo '</ul>';
