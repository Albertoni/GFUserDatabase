<?php
require_once 'app/lib/smarty/Smarty.class.php';

class Controller {

	protected $_db;
	protected $_action;
	protected $_params;
	protected $_smarty;
	private $_title;

	public function __construct($action, $params) {
		$this->_db = Database::getHandle();
		$this->_action = $action;
		$this->_params = $params;

		$this->_smarty = new Smarty;
		$this->_smarty->template_dir = 'app/templates';
		$this->_smarty->compile_dir = 'app/templates/compiled';

		session_name('sess_gfuserdb');
		session_start();

		if (!$this->_db->_connectionID) {
			$this->_handleDbFailure();
			die;
		}

		if (isset($_SESSION['num_added']) && isset($_COOKIE['login']) && !empty($_COOKIE['login'])) {
			$this->_addUserScore($_COOKIE['login'], $_SESSION['num_added']);
			unset($_SESSION['num_added']);
		}
	}

	private function _handleDbFailure() {
		$headers = apache_request_headers();
		
		if (isset($headers['X-Request']) && $headers['X-Request'] == 'JSON') {
			header('Content-type: text/javascript');
			$response = array(
				'status' => 'error',
				'error' => 'Database error. Please try again later.'
			);
			echo json_encode($response);
		} else {
			$this->_display('db-error');
		}
	}

	protected function _redirect($url) {
		header('Location: '.$url);
		die;
	}

	protected function _addUserScore($login_name, $score) {
		// Does the login user exist?
		$query = "
			SELECT
				COUNT(*)
			FROM
				logins
			WHERE
				name = ".$this->_db->qStr($login_name)."
			";
		$exists = $this->_db->getOne($query);

		if ($exists) {
			$query = "
				UPDATE
					logins
				SET
					num_added = num_added + ".((int)$score).",
					timestamp = UNIX_TIMESTAMP()
				WHERE
					name = ".$this->_db->qStr($login_name)."
				";
		} else {
			$query = "
				INSERT INTO
					logins
				SET
					name = ".$this->_db->qStr($login_name).",
					num_added = ".((int)$score).",
					timestamp = UNIX_TIMESTAMP()
				";
		}
		$this->_db->execute($query);
	}

	protected function _setTitle($title) {
		$this->_title = $title;
	}

	protected function _display($template) {
		// Assign title and template
		$this->_smarty->assign('title', $this->_title);
		$this->_smarty->assign('template', $template.'.tpl');
		$this->_smarty->assign('login_name', @$_COOKIE['login']);
		$this->_smarty->assign('num_users_added_this_session', @$_SESSION['num_added']);
		$this->_smarty->assign('login_num_users', (int) $this->_getNumUsersForLogin(@$_COOKIE['login']));

		// Assign stats
		$stats = unserialize(file_get_contents('app/private/stats.txt'));
		$this->_smarty->assign('stats', $stats);

		// Display template
		header('Content-Type: text/html; charset=UTF-8');
		echo $this->_compressHtml($this->_smarty->fetch('main.tpl'));
	}

	private function _compressHtml($html) {
		// Remove comments
		$html = preg_replace('#<!--.*?-->#is', '', $html);

		// Remove all whitespace next to tags
		$html = preg_replace('#>\s+#', '>', $html);
		$html = preg_replace('#\s+<#', '<', $html);

		// Remove start-of-line white space
		$html = preg_replace('#^\s+#m', '', $html);
		
		// Remove line breaks
		$html = str_replace("\n", '', $html);

		return $html;
	}

	private function _getNumUsersForLogin($login_name) {
		if (empty($login_name)) {
			return 0;
		}

		$query = "
			SELECT
				num_added
			FROM
				logins
			WHERE
				name = ".$this->_db->qStr($login_name)."
			";
		return $this->_db->getOne($query);
	}
}
