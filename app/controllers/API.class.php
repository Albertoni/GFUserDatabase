<?php

class APIController extends Controller {

	private $_worked;
	private $_data;
	private $_error;
	
	public function execute() {
		switch ($this->_action) {
			case 'Fetch':
				$this->_doFetch();
				break;
			case 'Lookup':
				$this->_doLookup();
				break;
			case 'Stats':
				$this->_doStats();
				break;
			case 'Browse':
				$this->_doBrowse();
				break;
			case 'Search':
				$this->_doSearch();
				break;
		}

		$this->_generateResponse();
	}
	
	private function _generateResponse() {
		if ($this->_worked) {
			$response = array(
				'status' => 'ok',
				'data' => $this->_data
			);
		} else {
			$response = array(
				'status' => 'error',
				'error' => $this->_error
			);
		}
		
		header('Content-type: text/javascript');
		echo json_encode($response);
		die;
	}

	private function _setError($error) {
		$this->_worked = false;
		$this->_error = $error;
	}
	
	private function _doFetch() {
		$board_id = (int) @$this->_params[0];
		$topic_id = (int) @$this->_params[1];
		$method = @$this->_params[2];
		$login_name = @$this->_params[3];

		if (empty($login_name)) {
			$login_name = @$_COOKIE['login'];
		}

		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->_setError('The request method must be POST.');
			return false;
		}

		// Set up cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, 'MDAUAuth='.urlencode(file_get_contents('app/private/mdauauth.txt')).'; ctk='.urlencode(file_get_contents('app/private/ctk.txt')).';');
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$page = 0;
		$all_user_ids = array();
		$all_usernames = array();

		// Loop through pages
		while (count($all_user_ids) == $page * 50) {
			curl_setopt($ch, CURLOPT_URL, "http://www.gamefaqs.com/boards/$board_id-/$topic_id?page=$page");
			$html = curl_exec($ch);

			if (strlen($html) < 500 && stripos($html, 'internal error') !== false) {
				$this->_setError('GameFAQs is having internal errors. Please try again later.');
				return false;
			}

			if (strlen($html) < 500 && stripos($html, 'maintenance') !== false) {
				$this->_setError('GameFAQs is currently down for maintenance. Please try again later.');
				return false;
			}

			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($http_code >= 400) {
				$this->_setError('Got HTTP code '.$http_code.' from GameFAQs.');
				return false;
			}

			if (empty($html)) {
				$this->_setError('Empty response from GameFAQs.');
				return false;
			}

			preg_match_all('#<form[^>]+class="send_pm_[\d]+">.*?</form>#i', $html, $matches);
			
			foreach ($matches[0] as $pmForm) {
				preg_match('#value="([^"]*)" name="to"#i', $pmForm, $match);
				$username = $match[1];
				
				preg_match('#class="send_pm_([\d]*)"#i', $pmForm, $match);
				$user_id = $match[1];
			
				$all_user_ids[] = $user_id;
				$all_usernames[] = $username;
			}

			$page++;
		}

		// Quit early if there's no data
		if (!count($all_user_ids)) {
			$this->_worked = true;
			$this->_data = array(
				'all' => array(),
				'new' => array()
			);
			return;
		}

		// Now process users
		$all_users = array();
		$new_users = array();

		
		foreach ($all_user_ids as $key => $user_id) {
			$username = $all_usernames[$key];

			$all_users[] = $user_id.'|'.$username;

			// Does the user exist?
			$query = "
				SELECT
					COUNT(*)
				FROM
					users
				WHERE
					id = ".$this->_db->qStr($user_id)."
				AND
					name = ".$this->_db->qStr($username)." COLLATE latin1_general_cs
				";
			$exists = $this->_db->getOne($query);

			if ($exists) {
				continue;
			}

			// Insert/update
			$query = "
				INSERT INTO
					users
				SET
					id = ".((int)$user_id).",
					name = ".$this->_db->qStr($username);
			$this->_db->execute($query);

			$new_users[] = (int) $user_id;
		}
		

