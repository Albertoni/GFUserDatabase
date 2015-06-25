<h1>Welcome to the GameFAQs User Database - {$stats.num_users|number_format} users and growing</h1>

<div id="latest-fetches">
	<h2>Latest Fetches</h2>
	<table>
		<tr>
			<th>When</th>
			<th>Where</th>
			<th>How many</th>
		</tr>
		{foreach from=$latest_fetches item=fetch}
			<tr>
				<td>{$fetch.timestamp|relative_time|ucfirst}</td>
				<td>
					<a href="http://www.gamefaqs.com/boards/{$fetch.board_id}-/{$fetch.topic_id}">{$fetch.topic_name|htmlspecialchars}</a>
					<br />from&nbsp;
					<a href="http://www.gamefaqs.com/boards/{$fetch.board_id}-">{$fetch.board_name|htmlspecialchars}</a>
				</td>
				<td class="num-added num-added-{$fetch.num_added}">{$fetch.num_added}</td>
			</tr>
		{/foreach}
	</table>
</div>

<div class="home-box">
	<h2>What is this thing?</h2>
	<p>The purpose of the GFuserDB is to create a full list of as many GameFAQs users as possible. Only usernames and user IDs are collected. Profiles are not gathered because of bandwidth issues, and the fact that they change over time.</p>
	<p>The GFuserDB is designed in such a way that you can submit usernames yourself. Simply paste a link to a GameFAQs topic in the Awesome Bar above.</p>
</div>

<div class="home-box">
	<h2>How it works</h2>
	<p>When you fetch a topic:</p>
	<ul>
		<li>The server downloads the topic list from your link and all the pages in it.</li>
		<li>The server sorts through the source codes of the pages and finds the user IDs and usernames which have posted in the topic.</li>
		<li>The user IDs and usernames are inserted into a database on this server, so you can browse and search them.</li>
	</ul>
</div>
