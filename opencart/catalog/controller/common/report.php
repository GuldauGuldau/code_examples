<?php
class ControllerCommonReport extends Controller {
	public function index() {
		$this->document->setTitle('Оценить доклад');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$this->load->model('ymreport/report');

    $filter = array(
      'report' => '1'
    );

		$reports = $this->model_ymreport_report->getReports($filter);

		$data['reports'] = array();
		$ip = $this->session->data['ym_user_id'];

		$this->load->model('ym/network');

		$data['auth'] = $this->model_ym_network->isAuth($ip);
		$data['count_contacts'] = count($this->model_ym_network->getContacts());

    $ratings = $this->model_ymreport_report->getReportRatings();

		foreach($reports as $report) {
      $data_rating = array();
      foreach($ratings as $rating) {
        $data_rating[] = array(
          'rating_id' => $rating['id'],
          'name'   => $rating['name'],
          'number' => $this->model_ymreport_report->getRatingUser($report['id'], $rating['id'], $ip)
        );
      }

			$data['reports'][] = array(
				'report_id'        => $report['id'],
				'time'             => substr($report['time_start'], 0, 5) . ' - ' .  substr($report['time_end'], 0, 5),
				'name'             => html_entity_decode($report['name']),
				'speaker_image'    => 'image/'.$report['speaker_image'],
				'speaker_name'     => $report['speaker_name'],
				'speaker_position' => $report['speaker_position'],
        'ratings'          => $data_rating
			);
		}

		//  Общая оценка

		 $data['total_rating'] = $this->model_ymreport_report->getTotalRatingUser($ip);

		 $data['network_link'] = $this->url->link('common/network', '', true);

		$this->response->setOutput($this->load->view('common/report', $data));
	}

  public function updateRating() {
    $json = array();

    if (isset($this->request->post['report_id']) && isset($this->request->post['rating_id']) && isset($this->request->post['number'])) {
      $this->load->model('ymreport/report');
      $ip = $this->session->data['ym_user_id'];
      $report = $this->model_ymreport_report->getRatingUser($this->request->post['report_id'], $this->request->post['rating_id'], $ip);

      $json['report'] = $report;
      if(!empty($report)) {
        $this->model_ymreport_report->updateRating($this->request->post['report_id'], $this->request->post['rating_id'], $ip, $this->request->post['number']);
        $json['success'] = 'Оценка обновлена!';
      } else {
        $this->model_ymreport_report->addRating($this->request->post['report_id'], $this->request->post['rating_id'], $ip, $this->request->post['number']);
        $json['success'] = 'Оценка добавлена!';
      }
    }

    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

  public function sendComment() {
    $json = array();

    if (isset($this->request->post['report_id']) && isset($this->request->post['comment'])) {
      $this->load->model('ymreport/report');
      $this->model_ymreport_report->addComment($this->request->post['report_id'], $this->request->post['comment']);
      $json['success'] = 'Комментарии добавлен!';
    }
    $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function clearRating() {
		$json = array();
		if (isset($this->request->post['report_id']) && isset($this->request->post['rating_id'])) {
			$this->load->model('ymreport/report');
			$ip = $this->session->data['ym_user_id'];
			$this->model_ymreport_report->clearRating($this->request->post['report_id'], $this->request->post['rating_id'], $ip);
			$json['success'] = 'Оценка удалена!';
		}
	}
}
