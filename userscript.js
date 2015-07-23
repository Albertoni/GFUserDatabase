// ==UserScript==
// @name           GameFAQs User Database Fetcher
// @namespace      UD4
// @author         UD4
// @license        https://creativecommons.org/licenses/by-nc-sa/4.0/
// @version        1.0
// @description    Adds links to fetch topics and tracks which have been fetched.
// @include        http://www.gamefaqs.com/*
// @match          http://www.gamefaqs.com/*
// @require        http://static.gamefaqs.com/js/jquery.js
// @grant          GM_xmlhttpRequest
// ==/UserScript==
'use strict';

// Stores the topic and number of posts so we don't fetch a topic needlessly
function storeFetchedTopic(topicId, numberPosts) {
	localStorage[topicId] = numberPosts;
}

function getStoredPostsFromFetchedTopic(topicId) {
	return localStorage[topicId];
}

// Checks if we don't need to fetch a topic needlessly
function checkIfTopicWasFetched(topicId, currentNumberPosts) {
	var postsDuringLastFetch = getStoredPostsFromFetchedTopic(topicId);
	return currentNumberPosts > postsDuringLastFetch;
}

// Adds a button to fetch topic
function addButtonToTopicContainer(htmlElement, boardId, topicId, numberPosts) {
	var button = document.createElement('button');
	if (checkIfTopicWasFetched(topicId, numberPosts)) {
		button.innerHTML = 'Already fetched';
	} else {
		button.innerHTML = 'Fetch topic';
		button.value = boardId + ';' + topicId + ';' + numberPosts;
		button.addEventListener('click', handleClick.bind(button));
	}
	button.style.cssFloat = 'right';
	htmlElement.appendChild(button);
}

function handleClick(){
	var data = this.value.split(';');
	var boardId = data[0];
	var topicId = data[1];
	this.removeEventListener('click', handleClick);
	this.innerHTML = 'Fetching...';
	postToDatabase(boardId, topicId, this);
}

function postToDatabase(boardId, topicId, buttonElement) {
	GM_xmlhttpRequest({
		method: 'POST',
		url: 'http://youreliteness.thengamer.com/gfusers/API/Fetch/' + boardId + '/' + topicId,
		responseType: 'json',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
		},
		onload: function (response){
			var topicData = this.value.split(';');
			storeFetchedTopic(topicData[1], topicData[2]);

			var responseObject = response.response.data;

			var newUsers = responseObject['new'].length;

			if(newUsers > 0){
				// Splits the data so we can fetch it easily later
				var idNames = [];
				for(var i = responseObject['all'].length - 1; i >= 0; i--){
					var data = responseObject['all'][i].split('|');
					idNames[data[0]] = data[1];
				}

				var usernames = [];
				for (var i = responseObject['new'].length - 1; i >= 0; i--) {
					usernames[i] = idNames[responseObject['new'][i]];
				}

				this.innerHTML = newUsers + ' new users found, click to see';
				this.addEventListener('click', function () {
					alert(usernames.join(', '));
				});
			} else {
				this.innerHTML = 'No new users found :(';
			}
		}.bind(buttonElement) // Sets "this" to buttonElement inside the function. Ugly hack but userscript scoping is uglier
	});
}

// Figures out what to call addButtonToTopicContainer on and does so
function processPage() {
	var topicURLs = $('td.topic > a');
	var topicContainers = $('td.topic');
	var numberPosts = $('td.count');
	if ((topicURLs.length != topicContainers.length) || (topicContainers.length != numberPosts.length)) {
		alert('User database script found an error! ' + topicURLs.length + ' links vs ' + topicContainers.length + ' containers vs ' + numberPosts.length + ' post counts (all 3 should be the same number) at ' + window.location.href);
	} else {
		// See info on isTopicList, remove last restriction on /s and add a (\d+) to get the topic id.
		var expression = /^http:\/\/www\.gamefaqs\.com\/boards\/(-1|[1-9][0-9]*)-.*?\/(\d+)*$/;
		for (var i = topicURLs.length - 1; i >= 0; i--) {
			var splitURL = expression.exec(topicURLs[i]); // splitURL == ["http://www.gamefaqs.com/boards/987-board/12345678", "987", "12345678"]
			addButtonToTopicContainer(topicContainers[i], splitURL[1], splitURL[2], numberPosts[i]);
		}
	}
}

function isTopicList(pageURL) {
	/* Matches http://www.gamefaqs.com/boards/ and then:
	 * - Either -1 for Brilliant
	 * - 1 number from 1 to 9 and as many as necessary to make up all other boards and avoid board 0
	 * - no /s until the end of the line, otherwise it's a message list
	 */
	var expression = /^http:\/\/www\.gamefaqs\.com\/boards\/(-1|[1-9][0-9]*)-[^\/]*$/;
	return expression.test(pageURL);
}

if (isTopicList(window.location.href)) {
	processPage();
}
