<?php
class ControllerCommonQuestion extends Controller {
	public function index() {
		$this->document->setTitle('Вопросы');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['action'] = $this->url->link('common/question/sendComment', '', true);
		$this->load->model('ymreport/question');
		$questions = $this->model_ymreport_question->getQuestions();
		$data['questions'] = array();
		$ip = $this->session->data['ym_user_id'];
		$this->load->model('ym/network');
		$data['auth'] = $this->model_ym_network->isAuth($ip);
		$data['count_contacts'] = count($this->model_ym_network->getContacts());
		$this->load->model('ym/quiz');
		$data['count_quiz'] = count($this->model_ym_quiz->getQuiz());
		foreach($questions as $question) {
			$comments = $this->model_ymreport_question->getComments($question['qsid']);
			$new_comment = array();
			foreach($comments as $comment) {
				$new_comment[] = array(
					'name' => $comment['name'],
					'text' => html_entity_decode($comment['text'], ENT_QUOTES, 'UTF-8')
				);
			}

			$data['questions'][] = array(
				'id'        => $question['qsid'],
				'name'      => $question['name'],
				'like'      => $question['qslike'],
				'dislike'      => $question['qsdislike'],
				'current_like' => $this->model_ymreport_question->getUserLike($ip, $question['qsid']),
				'current_dislike' => $this->model_ymreport_question->getUserDislike($ip, $question['qsid']),
				'comments'       => $new_comment

			);
		}

		$data['network_link'] = $this->url->link('common/network', '', true);
		$data['quiz_link'] = $this->url->link('common/quiz', '', true);
		$this->response->setOutput($this->load->view('common/question', $data));
	}

	public function sendComment() {
		$this->load->model('ymreport/question');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$this->model_ymreport_question->addQuestion($this->request->post['name']);

		}

		$this->response->redirect($this->url->link('common/question', '', true));
  }

	public function addLike() {
    $json = array();
		$ip = $this->session->data['ym_user_id'];
    if (isset($this->request->post['question_id'])) {
      $this->load->model('ymreport/question');
      $this->model_ymreport_question->addLikeToQuestion($this->request->post['question_id'], $ip);
      $json['success'] = 'Лайк добавлен!';
    }

    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function addDislike() {
    $json = array();
		$ip = $this->session->data['ym_user_id'];
    if (isset($this->request->post['question_id'])) {
      $this->load->model('ymreport/question');
      $this->model_ymreport_question->addDislikeToQuestion($this->request->post['question_id'], $ip);
      $json['success'] = 'Дизлайк добавлен!';
    }

    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }


	public function removeLike() {
    $json = array();
		$ip = $this->session->data['ym_user_id'];
    if (isset($this->request->post['question_id'])) {
      $this->load->model('ymreport/question');
      $this->model_ymreport_question->removeLikeToQuestion($this->request->post['question_id'], $ip);
      $json['success'] = 'Лайк удален!';
    }
    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function removeDislike() {
    $json = array();
		$ip = $this->session->data['ym_user_id'];
    if (isset($this->request->post['question_id'])) {
      $this->load->model('ymreport/question');
      $this->model_ymreport_question->removeDislikeToQuestion($this->request->post['question_id'], $ip);
      $json['success'] = 'Дизлайк удален!';
    }
    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

}
