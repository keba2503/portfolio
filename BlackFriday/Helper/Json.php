<?php

namespace Hiperdino\BlackFriday\Helper;

use Magento\Framework\App\ResourceConnection;

class Json {
	protected $connection;

	public function __construct(
		ResourceConnection $resourceConnection
	) {
		$this->connection = $resourceConnection->getConnection();
	}

	public function getBlackFridayStoresJson() {
		$result = [];
		$data = $this->connection->fetchAll("SELECT	t.id, t.name, i.name AS island, t.city, t.address, t.postcode, t.province, t.phone, t.fax, t.latitude, t.longitude, t.schedule, t.black_friday_postcode FROM singular_tiendas AS t LEFT JOIN singular_islands AS i ON t.island_id = i.id WHERE t.is_enabled = 1 AND t.is_black_friday = 1 AND t.black_friday_postcode IS NOT NULL AND t.black_friday_postcode != ''");
		foreach ($data as $row) {
			if (!isset($result[$row['island']])) $result[$row['island']] = [];
			if (!isset($result[$row['island']][$row['city']])) $result[$row['island']][$row['city']] = [];
			$result[$row['island']][$row['city']][] = [
				'id' => $row['id'],
				'name' => $row['name'],
				'address' => $row['address'],
				'postcode' => $row['postcode'],
				'city' => $row['city'],
				'province' => $row['province'],
				'phone' => $row['phone'],
				'fax' => $row['fax'],
				'latitude' => $row['latitude'],
				'longitude' => $row['longitude'],
				'schedule' => $row['schedule'],
				'black_friday_postcode' => $row['black_friday_postcode']
			];
		}
		return json_encode($result, JSON_UNESCAPED_UNICODE);
	}

	public function updateStoresJson($str = null) {
		if (is_null($str)) $str = $this->getBlackFridayStoresJson();
		$this->connection->query("INSERT INTO core_config_data (scope, scope_id, path, value) VALUES ('default', 0, '" . Config::STORES_JSON . "', '" . $str . "') ON DUPLICATE KEY UPDATE value = VALUES(value)");
	}
}