<?php
class ModelYmreportrating extends Model {
	public function addRating($name,$sort_order) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ym_rating SET name = '" . $name . "', sort_order = '" . $sort_order . "'");
		$report_id = $this->db->getLastId();
		return $report_id;
	}

	public function editRating($ratings) {
    $this->db->query("UPDATE " . DB_PREFIX . "ym_rating SET is_delete = 1");
    foreach($ratings as $rating) {
      if(empty($rating['id'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ym_rating SET name = '" . $this->db->escape($rating['name']) . "', sort_order = '" . $this->db->escape($rating['sort_order']) . "'");
      } else {
        $this->db->query("UPDATE " . DB_PREFIX . "ym_rating SET name = '" . $this->db->escape($rating['name']) . "',  sort_order = '" . $this->db->escape($rating['sort_order']) . "', is_delete = NULL WHERE id = '" . $rating['id'] . "'");
      }
    }
	}

	public function deleteRating($id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ym_rating` WHERE id = '" . (int)$id . "'");
	}

	public function getRating($id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_rating WHERE id = '" . (int)$id . "'");
		return $query->row;
	}

	public function getRatings() {
		$sql = "SELECT * FROM " . DB_PREFIX . "ym_rating WHERE is_delete IS NULL ORDER BY sort_order ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}

}
