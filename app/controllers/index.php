<?php
	$blogs = DB::selectAll("select blogs.id, title, poster, post_time from important_blogs, blogs where is_hidden = 0 and important_blogs.blog_id = blogs.id order by level desc, important_blogs.blog_id desc limit 5");
?>
<?php echoUOJPageHeader('UOJ') ?>
<div class="row">
	<div class="col-sm-12 col-md-8">
		<div class="panel panel-default">
  			<div class="panel-heading">
    			<h3 class="panel-title"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> <?= UOJLocale::get('announcements') ?></h3>
  			</div>
			<table class="table table-fixed">
				<thead>
					<tr>
						<th style="width:60%">标题</th>
						<th style="width:20%">作者</th>
						<th style="width:20%">时间</th>
					</tr>
				</thead>
		  		<tbody>
					<?php $now_cnt = 0; ?>
					<?php foreach ($blogs as $blog): ?>
						<?php
							$now_cnt++;
							$new_tag = '';
							if ((time() - strtotime($blog['post_time'])) / 3600 / 24 <= 7) {
								$new_tag = '<sup style="color:red">&nbsp;new</sup>';
							}
						?>
						<tr>
							<td><a href="/blog/<?= $blog['id'] ?>"><?= $blog['title'] ?></a><?= $new_tag ?></td>
							<td><?= getUserLink($blog['poster']) ?></td>
							<td><small><?= $blog['post_time'] ?></small></td>
						</tr>
					<?php endforeach ?>
					<!--<?php for ($i = $now_cnt + 1; $i <= 5; $i++): ?>
						<tr><td colspan="233">&nbsp;</td></tr>
					<?php endfor ?>-->
				</tbody>
			</table>
		</div>
		<div class="panel panel-default">
  			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-signal" aria-hidden="true"></span> <?= UOJLocale::get('top rated') ?></h3>
			</div>
			<?php echoRanklist(array('echo_full' => '', 'top10' => '')) ?>
		</div>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="panel panel-default">
  			<div class="panel-heading">
    			<h3 class="panel-title"><span class="glyphicon glyphicon-heart" aria-hidden="true"></span> 一言（ヒトコト）</h3>
  			</div>
			<div class="panel-body">
			<div id="hitokoto-content"></div>
			<div id="hitokoto-from"></div>
  			</div>
		</div>
		<div class="panel panel-default">
  			<div class="panel-heading">
    			<h3 class="panel-title"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> 近期比赛</h3>
  			</div>
			  <?php
	requirePHPLib('form');
	
	$upcoming_contest_name = null;
	$upcoming_contest_href = null;
	$rest_second = 1000000;
	function echoContest($contest) {
		global $myUser, $upcoming_contest_name, $upcoming_contest_href, $rest_second;
		
		$contest_name_link = <<<EOD
<a href="/contest/{$contest['id']}">{$contest['name']}</a>
EOD;
		genMoreContestInfo($contest);
		if ($contest['cur_progress'] == CONTEST_NOT_STARTED) {
			$cur_rest_second = $contest['start_time']->getTimestamp() - UOJTime::$time_now->getTimestamp();
			if ($cur_rest_second < $rest_second) {
				$upcoming_contest_name = $contest['name'];
				$upcoming_contest_href = "/contest/{$contest['id']}";
				$rest_second = $cur_rest_second;
			}
			if ($myUser != null && hasRegistered($myUser, $contest)) {
				$contest_name_link .= '<sup><a style="color:green">'.UOJLocale::get('contests::registered').'</a></sup>';
			} else {
				$contest_name_link .= '<sup><a style="color:red" href="/contest/'.$contest['id'].'/register">'.UOJLocale::get('contests::register').'</a></sup>';
			}
		} elseif ($contest['cur_progress'] == CONTEST_IN_PROGRESS) {
			$contest_name_link .= '<sup><a style="color:blue" href="/contest/'.$contest['id'].'">'.UOJLocale::get('contests::in progress').'</a></sup>';
		} elseif ($contest['cur_progress'] == CONTEST_PENDING_FINAL_TEST) {
			$contest_name_link .= '<sup><a style="color:blue" href="/contest/'.$contest['id'].'">'.UOJLocale::get('contests::pending final test').'</a></sup>';
		} elseif ($contest['cur_progress'] == CONTEST_TESTING) {
			$contest_name_link .= '<sup><a style="color:blue" href="/contest/'.$contest['id'].'">'.UOJLocale::get('contests::final testing').'</a></sup>';
		} elseif ($contest['cur_progress'] == CONTEST_FINISHED) {
			$contest_name_link .= '<sup><a style="color:grey" href="/contest/'.$contest['id'].'/standings">'.UOJLocale::get('contests::ended').'</a></sup>';
		}
		
		$last_hour = round($contest['last_min'] / 60, 2);
		
		$click_zan_block = getClickZanBlock('C', $contest['id'], $contest['zan']);
		echo '<tr>';
		echo '<td>', $contest_name_link, '</td>';
		echo '<td>', '<a href="'.HTML::timeanddate_url($contest['start_time'], array('duration' => $contest['last_min'])).'">'.$contest['start_time_str'].'</a>', '</td>';
		echo '</tr>';
	}
	$table_header = '';
	$table_header .= '<tr>';
	$table_header .= '<th style="width:60%">'.UOJLocale::get('contests::contest name').'</th>';
	$table_header .= '<th style="width:40%;">'.UOJLocale::get('contests::start time').'</th>';
	$table_header .= '</tr>';
	echoLongTable(array('*'), 'contests', "status != 'finished'", 'order by id desc', $table_header,
		echoContest,
		array('page_len' => 100)
	);

	if ($rest_second <= 86400) {
		echo <<<EOD
EOD;
	}
?>
		</div>
		<div class="panel panel-default">
  			<div class="panel-heading">
    			<h3 class="panel-title"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> 搜索</h3>
  			</div>
			<div class="panel-body">
				<div class="input-group">
      				<input type="text" class="form-control" placeholder="搜索题目（正在咕咕）">
      				<span class="input-group-btn">
        				<button class="btn btn-default" type="button">搜索</button>
      				</span>
    			</div>
  			</div>
		</div>
	</div>
</div>

<?php echoUOJPageFooter() ?>

<script>
    $.get('https://v1.hitokoto.cn/?c=a', function (data) {
      if (typeof data === 'string') data = JSON.parse(data);
      $('#hitokoto-content').css('display', '').text(data.hitokoto);
      if (data.from) {
        $('#hitokoto-from').css('display', '').text('——' + data.from);
      }
    });
</script>