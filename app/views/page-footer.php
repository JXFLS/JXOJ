<?php
	if (!isset($ShowPageFooter)) {
		$ShowPageFooter = true;
	}
?>
			</div>
			<?php if ($ShowPageFooter): ?>
			<!--<div class="uoj-footer">
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
						Developed by AlessandroChen,Terrasse and Llf0703 in <a href="https://github.com/JXFLS" target="_blank"> JXOJ Team </a>
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
				</div>
			</div>-->
			<!-- decorate -->
		    <?= HTML::js_src('/js/decorate.js') ?>
			<div align=center style="padding:1em;margin-top:1em;">
			<span style="color:#999;">
				JXOJ Powered by <a href="https://github.com/JXFLS/JXOJ" target="_blank">JXOJ</a>
			</span>
			</div>
			<?php endif ?>
		</div>
		<!-- /container -->
	</body>
</html>
