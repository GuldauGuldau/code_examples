<?php
class ModelYmreportQuestionComment extends Model {

  public function addComment($date) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ym_question_comment
                      SET question_id = '" . (int)$date['question_id'] . "',
                          author_id = '" . (int)$date['author_id'] . "',
                          text = '" . $this->db->escape($date['text']) . "',
                          sort_order = '0',
                          date_added = NOW()");

    $comment_id = $this->db->getLastId();

    return $comment_id;
  }

  public function getComments($question_id) {
		$query = $this->db->query("SELECT DISTINCT *, qc.id as comment_id
                              FROM " . DB_PREFIX . "ym_question_comment qc
                              LEFT JOIN " . DB_PREFIX . "ym_author a ON(qc.author_id = a.id)
                              WHERE question_id = '" . (int)$question_id . "' ORDER BY sort_order DESC");

		return $query->rows;
	}

  public function deleteComment($comment_id) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "ym_question_comment WHERE id = '" . (int)$comment_id . "'");
	}

  public function editComment($comment_id, $text) {
    $this->db->query("UPDATE " . DB_PREFIX . "ym_question_comment SET text = '" . $this->db->escape($text) . "' WHERE id = '" . (int)$comment_id . "'");
	}
}
