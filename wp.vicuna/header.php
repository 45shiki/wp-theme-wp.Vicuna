<?php vicuna_xml_declaration()?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php bloginfo('language')?>" xml:lang="<?php bloginfo('language')?>">
<head profile="http://purl.org/net/ns/metaprof">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset')?>" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta name="author" content="<?php bloginfo('name')?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url')?>" title="RSS 2.0" />
	<link rel="alternate" type="application/atom+xml" href="<?php bloginfo('atom_url')?>" title="Atom cite contents" />
	<link rel="pingback" href="<?php bloginfo('pingback_url')?>" />
<?php wp_head()?>
	<title><?php vicuna_page_head_title()?></title>
</head>
