<?php
class ControllerYmreportQuestion extends Controller {
	private $error = array();

	public function index() {
    $this->load->language('catalog/manufacturer');

		$this->document->setTitle('Вопросы');

		$this->load->model('ymreport/question');
		$this->load->model('ymreport/question_comment');

		$this->document->addStyle('view/stylesheet/ym.css');

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => 'Вопросы',
      'href' => $this->url->link('ymreport/question', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('ymreport/question', 'user_token=' . $this->session->data['user_token'], true);
    $questions = $this->model_ymreport_question->getQuestions();
		$data['questions'] = array();

		if(!empty($questions)) {
			foreach($questions as $question) {
				$comments = $this->model_ymreport_question_comment->getComments($question['qsid']);

				// комментарии от яндекс кассы
				$yk_comment = false;

				// комментарии от яндекс маркета
				$ym_comment = false;

				foreach($comments as $comment) {
					if($comment['author_id'] == 1) {
						$yk_comment = true;
					}

					if($comment['author_id'] == 2) {
						$ym_comment = true;
					}
				}

				$question['comments'] = $comments;
				$question['yk_comment'] = $yk_comment;
				$question['ym_comment'] = $ym_comment;
				$data['questions'][] = $question;
			}
		}

		$data['user_token'] = $this->session->data['user_token'];
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    $this->response->setOutput($this->load->view('ymreport/question_list', $data));
	}

	public function addComment() {
		$json = array();
		if (isset($this->request->post['text']) &&
		    isset($this->request->post['author_id']) &&
				isset($this->request->post['question_id'])) {

					$this->load->model('ymreport/question_comment');
					$this->model_ymreport_question_comment->addComment($this->request->post);
					$json['success'] = 'success';
  	}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function editComment() {
		$json = array();

		if (isset($this->request->post['text']) &&
				isset($this->request->post['comment_id'])) {
					$this->load->model('ymreport/question_comment');
					$this->model_ymreport_question_comment->editComment($this->request->post['comment_id'],$this->request->post['text']);
					$json['success'] = 'success';
  	}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function deleteComment() {
		$json = array();
		if (isset($this->request->post['comment_id'])) {
			$this->load->model('ymreport/question_comment');
			$this->model_ymreport_question_comment->deleteComment($this->request->post['comment_id']);
			$json['success'] = 'success';
  	}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
