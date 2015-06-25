<?php

class CronController extends Controller {
	
	public function execute() {

		switch ($this->_action) {
			case 'GenGraphs':
				$this->_generateUserDistributionGraph();
				$this->_generateBestFetchersGraph();
				break;
			case 'Optimise':
				$this->_optimise();
				break;
		}
	}

	private function _optimise() {
		// Select timestamp of the 10th-most recent fetch
		$query = "
			SELECT
				timestamp
			FROM
				latest_fetches
			ORDER BY
				timestamp DESC
			LIMIT 9, 1
			";
		$timestamp = $this->_db->getOne($query);

		// Delete fetches older than that one
		$query = "
			DELETE FROM
				latest_fetches
			WHERE
				timestamp < ".((int)$timestamp)."
			";
		$this->_db->execute($query);

		// Order users table by ID
		$this->_db->execute("ALTER TABLE users ORDER BY id ASC");

		// Optimise all tables
		$this->_db->execute("OPTIMIZE TABLE latest_fetches");
		$this->_db->execute("OPTIMIZE TABLE logins");
		$this->_db->execute("OPTIMIZE TABLE users");
	}

	private function _generateUserDistributionGraph() {
		$graph = new Graph;
		$graph->setMargin(10);
		$graph->setTextDirection('horizontal');

		// Get highest ID
		$query = "
			SELECT
				MAX(id)
			FROM
				users
			";
		$max_id = $this->_db->getOne($query);
		$data = array();

		for ($i = 0; $i < $max_id; $i += 1000000) {
			$query = "
				SELECT
					COUNT(*)
				FROM
					users
				WHERE
					id BETWEEN $i AND ($i + 999999)
				";
			$num_users = $this->_db->getOne($query);

			$data[] = array(
				'label' => ($i/1000000).'.x million',
				'value' => $num_users
			);
		}

		$graph->setData($data);

		$graph->saveTo('r/d/user-distribution.png');
	}

	private function _generateBestFetchersGraph() {
		$graph = new Graph;
		$graph->setMargin(100);

		$query = "
			SELECT
				name label,
				num_added value
			FROM
				logins
			ORDER BY
				value DESC
			LIMIT 50
			";
		$data = $this->_db->execute($query)->getArray();

		$graph->setData($data);
		$graph->saveTo('r/d/best-fetchers.png');
	}
}
