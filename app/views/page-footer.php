<?php
	if (!isset($ShowPageFooter)) {
		$ShowPageFooter = true;
	}
?>
			</div>
			<?php if ($ShowPageFooter): ?>
			<div class="uoj-footer">
				<!--<p>
					<a href="<?= HTML::url(UOJContext::requestURI(), array('params' => array('locale' => 'zh-cn'))) ?>"><img src="//img.uoj.ac/utility/flags/24/cn.png" alt="中文" /></a> 
					<a href="<?= HTML::url(UOJContext::requestURI(), array('params' => array('locale' => 'en'))) ?>"><img src="//img.uoj.ac/utility/flags/24/gb.png" alt="English" /></a>
				</p>
				
				<ul class="list-inline">
					<li><?= UOJConfig::$data['profile']['oj-name'] ?></li>
					<?php if (UOJConfig::$data['profile']['ICP-license']!=''): ?>
					 | <li><a href="http://www.miitbeian.gov.cn"><?= UOJConfig::$data['profile']['ICP-license'] ?></a></li>
					<?php endif ?>
				</ul>
				
				<p>Server time: <?= UOJTime::$time_now_str ?> | <a href="http://github.com/UniversalOJ/UOJ-System">开源项目</a></p>
			-->
				<div class="row">
					<div class="col-md-4 col-sm-12">
						<img style="vertical-align: baseline;" src="/pictures/logo.png" alt="logo">
						<div style="display:inline-block"> 
							<h2> JXFLS Online Judge </h2> 
							<p> &copy; 2017-<?php echo date('Y'); ?> JXOJ &nbsp;&nbsp; All rights reserved.</p>
						</div>
					</div>
					<div class="col-md-3"></div>
					<div class="col-md-5 col-sm-12" style="text-align:right;">
					<p>
						<a href="/faq"> 帮助 </a> | <a href="#"> 社区规则 </a> | <a href="#"> 关于JXOJ </a>
					</p>
					<p> 
						Developed by AlessandroChen,Terrasse and Llf0703 in <a href="#" target="_blank"> JXOJ Team </a>
					</p>
					<p>
						Based on <a href="https://github.com/vfleaking/uoj" target="_blank"> Universal Online Judge </a> & <a href="http://github.com/UniversalOJ/UOJ-System" target="_blank"> UOJ Community </a>
					</p>
					<p>
						Server time: <?= UOJTime::$time_now_str ?>
					</p>
					<p>
						Master: 香港記者號
					</p>
					<div class="dropup">
						<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					    	语言
					    	<span class="caret"></span>
					  	</button>
					  	<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
					  		<li><a href="<?= HTML::url(UOJContext::requestURI(), array('params' => array('locale' => 'zh-cn'))) ?>">中文</a></li>
							<li><a href="<?= HTML::url(UOJContext::requestURI(), array('params' => array('locale' => 'en'))) ?>">English</a></li>
					  	</ul>
					</div>
				</div>
			</div>
			<?php endif ?>
		</div>
		<!-- /container -->
	</body>
</html>
