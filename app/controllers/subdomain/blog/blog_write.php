<?php
	requirePHPLib('form');
	
	if (!UOJContext::hasBlogPermission()) {
		become403Page();
	}
	if (isset($_GET['id'])) {
		if (!validateUInt($_GET['id']) || !($blog = queryBlog($_GET['id'])) || !UOJContext::isHisBlog($blog)) {
			become404Page();
		}
	} else {
		$blog = DB::selectFirst("select * from blogs where poster = '".UOJContext::user()['username']."' and type = 'B' and is_draft = true");
	}

	$sol_id = $_GET['sol'];
	
	$blog_editor = new UOJBlogEditor();
	$blog_editor->name = 'blog';
	if ($blog && isset($sol_id)) {
		$blog_editor->cur_data = array(
			'title' => $blog['title'],
			'content_md' => $blog['content_md'],
			'content' => $blog['content'],
			'tags' => queryBlogTags($blog['id']),
			'sol' => $sol_id,
			'is_hidden' => $blog['is_hidden']
		);
	} else if ($blog && !isset($sol_id)) {
		$blog_editor->cur_data = array(
			'title' => $blog['title'],
			'content_md' => $blog['content_md'],
			'content' => $blog['content'],
			'tags' => queryBlogTags($blog['id']),
			'sol' => $blog['sol'],
			'is_hidden' => $blog['is_hidden']
		);
	} else if (!$blog && isset($sol_id)){
		$blog_editor->cur_data = array(
			'title' => '新博客',
			'content_md' => '',
			'content' => '',
			'tags' => array(),
			'sol' => $sol_id,
			'is_hidden' => true
		);
	} else {
		$blog_editor->cur_data = array(
			'title' => '新博客',
			'content_md' => '',
			'content' => '',
			'tags' => array(),
			'sol' => null,
			'is_hidden' => true
		);
	}
	if ($blog && !$blog['is_draft']) {
		$blog_editor->blog_url = "/blog/{$blog['id']}";
	} else {
		$blog_editor->blog_url = null;
	}
	
	function updateBlog($id, $data) {
		DB::update("update blogs set title = '".DB::escape($data['title'])."', content = '".DB::escape($data['content'])."', content_md = '".DB::escape($data['content_md'])."', is_hidden = {$data['is_hidden']} where id = {$id}");
	}
	function insertBlog($data) {
		DB::insert("insert into blogs (title, content, content_md, poster, is_hidden, is_draft, post_time) values ('".DB::escape($data['title'])."', '".DB::escape($data['content'])."', '".DB::escape($data['content_md'])."', '".Auth::id()."', {$data['is_hidden']}, {$data['is_draft']}, now())");
	}
	
	$blog_editor->save = function($data) {
		global $blog;
		$ret = array();
		if ($blog) {
			if ($blog['is_draft']) {
				if ($data['is_hidden']) {
					updateBlog($blog['id'], $data);
				} else {
					deleteBlog($blog['id']);
					insertBlog(array_merge($data, array('is_draft' => 0)));
					$blog = array('id' => DB::insert_id(), 'tags' => array());
					$ret['blog_write_url'] = "/blog/{$blog['id']}/write";
					$ret['blog_url'] = "/blog/{$blog['id']}";
				}
			} else {
				updateBlog($blog['id'], $data);
			}
		} else {
			$blog = array('id' => DB::insert_id(), 'tags' => array());
			if ($data['is_hidden']) {
				insertBlog(array_merge($data, array('is_draft' => 1)));
			} else {
				insertBlog(array_merge($data, array('is_draft' => 0)));
				$ret['blog_write_url'] = "/blog/{$blog['id']}/write";
				$ret['blog_url'] = "/blog/{$blog['id']}";
			}
		}
		if ($data['tags'] !== $blog['tags']) {
			DB::delete("delete from blogs_tags where blog_id = {$blog['id']}");
			foreach ($data['tags'] as $tag) {
				DB::insert("insert into blogs_tags (blog_id, tag) values ({$blog['id']}, '".DB::escape($tag)."')");
			}
		}
		if ($data['sol']!=null) {
			/*if (!$blog['is_permitted']) { //没有通过
				DB::update("update blogs set need_permit = 1 , sol = {$data['sol']} where id = {$blog['id']}");
			}
			else if ($blog['sol']!=$data['sol']) { //更换审核
				$qUser = queryUser($blog['poster']);
                $contri = $qUser['contribution']-1;
                DB::update("update user_info set contribution = {$contri} where username = '{$blog['poster']}'");
				DB::update("update blogs set is_permitted = 0 , need_permit = 1 , sol = {$data['sol']} where id = {$blog['id']}");
			}*/
			if ($data['sol']!=$blog['sol'] || $data['content']!=$blog['content'] || $data['title']!=$blog['title']) {
				if ($blog['is_permitted']) {
					$qUser = queryUser($blog['poster']);
            	    $contri = $qUser['contribution']-1;
            	    DB::update("update user_info set contribution = {$contri} where username = '{$blog['poster']}'");
				}
				DB::update("update blogs set is_permitted = 0 , need_permit = 1 , sol = {$data['sol']} where id = {$blog['id']}");
			}
		}
		else {
			if ($blog['is_permitted']) { //已审核通过则减去贡献
				$qUser = queryUser($blog['poster']);
                $contri = $qUser['contribution']-1;
                DB::update("update user_info set contribution = {$contri} where username = '{$blog['poster']}'");
			}
			DB::update("update blogs set is_permitted = 0 , need_permit = 0 , sol = 0 where id = {$blog['id']}");
		}
		return $ret;
	};
	
	$blog_editor->runAtServer();
?>
<?php echoUOJPageHeader('写博客') ?>
<div class="text-right" style="color:red">
<?php if ($blog['is_permitted'] && !isset($sol_id)) { ?>
这篇题解已通过题目 #<?=$blog['sol']?> 的审核，如果修改将重新审核。
<?php } else if (!$blog['is_permitted'] && isset($sol_id)) {?>
正在提交对题目 #<?=$sol_id?> 的审核。
<?php } else if ($blog['is_permitted'] && isset($sol_id)) {?>
这篇题解已通过题目 #<?=$blog['sol']?> 的审核，您确定要修改为题目 #<?=$sol_id?> 吗？
<?php } ?>
</div>
<?php $blog_editor->printHTML() ?>
<!--<h1>
由于技术升级原因，JXOJ博客系统由6.3 23:20-6.5 00:00期间维护，期间不能使用任何博客功能，正常做题不受影响。
<br>
造成不便，敬请谅解。
</h1>-->
<?php echoUOJPageFooter() ?>
