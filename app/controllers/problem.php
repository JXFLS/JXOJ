<?php
	requirePHPLib('form');
	requirePHPLib('judger');
	
	if (!validateUInt($_GET['id']) || !($problem = queryProblemBrief($_GET['id']))) {
		become404Page();
	}
	
	$problem_content = queryProblemContent($problem['id']);
	
	$contest = validateUInt($_GET['contest_id']) ? queryContest($_GET['contest_id']) : null;
	if ($contest != null) {
		genMoreContestInfo($contest);
		$problem_rank = queryContestProblemRank($contest, $problem);
		if ($problem_rank == null) {
			become404Page();
		} else {
			$problem_letter = chr(ord('A') + $problem_rank - 1);
		}
	}
	
	$is_in_contest = false;
	$ban_in_contest = false;
	if ($contest != null) {
		if (!hasContestPermission($myUser, $contest)) {
			if ($contest['cur_progress'] == CONTEST_NOT_STARTED) {
				become404Page();
			} elseif ($contest['cur_progress'] == CONTEST_IN_PROGRESS) {
				if ($myUser == null || !hasRegistered($myUser, $contest)) {
					becomeMsgPage("<h1>比赛正在进行中</h1><p>很遗憾，您尚未报名。比赛结束后再来看吧～</p>");
				} else {
					$is_in_contest = true;
					DB::update("update contests_registrants set has_participated = 1 where username = '{$myUser['username']}' and contest_id = {$contest['id']}");
				}
			} else {
				$ban_in_contest = !isProblemVisibleToUser($problem, $myUser);
			}
		}
	} else {
		if (!isProblemVisibleToUser($problem, $myUser)) {
			become404Page();
		}
	}

	$submission_requirement = json_decode($problem['submission_requirement'], true);
	$problem_extra_config = getProblemExtraConfig($problem);
	$custom_test_requirement = getProblemCustomTestRequirement($problem);

	if ($custom_test_requirement && Auth::check()) {
		$custom_test_submission = DB::selectFirst("select * from custom_test_submissions where submitter = '".Auth::id()."' and problem_id = {$problem['id']} order by id desc limit 1");
		$custom_test_submission_result = json_decode($custom_test_submission['result'], true);
	}
	if ($custom_test_requirement && $_GET['get'] == 'custom-test-status-details' && Auth::check()) {
		if ($custom_test_submission == null) {
			echo json_encode(null);
		} else if ($custom_test_submission['status'] != 'Judged') {
			echo json_encode(array(
				'judged' => false,
				'html' => getSubmissionStatusDetails($custom_test_submission)
			));
		} else {
			ob_start();
			$styler = new CustomTestSubmissionDetailsStyler();
			if (!hasViewPermission($problem_extra_config['view_details_type'], $myUser, $problem, $submission)) {
				$styler->fade_all_details = true;
			}
			echoJudgementDetails($custom_test_submission_result['details'], $styler, 'custom_test_details');
			$result = ob_get_contents();
			ob_end_clean();
			echo json_encode(array(
				'judged' => true,
				'html' => getSubmissionStatusDetails($custom_test_submission),
				'result' => $result
			));
		}
		die();
	}
	
	$can_use_zip_upload = true;
	foreach ($submission_requirement as $req) {
		if ($req['type'] == 'source code') {
			$can_use_zip_upload = false;
		}
	}
	
	function handleUpload($zip_file_name, $content, $tot_size) {
		global $problem, $contest, $myUser, $is_in_contest;
		
		$content['config'][] = array('problem_id', $problem['id']);
		if ($is_in_contest && $contest['extra_config']["contest_type"]!='IOI' && !isset($contest['extra_config']["problem_{$problem['id']}"])) {
			$content['final_test_config'] = $content['config'];
			$content['config'][] = array('test_sample_only', 'on');
		}
		$esc_content = DB::escape(json_encode($content));

		$language = '/';
		foreach ($content['config'] as $row) {
			if (strEndWith($row[0], '_language')) {
				$language = $row[1];
				break;
			}
		}
		if ($language != '/') {
			Cookie::set('uoj_preferred_language', $language, time() + 60 * 60 * 24 * 365, '/');
		}
		$esc_language = DB::escape($language);
 		
		$result = array();
		$result['status'] = "Waiting";
		$result_json = json_encode($result);
		
		if ($is_in_contest) {
			DB::query("insert into submissions (problem_id, contest_id, submit_time, submitter, content, language, tot_size, status, result, is_hidden) values (${problem['id']}, ${contest['id']}, now(), '${myUser['username']}', '$esc_content', '$esc_language', $tot_size, '${result['status']}', '$result_json', 0)");
		} else {
			DB::query("insert into submissions (problem_id, submit_time, submitter, content, language, tot_size, status, result, is_hidden) values (${problem['id']}, now(), '${myUser['username']}', '$esc_content', '$esc_language', $tot_size, '${result['status']}', '$result_json', {$problem['is_hidden']})");
		}
 	}
	function handleCustomTestUpload($zip_file_name, $content, $tot_size) {
		global $problem, $contest, $myUser;
		
		$content['config'][] = array('problem_id', $problem['id']);
		$content['config'][] = array('custom_test', 'on');
		$esc_content = DB::escape(json_encode($content));

		$language = '/';
		foreach ($content['config'] as $row) {
			if (strEndWith($row[0], '_language')) {
				$language = $row[1];
				break;
			}
		}
		if ($language != '/') {
			Cookie::set('uoj_preferred_language', $language, time() + 60 * 60 * 24 * 365, '/');
		}
		$esc_language = DB::escape($language);
 		
		$result = array();
		$result['status'] = "Waiting";
		$result_json = json_encode($result);
		
		DB::insert("insert into custom_test_submissions (problem_id, submit_time, submitter, content, status, result) values ({$problem['id']}, now(), '{$myUser['username']}', '$esc_content', '{$result['status']}', '$result_json')");
 	}
	
	if ($can_use_zip_upload) {
		$zip_answer_form = newZipSubmissionForm('zip_answer',
			$submission_requirement,
			'uojRandAvaiableSubmissionFileName',
			'handleUpload');
		$zip_answer_form->extra_validator = function() {
			global $ban_in_contest;
			if ($ban_in_contest) {
				return '请耐心等待比赛结束后题目对所有人可见了再提交';
			}
			return '';
		};
		$zip_answer_form->succ_href = $is_in_contest ? "/contest/{$contest['id']}/submissions" : '/submissions';
		$zip_answer_form->runAtServer();
	}
	
	$answer_form = newSubmissionForm('answer',
		$submission_requirement,
		'uojRandAvaiableSubmissionFileName',
		'handleUpload');
	$answer_form->extra_validator = function() {
		global $ban_in_contest;
		if ($ban_in_contest) {
			return '请耐心等待比赛结束后题目对所有人可见了再提交';
		}
		return '';
	};
	$answer_form->succ_href = $is_in_contest ? "/contest/{$contest['id']}/submissions" : '/submissions';
	$answer_form->runAtServer();

	if ($custom_test_requirement) {
		$custom_test_form = newSubmissionForm('custom_test',
			$custom_test_requirement,
			function() {
				return uojRandAvaiableFileName('/tmp/');
			},
			'handleCustomTestUpload');
		$custom_test_form->appendHTML(<<<EOD
<div id="div-custom_test_result"></div>
EOD
		);
		$custom_test_form->succ_href = 'none';
		$custom_test_form->extra_validator = function() {
			global $ban_in_contest, $custom_test_submission;
			if ($ban_in_contest) {
				return '请耐心等待比赛结束后题目对所有人可见了再提交';
			}
			if ($custom_test_submission && $custom_test_submission['status'] != 'Judged') {
				return '上一个测评尚未结束';
			}
			return '';
		};
		$custom_test_form->ctrl_enter_submit = true;
		$custom_test_form->setAjaxSubmit(<<<EOD
function(response_text) {custom_test_onsubmit(response_text, $('#div-custom_test_result')[0], '{$_SERVER['REQUEST_URI']}?get=custom-test-status-details')}
EOD
		);
		$custom_test_form->submit_button_config['text'] = UOJLocale::get('problems::run');
		$custom_test_form->runAtServer();
	}
