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
			<?= HTML::js_src('/js/jx-passport.js') ?>
			<div align=center style="padding:1em;margin-top:1em;">
			<span style="color:#999;">
				JXOJ Powered by <a href="https://github.com/JXFLS/JXOJ" target="_blank">JXOJ</a>
			</span>
			</div>
			<?php endif ?>
		</div>
		<!--<script>
		function getExploreName() {
		    var userAgent = navigator.userAgent;
		    if (userAgent.indexOf("Opera") > -1 || userAgent.indexOf("OPR") > -1) {
		        return 'Opera';
		    } else if (userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1) {
		        return 'IE';
		    } else if (userAgent.indexOf("Edge") > -1) {
		        return 'Edge';
		    } else if (userAgent.indexOf("Firefox") > -1) {
		        return 'Firefox';
		    } else if (userAgent.indexOf("Safari") > -1 && userAgent.indexOf("Chrome") == -1) {
		        return 'Safari';
		    } else if (userAgent.indexOf("Chrome") > -1 && userAgent.indexOf("Safari") > -1) {
		        return 'Chrome';
		    } else if (!!window.ActiveXObject || "ActiveXObject" in window) {
		        return 'IE>=11';
		    } else {
		        return 'Unkonwn';
		    }
		}
		if (getExploreName()!='Firefox' && getExploreName()!='Edge') {
			BootstrapDialog.show({
				title   : '推荐使用最新版Firefox浏览器或Edge浏览器访问本站',
				message : '除Firefox以外的浏览器访问本站会出现兼容性问题，请下载使用Firefox浏览器，或使用自带Edge浏览器',
				type    : BootstrapDialog.TYPE_DANGER,
				buttons : [{
					label: '好的',
					action: function(dialog) {
						window.location.href = 'https://www.firefox.com.cn/';
					}
				}],
				onhidden : function(dialog) {
					dialog.close();
				}
			});
		}
		</script>-->
	</body>
</html>
