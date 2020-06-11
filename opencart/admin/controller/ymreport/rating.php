<?php
class ControllerYmreportRating extends Controller {
	private $error = array();

	public function index() {
    $this->load->language('catalog/manufacturer');
		$this->document->setTitle('Оценки для голосования');
		$this->load->model('ymreport/rating');
    $data['breadcrumbs'] = array();
    $data['breadcrumbs'][] = array(
      'text' => 'Оценки',
      'href' => $this->url->link('ymreport/rating', 'user_token=' . $this->session->data['user_token'], true)
    );

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['rating'])) {
        $ratings = $this->request->post['rating'];
        $this->model_ymreport_rating->editRating($ratings);
    }

    $data['action'] = $this->url->link('ymreport/rating', 'user_token=' . $this->session->data['user_token'], true);
    $data['ratings'] = $this->model_ymreport_rating->getRatings();
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    $this->response->setOutput($this->load->view('ymreport/rating_list', $data));
	}

	public function add() {
    $this->load->language('catalog/manufacturer');
		$this->document->setTitle('Оценки');
		$this->load->model('ymreport/rating');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$this->model_ymreport_report->addReport($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
	}

	public function edit() {
    $this->load->language('catalog/manufacturer');
		$this->document->setTitle('Доклад');
		$this->load->model('ymreport/report');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$this->model_ymreport_report->editReport($this->request->get['report_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
	}

	public function delete() {
    $this->load->language('catalog/manufacturer');
		$this->document->setTitle('Доклад');
		$this->load->model('ymreport/report');
		if (isset($this->request->post['selected']) ) {
			foreach ($this->request->post['selected'] as $report_id) {
				$this->model_ymreport_report->deleteReport($report_id);
			}
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Доклады',
			'href' => $this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('ymreport/report/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('ymreport/report/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['reports'] = array();

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$report_total = $this->model_ymreport_report->getTotalReports();
		$results = $this->model_ymreport_report->getReports($filter_data);

		foreach ($results as $result) {
      if($result['time_end'] == '00:00:00') {
        $time = substr($result['time_start'], 0, 5);
      } else {
        $time = substr($result['time_start'], 0, 5) . ' - ' . substr($result['time_end'], 0, 5);
      }
			$data['reports'][] = array(
				'report_id' => $result['id'],
				'name'            => $result['name'],
				'speaker_name'      => $result['speaker_name'],
        'time'      => $time,
				'edit'            => $this->url->link('ymreport/report/edit', 'user_token=' . $this->session->data['user_token'] . '&report_id=' . $result['id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$pagination = new Pagination();
		$pagination->total = $report_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($report_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($report_total - $this->config->get('config_limit_admin'))) ? $report_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $report_total, ceil($report_total / $this->config->get('config_limit_admin')));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('ymreport/reporter_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['report_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['report_id'])) {
			$data['action'] = $this->url->link('ymreport/report/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('ymreport/report/edit', 'user_token=' . $this->session->data['user_token'] . '&report_id=' . $this->request->get['report_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('ymreport/report', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['report_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$report_info = $this->model_ymreport_report->getReport($this->request->get['report_id']);

      $data['comments'] = $this->model_ymreport_report->getReportComment($this->request->get['report_id']);
		}

    //
		$factor = array(
			'1' => -5,
			'2' => -3,
			'3' => 1,
			'4' => 3,
			'5' => 5
		);

		if(isset($this->request->get['report_id'])) {
			$ratings = $this->model_ymreport_report->getReportRatings();
			$data['ratings'] = array();
			foreach($ratings as $rating) {

				$numbers = array();
				for ($x=5; $x>=1; $x--) {
					$numbers[] = array(
						'number' => $x,
						'total'  => $this->model_ymreport_report->getTotalRating($this->request->get['report_id'], $rating['id'], $x),
						'factor'    => $factor[$x]
					);
				}

				 $data['ratings'][] = array(
					 'rating_id' => $rating['id'],
					 'name'      => $rating['name'],
					 'numbers'   => $numbers,
				 );

			}
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($report_info)) {
			$data['name'] = $report_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('setting/store');
		if (isset($this->request->post['speaker_image'])) {
			$data['speaker_image'] = $this->request->post['speaker_image'];
		} elseif (!empty($report_info)) {
			$data['speaker_image'] = $report_info['speaker_image'];
		} else {
			$data['speaker_image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['speaker_image']) && is_file(DIR_IMAGE . $this->request->post['speaker_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['speaker_image'], 100, 100);
		} elseif (!empty($report_info) && is_file(DIR_IMAGE . $report_info['speaker_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($report_info['speaker_image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

    if (isset($this->request->post['speaker_name'])) {
			$data['speaker_name'] = $this->request->post['speaker_name'];
		} elseif (!empty($report_info)) {
			$data['speaker_name'] = $report_info['speaker_name'];
		} else {
			$data['speaker_name'] = '';
		}

    if (isset($this->request->post['speaker_position'])) {
			$data['speaker_position'] = $this->request->post['speaker_position'];
		} elseif (!empty($report_info)) {
			$data['speaker_position'] = $report_info['speaker_position'];
		} else {
			$data['speaker_position'] = '';
		}

    if (isset($this->request->post['time_start'])) {
			$data['time_start'] = $this->request->post['time_start'];
		} elseif (!empty($report_info)) {
			$data['time_start'] = substr($report_info['time_start'], 0, 5);
		} else {
			$data['time_start'] = '';
		}

    if (isset($this->request->post['time_end'])) {
			$data['time_end'] = $this->request->post['time_end'];
		} elseif (!empty($report_info)) {
			$data['time_end'] = substr($report_info['time_end'], 0, 5);
		} else {
			$data['time_end'] = '';
		}

    if (isset($this->request->post['is_report'])) {
			$data['is_report'] = $this->request->post['is_report'];
		} elseif (!empty($report_info)) {
			$data['is_report'] = $report_info['is_report'];
		} else {
			$data['is_report'] = '1';
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ymreport/report_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ($this->request->post['manufacturer_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['manufacturer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['manufacturer_id']) || (($seo_url['query'] != 'manufacturer_id=' . $this->request->get['manufacturer_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/manufacturer');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}
		array_multisort($sort_order, SORT_ASC, $json);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
