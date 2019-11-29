<?php

if ($myUser == null) {
    header("Location: /login");
    die();
}

echoUOJPageHeader('CSP专题');
?>

<div class="page-header">
  <h1>CSP专题 <small>2019</small></h1>
</div>

<div class="panel panel-default mdui-hoverable">
	<div class="panel-heading">
		<h3 class="panel-title">报名</h3>
	</div>
	<div class="panel-body">
        CCF NMSL
	</div>
    <ul class="list-group">
        <a class="list-group-item" href="http://rg.noi.cn" target="_blank">氪金地址</a>
        <a class="list-group-item" href="https://www.jxoj.net/user/llf/blog/79" target="_blank">自动注册账户脚本</a>
  </ul>
</div>

<div class="panel panel-default mdui-hoverable">
	<div class="panel-heading">
		<h3 class="panel-title">初赛</h3>
	</div>
	<div class="panel-body">
        2019年CSP初赛将在10月19日举行。
	</div>
    <ul class="list-group">
        <a class="list-group-item" href="/csps1/problem.pdf" download="problem.pdf" target="_blank">下载题目</a>
        <a class="list-group-item" href="https://www.jxoj.net/user/llf/blog/81" target="_blank">查看题解</a>
    </ul>
</div>

<div class="panel panel-default mdui-hoverable">
	<div class="panel-heading">
		<h3 class="panel-title">复赛</h3>
	</div>
	<div class="panel-body">
        2019年CSP复赛将在11月16日举行。
	</div>
</div>

<?php echoUOJPageFooter() ?>