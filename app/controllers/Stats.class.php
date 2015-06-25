<?php

class StatsController extends Controller {
	
	public function execute() {
		$this->_setTitle('Statistics');

		$query = "
			SELECT
				*
			FROM
				users
			ORDER BY
				id DESC
			LIMIT 1
			";
		$highest = $this->_db->getRow($query);

		$this->_smarty->assign('highest', $highest);
		$this->_display('stats');
	}
}
