<?php
if ($myUser == null) {
	header("Location: /login");
	die();
}
	if (!validateUInt($_GET['id']) || !($blog = queryBlog($_GET['id']))) {
		become404Page();
	}
	
	redirectTo(HTML::blog_url($blog['poster'], $_SERVER["REQUEST_URI"]));
?>
