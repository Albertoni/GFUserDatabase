<?php /* Smarty version 3.0rc1, created on 2011-08-21 04:52:16
         compiled from "app/templates/browse.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6371997144e50c7402ea114-40828978%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '46b00aa6a773d41838c04978e074b8cdc04c4d3f' => 
    array (
      0 => 'app/templates/browse.tpl',
      1 => 1313915622,
    ),
  ),
  'nocache_hash' => '6371997144e50c7402ea114-40828978',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_smarty_tpl->getVariable('browse_method')->value=='By-Name'){?>
	<a class="browse-method" href="Browse/By-ID">Browse by user ID</a>
	<h1>Browse users by name</h1>
<?php }else{ ?>
	<a class="browse-method" href="Browse/By-Name">Browse by name</a>
	<h1>Browse users by user ID</h1>
<?php }?>

<ul class="browse-tabs <?php echo strtolower($_smarty_tpl->getVariable('browse_method')->value);?>
">
	<?php  $_smarty_tpl->tpl_vars['tab'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('tabs')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['tab']->key => $_smarty_tpl->tpl_vars['tab']->value){
?>
		<li><a href="Browse/<?php echo $_smarty_tpl->getVariable('browse_method')->value;?>
#!Browse/<?php echo $_smarty_tpl->getVariable('browse_method')->value;?>
/<?php echo $_smarty_tpl->tpl_vars['tab']->value['value'];?>
" rel="<?php echo $_smarty_tpl->tpl_vars['tab']->value['value'];?>
" id="browse-tab-<?php echo $_smarty_tpl->tpl_vars['tab']->value['value'];?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tab']->value['title']);?>
</a></li>
	<?php }} ?>
</ul>

<ul class="results" id="browse-results" rel="<?php echo $_smarty_tpl->getVariable('browse_method')->value;?>
">

</ul>

<div id="loading" class="browse"></div>
