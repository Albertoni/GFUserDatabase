<?php

class HomepageController extends Controller {
	
	public function execute() {

		// Display bubble if we haven't closed it
		if (!isset($_COOKIE['abIntroClosed'])) {
			$this->_smarty->assign('show_intro', true);
		}

		// Select recent fetches
		$query = "
			SELECT
				*
			FROM
				latest_fetches
			ORDER BY
				timestamp DESC
			LIMIT 5
			";
		$fetches = $this->_db->execute($query)->getArray();
		$this->_smarty->assign('latest_fetches', $fetches);

		$this->_display('home');
	}
}
