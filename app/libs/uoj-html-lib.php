<?php

function uojHandleAtSign($str, $uri) {
	$referrers = array();
	$res = preg_replace_callback('/@(@|[a-zA-Z0-9_]{1,20})/', function($matches) use(&$referrers) {
		if ($matches[1] === '@') {
			return '@';
		} else {
			$user = queryUser($matches[1]);
			if ($user == null) {
				return $matches[0];
			} else {
				$referrers[$user['username']] = '';
				return '<span class="uoj-username" data-rating="'.$user['rating'].'">@'.$user['username'].'</span>';
			}
		}
	}, $str);
	
	$referrers_list = array();
	foreach ($referrers as $referrer => $val) {
		$referrers_list[] = $referrer;
	}
	
	return array($res, $referrers_list);
}

function uojFilePreview($file_name, $output_limit, $file_type = 'text') {
	switch ($file_type) {
		case 'text':
			return strOmit(file_get_contents($file_name, false, null, 0, $output_limit + 4), $output_limit);
		default:
			return strOmit(shell_exec('xxd -g 4 -l 5000 ' . escapeshellarg($file_name) . ' | head -c ' . ($output_limit + 4)), $output_limit);
	}
}

function uojIncludeView($name, $view_params = array()) {
	extract($view_params);
	include $_SERVER['DOCUMENT_ROOT'].'/app/views/'.$name.'.php';
}

function redirectTo($url) {
	header('Location: '.$url);
	die();
}
function permanentlyRedirectTo($url) {
	header("HTTP/1.1 301 Moved Permanently"); 
	header('Location: '.$url);
	die();
}
function redirectToLogin() {
	if (UOJContext::isAjax()) {
		die('please <a href="'.HTML::url('/login').'">login</a>');
	} else {
		header('Location: '.HTML::url('/login'));
		die();
	}
}
function becomeMsgPage($msg, $title = '消息') {
	if (UOJContext::isAjax()) {
		die($msg);
	} else {
		echoUOJPageHeader($title);
		echo $msg;
		echoUOJPageFooter();
		die();
	}
}
function become404Page() {
	header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
	becomeMsgPage('<div class="text-center"><div style="font-size:233px">404</div><p>唔……未找到该页面……你是从哪里点进来的……&gt;_&lt;……</p></div>
<style><!--
#offline-resources{
			display: none;
		}
		.runner-container {
			height: 150px;
			max-width: 600px;
			overflow: hidden;
            opacity:1;
            margin-left: 20em;
		}
		#c{
			height: 150px;
			max-width: 600px;
			opacity: 1;
		}
		.offline {
			transition: -webkit-filter 1.5s cubic-bezier(0.65, 0.05, 0.36, 1),
			background-color 1.5s cubic-bezier(0.65, 0.05, 0.36, 1);
			will-change: -webkit-filter, background-color;
		}

		.offline.inverted {
			-webkit-filter: invert(100%);
			background-color: #000;
		}
        body {
            transition: -webkit-filter 1.5s cubic-bezier(0.65, 0.05, 0.36, 1), background-color 1.5s cubic-bezier(0.65, 0.05, 0.36, 1);
        }
