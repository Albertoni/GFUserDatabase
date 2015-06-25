<?php /* Smarty version 3.0rc1, created on 2011-08-21 04:51:17
         compiled from "app/templates/home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11038130814e50c7054bceb9-00143589%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a40fe53febc12a3a615b0f7ea4285f407586e5e2' => 
    array (
      0 => 'app/templates/home.tpl',
      1 => 1313915623,
    ),
  ),
  'nocache_hash' => '11038130814e50c7054bceb9-00143589',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_relative_time')) include '/home7/thengamer/public_html/youreliteness/gfusers-compressed/app/lib/smarty/plugins/modifier.relative_time.php';
?><h1>Welcome to the GameFAQs User Database - <?php echo number_format($_smarty_tpl->getVariable('stats')->value['num_users']);?>
 users and growing</h1>

<div id="latest-fetches">
	<h2>Latest Fetches</h2>
	<table>
		<tr>
			<th>When</th>
			<th>Where</th>
			<th>How many</th>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['fetch'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('latest_fetches')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['fetch']->key => $_smarty_tpl->tpl_vars['fetch']->value){
?>
			<tr>
				<td><?php echo ucfirst(smarty_modifier_relative_time($_smarty_tpl->tpl_vars['fetch']->value['timestamp']));?>
</td>
				<td>
					<a href="http://www.gamefaqs.com/boards/<?php echo $_smarty_tpl->tpl_vars['fetch']->value['board_id'];?>
-/<?php echo $_smarty_tpl->tpl_vars['fetch']->value['topic_id'];?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['fetch']->value['topic_name']);?>
</a>
					<br />from&nbsp;
					<a href="http://www.gamefaqs.com/boards/<?php echo $_smarty_tpl->tpl_vars['fetch']->value['board_id'];?>
-"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['fetch']->value['board_name']);?>
</a>
				</td>
				<td class="num-added num-added-<?php echo $_smarty_tpl->tpl_vars['fetch']->value['num_added'];?>
"><?php echo $_smarty_tpl->tpl_vars['fetch']->value['num_added'];?>
</td>
			</tr>
		<?php }} ?>
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
