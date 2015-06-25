<?php

class NotFoundController extends Controller {
	
	public function execute() {
		header('HTTP/1.1 404 Not Found');
		$this->_setTitle('Page Not Found');
		$this->_display('404');
	}
}