--></style>
<div id="runner-container" class="runner-container offline" style="height: 200px; width: 602px; background: #fff;">&nbsp;</div>
<p><span style="font-size: 18px;"><img id="sprite" style="display: none;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABNEAAABECAAAAACKI/xBAAAAAnRSTlMAAHaTzTgAAAoOSURBVHgB7J1bdqS4FkSDu7gPTYSh2AOATw1Pn6kBVA2FieiTrlesq6po8lgt0pj02b06E58HlRhXOCQBBcdxHMdxHOfDMeA7BfcIOI4VwISDKQhvK0O4H9iAobeFZSx8WIK0dqz4ztQRg1XdECNfX/CTGUDmNjJDP6MzuMnKKsQ0Y+Amyxnirurmx1KghAvWXoARAErEPUpAB/KzvK6YcAIl8lD2AtsCbENPS1XGwqMTSnvHhNOYgBV3mKlklKDqPUshMUIzsuzlOXFGW9AQS0C/lv/QMWrahOMoiKZL41HyUCRAdcKyDR0tVRkLD0+oV7Q7yLofm6w6rKbdrmNUL6NOyapMtGcUuixZ2WSHbsl+M97BoUX8TrpyrfGbJJ+saBQ0W9I6jnxF/ZO+4nqo66GQneo325keUjth7bFpX38MO6lbM+ZMaeOYETISzYzN9Wiy7shuyj4dI96JSQXuOMSlWcqkgQ2DSlVdUSIbWbVs2vJ41CvadDs0jTE63Y9NWO26r3x9MU3AzDGk1mQWZu2Bht6VaPzEXrl21gjyZRXNPnKFI8+TJnRKLEED24JNpaqqKBGx/C5oWLSlBR0+Pp4J5yM27YVydp8sX4p+SUGe661TuWE5Y78dtcDSX3u+oqWINjLmRm+wTsBUJWpK06pKaXZpJdbmhoH/LcByq6Rq+LMC+7Dl+OFjvzj2ObRJY/tOa1r/uUvDy9d9QaPz4utMP6ZDysxsPeScf3yly6bOfRbcemtPYESvpAn20GSS0efVKOGc4aNQgojj1ZnzvTEnkxqzOVfGllP3y9qnZ0S3pM2mK5jMwQcpiMb1ZVqdkBANl1aCFbBbdOR6Pvwgtjiu9vkx60jrXNpq15E8ywhz/2tbzGQQwQ4b59Zfe7aipVrSEhCP8mZG1UlzZ20tOgw9Hw6hrzCLZiyObqCkVauZFC0OPL8nqUrk/zHN1gopOfkzngH3fv8SQau20jtMQ09VUSmxQUS1OsZSDAWSwKNFq5SylzA6PhFf+Oo4x3m0pEuYKXb4s5WLAAaT1lwfc3Kr6CDZ6JD6hrUCWVhmjHFrzNk17pxWjdGl/Yi9AuBrBqAbusmvGNNCyWpbhvPU82j1aDMi9Q04p8aLaQtiw7plXZ0A7TwDSojO/GsCiAnE6qAGhg45/eAu7csrunGcEUpEN5NsXYDlUY6Mie67UGPTPiiO1xl0vgLYvXt83glmvkux7ke6WdGzz7mKmiSQM2ufmPEoQUv9d2fu3jEazGqc79JUQjRxghoZT9FoiJnjzvbYtDJGOXOcoxUt4hMybAucE3nloJPOSJh5v6cm8gwFWrnn72aj1txnvR+5RrzoXy8kBOAStWBtw/foGvd1NnyX+h2a+LXQUH2XKAFT0uLpi9byzXg2vrzy9Z6eAZmqIUnHoaJ9PlIofwaAYQMWu6XituAE6vWBgifhla/Xp3ClqjpFESRdt5Z+WCIkQ68vHNBAXysZH3CmuufhInRurCagvLk6QNXpbwMDNvouu+Vn/fLeVo3rA084PzAYiwDtzB1jIB3Jmvuc0YqzQRk6W0d8LhIQ9gPkNhSpEGjr2HKW4XyOuznthx/M+8V/W5+7/vRZ9yARQ4L5a18IIBetJbN18/oGYNjRHwyHt6qiJSj9R25zZ55M7Uiq6u3qglDF2KmBCqqTVqhNO0bQSp+gxRJkV9fi68uP/z8TzgYd3tyw9bQOqBUtpmdd9wwlGoGKGzDstMR7LR1EtENp582d1z5jL3yGrc79y83pSsbBZHquNluXZd5DfteKbbhaLc+Ongp1tUslUUvDve1drSPuSFoE2o/8AIL6rspChrbqZkkb0N5yhNa2E3B95Bm2vN+8m/me3lE9WaGp3LbPPDc/u9VZoJFbZ+uoCvaMhAJEDTS2xOO/Tdzp+Xs6C3mG7fXhnXlR4gnx4rXU7dma/FTl0YS29beOjztTx6NOUF2aVrNEe/bZa4m6+nmuEJUAbnFP15xH+/7fHU/FYG6LG+SmVL5bmnFZ/Ho0J4WP4NK4KMCtS7u0p/Bo9ngnXbfWXnVu/DcNdGf9rRgfeab6sWfR1KXZ1Z0kY7+l3rIToQCImiD2U9y4FepFaHm44jpJjDTGlOmfxVbGHMc92nkEW/PrrRSKJiqjF4CiHaqBNqEuLPxDLsGL/+xcvFavbLph6W89TdHCw5wZCW2zXggfe4Sqcc2oBhYYSAc+EY4zGhM5/teid0osBSaaBC3F/vPAjvpxsdDx5Dp1jjsnI7Y+95hT5z+erpZkzB/dpY2wJS0FPfLH0/wsj/AhJS0FJuTaWOPbHWFbN/9VdCUSwtPW5g81j2aMZULDkbtLE+GSBKOCdGiCURtVTXFpp7KCuEtzl3braVVFQ+g/8n6eQil/X24MmjAIe+oYJNqwK2M8uU5mXc8652rXOY6vdZ6NvdyoiXZ1jBqNcC7o0tKVaw2XlltdGs0VUwsYGTpbxwPO1JXcU7gTGLYfrx0tx6tjsW/PsjHd14p2l+YOzXGPdirBDAwdLe9sAf54IEh86zLA2qQj64SGYp9EM674Dk9Rqy4tY58B2MRqVRZOIr2t44FnymfRzlyJSOHBLg2rOzSnn5vxjI3O1hHXxyVNb8zqt2mNi6OrGzR9egPfH1QLREQgFSDs17Ky/zOoS+O7wVJNfN1axjh108L93G8dH3umelx7gGMTCuLbbfJEQZEYha6KGTbN9l2r+zNn2xkwLnzorNWqsLVP0eaGXMZ74pLWDNXLL0N7+GRnAmdqwgNqE4O7tQkREQmp+zMoudWlATcMaIRN28ErA5nv9pF/6PtEnak/1r8H53lRR6bcfuYe0DrCcZxL3vdk19PHBZQz73u6AT0ODZWGbTAY33Ud0nEcZ3hg64gmZjiO81YiCkK1dXytBauO/wwzsmxBqc3VIhP6DVNw5FhFywDS24/cKeHRCdLfoTiO3zMw58+uYUX/HYD2BLETinY4Z5Bk6+jaFo79DFm3LG4Q+pr6r97I5pH7pRsllgiQUEJ7QsSRCdN2aYfjuEczNDnollPLSKm/7EhQ6pgQ2yUKpx3OaQTZOra2gf7P0M/Q3+ScTJlLX6KgECb49h02lFLudPzVzn0lNQwEURQdrfGuc9anX34AIzk21c/xHjLYCo/JU2W1kLTm/7BeP7kkSZIkZbj0JhHZgDdAg5UeAA6f9f8Ar//eMZqUxs8ggs7BhAEarPQAsPm+hwFus4SnG6Mx3pI0xwEX/syoMMDteO0x17QlCd5m/CbX0STs9m3RDggXBLpKWv5S83eSF787y1Wd5apuCcXDHFu0HL1wPGbhz6lL2WL2VYrtE6NPZW7usXAEy1WZ5epGInCMMLhTBsCQ5erTyhXVlAASQROIjO0FvHBFh+evzparEMvVsp8XMGZ5HuHL3cZGzpu884kxZtN/1HLVynL1uiRJkvQFUg1OaKSaqSkAAAAASUVORK5CYII=" alt="test" /></span></p>
<p><span style="font-size: 18px;">
<script type="text/javascript">var canvas=document.createElement("canvas"),a=document.getElementById("runner-container");canvas.id="c";canvas.width=600;canvas.height=150;a.appendChild(canvas);var canvas=document.getElementById("c"),ctx=canvas.getContext("2d");var spriteDefinition={CACTUS_LARGE:{x:332,y:2},CACTUS_SMALL:{x:228,y:2},CLOUD:{x:86,y:2},HORIZON:{x:2,y:54},MOON:{x:484,y:2},PTERODACTYL:{x:134,y:2},RESTART:{x:2,y:2},TEXT_SPRITE:{x:655,y:2},TREX:{x:848,y:2},STAR:{x:645,y:2}},FPS=60,DEFAULT_WIDTH=600,c=document.getElementById("c"),ctx=c.getContext("2d"),imgSprite=document.getElementById("sprite");Runner.config={ACCELERATION:0.001,BG_CLOUD_SPEED:0.2,BOTTOM_PAD:10,CLEAR_TIME:3000,CLOUD_FREQUENCY:0.5,GAMEOVER_CLEAR_TIME:750,GAP_COEFFICIENT:0.6,GRAVITY:0.6,INITIAL_JUMP_VELOCITY:12,INVERT_FADE_DURATION:10000,INVERT_DISTANCE:700,MAX_CLOUDS:6,MAX_OBSTACLE_LENGTH:3,MAX_OBSTACLE_DUPLICATION:2,MAX_SPEED:13,MIN_JUMP_HEIGHT:35,MOBILE_SPEED_COEFFICIENT:1.2,RESOURCE_TEMPLATE_ID:"audio-resources",SPEED:6,SPEED_DROP_COEFFICIENT:3};Runner.defaultDimensions={HEIGHT:150,WIDTH:600};Runner.classes={CANVAS:"runner-canvas",CONTAINER:"runner-container",CRASHED:"crashed",ICON:"icon-offline",INVERTED:"inverted",SNACKBAR:"snackbar",SNACKBAR_SHOW:"snackbar-show",TOUCH_CONTROLLER:"controller"};Runner.sounds={BUTTON_PRESS:"offline-sound-press",HIT:"offline-sound-hit",SCORE:"offline-sound-reached"};Runner.keycodes={JUMP:{"38":1,"32":1},DUCK:{"40":1},RESTART:{"13":1}};Runner.events={ANIM_END:"webkitAnimationEnd",CLICK:"click",KEYDOWN:"keydown",KEYUP:"keyup",MOUSEDOWN:"mousedown",MOUSEUP:"mouseup",RESIZE:"resize",TOUCHEND:"touchend",TOUCHSTART:"touchstart",VISIBILITY:"visibilitychange",BLUR:"blur",FOCUS:"focus",LOAD:"load"};function Runner(d,b){if(Runner.instance_){return Runner.instance_}Runner.instance_=this;this.containerEl=null;this.snackbarEl=null;this.config=b||Runner.config;this.dimensions=Runner.defaultDimensions;this.canvas=null;this.ctx=null;this.tRex=null;this.distanceMeter=null;this.distanceRan=0;this.highestScore=0;this.time=0;this.runningTime=0;this.msPerFrame=1000/FPS;this.currentSpeed=this.config.SPEED;this.obstacles=[];this.started=false;this.activated=false;this.crashed=false;this.paused=false;this.inverted=false;this.invertTimer=0;this.resizeTimerId_=null;this.playCount=0;this.audioBuffer=null;this.soundFx={};this.audioContext=null;this.images={};this.imagesLoaded=0;this.loadImages()}Runner.prototype={loadImages:function(){this.spriteDef=spriteDefinition;this.init()},loadSounds:function(){this.audioContext=new AudioContext()},setSpeed:function(b){if(b){this.currentSpeed=b}},init:function(){this.setSpeed();this.canvas=c;this.ctx=ctx;this.ctx.fillStyle="#f7f7f7";this.ctx.fill();this.horizon=new Horizon(this.canvas,this.spriteDef,this.dimensions,this.config.GAP_COEFFICIENT);this.distanceMeter=new DistanceMeter(this.canvas,this.spriteDef.TEXT_SPRITE,this.dimensions.WIDTH);this.tRex=new Trex(this.canvas,this.spriteDef.TREX);this.startListening();this.update()},playIntro:function(){if(!this.started&&!this.crashed){this.playingIntro=true;this.tRex.playingIntro=true;var b="@-webkit-keyframes intro { "+"from { width:"+Trex.config.WIDTH+"px }"+"to { width: "+this.dimensions.WIDTH+"px }"+"}";document.styleSheets[0].insertRule(b,0);this.containerEl=document.getElementById("runner-container");this.containerEl.addEventListener("webkitAnimationEnd",this.startGame.bind(this));this.containerEl.style.webkitAnimation="intro .4s ease-out 1 both";this.containerEl.style.width=this.dimensions.WIDTH+"px";this.activated=true;this.started=true}else{if(this.crashed){this.restart()}}},startGame:function(){this.runningTime=0;this.playingIntro=false;this.tRex.playingIntro=false;this.containerEl.style.webkitAnimation="";this.playCount++;document.addEventListener("visibilitychange",this.onVisibilityChange.bind(this));window.addEventListener("blur",this.onVisibilityChange.bind(this));window.addEventListener("focus",this.onVisibilityChange.bind(this))},clearCanvas:function(){this.ctx.clearRect(0,0,this.dimensions.WIDTH,this.dimensions.HEIGHT)},update:function(){this.drawPending=false;var e=getTimeStamp();var d=e-(this.time||e);this.time=e;if(this.activated){this.clearCanvas();if(this.tRex.jumping){this.tRex.updateJump(d)}this.runningTime+=d;var b=this.runningTime>this.config.CLEAR_TIME;if(this.tRex.jumpCount==1&&!this.playingIntro){this.playIntro()}if(this.playingIntro){this.horizon.update(0,this.currentSpeed,b)}else{d=!this.started?0:d;this.horizon.update(d,this.currentSpeed,b,this.inverted)}var h=b&&checkForCollision(this.horizon.obstacles[0],this.tRex);if(!h){this.distanceRan+=this.currentSpeed*d/this.msPerFrame;if(this.currentSpeed<this.config.MAX_SPEED){this.currentSpeed+=this.config.ACCELERATION}}else{this.gameOver()}var f=this.distanceMeter.update(d,Math.ceil(this.distanceRan));if(f){this.playSound(this.soundFx.SCORE)}if(this.invertTimer>this.config.INVERT_FADE_DURATION){this.invertTimer=0;this.invertTrigger=false;this.invert()}else{if(this.invertTimer){this.invertTimer+=d}else{var g=this.distanceMeter.getActualDistance(Math.ceil(this.distanceRan));if(g>0){this.invertTrigger=!(g%this.config.INVERT_DISTANCE);if(this.invertTrigger&&this.invertTimer===0){this.invertTimer+=d;this.invert()}}}}}if(!this.crashed){this.tRex.update(d);this.raq()}},handleEvent:function(b){return(function(e,d){switch(e){case d.KEYDOWN:case d.TOUCHSTART:case d.MOUSEDOWN:this.onKeyDown(b);break;case d.KEYUP:case d.TOUCHEND:case d.MOUSEUP:this.onKeyUp(b);break}}.bind(this))(b.type,Runner.events)},startListening:function(){document.addEventListener(Runner.events.KEYDOWN,this);document.addEventListener(Runner.events.KEYUP,this);document.addEventListener(Runner.events.MOUSEDOWN,this);document.addEventListener(Runner.events.MOUSEUP,this)},stopListening:function(){document.removeEventListener(Runner.events.KEYDOWN,this);document.removeEventListener(Runner.events.KEYUP,this);document.removeEventListener(Runner.events.MOUSEDOWN,this);document.removeEventListener(Runner.events.MOUSEUP,this)},onKeyDown:function(b){if(b.target!=this.detailsButton){if(!this.crashed&&Runner.keycodes.JUMP[b.keyCode]){b.preventDefault();if(!this.activated){this.loadSounds();this.activated=true}if(!this.tRex.jumping&&!this.tRex.ducking){this.tRex.startJump(this.currentSpeed)}}}if(this.activated&&!this.crashed&&Runner.keycodes.DUCK[b.keyCode]){b.preventDefault();if(this.tRex.jumping){this.tRex.setSpeedDrop()}else{if(!this.tRex.jumping&&!this.tRex.ducking){this.tRex.setDuck(true)}}}},onKeyUp:function(g){var f=String(g.keyCode);var d=Runner.keycodes.JUMP[f]||g.type==Runner.events.TOUCHEND||g.type==Runner.events.MOUSEDOWN;if(this.isRunning()&&d){g.preventDefault();this.tRex.endJump()}else{if(Runner.keycodes.DUCK[f]){g.preventDefault();this.tRex.speedDrop=false;this.tRex.setDuck(false)}else{if(this.crashed){g.preventDefault();var b=getTimeStamp()-this.time;if(Runner.keycodes.RESTART[f]||this.isLeftClickOnCanvas(g)||(b>=this.config.GAMEOVER_CLEAR_TIME&&Runner.keycodes.JUMP[f])){g.preventDefault();this.restart()}}else{if(this.paused&&d){g.preventDefault();this.tRex.reset();this.play()}}}}},isLeftClickOnCanvas:function(b){return b.button!=null&&b.button<2&&b.type==Runner.events.MOUSEUP&&b.target==this.canvas},raq:function(){if(!this.drawPending){this.drawPending=true;this.raqId=requestAnimationFrame(this.update.bind(this))}},isRunning:function(){return !!this.raqId},gameOver:function(){this.stop();this.crashed=true;this.distanceMeter.acheivement=false;this.tRex.update(100,Trex.status.CRASHED);if(!this.gameOverPanel){this.gameOverPanel=new GameOverPanel(this.canvas,this.spriteDef.TEXT_SPRITE,this.spriteDef.RESTART,this.dimensions)}else{this.gameOverPanel.draw()}if(this.distanceRan>this.highestScore){this.highestScore=Math.ceil(this.distanceRan);this.distanceMeter.setHighScore(this.highestScore)}this.time=getTimeStamp()},stop:function(){this.activated=false;this.paused=true;cancelAnimationFrame(this.raqId);this.raqId=0},play:function(){if(!this.crashed){this.activated=true;this.paused=false;this.tRex.update(0,Trex.status.RUNNING);this.time=getTimeStamp();this.update()}},restart:function(){if(!this.raqId){this.playCount++;this.runningTime=0;this.activated=true;this.crashed=false;this.distanceRan=0;this.setSpeed(this.config.SPEED);this.time=getTimeStamp();this.containerEl.classList.remove(Runner.classes.CRASHED);this.clearCanvas();this.distanceMeter.reset(this.highestScore);this.horizon.reset();this.tRex.reset();this.invert(true);this.update()}},onVisibilityChange:function(b){if(document.hidden||document.webkitHidden||b.type=="blur"||document.visibilityState!="visible"){this.stop()}else{if(!this.crashed){this.tRex.reset();this.play()}}},playSound:function(d){if(d){var b=this.audioContext.createBufferSource();b.buffer=d;b.connect(this.audioContext.destination);b.start(0)}},invert:function(b){if(b){a.classList.toggle(Runner.classes.INVERTED,this.invertTrigger);this.invertTimer=0;this.inverted=false;document.body.style.backgroundColor="white"}else{this.inverted=a.classList.toggle(Runner.classes.INVERTED,this.invertTrigger);if(document.body.style.backgroundColor=="black"){document.body.style.backgroundColor="white"}else{document.body.style.backgroundColor="black"}}}};window["Runner"]=Runner;function decodeBase64ToArrayBuffer(h){var b=(h.length/4)*3;var g=atob(h);var f=new ArrayBuffer(b);var d=new Uint8Array(f);for(var e=0;e<b;e++){d[e]=g.charCodeAt(e)}return d.buffer}GameOverPanel.dimensions={TEXT_X:0,TEXT_Y:13,TEXT_WIDTH:191,TEXT_HEIGHT:11,RESTART_WIDTH:36,RESTART_HEIGHT:32};function GameOverPanel(d,e,b,f){this.canvas=d;this.ctx=d.getContext("2d");this.canvasDimensions=f;this.textImgPos=e;this.restartImgPos=b;this.draw()}GameOverPanel.prototype={updateDimensions:function(b,d){this.canvasDimensions.WIDTH=b;if(d){this.canvasDimensions.HEIGHT=d}},draw:function(){var b=GameOverPanel.dimensions;var e=this.canvasDimensions.WIDTH/2;var m=b.TEXT_X;var l=b.TEXT_Y;var h=b.TEXT_WIDTH;var k=b.TEXT_HEIGHT;var g=Math.round(e-(b.TEXT_WIDTH/2));var f=Math.round((this.canvasDimensions.HEIGHT-25)/3);var d=b.TEXT_WIDTH;var p=b.TEXT_HEIGHT;var j=b.RESTART_WIDTH;var i=b.RESTART_HEIGHT;var o=e-(b.RESTART_WIDTH/2);var n=this.canvasDimensions.HEIGHT/2;m+=this.textImgPos.x;l+=this.textImgPos.y;this.ctx.drawImage(imgSprite,m,l,h,k,g,f,d,p);this.ctx.drawImage(imgSprite,this.restartImgPos.x,this.restartImgPos.y,j,i,o,n,b.RESTART_WIDTH,b.RESTART_HEIGHT)}};function HorizonLine(d,b){this.spritePos=b;this.canvas=d;this.ctx=d.getContext("2d");this.sourceDimensions={};this.dimensions=HorizonLine.dimensions;this.sourceXPos=[this.spritePos.x,this.spritePos.x+this.dimensions.WIDTH];this.xPos=[];this.yPos=0;this.bumpThreshold=0.5;this.setSourceDimesions();this.draw()}HorizonLine.dimensions={WIDTH:600,HEIGHT:12,YPOS:127};HorizonLine.prototype={setSourceDimesions:function(){for(var b in HorizonLine.dimensions){this.sourceDimensions[b]=HorizonLine.dimensions[b];this.dimensions[b]=HorizonLine.dimensions[b]}this.xPos=[0,HorizonLine.dimensions.WIDTH];this.yPos=HorizonLine.dimensions.YPOS},getRandomType:function(){return Math.random()>this.bumpThreshold?this.dimensions.WIDTH:0},draw:function(){this.ctx.drawImage(imgSprite,this.sourceXPos[0],this.spritePos.y,this.sourceDimensions.WIDTH,this.sourceDimensions.HEIGHT,this.xPos[0],this.yPos,this.dimensions.WIDTH,this.dimensions.HEIGHT);this.ctx.drawImage(imgSprite,this.sourceXPos[1],this.spritePos.y,this.sourceDimensions.WIDTH,this.sourceDimensions.HEIGHT,this.xPos[1],this.yPos,this.dimensions.WIDTH,this.dimensions.HEIGHT)},updateXPos:function(f,d){var e=f,b=f===0?1:0;this.xPos[e]-=d;this.xPos[b]=this.xPos[e]+this.dimensions.WIDTH;if(this.xPos[e]<=-this.dimensions.WIDTH){this.xPos[e]+=this.dimensions.WIDTH*2;this.xPos[b]=this.xPos[e]-this.dimensions.WIDTH;this.sourceXPos[e]=this.getRandomType()+this.spritePos.x}},update:function(d,e){var b=Math.floor(e*(FPS/1000)*d);if(this.xPos[0]<=0){this.updateXPos(0,b)}else{this.updateXPos(1,b)}this.draw()},reset:function(){this.xPos[0]=0;this.xPos[1]=HorizonLine.dimensions.WIDTH}};Cloud.config={HEIGHT:14,MAX_CLOUD_GAP:400,MAX_SKY_LEVEL:30,MIN_CLOUD_GAP:100,MIN_SKY_LEVEL:71,WIDTH:46};function getRandomNum(d,b){return Math.floor(Math.random()*(b-d+1))+d}function getTimeStamp(){return performance.now()}function Cloud(d,b,e){this.canvas=d;this.ctx=d.getContext("2d");this.spritePos=b;this.containerWidth=e;this.xPos=e;this.yPos=0;this.remove=false;this.cloudGap=getRandomNum(Cloud.config.MIN_CLOUD_GAP,Cloud.config.MAX_CLOUD_GAP);this.init()}Cloud.prototype={init:function(){this.yPos=getRandomNum(Cloud.config.MAX_SKY_LEVEL,Cloud.config.MIN_SKY_LEVEL);this.draw()},draw:function(){this.ctx.save();var b=Cloud.config.WIDTH,d=Cloud.config.HEIGHT;this.ctx.drawImage(imgSprite,this.spritePos.x,this.spritePos.y,b,d,this.xPos,this.yPos,b,d);this.ctx.restore()},update:function(b){if(!this.remove){this.xPos-=Math.ceil(b);this.draw();if(!this.isVisible()){this.remove=true}}},isVisible:function(){return this.xPos+Cloud.config.WIDTH>0}};NightMode.config={FADE_SPEED:0.035,HEIGHT:40,MOON_SPEED:0.25,NUM_STARS:2,STAR_SIZE:9,STAR_SPEED:0.3,STAR_MAX_Y:70,WIDTH:20};NightMode.phases=[140,120,100,60,40,20,0];function NightMode(d,b,e){this.spritePos=b;this.canvas=d;this.ctx=d.getContext("2d");this.containerWidth=e;this.xPos=e-50;this.yPos=30;this.currentPhase=0;this.opacity=0;this.stars=[];this.drawStars=false;this.placeStars()}NightMode.prototype={update:function(b){if(b&&this.opacity==0){this.currentPhase++;if(this.currentPhase>=NightMode.phases.length){this.currentPhase=0}}if(b&&(this.opacity<1||this.opacity==0)){this.opacity+=NightMode.config.FADE_SPEED}else{if(this.opacity>0){this.opacity-=NightMode.config.FADE_SPEED}}if(this.opacity>0){this.xPos=this.updateXPos(this.xPos,NightMode.config.MOON_SPEED);if(this.drawStars){for(var d=0;d<NightMode.config.NUM_STARS;d++){this.stars[d].x=this.updateXPos(this.stars[d].x,NightMode.config.STAR_SPEED)}}this.draw()}else{this.opacity=0;this.placeStars()}this.drawStars=true},updateXPos:function(b,d){if(b<-NightMode.config.WIDTH){b=this.containerWidth}else{b-=d}return b},draw:function(){var f=this.currentPhase==3?NightMode.config.WIDTH*2:NightMode.config.WIDTH;var e=NightMode.config.HEIGHT;var d=this.spritePos.x+NightMode.phases[this.currentPhase];var b=f;var h=NightMode.config.STAR_SIZE;var j=spriteDefinition.STAR.x;this.ctx.save();this.ctx.globalAlpha=this.opacity;if(this.drawStars){for(var g=0;g<NightMode.config.NUM_STARS;g++){this.ctx.drawImage(imgSprite,j,this.stars[g].sourceY,h,h,Math.round(this.stars[g].x),this.stars[g].y,NightMode.config.STAR_SIZE,NightMode.config.STAR_SIZE)}}this.ctx.drawImage(imgSprite,d,this.spritePos.y,f,e,Math.round(this.xPos),this.yPos,b,NightMode.config.HEIGHT);this.ctx.globalAlpha=1;this.ctx.restore()},placeStars:function(){var d=Math.round(this.containerWidth/NightMode.config.NUM_STARS);for(var b=0;b<NightMode.config.NUM_STARS;b++){this.stars[b]={};this.stars[b].x=getRandomNum(d*b,d*(b+1));this.stars[b].y=getRandomNum(0,NightMode.config.STAR_MAX_Y);this.stars[b].sourceY=spriteDefinition.STAR.y+NightMode.config.STAR_SIZE*b}},reset:function(){this.currentPhase=0;this.opacity=0;this.update(false)}};Horizon.config={BG_CLOUD_SPEED:0.2,BUMPY_THRESHOLD:0.3,CLOUD_FREQUENCY:0.5,HORIZON_HEIGHT:16,MAX_CLOUDS:6};function Horizon(d,b,f,e){this.canvas=d;this.ctx=d.getContext("2d");this.config=Horizon.config;this.dimensions=f;this.gapCoefficient=e;this.obstacles=[];this.obstacleHistory=[];this.horizonOffsets=[0,0];this.cloudFrequency=this.config.CLOUD_FREQUENCY;this.spritePos=b;this.nightMode=null;this.clouds=[];this.cloudSpeed=this.config.BG_CLOUD_SPEED;this.horizonLine=null;this.init()}Horizon.prototype={init:function(){this.addCloud();this.horizonLine=new HorizonLine(this.canvas,this.spritePos.HORIZON);this.nightMode=new NightMode(this.canvas,this.spritePos.MOON,this.dimensions.WIDTH)},update:function(b,f,e,d){this.runningTime+=b;this.horizonLine.update(b,f);this.nightMode.update(d);this.updateClouds(b,f);if(e){this.updateObstacles(b,f)}},updateClouds:function(d,f){var b=this.cloudSpeed/1000*d*f;var h=this.clouds.length;if(h){for(var e=h-1;e>=0;e--){this.clouds[e].update(b)}var g=this.clouds[h-1];if(h<this.config.MAX_CLOUDS&&(this.dimensions.WIDTH-g.xPos)>g.cloudGap&&this.cloudFrequency>Math.random()){this.addCloud()}this.clouds=this.clouds.filter(function(i){return !i.remove})}else{this.addCloud()}},updateObstacles:function(b,f){var h=this.obstacles.slice(0);for(var e=0;e<this.obstacles.length;e++){var d=this.obstacles[e];d.update(b,f);if(d.remove){h.shift()}}this.obstacles=h;if(this.obstacles.length>0){var g=this.obstacles[this.obstacles.length-1];if(g&&!g.followingObstacleCreated&&g.isVisible()&&(g.xPos+g.width+g.gap)<this.dimensions.WIDTH){this.addNewObstacle(f);g.followingObstacleCreated=true}}else{this.addNewObstacle(f)}},removeFirstObstacle:function(){this.obstacles.shift()},addNewObstacle:function(e){var f=getRandomNum(0,Obstacle.types.length-1);var b=Obstacle.types[f];if(this.duplicateObstacleCheck(b.type)||e<b.minSpeed){this.addNewObstacle(e)}else{var d=this.spritePos[b.type];this.obstacles.push(new Obstacle(this.ctx,b,d,this.dimensions,this.gapCoefficient,e,b.width));this.obstacleHistory.unshift(b.type)}if(this.obstacleHistory.length>1){this.obstacleHistory.splice(Runner.config.MAX_OBSTACLE_DUPLICATION)}},duplicateObstacleCheck:function(e){var b=0;for(var d=0;d<this.obstacleHistory.length;d++){b=this.obstacleHistory[d]==e?b+1:0}return b>=Runner.config.MAX_OBSTACLE_DUPLICATION},reset:function(){this.obstacles=[];this.horizonLine.reset();this.nightMode.reset()},resize:function(d,b){this.canvas.width=d;this.canvas.height=b},addCloud:function(){this.clouds.push(new Cloud(this.canvas,this.spritePos.CLOUD,this.dimensions.WIDTH))}};Obstacle.MAX_GAP_COEFFICIENT=1.5;Obstacle.MAX_OBSTACLE_LENGTH=3;Obstacle.types=[{type:"CACTUS_SMALL",width:17,height:35,yPos:105,multipleSpeed:4,minGap:120,minSpeed:0,collisionBoxes:[new CollisionBox(0,7,5,27),new CollisionBox(4,0,6,34),new CollisionBox(10,4,7,14)]},{type:"CACTUS_LARGE",width:25,height:50,yPos:90,multipleSpeed:7,minGap:120,minSpeed:0,collisionBoxes:[new CollisionBox(0,12,7,38),new CollisionBox(8,0,7,49),new CollisionBox(13,10,10,38)]},{type:"PTERODACTYL",width:46,height:40,yPos:[100,75,50],yPosMobile:[100,50],multipleSpeed:999,minSpeed:8.5,minGap:150,collisionBoxes:[new CollisionBox(15,15,16,5),new CollisionBox(18,21,24,6),new CollisionBox(2,14,4,3),new CollisionBox(6,10,4,7),new CollisionBox(10,8,6,9)],numFrames:2,frameRate:1000/6,speedOffset:0.8}];function Obstacle(d,g,e,h,f,i,b){this.ctx=d;this.spritePos=e;this.typeConfig=g;this.gapCoefficient=f;this.size=getRandomNum(1,Obstacle.MAX_OBSTACLE_LENGTH);this.dimensions=h;this.remove=false;this.xPos=h.WIDTH+(b||0);this.yPos=0;this.width=0;this.collisionBoxes=[];this.gap=0;this.speedOffset=0;this.currentFrame=0;this.timer=0;this.init(i)}Obstacle.prototype={init:function(b){this.cloneCollisionBoxes();if(this.size>1&&this.typeConfig.multipleSpeed>b){this.size=1}this.width=this.typeConfig.width*this.size;if(Array.isArray(this.typeConfig.yPos)){var d=this.typeConfig.yPos;this.yPos=d[getRandomNum(0,d.length-1)]}else{this.yPos=this.typeConfig.yPos}this.draw();if(this.size>1){this.collisionBoxes[1].width=this.width-this.collisionBoxes[0].width-this.collisionBoxes[2].width;this.collisionBoxes[2].x=this.width-this.collisionBoxes[2].width}if(this.typeConfig.speedOffset){this.speedOffset=Math.random()>0.5?this.typeConfig.speedOffset:-this.typeConfig.speedOffset}this.gap=this.getGap(this.gapCoefficient,b)},draw:function(){var b=this.typeConfig.width;var d=this.typeConfig.height;var e=(b*this.size)*(0.5*(this.size-1))+this.spritePos.x;if(this.currentFrame>0){e+=b*this.currentFrame}this.ctx.drawImage(imgSprite,e,this.spritePos.y,b*this.size,d,this.xPos,this.yPos,b*this.size,d)},update:function(b,d){if(!this.remove){if(this.typeConfig.speedOffset){d+=this.speedOffset}this.xPos-=Math.floor((d*FPS/1000)*b);if(this.typeConfig.numFrames){this.timer+=b;if(this.timer>=this.typeConfig.frameRate){this.currentFrame=this.currentFrame==this.typeConfig.numFrames-1?0:this.currentFrame+1;this.timer=0}}this.draw();if(!this.isVisible()){this.remove=true}}},getGap:function(e,f){var d=Math.round(this.width*f+this.typeConfig.minGap*e);var b=Math.round(d*Obstacle.MAX_GAP_COEFFICIENT);return getRandomNum(d,b)},isVisible:function(){return this.xPos+this.width>0},cloneCollisionBoxes:function(){var d=this.typeConfig.collisionBoxes;for(var b=d.length-1;b>=0;b--){this.collisionBoxes[b]=new CollisionBox(d[b].x,d[b].y,d[b].width,d[b].height)}}};DistanceMeter.dimensions={WIDTH:10,HEIGHT:13,DEST_WIDTH:11};DistanceMeter.yPos=[0,13,27,40,53,67,80,93,107,120];DistanceMeter.config={MAX_DISTANCE_UNITS:5,ACHIEVEMENT_DISTANCE:100,COEFFICIENT:0.025,FLASH_DURATION:1000/4,FLASH_ITERATIONS:3};function DistanceMeter(e,d,b){this.canvas=e;this.ctx=e.getContext("2d");this.image=imgSprite;this.spritePos=d;this.x=0;this.y=5;this.currentDistance=0;this.maxScore=0;this.highScore=0;this.container=null;this.digits=[];this.acheivement=false;this.defaultString="";this.flashTimer=0;this.flashIterations=0;this.invertTrigger=false;this.config=DistanceMeter.config;this.maxScoreUnits=this.config.MAX_DISTANCE_UNITS;this.init(b)}DistanceMeter.prototype={init:function(d){var e="";this.calcXPos(d);this.maxScore=this.maxScoreUnits;for(var b=0;b<this.maxScoreUnits;b++){this.draw(b,0);this.defaultString+="0";e+="9"}this.maxScore=parseInt(e)},calcXPos:function(b){this.x=b-(DistanceMeter.dimensions.DEST_WIDTH*(this.maxScoreUnits+1))},draw:function(m,l,n){var b=DistanceMeter.dimensions.WIDTH;var f=DistanceMeter.dimensions.HEIGHT;var e=DistanceMeter.dimensions.WIDTH*l;var d=0;var k=m*DistanceMeter.dimensions.DEST_WIDTH;var i=this.y;var j=DistanceMeter.dimensions.WIDTH;var g=DistanceMeter.dimensions.HEIGHT;e+=this.spritePos.x;d+=this.spritePos.y;this.ctx.save();if(n){var h=this.x-(this.maxScoreUnits*2)*DistanceMeter.dimensions.WIDTH;this.ctx.translate(h,this.y)}else{this.ctx.translate(this.x,this.y)}this.ctx.drawImage(this.image,e,d,b,f,k,i,j,g);this.ctx.restore()},getActualDistance:function(b){return b?Math.round(b*this.config.COEFFICIENT):0},update:function(b,h){var f=true;var e=false;if(!this.acheivement){h=this.getActualDistance(h);if(h>this.maxScore&&this.maxScoreUnits==this.config.MAX_DISTANCE_UNITS){this.maxScoreUnits++;this.maxScore=parseInt(this.maxScore+"9")}else{this.distance=0}if(h>0){if(h%this.config.ACHIEVEMENT_DISTANCE==0){this.acheivement=true;this.flashTimer=0;e=true}var g=(this.defaultString+h).substr(-this.maxScoreUnits);this.digits=g.split("")}else{this.digits=this.defaultString.split("")}}else{if(this.flashIterations<=this.config.FLASH_ITERATIONS){this.flashTimer+=b;if(this.flashTimer<this.config.FLASH_DURATION){f=false}else{if(this.flashTimer>this.config.FLASH_DURATION*2){this.flashTimer=0;this.flashIterations++}}}else{this.acheivement=false;this.flashIterations=0;this.flashTimer=0}}if(f){for(var d=this.digits.length-1;d>=0;d--){this.draw(d,parseInt(this.digits[d]))}}this.drawHighScore();return e},drawHighScore:function(){this.ctx.save();this.ctx.globalAlpha=0.8;for(var b=this.highScore.length-1;b>=0;b--){this.draw(b,parseInt(this.highScore[b],10),true)}this.ctx.restore()},setHighScore:function(d){d=this.getActualDistance(d);var b=(this.defaultString+d).substr(-this.maxScoreUnits);this.highScore=["10","11",""].concat(b.split(""))},reset:function(){this.update(0);this.acheivement=false}};function CollisionBox(b,f,d,e){this.x=b;this.y=f;this.width=d;this.height=e}function checkForCollision(l,e,n){var p=Runner.defaultDimensions.WIDTH+l.xPos;var k=new CollisionBox(e.xPos+1,e.yPos+1,e.config.WIDTH-2,e.config.HEIGHT-2);var m=new CollisionBox(l.xPos+1,l.yPos+1,l.typeConfig.width*l.size-2,l.typeConfig.height-2);if(n){drawCollisionBoxes(n,k,m)}if(boxCompare(k,m)){var b=l.collisionBoxes;var j=e.ducking?Trex.collisionBoxes.DUCKING:Trex.collisionBoxes.RUNNING;for(var o=0;o<j.length;o++){for(var h=0;h<b.length;h++){var d=createAdjustedCollisionBox(j[o],k);var f=createAdjustedCollisionBox(b[h],m);var g=boxCompare(d,f);if(n){drawCollisionBoxes(n,d,f)}if(g){return[d,f]}}}}return false}function createAdjustedCollisionBox(d,b){return new CollisionBox(d.x+b.x,d.y+b.y,d.width,d.height)}function boxCompare(b,e){var d=false;var i=b.x;var h=b.y;var g=e.x;var f=e.y;if(b.x<g+e.width&&b.x+b.width>g&&b.y<e.y+e.height&&b.height+b.y>e.y){d=true}return d}function drawCollisionBoxes(e,b,d){e.save();e.lineWidth=0.5;e.strokeStyle="#f00";e.strokeRect(b.x+0.5,b.y+0.5,b.width,b.height);e.strokeStyle="#0f0";e.strokeRect(d.x+0.5,d.y+0.5,d.width,d.height);e.restore()}Trex.config={DROP_VELOCITY:-5,GRAVITY:0.6,HEIGHT:47,HEIGHT_DUCK:25,INIITAL_JUMP_VELOCITY:-10,INTRO_DURATION:1500,MAX_JUMP_HEIGHT:30,MIN_JUMP_HEIGHT:30,SPEED_DROP_COEFFICIENT:3,SPRITE_WIDTH:262,START_X_POS:50,WIDTH:44,WIDTH_DUCK:59};Trex.status={CRASHED:"CRASHED",DUCKING:"DUCKING",JUMPING:"JUMPING",RUNNING:"RUNNING",WAITING:"WAITING"};Trex.BLINK_TIMING=3000;Trex.collisionBoxes={DUCKING:[new CollisionBox(1,18,55,25)],RUNNING:[new CollisionBox(22,0,17,16),new CollisionBox(1,18,30,9),new CollisionBox(10,35,14,8),new CollisionBox(1,24,29,5),new CollisionBox(5,30,21,4),new CollisionBox(9,34,15,4)]};Trex.animFrames={WAITING:{frames:[44,0],msPerFrame:1000/3},RUNNING:{frames:[88,132],msPerFrame:1000/12},CRASHED:{frames:[220],msPerFrame:1000/60},JUMPING:{frames:[0],msPerFrame:1000/60},DUCKING:{frames:[262,321],msPerFrame:1000/8}};function Trex(d,b){this.canvas=d;this.ctx=d.getContext("2d");this.spritePos=b;this.xPos=0;this.yPos=0;this.groundYPos=0;this.currentFrame=0;this.currentAnimFrames=[];this.blinkDelay=0;this.animStartTime=0;this.timer=0;this.msPerFrame=1000/FPS;this.config=Trex.config;this.status=Trex.status.WAITING;this.jumping=false;this.ducking=false;this.jumpVelocity=0;this.reachedMinHeight=false;this.speedDrop=false;this.jumpCount=0;this.jumpspotX=0;this.init()}Trex.prototype={init:function(){this.blinkDelay=this.setBlinkDelay();this.groundYPos=Runner.defaultDimensions.HEIGHT-this.config.HEIGHT-Runner.config.BOTTOM_PAD;this.yPos=this.groundYPos;this.minJumpHeight=this.groundYPos-this.config.MIN_JUMP_HEIGHT;this.draw(0,0);this.update(0,Trex.status.WAITING)},setJumpVelocity:function(b){this.config.INIITAL_JUMP_VELOCITY=-b;this.config.DROP_VELOCITY=-b/2},update:function(b,d){this.timer+=b;if(d){this.status=d;this.currentFrame=0;this.msPerFrame=Trex.animFrames[d].msPerFrame;this.currentAnimFrames=Trex.animFrames[d].frames;if(d==Trex.status.WAITING){this.animStartTime=getTimeStamp();this.setBlinkDelay()}}if(this.playingIntro&&this.xPos<this.config.START_X_POS){this.xPos+=Math.round((this.config.START_X_POS/this.config.INTRO_DURATION)*b)}if(this.status==Trex.status.WAITING){this.blink(getTimeStamp())}else{this.draw(this.currentAnimFrames[this.currentFrame],0)}if(this.timer>=this.msPerFrame){this.currentFrame=this.currentFrame==this.currentAnimFrames.length-1?0:this.currentFrame+1;this.timer=0}if(this.speedDrop&&this.yPos==this.groundYPos){this.speedDrop=false;this.setDuck(true)}},setBlinkDelay:function(){this.blinkDelay=Math.ceil(Math.random()*Trex.BLINK_TIMING)},blink:function(d){var b=d-this.animStartTime;if(b>=this.blinkDelay){this.draw(this.currentAnimFrames[this.currentFrame],0);if(this.currentFrame==1){this.setBlinkDelay();this.animStartTime=d}}},startJump:function(b){if(!this.jumping){this.update(0,Trex.status.JUMPING);this.jumpVelocity=this.config.INIITAL_JUMP_VELOCITY-(b/10);this.jumping=true;this.reachedMinHeight=false;this.speedDrop=false}},endJump:function(){if(this.reachedMinHeight&&this.jumpVelocity<this.config.DROP_VELOCITY){this.jumpVelocity=this.config.DROP_VELOCITY}},updateJump:function(d,f){var e=Trex.animFrames[this.status].msPerFrame;var b=d/e;if(this.speedDrop){this.yPos+=Math.round(this.jumpVelocity*this.config.SPEED_DROP_COEFFICIENT*b)}else{this.yPos+=Math.round(this.jumpVelocity*b)}this.jumpVelocity+=this.config.GRAVITY*b;if(this.yPos<this.minJumpHeight||this.speedDrop){this.reachedMinHeight=true}if(this.yPos<this.config.MAX_JUMP_HEIGHT||this.speedDrop){this.endJump()}if(this.yPos>this.groundYPos){this.reset();this.jumpCount++}this.update(d)},setSpeedDrop:function(){this.speedDrop=true;this.jumpVelocity=1},setDuck:function(b){if(b&&this.status!=Trex.status.DUCKING){this.update(0,Trex.status.DUCKING);this.ducking=true}else{if(this.status==Trex.status.DUCKING){this.update(0,Trex.status.RUNNING);this.ducking=false}}},draw:function(b,h){var g=b;var f=h;var d=this.ducking&&this.status!=Trex.status.CRASHED?this.config.WIDTH_DUCK:this.config.WIDTH;var e=this.config.HEIGHT;g+=this.spritePos.x;f+=this.spritePos.y;if(this.ducking&&this.status!=Trex.status.CRASHED){this.ctx.drawImage(imgSprite,g,f,d,e,this.xPos,this.yPos,this.config.WIDTH_DUCK,this.config.HEIGHT)}else{if(this.ducking&&this.status==Trex.status.CRASHED){this.xPos++}this.ctx.drawImage(imgSprite,g,f,d,e,this.xPos,this.yPos,this.config.WIDTH,this.config.HEIGHT)}},reset:function(){this.yPos=this.groundYPos;this.jumpVelocity=0;this.jumping=false;this.ducking=false;this.update(0,Trex.status.RUNNING);this.midair=false;this.speedDrop=false;this.jumpCount=0}};var now=getTimeStamp();window.onload=function(){var b=new Runner(".interstitial-wrapper")};</script>
', '404');
}
function become403Page() {
	header($_SERVER['SERVER_PROTOCOL'] . " 403 Forbidden", true, 403); 
	becomeMsgPage('<div class="text-center"><div style="font-size:233px">403</div><p>禁止入内！ T_T</p></div>', '403');
}

