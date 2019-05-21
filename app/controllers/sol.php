<?php

    requirePHPLib('form');

    if (!validateUInt($_GET['id']) || !($problem = queryProblemBrief($_GET['id']))) {
		become404Page();
    }

    if (!isProblemVisibleToUser($problem, $myUser)) {
        become404Page();
    }

    $add_blog_id = new UOJForm('add_blog_id');
	$add_blog_id->addInput('blogid', 'text', '博客ID', '',
        function ($x) {
            if (!validateUInt($x)) return 'ID不合法';
            $qBlog = queryBlog($x);
            if (!$qBlog) return '博客不存在';
            if ($qBlog['is_permitted']) return '博客已经通过审核';
            if ($qBlog['need_permit'] && $qBlog['sol']==$_GET['id']) return '博客已经提交过了';
            if ($qBlog['poster']!=Auth::id()) return '你怎么交着别人的题解啊';
	    	return '';
	    },
	    null
	);
	$add_blog_id->handle = function() {
        $blog_id = $_POST['blogid'];
        if (Auth::check() && (isSuperUser(Auth::user()))) {
            $qBlog = queryBlog($blog_id);
            if ($qBlog['is_permitted']==0) {
                $qUser = queryUser($qBlog['poster']); //一定相同
                $contri = $qUser['contribution']+1;
                DB::update("update user_info set contribution = {$contri} where username = '{$qBlog['poster']}'");
            }
            DB::update("update blogs set is_permitted = 1 , is_hidden = 0 , sol = {$_GET['id']} where id = {$blog_id}");
        }
        else {
            DB::update("update blogs set need_permit = 1 , is_hidden = 0 , sol = {$_GET['id']} where id = {$blog_id}");
        }
	};
    $add_blog_id->runAtServer();
    
    $del_blog_id = new UOJForm('del_blog_id');
	$del_blog_id->addInput('delblogid', 'text', '博客ID', '',
        function ($x) {
	    	if (!validateUInt($x)) return 'ID不合法';
	    	$qBlog = queryBlog($x);
            if (!$qBlog) return '博客不存在';
            if (!$qBlog['is_permitted']) return '博客尚未通过审核';
            if ($qBlog['poster']!=Auth::id()) return '你好坏啊，怎么删别人的题解';
	    	return '';
	    },
	    null
	);
	$del_blog_id->handle = function() {
        $blog_id2 = $_POST['delblogid'];
        if (Auth::check() && (isSuperUser(Auth::user()))) {
            if ($qBlog['is_permitted']==1) {
                $qUser = queryUser($qBlog['poster']); //一定相同
                $contri = $qUser['contribution']-1;
                DB::update("update user_info set contribution = {$contri} where username = '{$qBlog['poster']}'");
            }
            DB::update("update blogs set need_permit = 0 , is_permitted = 0 , is_hidden = 0 where id = {$blog_id2}");
        }
        else {
            DB::update("update blogs set need_permit = 0 , is_hidden = 0 where id = {$blog_id2}");
        }
	};
	$del_blog_id->runAtServer();

	$blogs_pag = new Paginator(array(
		'col_names' => array('*'),
		'table_name' => 'blogs',
		'cond' => "sol = '".$_GET['id']."' and is_hidden = 0 and is_permitted=1",
		'tail' => 'order by zan desc limit 5',
		'echo_full' => true
    ));
    
    $REQUIRE_LIB['mathjax'] = '';
    $REQUIRE_LIB['shjs'] = '';
    
    echoUOJPageHeader(HTML::stripTags($problem['title']) . ' - ' . '题解');
?>

<h1 class="text-center" style="margin-bottom:1em;">题解 - #<?= $problem['id']?>. <?= $problem['title'] ?></h1>

<div class="row">
    <div class="col-md-9">
        <div class="alert alert-info" role="alert">题解功能尚在测试阶段，有锅请私信 <a href="http://172.16.49.190/user/msg?enter=Llf">@Llf</a> 。</div>
        <?php if ($blogs_pag->isEmpty()): ?>
		<div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>暂无题解。</strong> <a href="<?= HTML::blog_url(Auth::id(), '/blog/new/write')?>" class="alert-link">去写题解</a>，然后回到此页面提交。
        </div>
        <?php else: ?>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>以下题解仅供学习参考使用。</strong> 抄袭题解对提高水平没有帮助，请自觉。
        </div>
		<?php foreach ($blogs_pag->get() as $blog): ?>
			<?php echoSol($blog, array('is_preview' => true)) ?>
		<?php endforeach ?>
		<?php endif ?>
        <div id="check" class="panel panel-default mdui-hoverable">
  			<div class="panel-heading">
    			<h3 class="panel-title"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 提交审核</h3>
  			</div>
			<div class="panel-body">
                <?php if (Auth::check() && (isSuperUser(Auth::user()))): ?>
                <div class="admin-warning">
                    您是管理员，提交审核后将直接展示。
                </div>
                <?php endif; ?>
                <?php $add_blog_id->printHTML();?>
  			</div>
        </div>
        
        <?php //if (Auth::check() && (isSuperUser(Auth::user()))): ?> <!--没验证用户所以只能由管理员取消-->
        <div id="del-check" class="panel panel-default mdui-hoverable">
  			<div class="panel-heading">
    			<h3 class="panel-title"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 取消审核</h3>
  			</div>
			<div class="panel-body">
                <?php if (Auth::check() && (isSuperUser(Auth::user()))): ?>
                <div class="admin-warning">
                    您是管理员，取消审核后将直接取消展示。
                </div>
                <?php endif; ?>
                <?php $del_blog_id->printHTML();?>
  			</div>
        </div>
        <?php //endif; ?>
    </div>
    <div class="col-md-3" style="position:sticky;top:5em">
        <div class="panel panel-default sol-right">
            <div class="panel-body">
                题目： <div style="float:right"><a href="/problem/<?= $problem['id']?>">#<?= $problem['id']?>. <?= $problem['title'] ?></a></div>
                <br>
                数量： <div style="float:right">0 篇</div>
                <br> <br>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a type="button" class="btn btn-danger" href="<?= HTML::blog_url(Auth::id(), '/blog/new/write')?>">撰写题解</a>
                    <a type="button" class="btn btn-primary" href="#check">提交审核</a>
                    <a type="button" class="btn btn-info" href="#">帮助</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echoUOJPageFooter() ?>