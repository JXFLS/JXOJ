<?php
	$content = $blog['content'];
	
    $blog_type = $blog['type'] == 'B' ? 'blog' : 'slide';
?>
<?php if (!$show_title_only): ?>
<div class="panel panel-default mdui-hoverable">
    <div class="panel-heading">
        <div style="display:inline-block"><b>By <?= getUserLink($blog['poster']) ?></b> &nbsp;&nbsp; <?= $blog['post_time'] ?></div>
        <div class="text-right" style="float:right">
		    <ul class="list-inline bot-buffer-no">
		    	<li>
		    	<?php foreach (queryBlogTags($blog['id']) as $tag): ?>
		    		<?php echoBlogTag($tag) ?>
		    	<?php endforeach ?>
		    	</li>
  		    	<li><a href="<?= HTML::blog_url($blog['poster'].'/blog/'.$blog['id']) ?>">去博客查看</a></li>
  		    	<?php if (Auth::check() && (isSuperUser(Auth::user()) || Auth::id() == $blog['poster'])): ?>
		    	<li><a href="<?=HTML::blog_url($blog['poster'].'/'.$blog_type.'/'.$blog['id'].'/write')?>">修改</a></li>
		    	<li><a href="<?=HTML::blog_url($blog['poster'].'/blog/'.$blog['id'].'/delete')?>">删除</a></li>
		    	<?php endif ?>
  		    	<li><?= getClickZanBlock('B', $blog['id'], $blog['zan']) ?></li>
		    </ul>
        </div>
	</div>
	<div class="panel-body">
		<?php if ($blog_type == 'blog'): ?>
		<article><?= $content ?></article>
		<?php elseif ($blog_type == 'slide'): ?>
		<article>
			<div class="embed-responsive embed-responsive-16by9">
				<iframe class="embed-responsive-item" src="<?= HTML::blog_url(UOJContext::userid(), '/slide/'.$blog['id']) ?>"></iframe>
			</div>
			<div class="text-right top-buffer-sm">
				<a class="btn btn-default btn-md" href="<?= HTML::blog_url(UOJContext::userid(), '/slide/'.$blog['id']) ?>"><span class="glyphicon glyphicon-fullscreen"></span> 全屏</a>
			</div>
		</article>
		<?php endif ?>
	</div>
</div>
<?php endif ?>