?>
<?php
	$REQUIRE_LIB['mathjax'] = '';
	$REQUIRE_LIB['shjs'] = '';
?>
<?php echoUOJPageHeader(HTML::stripTags($problem['title']) . ' - ' . UOJLocale::get('problems::problem')) ?>
<!--<div class="pull-right">
	<?= getClickZanBlock('P', $problem['id'], $problem['zan']) ?>
</div>-->

<?php if ($contest): ?>
<div class="row">
	<h1 class="col-md-3 text-left"><small><?= $contest['name'] ?></small></h1>
	<h1 class="col-md-7 text-center"><?= $problem_letter ?>. <?= $problem['title'] ?></h1>
	<div class="col-md-2 text-right" id="contest-countdown"></div>
</div>
<!--<a role="button" class="btn btn-info pull-right" href="/contest/<?= $contest['id'] ?>/problem/<?= $problem['id'] ?>/statistics"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('problems::statistics') ?></a>-->
<?php if ($contest['cur_progress'] <= CONTEST_IN_PROGRESS): ?>
<script type="text/javascript">
checkContestNotice(<?= $contest['id'] ?>, '<?= UOJTime::$time_now_str ?>');
$('#contest-countdown').countdown(<?= $contest['end_time']->getTimestamp() - UOJTime::$time_now->getTimestamp() ?>);
</script>
<?php endif ?>
<?php else: ?>
<h1 class="text-center">#<?= $problem['id']?>. <?= $problem['title'] ?></h1>
<!--<a role="button" class="btn btn-info pull-right" href="/problem/<?= $problem['id'] ?>/statistics"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('problems::statistics') ?></a>-->
<?php endif ?>

