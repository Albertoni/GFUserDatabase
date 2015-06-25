<?php

class BrowseController extends Controller {
	
	public function execute() {

		switch ($this->_action) {
			case 'By-ID':
				$this->_browseById();
				break;
			case 'By-Name':
				$this->_browseByName();
				break;
			default:
				$this->_redirect('/gfusers/Browse/By-Name');
		}

		$this->_smarty->assign('browse_method', $this->_action);
		$this->_display('browse');
	}

	private function _browseById() {
		// Load max ID
		$query = "
			SELECT
				MAX(id)
			FROM
				users
			";
		$max_id = $this->_db->getOne($query);

		$num_millions = floor($max_id / 1000000);
		$tabs = array();
		$tabs[] = array(
			'title' => '< 1 million',
			'value' => 0,
		);

		for ($i = 1; $i <= $num_millions; $i++) {
			$tabs[] = array(
				'title' => $i.'.x million',
				'value' => $i
			);
		}

		$this->_smarty->assign('tabs', $tabs);
		$this->_setTitle('Browse users by ID');
	}

	private function _browseByName() {
		$chars = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$tabs = array(
			array(
				'title' => '#',
				'value' => 'hash'
			)
		);
		
		foreach ($chars as $char) {
			$tabs[] = array(
				'title' => $char,
				'value' => $char
			);
		}
		
		$this->_smarty->assign('tabs', $tabs);
		$this->_setTitle('Browse users by name');
	}
}
