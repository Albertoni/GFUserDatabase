<!DOCTYPE html>
<html>
<head>
	<base href="http://{$smarty.server.HTTP_HOST}/gfusers/" />
	<title>{if $title}{$title|htmlspecialchars} - {/if}GameFAQs User Database</title>
	<link href="r/c?1" type="text/css" rel="stylesheet" />
	<script src="r/j?1" type="text/javascript"></script>
	<link rel="shortcut icon" href="r/favicon.png" />
	<script type="text/javascript">
		{literal}
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-19014181-1']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = 'http://www.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		{/literal}
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
							{if $login_name}
								Logged in as&nbsp;<strong>{$login_name|htmlspecialchars}</strong>
							{else}
								Log in
							{/if}
						</a>
						<ul class="dropmenu" id="dropmenu-login">
							{if $login_name}
								<li class="pad">You have fetched a total of&nbsp;<strong>{$login_num_users}</strong>&nbsp;username{if $login_num_users != 1}s{/if} while logged in.</li>
								<li class="pad">Users you add to the database will be credited to your name.</li>
								<li><a href="javascript:Site.logout();">Log out</a></li>
							{else}
								{if $num_users_added_this_session == 0}
									<li class="pad">After logging in, users you add to the database will be credited to your name.</li>
								{elseif $num_users_added_this_session == 1}
									<li class="pad">You have recently fetched 1 user while not logged in.</li>
									<li class="pad">Log in below to have this user credited to your name.</li>
								{else}
									<li class="pad">You have recently fetched {$num_users_added_this_session} users while not logged in.</li>
									<li class="pad">Log in below to have these users credited to your name.</li>
								{/if}
								<li class="pad">
									<input type="text" id="username-input" placeholder="Write your name here" />
								</li>
							{/if}
						</ul>
					</li>
				</ul>
			</div>

			<form id="awesome-form" action="/" method="post">
				<input type="text" id="awesome-bar" placeholder="Enter a topic URL, user ID or username" autocomplete="off" />

				{if $show_intro}
					<div class="bubble-wrap" id="awesome-bubble">
						<div class="bubble-head"></div>
						<div class="bubble-body">
							<div class="heading">Say hello to the Awesome Bar</div>
							<p>Enter a GameFAQs topic URL to fetch it, a user's ID to look up their username or enter anything else to search.</p>
							<button id="close-awesome-bubble">That's awesome!</button>
						</div>
					</div>
				{/if}

				<div id="helper-text">&nbsp;</div>
			</form>

		</div>

	</div>

	<div id="content">
		<div class="container" id="content-inner">
			{include file="$template"}
		</div>
	</div>

	<div id="footer">
		<div class="container">
			<ul id="stats">
				<li><span id="stats-total">{$stats.num_realUsers|number_format}</span>&nbsp;recorded users</li>
				<li><span id="stats-percent">{$stats.percent|number_format:2}</span>% complete</li>
				<li>Last addition:&nbsp;<span id="stats-time">{$stats.timestamp|relative_time}</span></li>
				<li><a href="Stats">More stats</a></li>
			</ul>
			<div id="credit">
				Designed and developed by Ryan Dwyer
			</div>
		</div>
	</div>

</body>
</html>


