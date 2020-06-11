<?php
class ModelYmreportReport extends Model {

	public function getReport($report_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_report WHERE id = '" . (int)$report_id . "'");
		return $query->row;
	}

	public function getReports($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ym_report";
		if(isset($data['report'])) {
			$sql .= " WHERE is_report= '1'";
		}
		$sql .= " ORDER BY time_start ASC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalReports() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ym_report");
		return $query->row['total'];
	}

  public function getReportComment($report_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_report_comment WHERE id_report = '" . (int)$report_id . "'");
		return $query->rows;
	}

  public function getReportRatings() {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_rating");
		return $query->rows;
	}

  public function getRatingUser($report_id, $rating_id, $ip) {
		$query = $this->db->query("SELECT number FROM " . DB_PREFIX . "ym_report_to_rating WHERE id_report = '" . $report_id . "' AND id_rating = '" . $rating_id . "' AND user_ip = '" . $ip . "'");
		if(isset($query->row['number'])) {
			return $query->row['number'];
		} else {
			return 0;
		}
	}

	public function updateRating($report_id, $rating_id, $ip, $number) {
		$this->db->query("UPDATE " . DB_PREFIX . "ym_report_to_rating SET number = '" . (int)$number . "' WHERE id_report = '" . (int)$report_id . "' AND id_rating = '" . (int)$rating_id . "' AND user_ip = '" . $ip . "'");
	}

	public function addRating($report_id, $rating_id, $ip, $number) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ym_report_to_rating SET number = '" . (int)$number . "', id_report = '" . (int)$report_id . "', id_rating = '" . (int)$rating_id . "', user_ip = '" . $ip . "'");
	}

	public function addComment($report_id, $comment) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ym_report_comment SET id_report = '" . (int)$report_id . "', comment = '" . $this->db->escape($comment) . "'");
	}

	public function getTotalRatingUser($ip) {
		$query = $this->db->query("SELECT number FROM " . DB_PREFIX . "ym_report_to_rating WHERE id_report = '0' AND id_rating = '0' AND user_ip = '" . $ip . "'");
		if(isset($query->row['number'])) {
			return $query->row['number'];
		} else {
			return 0;
		}
	}

	public function clearRating($report_id, $rating_id, $ip) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ym_report_to_rating WHERE id_report = '" . (int)$report_id . "' AND id_rating = '" . (int)$rating_id . "' AND user_ip = '" . $ip . "'");
	}

}
