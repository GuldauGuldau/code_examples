<?php
class ModelYmreportReport extends Model {
	public function addReport($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ym_report SET name = '" . $this->db->escape($data['name']) . "', speaker_image = '" . $data['speaker_image'] . "', speaker_name = '" . $data['speaker_name'] . "', speaker_position = '" . $data['speaker_position'] . "', time_start = '" . $data['time_start'] . "', time_end = '" . $data['time_end'] . "', is_report = '" . $data['is_report'] . "'");
		$report_id = $this->db->getLastId();
		return $report_id;
	}

	public function editReport($report_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ym_report SET name = '" . $this->db->escape($data['name']) . "', speaker_image = '" . $this->db->escape($data['speaker_image']) . "', speaker_name = '" . $this->db->escape($data['speaker_name']) . "', speaker_position = '" . $this->db->escape($data['speaker_position']) . "', time_start = '" . $data['time_start'] . "', time_end = '" . $data['time_end'] . "', is_report = '" . (int)$data['is_report'] . "' WHERE id = '" . $report_id . "'");
	}

	public function deleteReport($report_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ym_report` WHERE id = '" . (int)$report_id . "'");
	}

	public function getReport($report_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_report WHERE id = '" . (int)$report_id . "'");
		return $query->row;
	}

	public function getReports($data = array()) {
		$sql = "SELECT *, convert(time_start,CHAR) FROM " . DB_PREFIX . "ym_report ORDER BY time_start ASC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 100;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getReportsReport($data = array()) {
		$sql = "SELECT *, convert(time_start,CHAR) FROM " . DB_PREFIX . "ym_report WHERE is_report='1' ORDER BY time_start ASC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 100;
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

  public function getTotalRating($report_id, $rating_id, $number) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ym_report_to_rating WHERE id_report = '" . $report_id . "' AND id_rating = '" . $rating_id . "' AND number = '" . $number . "'");
		return $query->row['total'];
	}
}
