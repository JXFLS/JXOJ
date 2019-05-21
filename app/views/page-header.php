<?php
	$new_user_msg_num = DB::selectCount("select count(*) from user_msg where receiver = '".Auth::id()."' and read_time is null");
	$new_system_msg_num = DB::selectCount("select count(*) from user_system_msg where receiver = '".Auth::id()."' and read_time is null");
	$new_msg_tot = $new_user_msg_num + $new_system_msg_num;
		
	if ($new_user_msg_num == 0) {
		$new_user_msg_num_html = '';
	} else {
		$new_user_msg_num_html = '<span class="badge">'.$new_user_msg_num.'</span>';
	}
	if ($new_system_msg_num == 0) {
		$new_system_msg_num_html = '';
	} else {
		$new_system_msg_num_html = '<span class="badge">'.$new_system_msg_num.'</span>';
	}
	if ($new_msg_tot == 0) {
		$new_msg_tot_html = '';
	} else {
		$new_msg_tot_html = '<sup><span class="badge">'.$new_msg_tot.'</span></sup>';
	}
	
	if (!isset($PageMainTitle)) {
		$PageMainTitle = UOJConfig::$data['profile']['oj-name'];
	}
	if (!isset($PageMainTitleOnSmall)) {
		$PageMainTitleOnSmall = UOJConfig::$data['profile']['oj-name-short'];
	}
	if (!isset($ShowPageHeader)) {
		$ShowPageHeader = true;
	}