<?php
    $limit = getUOJConf("/var/uoj_data/{$problem['id']}/problem.conf");
    $time_limit = $limit['time_limit'];
	$memory_limit = $limit['memory_limit'];
	$tests = $limit['n_tests'];
	$ex_tests = $limit['n_ex_tests'];
	$checker = $limit['use_builtin_checker'];
	$uper = $limit['uper'];
	$extra = $limit['extra'];
?>
<div class="row text-center">
    <?php if($time_limit != null ): ?>
	    <span class="label label-default">时间限制：<?=$time_limit?> s</span>
	<?php else: ?>
	    <span class="label label-default">时间限制：N/A</span>
	<?php endif ?>
    <?php if($memory_limit != null ): ?>
	    <span class="label label-default">内存限制：<?=$memory_limit?> MiB</span>
	<?php else: ?>
	    <span class="label label-default">内存限制：N/A</span>
	<?php endif ?>
	<?php if($checker != null ): ?>
	<span class="label label-default">检查器：<?=$checker?></span>
	<?php else: ?>
	<span class="label label-default">检查器：自定义</span>
	<?php endif ?>
</div>

<div class="row text-center" style="margin-top:0.5em;">
	<?php if($tests != null ): ?>
	<span class="label label-default">测试点：<?=$tests?> 个</span>
	<?php else: ?>
	<span class="label label-default">测试点：N/A</span>
	<?php endif; ?>
	<?php if($ex_tests != null ): ?>
	<span class="label label-default">附加测试点：<?=$ex_tests?> 个</span>
	<?php else: ?>
	<span class="label label-default">附加测试点：N/A</span>
	<?php endif; ?>
</div>

<div class="row text-center" style="margin-top:0.5em;">
	<?php if($uper != null ): ?>
	<span class="label label-default">上传者：<?=$uper?></span>
	<?php else: ?>
	<span class="label label-default">上传者：匿名</span>
	<?php endif; ?>
</div>

