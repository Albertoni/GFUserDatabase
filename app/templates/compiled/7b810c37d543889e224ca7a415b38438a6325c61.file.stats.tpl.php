<?php /* Smarty version 3.0rc1, created on 2011-08-21 04:56:03
         compiled from "app/templates/stats.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5793287754e50c8235015c6-58540217%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b810c37d543889e224ca7a415b38438a6325c61' => 
    array (
      0 => 'app/templates/stats.tpl',
      1 => 1313915623,
    ),
  ),
  'nocache_hash' => '5793287754e50c8235015c6-58540217',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<h1>Statistics</h1>

<p>These graphs are updated daily.</p>

<h2>User distribution</h2>
<div><img src="r/d/user-distribution.png" alt="" /></div>

<h2>Top 50 fetchers</h2>
<div><img src="r/d/best-fetchers.png" alt="" /></div>

<h2>Random statistic</h2>
<p>The highest user ID in the database is user #<?php echo $_smarty_tpl->getVariable('highest')->value['id'];?>
 (<?php echo $_smarty_tpl->getVariable('highest')->value['name'];?>
)</p>