		$num_new = count($new_users);
		

		// Scrape board and topic titles
		preg_match('#<h1.*?>(.*?)</h1>#is', $html, $match);
		$board_name = trim(strip_tags($match[1])); // strip_tags is needed because game boards have an A tag. Trim because there's some whitespace surrounding the text.

		preg_match('#<h2.*?>(.*?)</h2>#is', $html, $match);
		$topic_name = $match[1];

		
		$query = "
			INSERT INTO
				latest_fetches
			SET
				timestamp = UNIX_TIMESTAMP(),
				board_id = ".((int)$board_id).",
				topic_id = ".((int)$topic_id).",
				board_name = ".$this->_db->qStr($board_name).",
				topic_name = ".$this->_db->qStr($topic_name).",
				num_added = ".((int)$num_new)."
			";
		$this->_db->execute($query);

		// Update totals for this login user
		if ($num_new) {
			if (empty($login_name)) {
				if (isset($_SESSION['num_added'])) {
					$_SESSION['num_added'] += $num_new;
				} else {
					$_SESSION['num_added'] = $num_new;
				}
			} else {
				$this->_addUserScore($login_name, $num_new);
			}
		}

		// Update stats
		$stats = unserialize(file_get_contents('app/private/stats.txt'));
		
		$stats['num_users'] = $this->_db->getOne("SELECT COUNT(*) FROM users");
		$max_id = $this->_db->getOne("SELECT MAX(id) FROM users");
		$stats['percent'] = $stats['num_users'] / $max_id * 100;

		if (count($new_users)) {
			$stats['timestamp'] = time();
		}

		file_put_contents('app/private/stats.txt', serialize($stats));

		$this->_worked = true;
		$this->_data = array(
			'all' => $all_users,
			'new' => $new_users
		);
	}

	private function _doLookup() {
		$user_id = (int) @$this->_params[0];

		$query = "
			SELECT
				name
			FROM
				users
			WHERE
				id = $user_id
			";

		$this->_worked = true;
		$this->_data = array(
			'user_id' => $user_id,
			'usernames' => $this->_db->getCol($query)
		);
	}

	private function _doStats() {
		$this->_worked = true;
		$this->_data = unserialize(file_get_contents('app/private/stats.txt'));
		$this->_data['num_users'] = (int) $this->_data['num_users'];
	}
	
	private function _doBrowse() {
		$method = @$this->_params[0];
		$tab = @$this->_params[1];
		$offset = (int) @$this->_params[2];

		if ($method == 'By-Name' && $tab == 'hash') {
			$where_clause = "ASCII(name) NOT BETWEEN 65 AND 90 AND ASCII(name) NOT BETWEEN 97 AND 122";
			$order_field = 'name';
		} elseif ($method == 'By-Name') {
			$where_clause = "name LIKE ".$this->_db->qStr($tab{0}.'%');
			$order_field = 'name';
		} else {
			$tab = (int) $tab * 1000000;
			$where_clause = "id BETWEEN $tab AND ".($tab+1000000);
			$order_field = 'id ASC';
		}

		$query = "
			SELECT
				CONCAT(id, '|', name)
			FROM
				users
			WHERE
				$where_clause
			ORDER BY
				$order_field
			LIMIT
				$offset, 500
			";
		$this->_data = $this->_db->getCol($query);
		$this->_worked = true;
	}

	private function _doSearch() {
		$phrase = urldecode(@$this->_params[0]);
		$offset = (int) @$this->_params[1];

		if (empty($phrase)) {
			$this->_setError('You must enter a phrase to search for.');
			return false;
		}

		$query = "
			SELECT
				CONCAT(id, '|', name)
			FROM
				users
			WHERE
				name LIKE ".$this->_db->qStr('%'.$phrase.'%')."
			ORDER BY
				name
			LIMIT $offset, 500
			";
		$this->_data = $this->_db->getCol($query);
		$this->_worked = true;
	}
}