<ul class="nav nav-pills" role="tablist" id="tabs">
	<li class="active"><a href="#tab-statement" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-book"></span> <?= UOJLocale::get('problems::statement') ?></a></li>
	<li><a href="#tab-submit-answer" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-upload"></span> <?= UOJLocale::get('problems::submit') ?></a></li>
	<?php if ($custom_test_requirement): ?>
	<li><a href="#tab-custom-test" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-console"></span> <?= UOJLocale::get('problems::custom test') ?></a></li>
	<?php endif ?>
	<li><a href="/problem/<?= $problem['id'] ?>/sol"><span class="glyphicon glyphicon-eye-open"></span> 题解</a></li>
	<?php if ($extra != null): ?>
	<li><a href="<?=$extra?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> 附加文件</a></li>
	<?php endif ?>
	<?php if ($contest){ ?>
	<li><a href="/contest/<?= $contest['id'] ?>/problem/<?= $problem['id'] ?>/statistics" role="tab"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('problems::statistics') ?></a></li>
	<li><a href="/contest/<?= $contest['id'] ?>" role="tab"><span class="glyphicon glyphicon-chevron-left"></span> <?= UOJLocale::get('contests::back to the contest') ?></a></li>
	<?php } else { ?>
	<li><a href="/problem/<?= $problem['id'] ?>/statistics" role="tab"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('problems::statistics') ?></a></li>
	<?php } ?>
	<?php if (hasProblemPermission($myUser, $problem)): ?>
	<li><a href="/problem/<?= $problem['id'] ?>/manage/statement" role="tab"><span class="glyphicon glyphicon-edit"></span> <?= UOJLocale::get('problems::manage') ?></a></li>
	<?php else: ?>
	<li class="disabled"><a href="#" role="tab"><span class="glyphicon glyphicon-edit"></span> <?= UOJLocale::get('problems::manage') ?></a></li>
	<?php endif ?>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="tab-statement">
		<article class="top-buffer-md"><?= $problem_content['statement'] ?></article>
	</div>
	<div class="tab-pane" id="tab-submit-answer">
		<div class="top-buffer-sm"></div>
		<?php if ($can_use_zip_upload): ?>
		<?php $zip_answer_form->printHTML(); ?>
		<hr />
		<strong><?= UOJLocale::get('problems::or upload files one by one') ?><br /></strong>
		<?php endif ?>
		<?php $answer_form->printHTML(); ?>
	</div>
	<?php if ($custom_test_requirement): ?>
	<div class="tab-pane" id="tab-custom-test">
		<div class="top-buffer-sm"></div>
		<?php $custom_test_form->printHTML(); ?>
	</div>
	<?php endif ?>
</div>
<?php echoUOJPageFooter() ?>

<script>
    $(function(){
        $(window).scroll(function() {
            if($(window).scrollTop() >= 200) {
                $("#header-content").html("<li><a id=\"a-overview\" href=\"#\">#<?= $problem['id']?>. <?= $problem['title'] ?></a></li><li><a id=\"a-submit\" href=\"#\"><span class=\"glyphicon glyphicon-upload\"></span> <?= UOJLocale::get('problems::submit') ?></a></li><li><a id=\"a-test\" href=\"#\"><span class=\"glyphicon glyphicon-console\"></span> <?= UOJLocale::get('problems::custom test') ?></a></li><?php if ($contest): ?><li><a href=\"/contest/<?= $contest['id'] ?>\"><span class=\"glyphicon glyphicon-chevron-left\"></span> <?= UOJLocale::get('contests::back to the contest') ?></a></li><?php endif ?>");
				$('#a-overview').click(function () {
					$('#tabs li:eq(0) a').tab('show')
				})
				$('#a-submit').click(function () {
					$('#tabs li:eq(1) a').tab('show')
				})
				$('#a-test').click(function () {
					$('#tabs li:eq(2) a').tab('show')
				})
			}
			else {
				$("#header-content").html("<li><a href=\"<?= HTML::url('/') ?>\"><span class=\"glyphicon glyphicon-home\"></span> 首页</a></li><li><a href=\"/problems\"><span class=\"glyphicon glyphicon-list\"></span> <?= UOJLocale::get('problems') ?></a></li><li><a href=\"/contests\"><span class=\"glyphicon glyphicon-calendar\"></span> <?= UOJLocale::get('contests') ?></a></li><li><a href=\"/submissions\"><span class=\"glyphicon glyphicon-tasks\"></span> 评测</a></li><li><a href=\"/ranklist\"><span class=\"glyphicon glyphicon-signal\"></span> 排名</a></li><li><a href=\"/blogs\"><span class=\"glyphicon glyphicon-globe\"></span> <?= UOJLocale::get('blogs') ?></a></li>");
			}
        })
    }
 );
</script>