function getUserLink($username, $rating = null) {
	if (validateUsername($username) && ($user = queryUser($username))) {
		if ($rating == null) {
			$rating = $user['rating'];
		}
		if (/*$user['vip'] || (*/$user['usergroup']=="S"/*) || ($user['contribution']>10)*/) {
			$is_vip=1;
		}
		else {
			$is_vip=0;
		}
		return '<span class="uoj-username" data-rating="'.$rating.'" data-vip="'.$is_vip.'">'.$username.'</span>';
	} else {
		$esc_username = HTML::escape($username);
		return '<span>'.$esc_username.'</span>';
	}
}

function getProblemLink($problem, $problem_title = '!title_only') {
	if ($problem_title == '!title_only') {
		$problem_title = $problem['title'];
	} else if ($problem_title == '!id_and_title') {
		$problem_title = "#${problem['id']}. ${problem['title']}";
	}
	return '<a href="/problem/'.$problem['id'].'">'.$problem_title.'</a>';
}
function getContestProblemLink($problem, $contest_id, $problem_title = '!title_only') {
	if ($problem_title == '!title_only') {
		$problem_title = $problem['title'];
	} else if ($problem_title == '!id_and_title') {
		$problem_title = "#{$problem['id']}. {$problem['title']}";
	}
	return '<a href="/contest/'.$contest_id.'/problem/'.$problem['id'].'">'.$problem_title.'</a>';
}
function getBlogLink($id) {
	if (validateUInt($id) && $blog = queryBlog($id)) {
		return '<a href="/blog/'.$id.'">'.$blog['title'].'</a>';
	}
}
function getClickZanBlock($type, $id, $cnt, $val = null) {
	if ($val == null) {
		$val = queryZanVal($id, $type, Auth::user());
	}
	return '<div class="uoj-click-zan-block" data-id="'.$id.'" data-type="'.$type.'" data-val="'.$val.'" data-cnt="'.$cnt.'"></div>';
}


