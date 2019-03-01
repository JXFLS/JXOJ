<header class="navbar navbar-default navbar-fixed-top nav-fixed" role="navigation" id="header">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" id="logo" href="<?= HTML::url('/') ?>">JXOJ</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav nav-border">
				<li><a href="<?= HTML::url('/') ?>" <?php if ($_SERVER['REQUEST_URI']=='/') echo"class=\"nav-active\"" ?>>首页</a></li>
				<li><a href="/problems"<?php if ($_SERVER['REQUEST_URI']=='/problems') echo"class=\"nav-active\"" ?>><?= UOJLocale::get('problems') ?></a></li>
				<li><a href="/contests"<?php if ($_SERVER['REQUEST_URI']=='/contests') echo"class=\"nav-active\"" ?>><?= UOJLocale::get('contests') ?></a></li>
				<li><a href="/submissions"<?php if ($_SERVER['REQUEST_URI']=='/submissions') echo"class=\"nav-active\"" ?>><?= UOJLocale::get('submissions') ?></a></li>
				<li><a href="/hacks"<?php if ($_SERVER['REQUEST_URI']=='/hacks') echo"class=\"nav-active\"" ?>><?= UOJLocale::get('hacks') ?></a></li>
				<li><a href="/blogs"<?php if ($_SERVER['REQUEST_URI']=='/blogs') echo"class=\"nav-active\"" ?>><?= UOJLocale::get('blogs') ?></a></li>
				<li><a href="/faq"<?php if ($_SERVER['REQUEST_URI']=='/faq') echo"class=\"nav-active\"" ?>><?= UOJLocale::get('help') ?></a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if (Auth::check()): ?>
        			<li class="dropdown">
          				<a href="#" class="dropdown-toggle nav-border" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="uoj-username" data-rating="<?= Auth::user()['rating'] ?>" data-link="0"><?= Auth::id() ?></span> <?= $new_msg_tot_html ?><span class="caret"></span></a>
          				<ul class="dropdown-menu">
            				<li><a href="<?= HTML::url('/user/profile/' . Auth::id()) ?>"><?= UOJLocale::get('my profile') ?></a></li>
            				<li><a href="<?= HTML::url('/user/msg') ?>"><?= UOJLocale::get('private message') ?>&nbsp;&nbsp;<?= $new_user_msg_num_html ?></a></li>
							<li><a href="<?= HTML::url('/user/system-msg') ?>"><?= UOJLocale::get('system message') ?>&nbsp;&nbsp;<?= $new_system_msg_num_html ?></a></li>
							<?php if (isSuperUser(Auth::user())): ?>
								<li ><a href="<?= HTML::url('/super-manage') ?>"><?= UOJLocale::get('system manage') ?></a></li>
							<?php endif ?>
          				</ul>
					</li>
					<li role="presentation" class="nav-border"><a href="<?= HTML::url('/logout?_token='.crsf_token()) ?>"><?= UOJLocale::get('logout') ?></a></li>
				<?php else: ?>
					<li role="presentation" class="nav-border"><a href="<?= HTML::url('/login') ?>"><?= UOJLocale::get('login') ?></a></li>
					<li role="presentation" class="nav-border"><a href="<?= HTML::url('/register') ?>"><?= UOJLocale::get('register') ?></a></li>
				<?php endif ?>
      		</ul>
		</div><!--/.nav-collapse -->
	</div>
</header>

<script>
    $(function(){
        $(window).scroll(function() {
            if($(window).scrollTop() >= 5) {
                $("#header").addClass("uoj-header");
			}
			else {
				$("#header").removeClass("uoj-header");
			}
        })
    }
 );
</script>