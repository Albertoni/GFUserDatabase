<?php /* Smarty version 3.0rc1, created on 2011-08-22 06:59:02
         compiled from "app/templates/main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5810010514e5236769c8f78-69444936%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '277757febb2f9036b4ae7ace98162bde1552ce9f' => 
    array (
      0 => 'app/templates/main.tpl',
      1 => 1314010739,
    ),
  ),
  'nocache_hash' => '5810010514e5236769c8f78-69444936',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_relative_time')) include '/home7/thengamer/public_html/youreliteness/gfusers/app/lib/smarty/plugins/modifier.relative_time.php';
?><!DOCTYPE html>
<html>
<head>
	<base href="http://<?php echo $_SERVER['HTTP_HOST'];?>
/gfusers/" />
	<title><?php if ($_smarty_tpl->getVariable('title')->value){?><?php echo htmlspecialchars($_smarty_tpl->getVariable('title')->value);?>
 - <?php }?>GameFAQs User Database</title>
	<link href="r/c?1" type="text/css" rel="stylesheet" />
	<script src="r/j?1" type="text/javascript"></script>
	<link rel="shortcut icon" href="r/favicon.png" />
	<script type="text/javascript">
		
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-19014181-1']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = 'http://www.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		
	</script>
</head>
<body>

	<div id="header">

		<div class="container">

			<div id="top-bar">
				<ul id="nav">
					<li id="nav-home"><a href="/gfusers/">GameFAQs User Database</a></li>
					<li class="has-dropmenu">
						<a href="Browse/By-Name">Browse the list</a>
						<a href="javascript:Site.toggleMenu('browse');" class="dropmenu-indicator">Open</a>
						<ul class="dropmenu" id="dropmenu-browse">
							<li><a href="Browse/By-Name">By username (A-Z)</a></li>
							<li><a href="Browse/By-ID">By numerical ID</a></li>
						</ul>
					</li>
					<li><a href="Stats">Statistics</a></li>
					<li class="has-dropmenu">
						<a href="javascript:Site.toggleMenu('more');" class="with-dropmenu-indicator">More</a>
						<ul class="dropmenu" id="dropmenu-more">
							<li><a href="/forum/?f=gfuserdb">Discussion forums</a></li>
							<!--li><a href="#">Download the full list</a></li>
							<li><a href="#">Get the AutoFetch plugin</a></li>
							<li><a href="#">Help & about</a></li-->
						</ul>
					</li>
					<li id="login" class="has-dropmenu">
						<a href="javascript:Site.toggleMenu('login');" class="with-dropmenu-indicator">
							<?php if ($_smarty_tpl->getVariable('login_name')->value){?>
								Logged in as&nbsp;<strong><?php echo htmlspecialchars($_smarty_tpl->getVariable('login_name')->value);?>
</strong>
							<?php }else{ ?>
								Log in
							<?php }?>
						</a>
						<ul class="dropmenu" id="dropmenu-login">
							<?php if ($_smarty_tpl->getVariable('login_name')->value){?>
								<li class="pad">You have fetched a total of&nbsp;<strong><?php echo $_smarty_tpl->getVariable('login_num_users')->value;?>
</strong>&nbsp;user<?php if ($_smarty_tpl->getVariable('login_num_users')->value!=1){?>s<?php }?> while logged in.</li>
								<li class="pad">Users you add to the database will be credited to your name.</li>
								<li><a href="javascript:Site.logout();">Log out</a></li>
							<?php }else{ ?>
								<?php if ($_smarty_tpl->getVariable('num_users_added_this_session')->value==0){?>
									<li class="pad">After logging in, users you add to the database will be credited to your name.</li>
								<?php }elseif($_smarty_tpl->getVariable('num_users_added_this_session')->value==1){?>
									<li class="pad">You have recently fetched 1 user while not logged in.</li>
									<li class="pad">Log in below to have this user credited to your name.</li>
								<?php }else{ ?>
									<li class="pad">You have recently fetched <?php echo $_smarty_tpl->getVariable('num_users_added_this_session')->value;?>
 users while not logged in.</li>
									<li class="pad">Log in below to have these users credited to your name.</li>
								<?php }?>
								<li class="pad">
									<input type="text" id="username-input" placeholder="Write your name here" />
								</li>
							<?php }?>
						</ul>
					</li>
				</ul>
			</div>

			<form id="awesome-form" action="/" method="post">
				<input type="text" id="awesome-bar" placeholder="Enter a topic URL, user ID or username" autocomplete="off" />

				<?php if ($_smarty_tpl->getVariable('show_intro')->value){?>
					<div class="bubble-wrap" id="awesome-bubble">
						<div class="bubble-head"></div>
						<div class="bubble-body">
							<div class="heading">Say hello to the Awesome Bar</div>
							<p>Enter a GameFAQs topic URL to fetch it, a user's ID to look up their username or enter anything else to search.</p>
							<button id="close-awesome-bubble">That's awesome!</button>
						</div>
					</div>
				<?php }?>

				<div id="helper-text">&nbsp;</div>
			</form>

		</div>

	</div>

	<div id="content">
		<div class="container" id="content-inner">
			<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('template')->value), $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

		</div>
	</div>

	<div id="footer">
		<div class="container">
			<ul id="stats">
				<li><span id="stats-total"><?php echo number_format($_smarty_tpl->getVariable('stats')->value['num_users']);?>
</span>&nbsp;recorded users</li>
				<li><span id="stats-percent"><?php echo number_format($_smarty_tpl->getVariable('stats')->value['percent'],2);?>
</span>% complete</li>
				<li>Last addition:&nbsp;<span id="stats-time"><?php echo smarty_modifier_relative_time($_smarty_tpl->getVariable('stats')->value['timestamp']);?>
</span></li>
				<li><a href="Stats">More stats</a></li>
			</ul>
			<div id="credit">
				Designed and developed by Ryan Dwyer
			</div>
		</div>
	</div>

</body>
</html>


