<?php
class ModelYmreportQuestion extends Model {

	public function getQuestions() {
		$query = $this->db->query("SELECT yq.name,
                              (SELECT COUNT(yqr.rating) FROM " . DB_PREFIX . "ym_question_rating yqr WHERE yqr.rating = '1' AND yqr.id_question = yq.id) AS qslike,
                              (SELECT COUNT(yqr.rating) FROM " . DB_PREFIX . "ym_question_rating yqr WHERE yqr.rating = '0' AND yqr.id_question = yq.id) AS qsdislike,
                              yq.id as qsid
                              FROM " . DB_PREFIX . "ym_question yq
                              LEFT JOIN " . DB_PREFIX . "ym_question_rating yqr ON(yq.id = yqr.id_question)
                              GROUP BY yq.id ORDER BY qslike DESC");

		return $query->rows;
	}

	public function addQuestion($name) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ym_question SET name = '" . $this->db->escape($name) . "'");

		$question_id = $this->db->getLastId();

		return $question_id;
	}

	public function getUserLike($user_id,$question_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_question_rating WHERE user_id='" . $user_id . "' AND rating='1' AND id_question='" . $question_id . "'");

		if(!empty($query->rows)) {
			return true;
		}  else {
			return false;
		}
	}

	public function getUserDislike($user_id,$question_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ym_question_rating WHERE user_id='" . $user_id . "' AND rating='0' AND id_question='" . $question_id . "'");

		if(!empty($query->rows)) {
			return true;
		}  else {
			return false;
		}
	}


	public function addLikeToQuestion($question_id, $user_id) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ym_question_rating SET id_question = '" . (int)$question_id . "', rating = '1', user_id = '" . $user_id . "'");
	}

	public function addDislikeToQuestion($question_id, $user_id) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ym_question_rating SET id_question = '" . (int)$question_id . "', rating = '0', user_id = '" . $user_id . "'");
	}

	public function removeLikeToQuestion($question_id, $user_id) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "ym_question_rating WHERE id_question = '" . (int)$question_id . "' AND rating = '1' AND user_id = '" . $user_id . "'");
	}

	public function removeDislikeToQuestion($question_id, $user_id) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "ym_question_rating WHERE id_question = '" . (int)$question_id . "' AND rating = '0' AND user_id = '" . $user_id . "'");
	}


	public function getCountQuestions() {
		$query = $this->db->query("SELECT DISTINCT *  FROM " . DB_PREFIX . "ym_question");

		return count($query->rows);
	}



}