?>
<!DOCTYPE html>
<html lang="<?= UOJLocale::locale() ?>">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?php if (isset($_GET['locale'])): ?>
		<meta name="robots" content="noindex, nofollow" />
		<?php endif ?>
		<title><?= isset($PageTitle)&&$PageTitle!="UOJ" ? $PageTitle : UOJConfig::$data['profile']['oj-name-short'] ?> - <?= $PageMainTitle ?></title>

		<script type="text/javascript">uojHome = '<?= HTML::url('/') ?>'</script>

		<!-- Bootstrap core CSS -->
		<?= HTML::css_link('/css/bootstrap.min.css') ?>
		<!-- Bootstrap theme -->

		<!-- Custom styles for this template -->
		<?= HTML::css_link('/css/uoj-theme.css') ?>
		
		<!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
		<?= HTML::js_src('/js/jquery.min.js') ?>
	
		<!-- jQuery autosize -->
		<?= HTML::js_src('/js/jquery.autosize.min.js') ?>
		<script type="text/javascript">
			$(document).ready(function() {
				$('textarea').autosize();
			});
		</script>
		
		<!-- jQuery cookie -->
		<?= HTML::js_src('/js/jquery.cookie.min.js') ?>
		
		<!-- jQuery modal -->
		<?= HTML::js_src('/js/jquery.modal.js') ?>
		
		<!-- jQuery tag canvas -->
		<?php if (isset($REQUIRE_LIB['tagcanvas'])): ?>
		<?= HTML::js_src('/js/jquery.tagcanvas.min.js') ?>
		<?php endif ?>
		
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<?= HTML::js_src('/js/bootstrap.min.js') ?>
		
		<!-- Color converter -->
		<?= HTML::js_src('/js/color-converter.min.js') ?>
		
		<!-- uoj -->
		<?= HTML::js_src('/js/uoj.js?v=2016.8.15') ?>
		
		<!-- LAB -->
		<?= HTML::js_src('/js/LAB.min.js') ?>

		<!-- UOJ ico -->
		<link rel="shortcut icon" href="<?= HTML::url('/pictures/jxoj-logo.png') ?>" />
		
		<?php if (isset($REQUIRE_LIB['blog-editor'])): ?>
		<!-- UOJ blog editor -->
		<?php $REQUIRE_LIB['jquery.hotkeys'] = '' ?>
		<?php $REQUIRE_LIB['switch'] = '' ?>
		<?= HTML::css_link('/js/codemirror/lib/codemirror.css') ?>
		<?= HTML::css_link('/css/blog-editor.css') ?>
		<?= HTML::js_src('/js/marked.js') ?>
		<?= HTML::js_src('/js/blog-editor/blog-editor.js?v=2015.7.9') ?>
		<?= HTML::js_src('/js/codemirror/lib/codemirror.js') ?>
		<?= HTML::js_src('/js/codemirror/addon/mode/overlay.js') ?>
		<?= HTML::js_src('/js/codemirror/addon/selection/active-line.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/xml/xml.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/gfm/gfm.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/markdown/markdown.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/javascript/javascript.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/css/css.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/htmlmixed/htmlmixed.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/clike/clike.js') ?>
		<?= HTML::js_src('/js/codemirror/mode/pascal/pascal.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['slide-editor'])): ?>
		<!-- UOJ slide editor -->
		<?= HTML::css_link('/js/codemirror/lib/codemirror.css') ?>
		<?= HTML::css_link('/css/slide-editor.css') ?>
		<?= HTML::js_src('/js/slide-editor/slide-editor.js') ?>
		<?= HTML::js_src('/js/codemirror/lib/codemirror.js') ?>
		<?= HTML::js_src('/js/codemirror/addon/mode/overlay.js') ?>
		<?= HTML::js_src('/js/codemirror/addon/selection/active-line.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['md5'])): ?>
		<!-- MD5 -->
		<?= HTML::js_src('/js/md5.min.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['dialog'])): ?>
		<!-- Bootstrap dialog -->
		<?= HTML::css_link('/css/bootstrap-dialog.min.css') ?>
		<?= HTML::js_src('/js/bootstrap-dialog.min.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['switch'])): ?>
		<!-- Bootstrap switch -->
		<?= HTML::css_link('/css/bootstrap-switch.min.css') ?>
		<?= HTML::js_src('/js/bootstrap-switch.min.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['mathjax'])): ?>
		<!-- MathJax -->
		<script type="text/x-mathjax-config">
			MathJax.Hub.Config({
				showProcessingMessages: false,
				tex2jax: {
					inlineMath: [["$", "$"], ["\\\\(", "\\\\)"]],
					processEscapes:true
				},
				menuSettings: {
					zoom: "Hover"
    			}
			});
		</script>
		<?= HTML::js_src('/js/MathJax/MathJax.js?config=TeX-AMS_HTML') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['jquery.form'])): ?>
		<!-- jquery form -->
		<?= HTML::js_src('/js/jquery.form.min.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['jquery.hotkeys'])): ?>
		<!-- jquery hotkeys -->
		<?= HTML::js_src('/js/jquery.hotkeys.js') ?>
		<?php endif ?>

		<?php if (isset($REQUIRE_LIB['flot'])): ?>
		<!-- flot -->
		<?= HTML::js_src('/js/jquery.flot.min.js') ?>
		<?= HTML::js_src('/js/jquery.flot.time.min.js') ?>
		<?= HTML::js_src('/js/jquery.flot.resize.min.js') ?>
		<?php
			$REQUIRE_LIB['colorhelpers'] = "";
		?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['colorhelpers'])): ?>
		<!-- colorhelpers -->
		<?= HTML::js_src('/js/jquery.colorhelpers.min.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['morris'])): ?>
		<!-- morris -->
		<?= HTML::js_src('/js/morris.min.js') ?>
		<?= HTML::css_link('/css/morris.css') ?>
		<?php $REQUIRE_LIB['raphael'] = "" ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['raphael'])): ?>
		<!-- raphael -->
		<?= HTML::js_src('/js/raphael.min.js') ?>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['shjs'])): ?>
		<!-- shjs -->
		<?= HTML::css_link('/css/sh_typical.min.css') ?>
		<?= HTML::js_src('/js/sh_main.min.js') ?>
		<script type="text/javascript">$(document).ready(function(){sh_highlightDocument()})</script>
		<?php endif ?>
		
		<?php if (isset($REQUIRE_LIB['ckeditor'])): ?>
		<!-- ckeditor -->
		<?= HTML::js_src('/js/ckeditor/ckeditor.js') ?>
		<?php endif ?>
		
		<script type="text/javascript">
		before_window_unload_message = null;
		$(window).on('beforeunload', function() {
			if (before_window_unload_message !== null) {
			    return before_window_unload_message;
			}
		});
		</script>
	</head>
	<body role="document">
		<?php uojIncludeView($PageNav) ?>
		<div class="uoj-content">
</body>