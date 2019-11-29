<header class="navbar navbar-default navbar-fixed-top nav-fixed" role="navigation" id="header">
	<div class="nav-content container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" id="logo" href="<?= HTML::url('/') ?>"><img alt="JXOJ" src="/pictures/jxoj-logo.png"></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav nav-border" id="header-content">
				<li><a href="<?= HTML::blog_url(UOJContext::userid(), '/blog')?>" class="nav-active"><?= UOJContext::userid() ?> 的博客</a></li>
				<li><a href="/user/profile/<?= UOJContext::userid() ?>">关于我</a></li>
				<li><a href="<?= HTML::url('/') ?>"><?= UOJConfig::$data['profile']['oj-name-short'] ?></a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if (Auth::check()): ?>
        			<li class="dropdown">
          				<a href="#" class="dropdown-toggle nav-border" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="uoj-username" data-rating="<?= Auth::user()['rating'] ?>" data-link="0" data-vip="<?= /*Auth::user()['vip'] || */Auth::user()['usergroup']=='S'/* || Auth::user()['contribution']>10 */?>"><?= Auth::id() ?></span> <?= $new_msg_tot_html ?><span class="caret"></span></a>
          				<ul class="dropdown-menu">
            				<li><a href="<?= HTML::url('/user/profile/' . Auth::id()) ?>"><span class="glyphicon glyphicon-user"></span> <?= UOJLocale::get('my profile') ?></a></li>
            				<li><a href="<?= HTML::url('/user/msg') ?>"><span class="glyphicon glyphicon-comment"></span> <?= UOJLocale::get('private message') ?>&nbsp;&nbsp;<?= $new_user_msg_num_html ?></a></li>
							<li><a href="<?= HTML::url('/user/system-msg') ?>"><span class="glyphicon glyphicon-bell"></span> <?= UOJLocale::get('system message') ?>&nbsp;&nbsp;<?= $new_system_msg_num_html ?></a></li>
							<?php if (isSuperUser(Auth::user())): ?>
								<li ><a href="<?= HTML::url('/super-manage') ?>"><span class="glyphicon glyphicon-cog"></span> <?= UOJLocale::get('system manage') ?></a></li>
							<?php endif ?>
							<li><a href="<?= HTML::url('/logout?_token='.crsf_token()) ?>"><span class="glyphicon glyphicon-off"></span> <?= UOJLocale::get('logout') ?></a></li>
          				</ul>
					</li>
				<?php else: ?>
					<li role="presentation" class="nav-border"><a href="<?= HTML::url('/login') ?>"><?= UOJLocale::get('login') ?></a></li>
					<li role="presentation" class="nav-border"><a href="<?= HTML::url('/register') ?>"><?= UOJLocale::get('register') ?></a></li>
				<?php endif ?>
      		</ul>
		</div><!--/.nav-collapse -->
	</div>
</header>

<script type="text/javascript">
	var uojBlogUrl = '<?= HTML::blog_url(UOJContext::userid(), '')?>';
</script>

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