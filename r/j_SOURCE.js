var Site = {
	headerHovered: true,
	barMessageTimer: null,
	searchScrollEventAdded: false,
	request: null,
	config: {},
	start: function() {
		Site.config = JSON.decode(Cookie.read('gfudb_config')) || {
			recordFetches: true,
			showExisting: false,
			searchOrder: 'name'
		};
		Site.request = new Request.JSON({
			onFailure: function() {
				Site.setFailureMessage('The request failed. This might happen if your internet connection has dropped out.');
			}
		});
		$('awesome-bar').addEvent('keyup', Site.updateAwesomeBar);
		$('awesome-form').addEvent('submit', Site.submitAwesomeForm);
		if ($('close-awesome-bubble')) {
			$('close-awesome-bubble').addEvent('click', Site.closeAwesomeBubble);
		}
		if ($('username-input')) {
			Site.initLoginHandler();
		}
		Site.initHeaderOpacity();
		Site.checkShebang();
		Site.initBrowse();
		Site.updateStats.periodical(60000);
		$('awesome-bar').focus();
	},
	saveConfig: function() {
		Cookie.write('gfudb_config', JSON.encode(Site.config), {
			duration: 365
		});
	},
	clearContent: function() {
		$('content-inner').getChildren().each(function(elem) {
			elem.dispose();
		});
	},
	setFailureMessage: function(msg) {
		Site.clearContent();
		$('content-inner').adopt(new Element('h1', {
			html: 'Oh noes!'
		}));
		$('content-inner').adopt(new Element('p', {
			html: msg
		}));
	},
	showLoading: function() {
		if (!$('loading')) {
			$('content-inner').adopt(new Element('div', {
				id: 'loading'
			}));
		}
		$('loading').setStyle('display', 'block');
	},
	hideLoading: function() {
		if ($('loading')) {
			$('loading').setStyle('display', 'none');
		}
	},
	initLoginHandler: function() {
		$('username-input').addEvent('keyup', function(e) {
			if (e.key != 'enter') {
				return;
			}
			Cookie.write('login', this.getProperty('value'), {
				duration: 365
			});
			window.location.reload(true);
		});
	},
	logout: function() {
		Cookie.write('login', '');
		window.location.reload(true);
	},
	initHeaderOpacity: function() {
		$('header').addEvent('mouseover', function() {
			Site.headerHovered = true;
			Site.updateHeaderOpacity();
		});
		$('content').addEvent('mouseover', function() {
			Site.headerHovered = false;
			Site.updateHeaderOpacity();
		});
		window.addEvent('scroll', Site.updateHeaderOpacity);
		Site.updateHeaderOpacity();
	},
	updateHeaderOpacity: function() {
		if (window.getScroll().y > 0 && !Site.headerHovered) {
			$('header').setStyle('opacity', 0.3);
		} else {
			$('header').setStyle('opacity', 1);
		}
	},
	checkShebang: function() {
		if (!document.location.toString().contains('#!')) {
			return;
		}
		var shebang = document.location.toString().split('#!')[1].split('/');
		switch (shebang[0]) {
			case 'Lookup':
				$('awesome-bar').setProperty('value', shebang[1]);
				Site.doLookup();
				break;
			case 'Search':
				$('awesome-bar').setProperty('value', shebang[1]);
				Site.doSearch();
				break;
		}
	},
	browseMethod: null,
	browseTab: null,
	browseOffset: 0,
	browseDone: false,
	initBrowse: function() {
		if (!$('browse-results')) {
			return;
		}
		$$('.browse-tabs a').each(function(anchor) {
			anchor.addEvent('click', function() {
				$$('.browse-tabs a').each(function(elem) {
					elem.removeClass('active');
				});
				this.addClass('active');
				$$('#browse-results li').each(function(elem) {
					elem.dispose();
				});
				Site.browseOffset = 0;
				Site.browseTab = this.getProperty('rel');
				Site.browseDone = false;
				Site.browseMore();
			});
		});
		Site.browseMethod = $('browse-results').getProperty('rel');
		if (document.location.toString().contains('#!Browse/')) {
			var tab_value = document.location.toString().split('#!Browse/')[1].split('/')[1];
			$('browse-tab-' + tab_value).fireEvent('click');
		} else {
			$$('.browse-tabs a')[0].fireEvent('click');
		}
		window.addEvent('scroll', function() {
			if (window.getScroll().y > window.getScrollSize().y - window.getSize().y - 500) {
				Site.browseMore();
			}
		});
	},
	browseMore: function() {
		if (Site.browseDone || !$('browse-results')) {
			return;
		}
		Site.request.addEvent('success', Site.handleBrowseResponse).send({
			url: 'API/Browse/' + Site.browseMethod + '/' + Site.browseTab + '/' + Site.browseOffset
		});
		Site.showLoading();
		_gaq.push(['_trackEvent', 'Users', 'Browse', Site.browseMethod]);
	},
	handleBrowseResponse: function(response) {
		Site.request.removeEvents('success');
		if (response.status != 'ok') {
			Site.setFailureMessage(response.error);
			return;
		}
		Site.hideLoading();
		var i;
		for (i = 0; i < response.data.length; i++) {
			var id_name = response.data[i].split('|');
			var li = new Element('li', {
				html: '<div class="username">' + id_name[1] + '</div>' + '<div>#' + id_name[0] + '</div>'
			}).setProperty('title', id_name[1] + '-#' + id_name[0]);
			$('browse-results').adopt(li);
		}
		Site.browseOffset += response.data.length;
		if (response.data.length != 500) {
			Site.browseDone = true;
		}
	},
	updateAwesomeBar: function(e) {
		window.clearTimeout(Site.barMessageTimer);
		if (e && e.key == 'enter') {
			return;
		}
		var new_type, helper_text;
		var ab = $('awesome-bar');
		if (ab.getProperty('value').contains('://')) {
			new_type = 'fetch';
			helper_text = 'Press Enter to fetch this topic.';
		} else if (ab.getProperty('value').toInt() == ab.getProperty('value')) {
			new_type = 'lookup';
			helper_text = 'Press Enter to look up this user ID.';
		} else if (ab.getProperty('value').length == 0) {
			new_type = 'empty';
			helper_text = '&nbsp;';
		} else {
			new_type = 'search';
			helper_text = 'Press Enter to search.';
		}
		Site.barMessageTimer = window.setTimeout(function() {
			$('helper-text').removeClass('error').set('html', helper_text);
		}, 3000);
		var form = $('awesome-form');
		if (form.retrieve('type') == new_type) {
			return;
		}
		$('helper-text').set('html', '&nbsp;');
		form.removeClass(form.retrieve('type'));
		form.addClass(new_type);
		form.store('type', new_type);
	},
	submitAwesomeForm: function() {
		$('helper-text').set('html', '&nbsp;');
		switch ($('awesome-form').retrieve('type')) {
			case 'fetch':
				Site.doFetch();
				break;
			case 'lookup':
				Site.doLookup();
				break;
			case 'search':
				Site.doSearch();
				break;
		}
		return false;
	},
	doFetch: function() {
		var url = $('awesome-bar').getProperty('value');
		var board_id = null;
		var topic_id = null;
		if (url.contains('board=') && url.contains('topic=')) {
			var board_ids = url.match(/board\=-?[0-9]+/g);
			var topic_ids = url.match(/topic\=[0-9]+/g);
			if (board_ids.length) board_id = board_ids[0].substr(6) * 1;
			if (topic_ids.length) topic_id = topic_ids[0].substr(6) * 1;
		} else {
			var ids = url.match(/\/-?[0-9]+/g);
			if (ids && ids.length >= 2) {
				board_id = ids[0].substr(1) * 1;
				topic_id = ids[1].substr(1) * 1;
			}
		}
		if (board_id == null || !topic_id) {
			Site.setFailureMessage('Bad topic URL. It should be something like http://www.gamefaqs.com/boards/<strong>XXX</strong>-board-name/<strong>XXX</strong>.');
			return;
		}
		Site.request.addEvent('success', Site.handleFetchResponse).send({
			url: 'API/Fetch/' + board_id + '/' + topic_id
		});
		Site.clearContent();
		Site.showLoading();
		_gaq.push(['_trackEvent', 'Users', 'Fetch']);
	},
	handleFetchResponse: function(response) {
		Site.request.removeEvents('success');
		if (response.status != 'ok') {
			Site.setFailureMessage(response.error);
			return;
		}
		if (!response.data.all.length) {
			Site.setFailureMessage('We couldn\'t find any users<em>at all</em>. Maybe there\'s something wrong with your link,or the account used to fetch topics doesn\'t have access to that board.');
			return;
		}
		Site.clearContent();
		var link = new Element('a', {
			html: (Site.config.showExisting ? 'Hide' : 'Show') + ' existing users',
			href: 'javascript:Site.toggleExisting();',
			'class': 'toggle-existing-link'
		});
		$('content-inner').adopt(link);
		var h1 = new Element('h1');
		if (response.data.new.length == 0) {
			h1.set('html', 'No new additions:(');
		} else if (response.data.new.length == 1) {
			h1.set('html', '1 new addition');
		} else {
			h1.set('html', response.data.new.length + ' new additions');
		}
		$('content-inner').adopt(h1);
		var i, ul;
		for (i = 0; i < response.data.all.length; i++) {
			if (i % 50 == 0) {
				var h2 = new Element('h2', {
					html: 'Page ' + ((i / 50) + 1),
					style: 'clear:left;'
				});
				$('content-inner').adopt(h2);
				ul = new Element('ul').addClass('results');
				$('content-inner').adopt(ul);
			}
			var id_name = response.data.all[i].split('|');
			var user_id = id_name[0] * 1;
			var username = id_name[1];
			var li = new Element('li', {
				html: '<div class="username">' + username + '</div>' + '<div>#' + user_id + '</div>'
			}).setProperty('title', username + '-#' + user_id);
			if (response.data.new.contains(user_id)) {
				response.data.new.erase(user_id);
				li.addClass('new');
			} else {
				li.addClass('existing');
				if (!Site.config.showExisting) {
					li.setStyle('display', 'none');
				}
			}
			ul.adopt(li);
		}
		$('awesome-bar').setProperty('value', '');
		Site.updateAwesomeBar();
		Site.updateStats();
	},
	toggleExisting: function() {
		Site.config.showExisting = !Site.config.showExisting;
		$$('.existing').each(function(elem) {
			elem.setStyle('display', Site.config.showExisting ? 'block' : 'none');
		});
		Site.saveConfig();
	},
	doLookup: function() {
		var user_id = $('awesome-bar').getProperty('value') * 1;
		Site.request.addEvent('success', Site.handleLookupResponse).send({
			url: 'API/Lookup/' + user_id
		}).send();
		Site.clearContent();
		Site.showLoading();
		_gaq.push(['_trackEvent', 'Users', 'Lookup']);
	},
	handleLookupResponse: function(response) {
		Site.request.removeEvents('success');
		if (response.status != 'ok') {
			Site.setFailureMessage(response.error);
			return;
		}
		Site.clearContent();
		var div_user_id = new Element('div', {
			id: 'lookup-user-id',
			html: 'User ID #' + response.data.user_id
		});
		$('content-inner').adopt(div_user_id);
		var div_user_id = new Element('div', {
			id: 'lookup-user-name',
			html: response.data.username.length ? response.data.username : '???'
		});
		$('content-inner').adopt(div_user_id);
		var a = new Element('a', {
			href: '#',
			html: 'Search for ' + response.data.user_id,
			onclick: "$('awesome-bar').setProperty('value','" + response.data.user_id + "');Site.doSearch();"
		});
		$('content-inner').adopt(new Element('div', {
			id: 'lookup-search-link'
		}).adopt(a));
		if (document.location.toString().contains('#')) {
			var tmp = document.location.toString().split('#')[0];
			document.location = tmp + '#!Lookup/' + response.data.user_id;
		} else {
			document.location += '#!Lookup/' + response.data.user_id;
		}
	},
	searchPhrase: null,
	searchOffset: 0,
	searchDone: false,
	doSearch: function() {
		Site.searchPhrase = $('awesome-bar').getProperty('value');
		Site.searchOffset = 0;
		Site.searchDone = false;
		$('content-inner').getChildren().each(function(elem) {
			elem.dispose();
		});
		$('content-inner').adopt(new Element('h1', {
			html: 'Search results'
		}));
		$('content-inner').adopt(new Element('ul', {
			id: 'search-results',
			'class': 'results'
		}));
		$('content-inner').adopt(new Element('div', {
			id: 'loading'
		}).setStyle('display', 'block'));
		if (!Site.searchScrollEventAdded) {
			window.addEvent('scroll', function() {
				if (window.getScroll().y > window.getScrollSize().y - window.getSize().y - 500) {
					Site.searchMore();
				}
			});
			Site.searchScrollEventAdded = true;
		}
		Site.searchMore();
	},
	searchMore: function() {
		if (Site.searchDone) {
			return;
		}
		Site.request.addEvent('success', Site.handleSearchResponse).send({
			url: 'API/Search/' + escape(Site.searchPhrase) + '/' + Site.searchOffset
		});
		Site.showLoading();
		_gaq.push(['_trackEvent', 'Users', 'Search']);
	},
	handleSearchResponse: function(response) {
		Site.request.removeEvents('success');
		if (response.status != 'ok') {
			Site.setFailureMessage(response.error);
			return;
		}
		Site.hideLoading();
		if (!Site.searchOffset && !response.data.length) {
			$('content-inner').adopt(new Element('p', {
				html: 'There were no results.'
			}));
			Site.searchDone = true;
			return;
		}
		var i;
		for (i = 0; i < response.data.length; i++) {
			var id_name = response.data[i].split('|');
			var li = new Element('li', {
				html: '<div class="username">' + id_name[1] + '</div>' + '<div>#' + id_name[0] + '</div>'
			}).setProperty('title', id_name[1] + '-#' + id_name[0]);
			$('search-results').adopt(li);
		}
		Site.searchOffset += response.data.length;
		if (response.data.length != 500) {
			Site.searchDone = true;
		}
	},
	updateStats: function() {
		if (!$('stats')) {
			return;
		}
		new Request.JSON({
			url: 'API/Stats',
			onSuccess: function(response) {
				if (response.status != 'ok') {
					return;
				}
				$('stats-total').set('html', response.data.num_users.toLocaleString());
				$('stats-percent').set('html', response.data.percent.toFixed(2));
				$('stats-time').set('html', Site.getRelativeTime(response.data.timestamp));
			}
		}).send();
	},
	getRelativeTime: function(timestamp) {
		var secs = (Date.now() / 1000) - timestamp;
		if (secs < 10) {
			return 'moments ago';
		}
		if (secs < 60) {
			return 'less than a minute ago';
		}
		var mins = Math.floor(secs / 60);
		if (mins == 1) {
			return 'a minute ago';
		}
		if (mins < 60) {
			return mins + ' minutes ago';
		}
		var hours = Math.floor(mins / 60);
		if (hours == 1) {
			return 'an hour ago';
		}
		if (hours < 48) {
			return hours + ' hours ago';
		}
		var days = Math.floor(hours / 24);
		return days + ' days ago';
	},
	closeAwesomeBubble: function() {
		$('awesome-bar').focus();
		new Fx.Morph('awesome-bubble', {
			duration: 'short',
			transition: Fx.Transitions.Sine.easeOut
		}).addEvent('complete', function() {
			$('awesome-bubble').dispose();
		}).start({
			'opacity': [1, 0]
		});
		Cookie.write('abIntroClosed', 1, {
			'duration': 365
		});
		return false;
	},
	toggleMenu: function(menu) {
		var elem = $('dropmenu-' + menu);
		
		if (elem.getStyle('display') == 'none') {
			$$('.dropmenu').each(function(el) {
				if(el.className == 'dropmenu'){
					el.setStyle('display', 'none');
				}
			});
			elem.setStyle('display', 'block');
		} else {
			elem.setStyle('display', 'none');
		}
	}
};
window.addEvent('domready', Site.start);