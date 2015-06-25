<?php

class Graph {

	private $_img;
	private $_data;

	private $_item_width;
	private $_unit_height;
	private $_margin = 50;
	private $_text_dir = 'vertical';
	
	public function setData($data) {
		$this->_data = $data;
	}

	public function setMargin($margin) {
		$this->_margin = $margin;
	}

	public function setTextDirection($dir) {
		$this->_text_dir = $dir;
	}

	public function saveTo($filename) {
		$this->_img = imagecreatetruecolor(960, 300);
		imagefill($this->_img, 0, 0, 0xFFFFFF);

		$this->_analyseData();
		$this->_drawBackground();
		$this->_drawData();

		imagepng($this->_img, $filename);
	}

	private function _analyseData() {
		// Determine item width
		$this->_item_width = 960 / count($this->_data);

		// Determine unit height
		$max_value = 0;

		foreach ($this->_data as $item) {
			if ($item['value'] > $max_value) {
				$max_value = $item['value'];
			}
		}

		$this->_unit_height = (300 - $this->_margin - 50) / $max_value;
	}

	private function _drawBackground() {
		imagefilledrectangle($this->_img, 0, 0, 960, 300 - $this->_margin, 0xB2B2FF);
	}

	private function _drawData() {
		$x = 0;
		$y0 = $this->_getY(0);

		foreach ($this->_data as $key => $item) {
			$y = $this->_getY($item['value']);
			imagefilledrectangle($this->_img, $x, $y0, $x + $this->_item_width, $y, 0x6564FF);
			imagerectangle($this->_img, $x, $y0, $x + $this->_item_width, $y, 0x4A49C0);

			if ($this->_text_dir == 'vertical') {
				imagestringup($this->_img, 2, $x+3, $y-3, number_format($item['value']), 0x4A49C0);
				imagestringup($this->_img, 2, $x+3, 299, str_pad($item['label'], 16, ' ', STR_PAD_LEFT), 0x333333);
			} else {
				$text_len_px = strlen(number_format($item['value'])) * 7;
				$text_x = $x + ($this->_item_width - $text_len_px) / 2;
				imagestring($this->_img, 2, $text_x, $y-13, number_format($item['value']), 0x4A49C0);

				$text_len_px = strlen($item['label']) * 7;
				$text_x = $x + ($this->_item_width - $text_len_px) / 2;
				imagestring($this->_img, 2, $text_x, 289, $item['label'], 0x333333);
			}

			$x += $this->_item_width;
		}
	}

	private function _getY($value) {
		return (300 - $this->_margin) - ($value * $this->_unit_height);
	}
}
