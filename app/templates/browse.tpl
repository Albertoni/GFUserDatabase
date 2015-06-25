{if $browse_method == 'By-Name'}
	<a class="browse-method" href="Browse/By-ID">Browse by user ID</a>
	<h1>Browse users by name</h1>
{else}
	<a class="browse-method" href="Browse/By-Name">Browse by name</a>
	<h1>Browse users by user ID</h1>
{/if}

<ul class="browse-tabs {$browse_method|strtolower}">
	{foreach from=$tabs item=tab}
		<li><a href="Browse/{$browse_method}#!Browse/{$browse_method}/{$tab.value}" rel="{$tab.value}" id="browse-tab-{$tab.value}">{$tab.title|htmlspecialchars}</a></li>
	{/foreach}
</ul>

<ul class="results" id="browse-results" rel="{$browse_method}">

</ul>

<div id="loading" class="browse"></div>