function getLongTablePageRawUri($page) {
		$path = strtok(UOJContext::requestURI(), '?');
		$query_string = strtok('?');
		parse_str($query_string, $param);
			
		$param['page'] = $page;
		if ($page == 1)
			unset($param['page']);
			
		if ($param) {
			return $path . '?' . http_build_query($param);
		} else {
			return $path;
		}
	}
function getLongTablePageUri($page) {
	return HTML::escape(getLongTablePageRawUri($page));
}

function echoLongTable($col_names, $table_name, $cond, $tail, $header_row, $print_row, $config) {
	$pag_config = $config;
	$pag_config['col_names'] = $col_names;
	$pag_config['table_name'] = $table_name;
	$pag_config['cond'] = $cond;
	$pag_config['tail'] = $tail;
	$pag = new Paginator($pag_config);

	$div_classes = isset($config['div_classes']) ? $config['div_classes'] : array('table-responsive');
	$table_classes = isset($config['table_classes']) ? $config['table_classes'] : array('table', 'table-bordered', 'table-hover', 'table-striped', 'table-text-center');
		
	echo '<div class="', join($div_classes, ' '), '">';
	echo '<table class="', join($table_classes, ' '), '">';
	echo '<thead>';
	echo $header_row;
	echo '</thead>';
	echo '<tbody>';

	foreach ($pag->get() as $idx => $row) {
		if (isset($config['get_row_index'])) {
			$print_row($row, $idx);
		} else {
			$print_row($row);
		}
	}
	if ($pag->isEmpty()) {
		echo '<tr><td colspan="233">'.UOJLocale::get('none').'</td></tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	
	if (isset($config['print_after_table'])) {
		$fun = $config['print_after_table'];
		$fun();
	}
		
	echo $pag->pagination();
}

function echoLongTableForRank($col_names, $table_name, $cond, $tail, $header_row, $print_row, $config) {
	$pag_config = $config;
	$pag_config['col_names'] = $col_names;
	$pag_config['table_name'] = $table_name;
	$pag_config['cond'] = $cond;
	$pag_config['tail'] = $tail;
	$pag = new Paginator($pag_config);
	$div_classes = isset($config['div_classes']) ? $config['div_classes'] : array('table-responsive');
	$table_classes = isset($config['table_classes']) ? $config['table_classes'] : array('table', 'table-bordered', 'table-hover', 'table-striped', 'table-text-center');
		
	echo '<table class="table table-fixed">';
	echo '<thead>';
	echo $header_row;
	echo '</thead>';
	echo '<tbody>';
	foreach ($pag->get() as $idx => $row) {
		if (isset($config['get_row_index'])) {
			$print_row($row, $idx);
		} else {
			$print_row($row);
		}
	}
	if ($pag->isEmpty()) {
		echo '<tr><td colspan="233">'.UOJLocale::get('none').'</td></tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
	if (isset($config['print_after_table'])) {
		$fun = $config['print_after_table'];
		$fun();
	}
		
	echo $pag->pagination();
}

function getSubmissionStatusDetails($submission) {
	$html = '<td colspan="233" style="vertical-align: middle">';
	
	$out_status = explode(', ', $submission['status'])[0];
	
	$fly = '<img src="/pictures/bear-flying.gif" alt="小熊像超人一样飞" class="img-rounded" />';
	$think = '<img src="/pictures/bear-thinking.gif" alt="小熊像在思考" class="img-rounded" />';
	
	if ($out_status == 'Judged') {
		$status_text = '<strong>Judged!</strong>';
		$status_img = $fly;
	} else {
		if ($submission['status_details'] !== '') {
			$status_img = $fly;
			$status_text = HTML::escape($submission['status_details']);
		} else  {
			$status_img = $think;
			$status_text = $out_status;
		}
	}
	$html .= '<div class="uoj-status-details-img-div">' . $status_img . '</div>';
	$html .= '<div class="uoj-status-details-text-div">' . $status_text . '</div>';

	$html .= '</td>';
	return $html;
}

function echoSubmission($submission, $config, $user) {
	$problem = queryProblemBrief($submission['problem_id']);
	$submitterLink = getUserLink($submission['submitter']);
	
	if ($submission['score'] == null) {
		$used_time_str = "/";
		$used_memory_str = "/";
	} else {
		$used_time_str = $submission['used_time'] . 'ms';
		$used_memory_str = $submission['used_memory'] . 'kb';
	}
	
	$status = explode(', ', $submission['status'])[0];
	
	$show_status_details = Auth::check() && $submission['submitter'] === Auth::id() && $status !== 'Judged';
	
	if (!$show_status_details) {
		echo '<tr>';
	} else {
		echo '<tr class="warning">';
	}
	if (!isset($config['id_hidden'])) {
		echo '<td><a href="/submission/', $submission['id'], '">#', $submission['id'], '</a></td>';
	}
	if (!isset($config['problem_hidden'])) {
		if ($submission['contest_id']) {
			echo '<td>', getContestProblemLink($problem, $submission['contest_id'], '!id_and_title'), '</td>';
		} else {
			echo '<td>', getProblemLink($problem, '!id_and_title'), '</td>';
		}
	}
	if (!isset($config['submitter_hidden'])) {
		echo '<td>', $submitterLink, '</td>';
	}
	if (!isset($config['result_hidden'])) {
		echo '<td>';
		if ($status == 'Judged') {
			if ($submission['score'] == null) {
				echo '<a href="/submission/', $submission['id'], '" class="small">', $submission['result_error'], '</a>';
			} else {
				echo '<a href="/submission/', $submission['id'], '" class="uoj-score">', $submission['score'], '</a>';
			}
		} else {
			echo '<a href="/submission/', $submission['id'], '" class="small">', $status, '</a>';
		}
		echo '</td>';
	}
	if (!isset($config['used_time_hidden']))
		echo '<td>', $used_time_str, '</td>';
	if (!isset($config['used_memory_hidden']))
		echo '<td>', $used_memory_str, '</td>';

	echo '<td>', '<a href="/submission/', $submission['id'], '">', $submission['language'], '</a>', '</td>';

	if ($submission['tot_size'] < 1024) {
		$size_str = $submission['tot_size'] . 'b';
	} else {
		$size_str = sprintf("%.1f", $submission['tot_size'] / 1024) . 'kb';
	}
	echo '<td>', $size_str, '</td>';

	if (!isset($config['submit_time_hidden']))
		echo '<td><small>', $submission['submit_time'], '</small></td>';
	/*if (!isset($config['judge_time_hidden']))
		echo '<td><small>', $submission['judge_time'], '</small></td>';*/
	echo '</tr>';
	if ($show_status_details) {
		echo '<tr id="', "status_details_{$submission['id']}", '" class="info">';
		echo getSubmissionStatusDetails($submission);
		echo '</tr>';
		echo '<script type="text/javascript">update_judgement_status_details('.$submission['id'].')</script>';
	}
}


function echoSubmissionsListOnlyOne($submission, $config, $user) {
	echo '<div class="table-responsive">';
	echo '<table class="table table-text-center">';
	echo '<thead>';
	echo '<tr>';
	if (!isset($config['id_hidden']))
		echo '<th>ID</th>';
	if (!isset($config['problem_hidden']))
		echo '<th>'.UOJLocale::get('problems::problem').'</th>';
	if (!isset($config['submitter_hidden']))
		echo '<th>'.UOJLocale::get('problems::submitter').'</th>';
	if (!isset($config['result_hidden']))
		echo '<th>'.UOJLocale::get('problems::result').'</th>';
	if (!isset($config['used_time_hidden']))
		echo '<th>'.UOJLocale::get('problems::used time').'</th>';
	if (!isset($config['used_memory_hidden']))
		echo '<th>'.UOJLocale::get('problems::used memory').'</th>';
	echo '<th>'.UOJLocale::get('problems::language').'</th>';
	echo '<th>'.UOJLocale::get('problems::file size').'</th>';
	if (!isset($config['submit_time_hidden']))
		echo '<th>'.UOJLocale::get('problems::submit time').'</th>';
	/*if (!isset($config['judge_time_hidden']))
		echo '<th>'.UOJLocale::get('problems::judge time').'</th>';*/
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echoSubmission($submission, $config, $user);
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}


function echoSubmissionsList($cond, $tail, $config, $user) {
	$header_row = '<tr>';
	$col_names = array();
	$col_names[] = 'submissions.status_details';
	$col_names[] = 'submissions.status';
	$col_names[] = 'submissions.result_error';
	$col_names[] = 'submissions.score';
	
	if (!isset($config['id_hidden'])) {
		$header_row .= '<th>ID</th>';
		$col_names[] = 'submissions.id';
	}
	if (!isset($config['problem_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::problem').'</th>';
		$col_names[] = 'submissions.problem_id';
		$col_names[] = 'submissions.contest_id';
	}
	if (!isset($config['submitter_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::submitter').'</th>';
		$col_names[] = 'submissions.submitter';
	}
	if (!isset($config['result_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::result').'</th>';
	}
	if (!isset($config['used_time_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::used time').'</th>';
		$col_names[] = 'submissions.used_time';
	}
	if (!isset($config['used_memory_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::used memory').'</th>';
		$col_names[] = 'submissions.used_memory';
	}
	$header_row .= '<th>'.UOJLocale::get('problems::language').'</th>';
	$col_names[] = 'submissions.language';
	$header_row .= '<th>'.UOJLocale::get('problems::file size').'</th>';
	$col_names[] = 'submissions.tot_size';

	if (!isset($config['submit_time_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::submit time').'</th>';
		$col_names[] = 'submissions.submit_time';
	}
	if (!isset($config['judge_time_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::judge time').'</th>';
		$col_names[] = 'submissions.judge_time';
	}
	$header_row .= '</tr>';
	
	$table_name = isset($config['table_name']) ? $config['table_name'] : 'submissions';
	
	if (!isSuperUser($user)) {
		if ($user != null) {
			$permission_cond = "submissions.is_hidden = false or (submissions.is_hidden = true and submissions.problem_id in (select problem_id from problems_permissions where username = '{$user['username']}')) or (submissions.is_hidden = true and submissions.submitter = '{$user['username']}') or (submissions.is_hidden = true and submissions.contest_id in (select contest_id from contests_registrants where username = '{$user['username']}'))";
		} else {
			$permission_cond = "submissions.is_hidden = false";
		}
		if ($cond !== '1') {
			$cond = "($cond) and ($permission_cond)";
		} else {
			$cond = $permission_cond;
		}
	}
	
	$table_config = isset($config['table_config']) ? $config['table_config'] : null;
	
	echoLongTable($col_names, $table_name, $cond, $tail, $header_row,
		function($submission) use($config, $user) {
			echoSubmission($submission, $config, $user);
		}, $table_config);
}


function echoSubmissionContent($submission, $requirement) {
	$zip_file = new ZipArchive();
	$submission_content = json_decode($submission['content'], true);
	$zip_file->open(UOJContext::storagePath().$submission_content['file_name']);
	
	$config = array();
	foreach ($submission_content['config'] as $config_key => $config_val) {
		$config[$config_val[0]] = $config_val[1];
	}
	
	foreach ($requirement as $req) {
		if ($req['type'] == "source code") {
			$file_content = $zip_file->getFromName("{$req['name']}.code");
			$file_content = uojTextEncode($file_content, array('allow_CR' => true, 'html_escape' => true));
			$file_language = htmlspecialchars($config["{$req['name']}_language"]);
			$footer_text = UOJLocale::get('problems::source code').', '.UOJLocale::get('problems::language').': '.$file_language;
			switch ($file_language) {
				case 'C++':
				case 'C++11':
					$sh_class = 'sh_cpp';
					break;
				case 'Python2.7':
				case 'Python3':
					$sh_class = 'sh_python';
					break;
				case 'Java7':
				case 'Java8':
					$sh_class = 'sh_java';
					break;
				case 'C':
					$sh_class = 'sh_c';
					break;
				case 'Pascal':
					$sh_class = 'sh_pascal';
					break;
				default:
					$sh_class = '';
					break;
			}
			//echo '<div class="panel panel-info">';
			//echo '<div class="panel-heading">';
			//echo '<h4 class="panel-title">'.$req['name'].'</h4>';
			//echo '</div>';
			//echo '<div class="panel-body">';
			echo '<pre><code class="'.$sh_class.'">'.$file_content."\n".'</code></pre>';
			//echo '</div>';
			//echo '<div class="panel-footer">'.$footer_text.'</div>';
			//echo '</div>';
		}
		else if ($req['type'] == "text") {
			$file_content = $zip_file->getFromName("{$req['file_name']}", 504);
			$file_content = strOmit($file_content, 500);
			$file_content = uojTextEncode($file_content, array('allow_CR' => true, 'html_escape' => true));
			$footer_text = UOJLocale::get('problems::text file');
			//echo '<div class="panel panel-info">';
			//echo '<div class="panel-heading">';
			//echo '<h4 class="panel-title">'.$req['file_name'].'</h4>';
			//echo '</div>';
			//echo '<div class="panel-body">';
			echo '<pre>'."\n".$file_content."\n".'</pre>';
			//echo '</div>';
			//echo '<div class="panel-footer">'.$footer_text.'</div>';
			//echo '</div>';
		}
	}

	$zip_file->close();
}


class JudgementDetailsPrinter {
	private $name;
	private $styler;
	private $dom;
	
	private $subtask_num;

	private function _print_c($node) {
		foreach ($node->childNodes as $child) {
			if ($child->nodeName == '#text') {
				echo htmlspecialchars($child->nodeValue);
			} else {
				$this->_print($child);
			}
		}
	}
	private function _print($node) {
		if ($node->nodeName == 'error') {
			echo "<pre>\n";
			$this->_print_c($node);
			echo "\n</pre>";
		} elseif ($node->nodeName == 'tests') {
			echo '<div class="panel-group" id="', $this->name, '_details_accordion">';
			/*if ($this->styler->show_small_tip) {
				echo '<div class="text-right text-muted">', '小提示：点击横条可展开更详细的信息', '</div>';
			}
			elseif ($this->styler->ioi_contest_is_running) {
				echo '<div class="text-right text-muted">', 'IOI赛制比赛中不支持显示详细信息', '</div>';
			}*/
			$this->_print_c($node);
			echo '</div>';
		} elseif ($node->nodeName == 'subtask') {
			$subtask_num = $node->getAttribute('num');
			$subtask_score = $node->getAttribute('score');
			$subtask_info = $node->getAttribute('info');
			
			echo '<div class="panel ', $this->styler->getTestInfoClass($subtask_info), '">';
			
			$accordion_parent = "{$this->name}_details_accordion";
			$accordion_collapse =  "{$accordion_parent}_collapse_subtask_{$subtask_num}";
			$accordion_collapse_accordion =  "{$accordion_collapse}_accordion";
			echo 	'<div class="panel-heading" data-toggle="collapse" data-parent="#', $accordion_parent, '" data-target="#', $accordion_collapse, '">';
			
			echo 		'<div class="row">';
			echo 			'<div class="col-sm-2">';
			echo 				'<h3 class="panel-title">', 'Subtask #', $subtask_num, ': ', '</h3>';
			echo 			'</div>';
			
			if ($this->styler->show_score) {
				echo 		'<div class="col-sm-2">';
				echo 			'score: ', $subtask_score;
				echo 		'</div>';
				echo 		'<div class="col-sm-2">';
				echo 			htmlspecialchars($subtask_info);
				echo 		'</div>';
			} else {
				echo 		'<div class="col-sm-4">';
				echo 			htmlspecialchars($subtask_info);
				echo 		'</div>';
			}

			echo 		'</div>';
			echo 	'</div>';
			
			echo 	'<div id="', $accordion_collapse, '" class="panel-collapse collapse">';
			echo 		'<div class="panel-body">';

			echo 			'<div id="', $accordion_collapse_accordion, '" class="panel-group">';
			$this->subtask_num = $subtask_num;
			$this->_print_c($node);
			$this->subtask_num = null;
			echo 			'</div>';

			echo 		'</div>';
			echo 	'</div>';
			echo '</div>';
		} elseif ($node->nodeName == 'test') {
			$test_info = $node->getAttribute('info');
			$test_num = $node->getAttribute('num');
			$test_score = $node->getAttribute('score');
			$test_time = $node->getAttribute('time');
			$test_memory = $node->getAttribute('memory');

			echo '<div class="panel ', $this->styler->getTestInfoClass($test_info), '">';
			
			$accordion_parent = "{$this->name}_details_accordion";
			if ($this->subtask_num != null) {
				$accordion_parent .= "_collapse_subtask_{$this->subtask_num}_accordion";
			}
			$accordion_collapse = "{$accordion_parent}_collapse_test_{$test_num}";
			if (!$this->styler->shouldFadeDetails($test_info)) {
				echo '<div class="panel-heading" data-toggle="collapse" data-parent="#', $accordion_parent, '" data-target="#', $accordion_collapse, '">';
			} else {
				echo '<div class="panel-heading">';
			}
			echo '<div class="row">';
			echo '<div class="col-sm-2">';
			if ($test_num > 0) {
				echo '<h4 class="panel-title">', 'Test #', $test_num, ': ', '</h4>';
			} else {
				echo '<h4 class="panel-title">', 'Extra Test:', '</h4>';
			}
			echo '</div>';
				
			if ($this->styler->show_score) {
				echo '<div class="col-sm-2">';
				echo 'score: ', $test_score;
				echo '</div>';
				echo '<div class="col-sm-2">';
				echo htmlspecialchars($test_info);
				echo '</div>';
			} else {
				echo '<div class="col-sm-4">';
				echo htmlspecialchars($test_info);
				echo '</div>';
			}
				
			echo '<div class="col-sm-3">';
			if ($test_time >= 0) {
				echo 'time: ', $test_time, 'ms';
			}
			echo '</div>';

			echo '<div class="col-sm-3">';
			if ($test_memory >= 0) {
				echo 'memory: ', $test_memory, 'kb';
			}
			echo '</div>';

			echo '</div>';
			echo '</div>';

			if (!$this->styler->shouldFadeDetails($test_info)) {
				$accordion_collapse_class = 'panel-collapse collapse';
				if ($this->styler->collapse_in) {
					$accordion_collapse_class .= ' in';
				}
				echo '<div id="', $accordion_collapse, '" class="', $accordion_collapse_class, '">';
				echo '<div class="panel-body">';

				$this->_print_c($node);

				echo '</div>';
				echo '</div>';
			}

			echo '</div>';
		} elseif ($node->nodeName == 'custom-test') {
			$test_info = $node->getAttribute('info');
			$test_time = $node->getAttribute('time');
			$test_memory = $node->getAttribute('memory');

			echo '<div class="panel ', $this->styler->getTestInfoClass($test_info), '">';
			
			$accordion_parent = "{$this->name}_details_accordion";
			$accordion_collapse = "{$accordion_parent}_collapse_custom_test";
			if (!$this->styler->shouldFadeDetails($test_info)) {
				echo '<div class="panel-heading" data-toggle="collapse" data-parent="#', $accordion_parent, '" data-target="#', $accordion_collapse, '">';
			} else {
				echo '<div class="panel-heading">';
			}
			echo '<div class="row">';
			echo '<div class="col-sm-2">';
			echo '<h4 class="panel-title">', 'Custom Test: ', '</h4>';
			echo '</div>';
				
			echo '<div class="col-sm-4">';
			echo htmlspecialchars($test_info);
			echo '</div>';
				
			echo '<div class="col-sm-3">';
			if ($test_time >= 0) {
				echo 'time: ', $test_time, 'ms';
			}
			echo '</div>';

			echo '<div class="col-sm-3">';
			if ($test_memory >= 0) {
				echo 'memory: ', $test_memory, 'kb';
			}
			echo '</div>';

			echo '</div>';
			echo '</div>';

			if (!$this->styler->shouldFadeDetails($test_info)) {
				$accordion_collapse_class = 'panel-collapse collapse';
				if ($this->styler->collapse_in) {
					$accordion_collapse_class .= ' in';
				}
				echo '<div id="', $accordion_collapse, '" class="', $accordion_collapse_class, '">';
				echo '<div class="panel-body">';

				$this->_print_c($node);

				echo '</div>';
				echo '</div>';

				echo '</div>';
			}
		} elseif ($node->nodeName == 'in') {
			echo "<h4>input:</h4><pre>\n";
			$this->_print_c($node);
			echo "\n</pre>";
		} elseif ($node->nodeName == 'out') {
			echo "<h4>output:</h4><pre>\n";
			$this->_print_c($node);
			echo "\n</pre>";
		} elseif ($node->nodeName == 'res') {
			echo "<h4>result:</h4><pre>\n";
			$this->_print_c($node);
			echo "\n</pre>";
		} else {
			echo '<', $node->nodeName;
			foreach ($node->attributes as $attr) {
				echo ' ', $attr->name, '="', htmlspecialchars($attr->value), '"';
			}
			echo '>';
			$this->_print_c($node);
			echo '</', $node->nodeName, '>';
		}
	}

	public function __construct($details, $styler, $name) {
		$this->name = $name;
		$this->styler = $styler;
		$this->details = $details;
		$this->dom = new DOMDocument();
		if (!$this->dom->loadXML($this->details)) {
			throw new Exception("XML syntax error");
		}
		$this->details = '';
	}
	public function printHTML() {
		$this->subtask_num = null;
		$this->_print($this->dom->documentElement);
	}
}

function echoJudgementDetails($raw_details, $styler, $name) {
	try {
		$printer = new JudgementDetailsPrinter($raw_details, $styler, $name);
		$printer->printHTML();
	} catch (Exception $e) {
		echo 'Failed to show details';
	}
}

class SubmissionDetailsStyler {
	public $show_score = true;
	public $show_small_tip = true;
	public $collapse_in = false;
	public $fade_all_details = false;
	public function getTestInfoClass($info) {
		if ($info == 'Accepted' || $info == 'Extra Test Passed') {
			return 'panel-uoj-accepted';
		} elseif ($info == 'Time Limit Exceeded') {
			return 'panel-uoj-tle';
		} elseif ($info == 'Acceptable Answer') {
			return 'panel-uoj-acceptable-answer';
		} else {
			return 'panel-uoj-wrong';
		}
	}
	public function shouldFadeDetails($info) {
		return $this->fade_all_details || $info == 'Extra Test Passed';
	}
}
class CustomTestSubmissionDetailsStyler {
	public $show_score = true;
	public $show_small_tip = false;
	public $collapse_in = true;
	public $fade_all_details = false;
	public $ioi_contest_is_running = false;
	public function getTestInfoClass($info) {
		if ($info == 'Success') {
			return 'panel-uoj-accepted';
		} elseif ($info == 'Time Limit Exceeded') {
			return 'panel-uoj-tle';
		} elseif ($info == 'Acceptable Answer') {
			return 'panel-uoj-acceptable-answer';
		} else {
			return 'panel-uoj-wrong';
		}
	}
	public function shouldFadeDetails($info) {
		return $this->fade_all_details;
	}
}
class HackDetailsStyler {
	public $show_score = false;
	public $show_small_tip = false;
	public $collapse_in = true;
	public $fade_all_details = false;
	public function getTestInfoClass($info) {
		if ($info == 'Accepted' || $info == 'Extra Test Passed') {
			return 'panel-uoj-accepted';
		} elseif ($info == 'Time Limit Exceeded') {
			return 'panel-uoj-tle';
		} elseif ($info == 'Acceptable Answer') {
			return 'panel-uoj-acceptable-answer';
		} else {
			return 'panel-uoj-wrong';
		}
	}
	public function shouldFadeDetails($info) {
		return $this->fade_all_details;
	}
}

function echoSubmissionDetails($submission_details, $name) {
	echoJudgementDetails($submission_details, new SubmissionDetailsStyler(), $name);
}
function echoCustomTestSubmissionDetails($submission_details, $name) {
	echoJudgementDetails($submission_details, new CustomTestSubmissionDetailsStyler(), $name);
}
function echoHackDetails($hack_details, $name) {
	echoJudgementDetails($hack_details, new HackDetailsStyler(), $name);
}

function echoHack($hack, $config, $user) {
	$problem = queryProblemBrief($hack['problem_id']);
	echo '<tr>';
	if (!isset($config['id_hidden']))
		echo '<td><a href="/hack/', $hack['id'], '">#', $hack['id'], '</a></td>';
	if (!isset($config['submission_hidden']))
		echo '<td><a href="/submission/', $hack['submission_id'], '">#', $hack['submission_id'], '</a></td>';
	if (!isset($config['problem_hidden'])) {
		if ($hack['contest_id']) {
			echo '<td>', getContestProblemLink($problem, $hack['contest_id'], '!id_and_title'), '</td>';
		} else {
			echo '<td>', getProblemLink($problem, '!id_and_title'), '</td>';
		}
	}
	if (!isset($config['hacker_hidden']))
		echo '<td>', getUserLink($hack['hacker']), '</td>';
	if (!isset($config['owner_hidden']))
		echo '<td>', getUserLink($hack['owner']), '</td>';
	if (!isset($config['result_hidden']))
	{
		if($hack['judge_time'] == null) {
			echo '<td><a href="/hack/', $hack['id'], '">Waiting</a></td>';
		} elseif ($hack['success'] == null) {
			echo '<td><a href="/hack/', $hack['id'], '">Judging</a></td>';
		} elseif ($hack['success']) {
			echo '<td><a href="/hack/', $hack['id'], '" class="uoj-status" data-success="1"><strong>Success!</strong></a></td>';
		} else {
			echo '<td><a href="/hack/', $hack['id'], '" class="uoj-status" data-success="0"><strong>Failed.</strong></a></td>';
		}
	}
	else
		echo '<td>Hidden</td>';
	if (!isset($config['submit_time_hidden']))
		echo '<td>', $hack['submit_time'], '</td>';
	if (!isset($config['judge_time_hidden']))
		echo '<td>', $hack['judge_time'], '</td>';
	echo '</tr>';
}
function echoHackListOnlyOne($hack, $config, $user) {
	echo '<div class="table-responsive">';
	echo '<table class="table table-bordered table-text-center">';
	echo '<thead>';
	echo '<tr>';
	if (!isset($config['id_hidden']))
		echo '<th>ID</th>';
	if (!isset($config['submission_id_hidden']))
		echo '<th>'.UOJLocale::get('problems::submission id').'</th>';
	if (!isset($config['problem_hidden']))
		echo '<th>'.UOJLocale::get('problems::problem').'</th>';
	if (!isset($config['hacker_hidden']))
		echo '<th>'.UOJLocale::get('problems::hacker').'</th>';
	if (!isset($config['owner_hidden']))
		echo '<th>'.UOJLocale::get('problems::owner').'</th>';
	if (!isset($config['result_hidden']))
		echo '<th>'.UOJLocale::get('problems::result').'</th>';
	if (!isset($config['submit_time_hidden']))
		echo '<th>'.UOJLocale::get('problems::submit time').'</th>';
	if (!isset($config['judge_time_hidden']))
		echo '<th>'.UOJLocale::get('problems::judge time').'</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	echoHack($hack, $config, $user);
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}
function echoHacksList($cond, $tail, $config, $user) {
	$header_row = '<tr>';
	$col_names = array();
	
	$col_names[] = 'id';
	$col_names[] = 'success';
	$col_names[] = 'judge_time';
	
	if (!isset($config['id_hidden'])) {
		$header_row .= '<th>ID</th>';
	}
	if (!isset($config['submission_id_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::submission id').'</th>';
		$col_names[] = 'submission_id';
	}
	if (!isset($config['problem_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::problem').'</th>';
		$col_names[] = 'problem_id';
	}
	if (!isset($config['hacker_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::hacker').'</th>';
		$col_names[] = 'hacker';
	}
	if (!isset($config['owner_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::owner').'</th>';
		$col_names[] = 'owner';
	}
	if (!isset($config['result_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::result').'</th>';
	}
	if (!isset($config['submit_time_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::submit time').'</th>';
		$col_names[] = 'submit_time';
	}
	if (!isset($config['judge_time_hidden'])) {
		$header_row .= '<th>'.UOJLocale::get('problems::judge time').'</th>';
	}
	$header_row .= '</tr>';

	if (!isSuperUser($user)) {
		if ($user != null) {
			$permission_cond = "is_hidden = false or (is_hidden = true and problem_id in (select problem_id from problems_permissions where username = '{$user['username']}'))";
		} else {
			$permission_cond = "is_hidden = false";
		}
		if ($cond !== '1') {
			$cond = "($cond) and ($permission_cond)";
		} else {
			$cond = $permission_cond;
		}
	}

	echoLongTable($col_names, 'hacks', $cond, $tail, $header_row,
		function($hacks) use($config, $user) {
			echoHack($hacks, $config, $user);
		}, null);
}

function echoBlog($blog, $config = array()) {
	$default_config = array(
		'blog' => $blog,
		'show_title_only' => false,
		'is_preview' => false
	);
	foreach ($default_config as $key => $val) {
		if (!isset($config[$key])) {
			$config[$key] = $val;
		}
	}
	uojIncludeView('blog-preview', $config);
}
function echoBlogTag($tag, $user) {
	echo '<a class="uoj-blog-tag" href="/'.blog_name_encode($user).'/blog?tag='.HTML::escape($tag).'"><span class="badge">', HTML::escape($tag), '</span></a>';
}

function echoSol($blog, $config = array()) {
	$default_config = array(
		'blog' => $blog,
		'show_title_only' => false,
		'is_preview' => false
	);
	foreach ($default_config as $key => $val) {
		if (!isset($config[$key])) {
			$config[$key] = $val;
		}
	}
	uojIncludeView('sol-preview', $config);
}

function echoUOJPageHeader($page_title, $extra_config = array()) {
	global $REQUIRE_LIB;
	$config = UOJContext::pageConfig();
	$config['REQUIRE_LIB'] = $REQUIRE_LIB;
	$config['PageTitle'] = $page_title;
	$config = array_merge($config, $extra_config);
	uojIncludeView('page-header', $config);
}
function echoUOJPageFooter($config = array()) {
	uojIncludeView('page-footer', $config);
}

function echoRanklist($config = array()) {
	$header_row = '';
	$header_row .= '<tr>';
	$header_row .= '<th style="width: 5em;">#</th>';
	$header_row .= '<th style="width: 14em;">'.UOJLocale::get('username').'</th>';
	$header_row .= '<th style="width: 50em;">'.UOJLocale::get('motto').'</th>';
	$header_row .= '<th style="width: 5em;">'.UOJLocale::get('rating').'</th>';
	$header_row .= '</tr>';
	
	$users = array();
	$print_row = function($user, $now_cnt) use(&$users) {
		if (!$users) {
			$rank = DB::selectCount("select count(*) from user_info where rating > {$user['rating']}") + 1;
		} else if ($user['rating'] == $users[count($users) - 1]['rating']) {
			$rank = $users[count($users) - 1]['rank'];
		} else {
			$rank = $now_cnt;
		}
		
		$user['rank'] = $rank;
		
		echo '<tr>';
		echo '<td>' . $user['rank'] . '</td>';
		echo '<td>' . getUserLink($user['username']) . '</td>';
		echo '<td>' . HTML::escape($user['motto']) . '</td>';
		echo '<td>' . $user['rating'] . '</td>';
		echo '</tr>';
		
		$users[] = $user;
	};
	$col_names = array('username', 'rating', 'motto');
	$tail = 'order by rating desc, username asc';
	
	if (isset($config['top10'])) {
		$tail .= ' limit 10';
	}
	
	$config['get_row_index'] = '';
	echoLongTableForRank($col_names, 'user_info', '1', $tail, $header_row, $print_row, $config);
}
