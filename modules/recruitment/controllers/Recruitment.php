<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Recruitment Controller
 */
class recruitment extends AdminController {
	public function __construct() {
		parent::__construct();
		$this->load->model('recruitment_model');
		$this->load->library('email');
		$this->load->config('email');
	}

	/**
	 * setting
	 * @return view
	 */
	public function setting() {
		if (!has_permission('recruitment', '', 'edit') && !is_admin()) {
			access_denied('recruitment');
		}
		$data['group'] = $this->input->get('group');
		$data['title'] = _l('setting');
		$data['tab'][] = 'job_position';
		$data['tab'][] = 'evaluation_criteria';
		$data['tab'][] = 'evaluation_form';
		$data['tab'][] = 'tranfer_personnel';
		$data['tab'][] = 'skills';
		$data['tab'][] = 'company_list';
		$data['tab'][] = 'industry_list';
		$data['tab'][] = 'recruitment_campaign_setting';
		$data['tab'][] = 'default_approver';


		if ($data['group'] == '') {
			$data['group'] = 'job_position';
		}
		$data['tabs']['view'] = 'includes/' . $data['group'];

		$data['positions'] = $this->recruitment_model->get_job_position();

		$data['list_group'] = $this->recruitment_model->get_group_evaluation_criteria();

		$data['group_criterias'] = $this->recruitment_model->get_list_child_criteria();

		$data['list_form'] = $this->recruitment_model->get_list_evaluation_form();

		$data['list_set_tran'] = $this->recruitment_model->get_list_set_transfer();

		$data['skills'] = $this->recruitment_model->get_skill();

		$data['company_list'] = $this->recruitment_model->get_company();

		$data['industry_list'] = $this->recruitment_model->get_industry();

		foreach($data['positions'] as $position) {
			$jd_file_data = $this->get_lastest_job_position_jd_file($position['position_id']);
			if($jd_file_data == '') {
				unset($jd_file_data);
				$jd_file_data = ['name' => '', 'url' => ''];
			}
			$data['positions_files'][$position['position_id']] = $jd_file_data;
		}
		$data['default_approver'] = $this->recruitment_model->get_default_approver();

		$data['staffs'] = $this->staff_model->get();

		$this->load->view('manage_setting', $data);
	}

	public function get_lastest_job_position_jd_file($position_id) {
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$this->load->helper('file');
		$position_jd_folder_path = RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $position_id;
		if(!empty(get_filenames($position_jd_folder_path))) {
			$file_names = get_filenames($position_jd_folder_path);
			$lastest_file_name = '';
			$lastest_date = '';
			foreach($file_names as $index => $file_name) {
				$file_info = get_file_info($position_jd_folder_path. '/' . $file_name);
				if($index == 0) {
					$lastest_date = $file_info['date'];
					$lastest_file_name = $file_info['name'];
				} else {
					if($file_info['date'] > $lastest_date) {
						$lastest_date =  $file_info['date'];
						$lastest_file_name = $file_info['name'];
					}
				}
			}
			$lastest_file_download_url = base_url() . 'modules/recruitment/uploads/hr_jd/' . $position_id . '/' . $lastest_file_name;
			return ['name' => $lastest_file_name, 'url' => $lastest_file_download_url];
		} else {
			return null;
		}
	}

	public function get_lastest_job_position_jd_file_ajax($position_id = '') {
		if(empty($position_id))
			return;
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$this->load->helper('file');
		$position_jd_folder_path = RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $position_id;
		if(!empty(get_filenames($position_jd_folder_path))) {
			$file_names = get_filenames($position_jd_folder_path);
			$lastest_file_name = '';
			$lastest_date = '';
			foreach($file_names as $index => $file_name) {
				$file_info = get_file_info($position_jd_folder_path. '/' . $file_name);
				if($index == 0) {
					$lastest_date = $file_info['date'];
					$lastest_file_name = $file_info['name'];
				} else {
					if($file_info['date'] > $lastest_date) {
						$lastest_date =  $file_info['date'];
						$lastest_file_name = $file_info['name'];
					}
				}
			}
			$lastest_file_download_url = base_url() . 'modules/recruitment/uploads/hr_jd/' . $position_id . '/' . $lastest_file_name;
			echo json_encode(['name' => $lastest_file_name, 'url' => $lastest_file_download_url]);
		} else {
			echo '';
		}
	}

	public function remove_job_jd_files($position_id) {
		if($this->input->is_ajax_request()) {
			$dir_path = RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $position_id;
			$this->load->helper('file');
			if(delete_files($dir_path)) {
				echo 1;
			} else {
				echo 0;
			}
		} else {
			redirect(admin_url('recruitment/setting?group=job_position'));
		}
	}

	/**
	 * job position
	 * @return redirect
	 */
	public function job_position() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if(!is_dir('uploads/hr_jd')) {
				umask(0);
				mkdir('uploads/hr_jd', 0755, TRUE);
				chmod('uploads/hr_jd', 0755);
			}
			
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_job_position($data);
				if ($id) {
					if(!is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id)) {
						umask(0);
						mkdir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id, 0755, TRUE);
						chmod(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id, 0755);
					}
					if(isset($_FILES)) {
						$original_file_name = $_FILES['job_jd_file']['name'];
						$config['upload_path']          = RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id;
		                $config['allowed_types']        = 'pdf|doc|docx|txt';
		                $config['overwrite']			= TRUE;
		                $config['file_name'] = $original_file_name;
		                $this->load->library('upload', $config);
		                $this->upload->do_upload('job_jd_file');
		                unset($data['job_jd_file']);	
					}
					$success = true;
					$message = _l('added_successfully', _l('job_position'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=job_position'));
			} else {
				$id = $data['id'];
				unset($data['id']);

				if(!is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id)) {
					umask(0);
					mkdir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id, 0755, TRUE);
					chmod(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id, 0755);
				}

				if(isset($_FILES)) {
					$original_file_name = $_FILES['job_jd_file']['name'];
					$config['upload_path']          = RECRUITMENT_MODULE_UPLOAD_FOLDER . '/hr_jd/' . $id;
	                $config['allowed_types']        = 'pdf|doc|docx|txt';
	                $config['overwrite']			= TRUE;
	                $config['file_name'] = $original_file_name;
	                $this->load->library('upload', $config);
	                $this->upload->do_upload('job_jd_file');
	                unset($data['job_jd_file']);	
				}

				$success = $this->recruitment_model->update_job_position($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('job_position'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=job_position'));
			}
			die;
		}
	}

	/**
	 * delete job_position
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_job_position($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=job_position'));
		}
		$response = $this->recruitment_model->delete_job_position($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('job_position')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('job_position')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('job_position')));
		}
		redirect(admin_url('recruitment/setting?group=job_position'));
	}

	/**
	 * recruitmentproposal
	 * @param  string $id 
	 * @return view
	 */
	public function recruitment_proposal($id = '') {
		$this->load->model('departments_model');
		$this->load->model('staff_model');

		$data['departments'] = $this->departments_model->get();
		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['staffs'] = $this->staff_model->get();
		$data['proposal_id'] = $id;

		$data['title'] = _l('recruitment_proposal');
		$this->load->view('recruitment_proposal', $data);
	}

	/**
	 * proposal
	 * @return redirect
	 */
	public function proposal() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$data = $this->input->post();
			$data['job_description'] = $this->input->post('job_description', false);
			if ($this->input->post('no_editor')) {
				$data['job_description'] = nl2br(clear_textarea_breaks($this->input->post('job_description')));
			}
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_recruitment_proposal($data);
				if ($id) {
					handle_rec_proposal_file($id);
					$success = true;
					$message = _l('added_successfully', _l('recruitment_proposal'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/recruitment_proposal'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_recruitment_proposal($data, $id);
				handle_rec_proposal_file($id);
				if ($success) {
					$message = _l('updated_successfully', _l('recruitment_proposal'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/recruitment_proposal'));
			}
			die;
		}
	}

	/**
	 * delete recruitment proposal
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_recruitment_proposal($id) {
		if (!$id) {
			redirect(admin_url('recruitment/recruitment_proposal'));
		}
		$response = $this->recruitment_model->delete_recruitment_proposal($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('recruitment_proposal')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('recruitment_proposal')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('recruitment_proposal')));
		}
		redirect(admin_url('recruitment/recruitment_proposal'));
	}

	/**
	 * table proposal
	 * @return
	 */
	public function table_proposal() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('recruitment', 'table_proposal'));
		}
	}

	/**
	 * table campaign
	 * @return
	 */
	public function table_campaign() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('recruitment', 'recruitment_campaign/table_campaign'));
		}
	}

	/**
	 * get proposal data ajax
	 * @param  integer $id
	 * @return view
	 */
	public function get_proposal_data_ajax($id) {

		$data['id'] = $id;
		$data['proposals'] = $this->recruitment_model->get_rec_proposal($id);
		$data['proposal_file'] = $this->recruitment_model->get_proposal_file($id);

		$this->load->view('proposal_preview', $data);
	}

	/**
	 * delete proposal attachment
	 * @param  int $id
	 * @return
	 */
	public function delete_proposal_attachment($id) {
		$this->load->model('misc_model');
		$file = $this->misc_model->get_file($id);
		if ($file->staffid == get_staff_user_id() || is_admin()) {
			echo html_entity_decode($this->recruitment_model->delete_proposal_attachment($id));
		} else {
			header('HTTP/1.0 400 Bad error');
			echo _l('access_denied');
			die;
		}
	}

	/**
	 * file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return view
	 */
	public function file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('_file', $data);
	}

	/**
	 * approve reject proposal
	 * @param  int $type
	 * @param  int $id
	 * @return redirect
	 */
	public function approve_reject_proposal($type, $id) {
		$result = $this->recruitment_model->approve_reject_proposal($type, $id);
		if ($result == 'approved') {
			set_alert('success', _l('approved') . ' ' . _l('recruitment_proposal') . ' ' . _l('successfully'));
		} elseif ($result == 'reject') {
			set_alert('success', _l('reject') . ' ' . _l('recruitment_proposal') . ' ' . _l('successfully'));
		} else {
			set_alert('warning', _l('action') . ' ' . _l('fail'));
		}
		redirect(admin_url('recruitment/recruitment_proposal#' . $id));
	}

	/**
	 * recruitment campaign
	 * @param  int $id
	 * @return view
	 */
	public function recruitment_campaign($id = '') {
		$this->load->model('departments_model');
		$this->load->model('staff_model');

		$data['rec_proposal'] = $this->recruitment_model->get_rec_proposal_by_status(2);
		$data['departments'] = $this->departments_model->get();
		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['staffs'] = $this->staff_model->get();
		$data['campaign_id'] = $id;
		$data['rec_channel_form']	= $this->recruitment_model->get_recruitment_channel();
		$data['company_list'] = $this->recruitment_model->get_company();
		$data['default_approver'] = $this->recruitment_model->get_default_approver();
		$data['title'] = _l('recruitment_campaign');
		$this->load->view('recruitment_campaign/recruitment_campaign', $data);
	}

	/**
	 * campaign
	 * @return redirect
	 */
	public function campaign() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$data = $this->input->post();
			$data['cp_job_description'] = $this->input->post('cp_job_description', false);
			if ($this->input->post('no_editor')) {
				$data['cp_job_description'] = nl2br(clear_textarea_breaks($this->input->post('cp_job_description')));
			}
			if (!$this->input->post('cp_id')) {
				$data['cp_created_by'] = get_staff_user_id();
				$id = $this->recruitment_model->add_recruitment_campaign($data);
				if ($id) {
					$this->send_mail_new_recruitment_campaign();
					handle_rec_campaign_file($id);
					$success = true;
					$message = _l('added_successfully', _l('recruitment_campaign'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/recruitment_campaign'));
			} else {
				$id = $data['cp_id'];
				unset($data['cp_id']);
				$success = $this->recruitment_model->update_recruitment_campaign($data, $id);
				handle_rec_campaign_file($id);
				if ($success) {
					$message = _l('updated_successfully', _l('recruitment_campaign'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/recruitment_campaign'));
			}
			die;
		}
	}

	/**
	 * delete recruitment campaign
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_recruitment_campaign($id) {
		if (!$id) {
			redirect(admin_url('recruitment/recruitment_campaign'));
		}
		$response = $this->recruitment_model->delete_recruitment_campaign($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('recruitment_campaign')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('recruitment_campaign')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('recruitment_campaign')));
		}
		redirect(admin_url('recruitment/recruitment_campaign'));
	}

	/**
	 * campaign code exists
	 * @return
	 */
	public function campaign_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$cp_id = $this->input->post('cp_id');
				if ($cp_id != '') {
					$this->db->where('cp_id', $cp_id);
					$campaign = $this->db->get('tblrec_campaign')->row();
					if ($campaign->campaign_code == $this->input->post('campaign_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('campaign_code', $this->input->post('campaign_code'));
				$total_rows = $this->db->count_all_results('tblrec_campaign    ');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/**
	 * get campaign data ajax
	 * @param  int $id
	 * @return view
	 */
	public function get_campaign_data_ajax($id) {
		$this->load->model('departments_model');
		$data['id'] = $id;
		$data['campaigns'] = $this->recruitment_model->get_rec_campaign($id);
		$position_id = $data['campaigns']->cp_position;
		$data['position_jd'] = $this->get_lastest_job_position_jd_file($position_id);
		$data['campaign_file'] = $this->recruitment_model->get_campaign_file($id);
		$data['departments'] = $this->departments_model->get();
		$data['rec_channel_form'] = $this->recruitment_model->get_recruitment_channel($data['campaigns']->rec_channel_form_id);
		$this->load->view('recruitment_campaign/campaign_preview', $data);
	}

	/**
	 * campaign file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return
	 */
	public function campaign_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('recruitment_campaign/_file', $data);
	}

	/**
	 * delete campaign attachment
	 * @param  int $id
	 * @return
	 */
	public function delete_campaign_attachment($id) {
		$this->load->model('misc_model');
		$file = $this->misc_model->get_file($id);
		if ($file->staffid == get_staff_user_id() || is_admin()) {
			echo html_entity_decode($this->recruitment_model->delete_campaign_attachment($id));
		} else {
			header('HTTP/1.0 400 Bad error');
			echo _l('access_denied');
			die;
		}
	}

	/**
	 * candidate profile
	 * @return view
	 */
	public function candidate_profile() {
		if ($this->input->get('kanban')) {
            $this->switch_kanban_candidate(0, true);
        }
        $data['switch_kanban_candidate'] = false;
        $data['bodyclass']     = 'recruitment-page';
        if ($this->session->userdata('candidate_profile_kanban_view') == 'true') {
            $data['switch_kanban_candidate'] = true;
            $data['bodyclass']     = 'recruitment-page kan-ban-body';
        }
        $data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();
        
		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['title'] = _l('candidate_profile');
		$this->load->view('candidate_profile/candidate_profile', $data);
	}

	/**
	 * candidates
	 * @param  int $id
	 * @return
	 */
	public function candidates($id = '') {
		if ($id != '') {

			$data['candidate'] = $this->recruitment_model->get_candidates($id);

			$data['title'] = $data['candidate']->candidate_name;

		} else {

			$data['title'] = _l('new_candidate');
		}

		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();
		$data['skills'] = $this->recruitment_model->get_skill();


		$this->load->view('candidate_profile/candidate', $data);
	}

	/**
	 * add update candidate
	 * @param int $id
	 */
	public function add_update_candidate($id = '') {

		$data = $this->input->post();
		if ($data) {
			if ($id == '') {
				$ids = $this->recruitment_model->add_candidate($data);
				if ($ids) {
					handle_rec_candidate_file($ids);
					handle_rec_candidate_avar_file($ids);
					$success = true;
					$message = _l('added_successfully', _l('candidate_profile'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/candidate_profile'));
			} else {
				$success = $this->recruitment_model->update_cadidate($data, $id);
				if ($success == true) {
					handle_rec_candidate_file($id);
					handle_rec_candidate_avar_file($id);
					$message = _l('updated_successfully', _l('candidate_profile'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/candidate_profile'));
			}
		}
	}

	/**
	 * table candidates
	 * @return
	 */
	public function table_candidates() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('recruitment', 'candidate_profile/table_candidates'));
		}
	}

	public function send_mail_campaign_status_changed($cp_id) {
		$rec_campaign=$this->recruitment_model->get_rec_campaign($cp_id);
		$rec_job_position=$this->recruitment_model->get_rec_job_position($rec_campaign->cp_position);
		$department=$this->recruitment_model->get_department($rec_campaign->cp_department);
		$followers=explode(',',$this->recruitment_model->get_rec_campaign_follower($cp_id));
		$managers=explode(',',$this->recruitment_model->get_rec_campaign_manager($cp_id));
		$approvers=explode(',',$this->recruitment_model->get_rec_campaign_approver($cp_id));
		$staffs=array_unique(array_merge($followers,$managers,$approvers));
		$status='';
		switch($rec_campaign->cp_status) {
			case 1:
				$status="Planning";
				break;
			case 2:
				$status="Overdue";
				break;
			case 3:
				$status="Processing";
				break;
			case 4:
				$status="Finish";
				break;
			case 5:
				$status="Cancel";
				break;
			default:
				break;
		}
		foreach($staffs as $key => $value) {
			$staff=$this->staff_model->get($value);
			if(!empty($staff->email)) {
				$template = new StdClass();
				$template->message = 
				'<br/>Hi '.$staff->full_name.'<br />
				<br/>Approver marked campaign as "'.$status.'"<br/>
				<br/><strong>Campaign code</strong>: '.$rec_campaign->campaign_code.'<br/>
				<br/><strong>Campaign name</strong>: '.$rec_campaign->campaign_name.'<br/>
				<br/><strong>Position</strong>: '.$rec_job_position.'<br/>
				<br/><strong>Department</strong>: '.$department.'<br/>
				<br/>You can view the campaign on the following link : <a href='.admin_url('recruitment/recruitment_campaign#'.$cp_id).'>link<a/><br/>
				<br/>Kind Regards,<br/>
				<br/>'. get_option('email_signature').'<br/>';
				$template->fromname = get_option('companyname') != '' ? get_option('companyname') : 'TEST';
				$template->subject  = 'Campaign Status Changed - '.$rec_campaign->campaign_code.' - '.$rec_campaign->campaign_name;
				$template = parse_email_template($template);
				hooks()->do_action('before_send_test_smtp_email');
				$this->email->initialize();
				$this->email->set_newline(config_item('newline'));
				$this->email->set_crlf(config_item('crlf'));
				$this->email->from(get_option('smtp_email'), $template->fromname);
				$this->email->to($staff->email);
				$systemBCC = get_option('bcc_emails');
				if ($systemBCC != '') {
				    $this->email->bcc($systemBCC);
				}
				$this->email->subject($template->subject);		
				$this->email->message($template->message);
				$this->email->send(true);
			}	
		}
	}
	public function send_mail_new_recruitment_campaign() {
		$rec_campaign     = $this->recruitment_model->get_newest_rec_campaign();
		$rec_job_position = $this->recruitment_model->get_rec_job_position($rec_campaign->cp_position);
		$department       = $this->recruitment_model->get_department($rec_campaign->cp_department);
		$id               = $rec_campaign->cp_id;
		$followers=explode(',',$this->recruitment_model->get_rec_campaign_follower($id));
		$managers=explode(',',$this->recruitment_model->get_rec_campaign_manager($id));
		$approvers=explode(',',$this->recruitment_model->get_rec_campaign_approver($id));
		$notified_staffs=[];
		foreach($followers as $follower) {
			$notified_staffs[$follower] = "Follower";
		}
		foreach($managers as $manager) {
			if(array_key_exists($manager,$notified_staffs)) {		
				$notified_staffs[$manager] = $notified_staffs[$manager].", Manager";
			} else {
				$notified_staffs[$manager] = "Manager";
			}
		}	
		foreach($approvers as $approver) {
			if(array_key_exists($approver,$notified_staffs)) {
				$notified_staffs[$approver] = $notified_staffs[$approver].", Approver";
			} else {
				$notified_staffs[$approver] = "Approver";
			}			
		}
		foreach($notified_staffs as $key => $value){		
			$staff = $this->staff_model->get($key);
			if(!empty($staff->email)) {
				// Simulate fake template to be parsed
				$template = new StdClass();
				$template->message  = 
				'<br/>Hi '.$staff->full_name.'<br/>
				<br/>You are added as '.$value.' on campaign<br/>
				<br/><strong>Campaign code</strong>: '.$rec_campaign->campaign_code.'<br/>
				<br/><strong>Campaign name</strong>: '.$rec_campaign->campaign_name.'<br/>
				<br/><strong>Position</strong>: '.$rec_job_position.'<br/>
				<br/><strong>Department</strong>: '.$department.'<br/>
				<br/><strong>Reason Recruitment</strong>: '.$rec_campaign->cp_reason_recruitment.'<br />
				<br/>You can view the campaign on the following link : <a href='.admin_url('recruitment/recruitment_campaign#'.$rec_campaign->cp_id).'>"link"<a/><br/>
				<br/>Kind Regards,<br/>
				<br/>'.get_option('email_signature').'<br/>' ;
				$template->fromname = get_option('companyname') != '' ? get_option('companyname') : 'TEST';
				$template->subject  = 'New Recruitment Campaign - '.$rec_campaign->campaign_code.' - '.$rec_campaign->campaign_name;
				$template = parse_email_template($template);
				hooks()->do_action('before_send_test_smtp_email');
				$this->email->initialize();
				$this->email->set_newline(config_item('newline'));
				$this->email->set_crlf(config_item('crlf'));
				$this->email->from(get_option('smtp_email'), $template->fromname);
				$this->email->to($staff->email);
				$systemBCC = get_option('bcc_emails');
				if ($systemBCC != '') {
					$this->email->bcc($systemBCC);
				}
				$this->email->subject($template->subject);
				$this->email->message($template->message);
				$this->email->send(true);
			}
		}
	}
	public function send_mail_new_interview_schedule($id,$cp_id) {
		$staff = $this->staff_model->get($id);
		$rec_campaign = $this->recruitment_model->get_rec_campaign($cp_id);
		$interview = $this->recruitment_model->get_newest_interview_schedule();
		$template = new StdClass();
		$template->message = 
		'<br/>Hi '.$staff->full_name.'.<br/>
		<br/>A new interview has been created.<br/>
		<br/>You are added as Interviewer on interview<br/>
		<br/><strong>Interview Name:</strong> '.$interview->is_name.'<br/>
		<br/><strong>Time:</strong> from '.$interview->from_time.' to '.$interview->to_time.'<br/>
		<br/><strong>Interview Day:</strong> '.$interview->interview_day.'<br/>
		<br/><strong>Recruitment Campaign:</strong> '.$rec_campaign->campaign_name.'<br/>
		<br/>You can view the campaign on the following link: <a href='.admin_url('recruitment/interview_schedule#'.$interview->id).'>"link"<a/><br/>
		<br/>Kind Regards,<br/>
		<br/>'.get_option('email_signature').'<br/>' ;
		$template->fromname = get_option('companyname') != '' ? get_option('companyname') : 'TEST';
		$template->subject  = _l('interview_letter');
		$template = parse_email_template($template);
		hooks()->do_action('before_send_test_smtp_email');
		$this->email->initialize();
		$this->email->set_newline(config_item('newline'));
		$this->email->set_crlf(config_item('crlf'));
		$this->email->from(get_option('smtp_email'), $template->fromname);
		$this->email->to($staff->email);
		$systemBCC = get_option('bcc_emails');
		if ($systemBCC != '') {
			$this->email->bcc($systemBCC);
		}
		$this->email->subject($template->subject);		
		$this->email->message($template->message);
		$this->email->send(true);
	}
	/**
	 * change status campaign
	 * @param  int $status
	 * @param  int $cp_id
	 * @return
	 */
	public function change_status_campaign($status, $cp_id) {
		$change = $this->recruitment_model->change_status_campaign($status, $cp_id);
		$this->send_mail_campaign_status_changed($cp_id);
		if ($change == true) {
			$message = _l('change_status_campaign') . ' ' . _l('successfully');
			echo json_encode([
				'result' => $message,
			]);
		} else {
			$message = _l('change_status_campaign') . ' ' . _l('fail');
			echo json_encode([
				'result' => $message,
			]);
		}
	}
	/**
	 * candidate code exists
	 * @return
	 */
	public function candidate_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$candidate = $this->input->post('candidate');
				if ($candidate != '') {
					$this->db->where('id', $candidate);
					$cd = $this->db->get('tblrec_candidate')->row();
					if ($cd->candidate_code == $this->input->post('candidate_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('candidate_code', $this->input->post('candidate_code'));
				$total_rows = $this->db->count_all_results('tblrec_candidate');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}
	/**
	 * candidate email exists
	 * @return
	 */
	public function candidate_email_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$candidate = $this->input->post('candidate');
				if ($candidate != '') {
					$this->db->where('id', $candidate);
					$_current_email = $this->db->get(db_prefix() . 'rec_candidate')->row();
					if ($_current_email->email == $this->input->post('email')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('email', $this->input->post('email'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'rec_candidate');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}
	/**
	 * interview schedule
	 * @param  int $id
	 * @return view
	 */
	public function interview_schedule($id = '') {
		$data['staffs'] = $this->staff_model->get();
		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['list_cd'] = $this->recruitment_model->get_list_cd();
		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();
		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['interview_id'] = $id;
		$data['title'] = _l('interview_schedule');
		$this->load->view('interview_schedule/interview_schedule', $data);
	}
	/**
	 * get candidate infor change
	 * @param  object $candidate
	 * @return json
	 */
	public function get_candidate_infor_change($candidate) {
		$infor = $this->recruitment_model->get_candidates($candidate);
		echo json_encode([
			'email' => $infor->email,
			'phonenumber' => $infor->phonenumber,
		]);
	}
	/**
	 * interview schedules
	 * @return redirect
	 */
	public function interview_schedules() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_interview_schedules($data);
				if ($id) {
					foreach($data['interviewer'] as $key => $value) {
						$this->send_mail_new_interview_schedule( $value,$data['campaign']);
					}
					$message = _l('added_successfully', _l('interview_schedule'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/interview_schedule'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_interview_schedules($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('interview_schedule'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/interview_schedule'));
			}
			die;
		}
	}
	/**
	 * deletecandidate
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_candidate($id) {
		if (!$id) {
			redirect(admin_url('recruitment/candidate_profile'));
		}
		$response = $this->recruitment_model->delete_candidate($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('candidate')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('candidate')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('candidate')));
		}
		redirect(admin_url('recruitment/candidate_profile'));
	}
	/**
	 * table interview
	 * @return
	 */
	public function table_interview() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('recruitment', 'interview_schedule/table_interview'));
		}
	}
	/**
	 * candidate
	 * @param  int $id
	 * @return view
	 */
	public function candidate($id) {
		$data['title'] = _l('candidate_detail');
		$data['candidate'] = $this->recruitment_model->get_candidates($id);
		$data['skill_name'] ='';
		if($data['candidate']) {
			if($data['candidate']->skill) {
				$skill_array = explode(',', $data['candidate']->skill);
				foreach ($skill_array as $value) {
					if($value) {
					    $skill_value = $this->recruitment_model->get_skill($value);
					    if($skill_value) {
					    	$data['skill_name'] .= $skill_value->skill_name.', ';
					    }
					}
				}
			}
		}
		if ($data['candidate']->rec_campaign > 0) {
			$campaign = $this->recruitment_model->get_rec_campaign($data['candidate']->rec_campaign);
			if($campaign) {
				$data['evaluation'] = $this->recruitment_model->get_evaluation_form_by_position($campaign->cp_position);
			} else {
				$data['evaluation'] = '';
			}
		} else {
			$data['evaluation'] = '';
		}
		$data['list_interview'] = $this->recruitment_model->get_interview_by_candidate($id);
		$data['cd_evaluation'] = $this->recruitment_model->get_cd_evaluation($id);
		$data['assessor'] = '';
		$data['feedback'] = '';
		$data['evaluation_date'] = '';
		$data['avg_score'] = 0;
		$data['data_group'] = [];
		$rs_evaluation = [];
		if (count($data['cd_evaluation']) > 0) {
			$data['assessor'] = $data['cd_evaluation'][0]['assessor'];
			$data['feedback'] = $data['cd_evaluation'][0]['feedback'];
			$data['evaluation_date'] = $data['cd_evaluation'][0]['evaluation_date'];
			foreach ($data['cd_evaluation'] as $eval) {
				$data['avg_score'] += ($eval['rate_score'] * $eval['percent']) / 100;

				if (!isset($rs_evaluation[$eval['group_criteria']])) {
					$rs_evaluation[$eval['group_criteria']]['toltal_percent'] = 0;
					$rs_evaluation[$eval['group_criteria']]['result'] = 0;
				}
				$rs_evaluation[$eval['group_criteria']]['toltal_percent'] += $eval['percent'];
				$rs_evaluation[$eval['group_criteria']]['result'] += ($eval['rate_score'] * $eval['percent']) / 100;
			}
			$data['data_group'] = $rs_evaluation;
		}
		$this->load->view('candidate_profile/candidate_detail', $data);
	}
	/**
	 * candidate file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return view
	 */
	public function candidate_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('candidate_profile/_file', $data);
	}
	/**
	 * deletec andidate attachment
	 * @param  int $id
	 * @return
	 */
	public function delete_candidate_attachment($id) {
		$this->load->model('misc_model');
		$file = $this->misc_model->get_file($id);
		if ($file->staffid == get_staff_user_id() || is_admin()) {
			echo html_entity_decode($this->recruitment_model->delete_candidate_attachment($id));
		} else {
			header('HTTP/1.0 400 Bad error');
			echo _l('access_denied');
			die;
		}
	}
	/**
	 * care candidate
	 * @return json
	 */
	public function care_candidate() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$id = $this->recruitment_model->add_care_candidate($data);
			if ($id) {
				$mess = _l('care_candidate_success');
				echo json_encode([
					'mess' => $mess,
				]);
			} else {
				$mess = _l('care_candidate_fail');
				echo json_encode([
					'mess' => $mess,
				]);
			}
		}
	}
	/**
	 * rating candidate
	 * @return json
	 */
	public function rating_candidate() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$id = $this->recruitment_model->rating_candidate($data);
			if ($id == true) {
				$mess = _l('rating_candidate_success');
				echo json_encode([
					'mess' => $mess,
					'rate' => $data['rating'],
				]);
			} else {
				$mess = _l('rating_candidate_fail');
				echo json_encode([
					'mess' => $mess,
					'rate' => 0,
				]);
			}
		}
	}
	/**
	 * send mail candidate
	 * @return redirect
	 */
	public function send_mail_candidate() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$rs = $this->recruitment_model->send_mail_candidate($data);
			if ($rs == true) {
				set_alert('success', _l('send_mail_successfully'));
			}
			redirect(admin_url('recruitment/candidate/' . $data['candidate']));
		}
	}
	/**
	 * send mail list candidate
	 * @return redirect
	 */
	public function send_mail_list_candidate() {
		if ($this->input->post()) {
			$data = $this->input->post();
			foreach ($data['candidate'] as $cd) {
				$cdate = $this->recruitment_model->get_candidates($cd);
				$data['email'][] = $cdate->email;
			}
			$rs = $this->recruitment_model->send_mail_list_candidate($data);
			if ($rs == true) {
				set_alert('success', _l('send_mail_successfully'));
			}
			redirect(admin_url('recruitment/candidate_profile'));
		}
	}
	/**
	 * check time interview
	 * @return json
	 */
	public function check_time_interview() {
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['candidate'] != '') {
				if ($data['interview_day'] == '' || $data['from_time'] == '' || $data['to_time'] == '') {
					$rs = _l('please_enter_the_full_interview_time');
					echo json_encode([
						'return' => true,
						'rs' => $rs,
					]);
				} elseif ($data['interview_day'] != '' && $data['from_time'] != '' && $data['to_time'] != '') {
					$check = $this->recruitment_model->check_candidate_interview($data);
					if (count($check) > 0) {
						$rs = _l('check_candidate_interview_1');
						echo json_encode([
							'return' => true,
							'rs' => $rs,
						]);
					} else {
						echo json_encode([
							'return' => false,
						]);
					}
				}
			}
		}
	}
	/**
	 * [get_candidate_edit_interview description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_candidate_edit_interview($id) {
		$list_cd = $this->recruitment_model->get_list_candidates_interview($id);
		$cd = $this->recruitment_model->get_candidates();
		$html = '';
		$count = 0;
		foreach ($list_cd as $l) {
			if ($count == 0) {
				$class = 'success';
				$class_btn = 'new_candidates';
				$i = 'plus';
			} else {
				$class_btn = 'remove_candidates';
				$class = 'danger';
				$i = 'minus';
			}
			$html .= '<div class="row col-md-12" id="candidates-item">
                        <div class="col-md-4 form-group">
                          <select name="candidate[' . $count . ']" onchange="candidate_infor_change(this); return false;" id="candidate[' . $count . ']" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="' . _l('ticket_settings_none_assigned') . '" required>
                              <option value=""></option>';
			foreach ($cd as $s) {
				$attr = '';
				if ($s['id'] == $l['candidate']) {
					$attr = 'selected';
				}
				$html .= '<option value="' . $s['id'] . '" ' . $attr . ' >' . $s['candidate_code'] . ' ' . $s['candidate_name'] . '</option>';
			}
			$html .= '</select>
                        </div>
                        <div class="col-md-4">
                          <input type="text" disabled="true" name="email[' . $count . ']" id="email[' . $count . ']" value="' . $l['email'] . '" class="form-control" />
                        </div>
                        <div class="col-md-3">
                          <input type="text" disabled="true" name="phonenumber[' . $count . ']" id="phonenumber[' . $count . ']" value="' . $l['phonenumber'] . '" class="form-control" />
                        </div>
                        <div class="col-md-1 lightheight-34-nowrap">
                              <span class="input-group-btn pull-bot">
                                  <button name="add" class="btn ' . $class_btn . ' btn-' . $class . ' border-radius-4" data-ticket="true" type="button"><i class="fa fa-' . $i . '"></i></button>
                              </span>
                        </div>
                      </div>';
			$count++;
		}
		echo json_encode([
			'html' => $html,
		]);
	}

	/**
	 * delete interview schedule
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_interview_schedule($id) {
		if (!$id) {
			redirect(admin_url('recruitment/interview_schedule'));
		}
		$response = $this->recruitment_model->delete_interview_schedule($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('interview_schedule')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('interview_schedule')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('interview_schedule')));
		}
		redirect(admin_url('recruitment/interview_schedule'));
	}

	/**
	 * get interview data ajax
	 * @param  int $id
	 * @return view
	 */
	public function get_interview_data_ajax($id) {
		$data['id'] = $id;
		$data['intv_sch'] = $this->recruitment_model->get_interview_schedule($id);
		$this->load->view('interview_schedule/intv_sch_preview', $data);
	}

	/**
	 * evaluation criteria
	 * @return redirect
	 */
	public function evaluation_criteria() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_evaluation_criteria($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('evaluation_criteria'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=evaluation_criteria'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_evaluation_criteria($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('evaluation_criteria'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=evaluation_criteria'));
			}
			die;
		}
	}

	/**
	 * delete evaluation criteria
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_evaluation_criteria($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=evaluation_criteria'));
		}
		$response = $this->recruitment_model->delete_evaluation_criteria($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('evaluation_criteria')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('evaluation_criteria')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('evaluation_criteria')));
		}
		redirect(admin_url('recruitment/setting?group=evaluation_criteria'));
	}

	/**
	 * get criteria by group
	 * @param  int $id
	 * @return json
	 */
	public function get_criteria_by_group($id) {
		$list = $this->recruitment_model->get_criteria_by_group($id);
		$html = '<option value=""></option>';
		foreach ($list as $li) {
			$html .= '<option value="' . $li['criteria_id'] . '">' . $li['criteria_title'] . '</option>';
		}
		echo json_encode([
			'html' => $html,
		]);
	}

	/**
	 * evaluation form
	 * @return redirect
	 */
	public function evaluation_form() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_evaluation_form($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('evaluation_form'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=evaluation_form'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_evaluation_form($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('evaluation_form'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=evaluation_form'));
			}
			die;
		}
	}

	/**
	 * delete evaluation form
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_evaluation_form($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=evaluation_form'));
		}
		$response = $this->recruitment_model->delete_evaluation_form($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('evaluation_form')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('evaluation_form')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('evaluation_form')));
		}
		redirect(admin_url('recruitment/setting?group=evaluation_form'));
	}

	/**
	 * get list criteria edit
	 * @param  int $id
	 * @return json
	 */
	public function get_list_criteria_edit($id) {
		$list = $this->recruitment_model->get_list_criteria_edit($id);
		echo json_encode([
			'html' => $list,
		]);
	}

	/**
	 * change status candidate
	 * @param  int $status
	 * @param  int $id
	 * @return json
	 */
	public function change_status_candidate($status, $id) {
		$change = $this->recruitment_model->change_status_candidate($status, $id);
		if ($change == true) {

			$message = _l('change_status_campaign') . ' ' . _l('successfully');
			echo json_encode([
				'result' => $message,
			]);
		} else {
			$message = _l('change_status_campaign') . ' ' . _l('fail');
			echo json_encode([
				'result' => $message,
			]);
		}
	}

	/**
	 * change send to
	 * @param  int $type
	 * @return json
	 */
	public function change_send_to($type) {
		$this->load->model('staff_model');
		$this->load->model('departments_model');
		if ($type == 'staff') {
			$staff = $this->staff_model->get();
			echo json_encode([
				'type' => $type,
				'list' => $staff,
			]);
		} elseif ($type == 'department') {
			$dpm = $this->departments_model->get();
			echo json_encode([
				'type' => $type,
				'list' => $dpm,
			]);
		}
	}

	/**
	 * setting tranfer
	 * @return redirect
	 */
	public function setting_tranfer() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			$data['content'] = $this->input->post('content', false);
			if ($this->input->post('no_editor')) {
				$data['content'] = nl2br(clear_textarea_breaks($this->input->post('content')));
			}
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_setting_tranfer($data);
				if ($id) {
					handle_rec_set_transfer_record($id);
					$success = true;
					$message = _l('added_successfully', _l('setting_tranfer'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=tranfer_personnel'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_setting_tranfer($data, $id);
				if ($success) {
					handle_rec_set_transfer_record($id);
					$message = _l('updated_successfully', _l('setting_tranfer'));
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=tranfer_personnel'));
			}
			die;
		}
	}

	/**
	 * delete setting tranfer
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_setting_tranfer($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=tranfer_personnel'));
		}
		$response = $this->recruitment_model->delete_setting_tranfer($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('setting_tranfer')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('setting_tranfer')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('setting_tranfer')));
		}
		redirect(admin_url('recruitment/setting?group=tranfer_personnel'));
	}

	/**
	 * transfer to hr
	 * @param  int $candidate
	 * @return view
	 */
	public function transfer_to_hr($candidate) {
		$this->load->model('roles_model');
		$data['candidate'] = $this->recruitment_model->get_candidates($candidate);
		$data['title'] = _l('tranfer_personnel');
		$data['roles'] = $this->roles_model->get();
		$this->load->view('candidate_profile/transfer_to_hr', $data);
	}

	/**
	 * transfer hr
	 * @param  int $candidate
	 * @return redirect
	 */
	public function transfer_hr($candidate) {

		$this->load->model('hrm/hrm_model');

		if ($this->input->post()) {
			$data = $this->input->post();
			$id = $this->hrm_model->add_staff($data);
			if ($id) {
				$change = $this->recruitment_model->change_status_candidate(9, $candidate);
				//handle_staff_profile_image_upload($id);
				if ($change == true) {
					set_alert('success', _l('added_successfully', _l('staff_member')));
				}

				redirect(admin_url('recruitment/candidate_profile'));
			}
		}
	}

	/**
	 * action transfer hr
	 * @param  int $candidate
	 * @return json
	 */
	public function action_transfer_hr($candidate) {
		$this->load->model('departments_model');
		$this->load->model('staff_model');
		$cd = $this->recruitment_model->get_candidates($candidate);
		$step_setting = $this->recruitment_model->get_step_transfer_setting();
		$step = [];
		foreach ($step_setting as $st) {
			$step['id'] = $st['set_id'];
			$step['subject'] = $st['subject'];
			$step['content'] = $st['content'];
			if ($st['send_to'] = 'candidate') {
				$step['email'] = $cd->email;
				$action_step = $this->recruitment_model->action_transfer_hr($step);
			}

			if ($st['send_to'] = 'staff') {
				$step['email'] = $st['email_to'];
				$action_step = $this->recruitment_model->action_transfer_hr($step);
			}

			if ($st['send_to'] = 'department') {
				$dpm = [];
				if (strlen($st['email_to']) == 1) {
					$dpm[] = $st['email_to'];
				} else {
					$dpm[] = explode(',', $st['email_to']);
				}
				$list_mail = [];
				foreach ($dpm as $dp) {
					$dpment = $this->departments_model->get($dp);
					if (isset($dpment->manager_id) && $dpment->manager_id != '') {
						$mng_dpm = $this->staff_model->get($dpment->manager_id);
						if ($mng_dpm != '') {
							$list_mail[] = $mng_dpm->email;
						} else {
							$list_mail[] = '';
						}
					}

				}
				$step['email'] = implode(',', $list_mail);
				$action_step = $this->recruitment_model->action_transfer_hr($step);
			}

		}
		echo json_encode([
			'rs' => true,
		]);
	}

	/**
	 * dashboard
	 * @return view
	 */
	public function dashboard() {
		$data['title'] = _l('dashboard');

		$data['rec_campaign_chart_by_status'] = json_encode($this->recruitment_model->rec_campaign_chart_by_status());
		$data['rec_plan_chart_by_status'] = json_encode($this->recruitment_model->rec_plan_chart_by_status());
		$data['cp_count'] = $this->recruitment_model->get_rec_dashboard_count();
		$data['upcoming_interview'] = $this->recruitment_model->get_upcoming_interview();
		$this->load->view('dashboard', $data);
	}

	/**
	 * get recruitment proposal edit
	 * @param  int $id
	 * @return
	 */
	public function get_recruitment_proposal_edit($id) {
		$list = $this->recruitment_model->get_rec_proposal($id);
		if (isset($list)) {
			$description = $list->job_description;
		} else {
			$description = '';

		}
		echo json_encode([
			'description' => $description,

		]);
	}

	/**
	 * get recruitment campaign edit
	 * @param  int $id
	 * @return json
	 */
	public function get_recruitment_campaign_edit($id) {
		$list = $this->recruitment_model->get_rec_campaign($id);
		if (isset($list)) {
			$description = $list->cp_job_description;
		} else {
			$description = '';

		}
		echo json_encode([
			'description' => $description,

		]);
	}

	/**
	 * get tranfer personnel edit
	 * @param  int $id
	 * @return json
	 */
	public function get_tranfer_personnel_edit($id) {
		$list = $this->recruitment_model->get_list_set_transfer($id);
		if (isset($list)) {
			$description = $list->content;
		} else {
			$description = '';

		}
		echo json_encode([
			'description' => $description,

		]);
	}

	/**
	 * recruitment channel
	 * @param  int $id
	 * @return view
	 */
	public function recruitment_channel($id = '') {
		if (!has_permission('recruitment', '', 'view') && !is_admin()) {
			access_denied('_recruitment_channel');
		}
		$data['rec_channel_id'] = $id;
		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['title'] = _l('_recruitment_channel');
		$this->load->view('recruitment_channel/manage_recruitment_channel', $data);
	}

	/**
	 * add edit recruitment channel
	 * @param string $id [description]
	 */
	public function add_edit_recruitment_channel($id = '') {
		if ($this->input->post()) {
			$data = $this->input->post();

			if (!isset($data['recruitment_channel_id'])) {

				if (!has_permission('recruitment', '', 'create') && !is_admin()) {
					access_denied('_recruitment_channel');
				}

				$ids = $this->recruitment_model->add_recruitment_channel($data);
				
				if ($ids) {					
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/recruitment_channel'));
			} else {

				$id = $data['recruitment_channel_id'];

				if (!has_permission('recruitment', '', 'edit') && !is_admin()) {
					access_denied('_recruitment_channel');
				}

				if (isset($data['recruitment_channel_id'])) {
					unset($data['recruitment_channel_id']);
				}

				$success = $this->recruitment_model->update_recruitment_channel($data, $id);
				if ($success == true) {
					$message = _l('updated_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/recruitment_channel'));
			}
		}

		if ($id != '') {
			/*edit*/
			$data['form'] = $this->recruitment_model->get_recruitment_channel($id);
			$data['formData'] = $data['form']->form_data;
			$data['recruitment_channel_id'] = $id;

		} else {
			/*add*/
			$data['title'] = _l('new_candidate');
			$data['formData'] = '';
			$data['form'] = $this->recruitment_model->get_form([
				'id' => 1,
			]);
		}

		$custom_fields = get_custom_fields('leads', 'type != "link"');
		$cfields = format_external_form_custom_fields($custom_fields);

		$data['languages'] = $this->app->get_available_languages();
		$data['cfields'] = $cfields;

		$data['members'] = $this->staff_model->get('', [
			'active' => 1,
			'is_not_staff' => 0,
		]);

		$db_fields = [];
		$fields = [
			'candidate_name',
			'candidate_code',
			'birthday',
			'gender',
			'desired_salary',
			'birthplace',
			'home_town',
			'identification',
			'place_of_issue',
			'marital_status',
			'nation',
			'religion',
			'height',
			'weight',
			'email',
			'phonenumber',
			'company',
			'resident',
			'nationality',
			'zip',
			'introduce_yourself',
			'skype',
			'facebook',
			'current_accommodation',
			'position',
			'contact_person',
			'salary',
			'reason_quitwork',
			'job_description',
			'diploma',
			'training_places',
			'specialized',
			'training_form',
			'days_for_identity',
			'year_experience',
			'skill',
			'interests'
		];
		$className = 'form-control';

		foreach ($fields as $f) {
			$_field_object = new stdClass();
			$type = 'text';
			$subtype = '';
			$class = $className;
			if ($f == 'email') {
				$subtype = 'email';
			} elseif ($f == 'current_accommodation' || $f == 'address') {
				$type = 'textarea';
			} elseif ($f == 'nationality') {
				$type = 'select';
			} elseif ($f == 'marital_status') {
				$type = 'select';
			} elseif ($f == 'gender') {
				$type = 'select';
			} elseif ($f == 'diploma') {
				$type = 'select';
			} elseif ($f == 'days_for_identity') {
				$type = 'text';
				$class .= ' datepicker';
			}elseif ($f == 'birthday') {
				$type = 'text';
				$class .= ' datepicker';
			} elseif ($f == 'position') {
				$type = 'text';
			} elseif ($f == 'year_experience') {
				$type = 'select';
			} elseif ($f == 'skill') {
				$type = 'select';
			} elseif ($f == 'interests') {
				$type = 'textarea';
			}

			if ($f == 'candidate_name') {
				$label = _l('candidate_name');
			} elseif ($f == 'email') {
				$label = _l('lead_add_edit_email');
			} elseif ($f == 'phonenumber') {
				$label = _l('lead_add_edit_phonenumber');
			} elseif ($f == 'candidate_code') {
				$label = _l('candidate_code');
			} elseif ($f == 'birthday') {
				$label = _l('birthday');
			} elseif ($f == 'gender') {
				$label = _l('gender');
			} elseif ($f == 'desired_salary') {
				$label = _l('desired_salary');
			} elseif ($f == 'birthplace') {
				$label = _l('birthplace');
			} elseif ($f == 'home_town') {
				$label = _l('home_town');
			} elseif ($f == 'identification') {
				$label = _l('identification');
			} elseif ($f == 'place_of_issue') {
				$label = _l('place_of_issue');
			} elseif ($f == 'marital_status') {
				$label = _l('marital_status');
			} elseif ($f == 'nationality') {
				$label = _l('nationality');
			} elseif ($f == 'nation') {
				$label = _l('nation');
			} elseif ($f == 'religion') {
				$label = _l('religion');
			} elseif ($f == 'height') {
				$label = _l('height');
			} elseif ($f == 'weight') {
				$label = _l('weight');
			} elseif ($f == 'introduce_yourself') {
				$label = _l('introduce_yourself');
			} elseif ($f == 'skype') {
				$label = _l('skype');
			} elseif ($f == 'facebook') {
				$label = _l('facebook');
			} elseif ($f == 'resident') {
				$label = _l('resident');
			} elseif ($f == 'current_accommodation') {
				$label = _l('current_accommodation');
			} elseif ($f == 'position') {
				$label = _l('position_in_the_old_company');
			} elseif ($f == 'contact_person') {
				$label = _l('contact_person');
			} elseif ($f == 'reason_quitwork') {
				$label = _l('reason_quitwork');
			} elseif ($f == 'salary') {
				$label = _l('salary');
			} elseif ($f == 'job_description') {
				$label = _l('job_description');
			} elseif ($f == 'diploma') {
				$label = _l('diploma');
			} elseif ($f == 'training_places') {
				$label = _l('training_places');
			} elseif ($f == 'specialized') {
				$label = _l('specialized');
			} elseif ($f == 'training_form') {
				$label = _l('training_form');
			} elseif ($f == 'diploma') {
				$label = _l('diploma');
			} elseif ($f == 'days_for_identity') {
				$label = _l('days_for_identity');
			} elseif ($f == 'year_experience') {
				$label = _l('experience');
			} elseif($f == 'skill'){
				$label = _l('skill');
			} elseif($f == 'interests'){
				$label = _l('interests');
			} else {
				$label = _l('lead_' . $f);
			}

			$field_array = [
				'subtype' => $subtype,
				'type' => $type,
				'label' => $label,
				'className' => $class,
				'name' => $f,
			];

			if ($f == 'nationality') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];

				$countries = get_all_countries();
				foreach ($countries as $country) {
					$selected = false;
					if (get_option('customer_default_country') == $country['country_id']) {
						$selected = true;
					}

					if ((int) $country['country_id'] == '54') {
						$label = str_replace("'", "", $country['short_name']);

						array_push($field_array['values'], [
							'label' => $label,
							'value' => (int) $country['country_id'],
							'selected' => $selected,
						]);

					} else {
						array_push($field_array['values'], [
							'label' => $country['short_name'],
							'value' => (int) $country['country_id'],
							'selected' => $selected,
						]);

					}
				}
			}

			if ($f == 'skill') {
				$field_array['values'] = [];

				
				$field_array['multiple'] = true;

				$skills = $this->recruitment_model->get_skill();
				foreach ($skills as $skill) {
					$selected = false;
					
						 {
						array_push($field_array['values'], [
							'label' => $skill['skill_name'],
							'value' => (int) $skill['id'],
							'selected' => $selected,
						]);

					}
				}
			}

			if ($f == 'marital_status') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];
				array_push($field_array['values'], [
					'label' => _l('single'),
					'value' => 'single',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('married'),
					'value' => 'married',
					'selected' => false,
				]);
			}
			if ($f == 'gender') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];
				array_push($field_array['values'], [
					'label' => _l('male'),
					'value' => 'male',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('female'),
					'value' => 'female',
					'selected' => false,
				]);
			}
			if ($f == 'diploma') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];

				array_push($field_array['values'], [
					'label' => _l('primary_level'),
					'value' => 'primary_level',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('intermediate_level'),
					'value' => 'intermediate_level',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('college_level'),
					'value' => 'college_level',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('masters'),
					'value' => 'masters',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('doctor'),
					'value' => 'doctor',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('bachelor'),
					'value' => 'bachelor',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('engineer'),
					'value' => 'engineer',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('university'),
					'value' => 'university',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('intermediate_vocational'),
					'value' => 'intermediate_vocational',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('college_vocational'),
					'value' => 'college_vocational',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('in-service'),
					'value' => 'in-service',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('high_school'),
					'value' => 'high_school',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('intermediate_level_pro'),
					'value' => 'intermediate_level_pro',
					'selected' => false,
				]);
			}
			if ($f == 'year_experience') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => _l('no_experience_yet'),
					'value' => 'no_experience_yet',
					'selected' => false,
				];
				array_push($field_array['values'], [
					'label' => _l('less_than_1_year'),
					'value' => 'less_than_1_year',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('1_year'),
					'value' => '1_year',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('2_years'),
					'value' => '2_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('3_years'),
					'value' => '3_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('4_years'),
					'value' => '4_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('5_years'),
					'value' => '5_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => _l('over_5_years'),
					'value' => 'over_5_years',
					'selected' => false,
				]);
			}
			if ($f == 'name') {
				$field_array['required'] = true;
			}
			$_field_object->label = $label;
			$_field_object->name = $f;
			$_field_object->fields = [];
			$_field_object->fields[] = $field_array;
			$db_fields[] = $_field_object;
		}
		$data['bodyclass'] = 'web-to-lead-form';
		$data['db_fields'] = $db_fields;
		$data['par_id'] = $id;
		$data['list_rec_campaign'] = $this->recruitment_model->get_rec_campaign();
		$this->load->model('roles_model');
		$data['roles'] = $this->roles_model->get();
		$this->load->view('recruitment_channel/recruitment_channel_detail', $data);
	}
	/**
	 * table recruitment channel
	 * @return
	 */
	public function table_recruitment_channel() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('recruitment', 'recruitment_channel/table_recruitment_channel'));
		}
	}
	/**
	 * delete recruitment channel
	 * @param  int $id
	 * @return [type]
	 */
	public function delete_recruitment_channel($id) {
		if (!$id) {
			redirect(admin_url('recruitment/recruitment_campaign'));
		}
		if (!has_permission('recruitment', '', 'delete()') && !is_admin()) {
			access_denied('_recruitment_channel');
		}
		$response = $this->recruitment_model->delete_recruitment_channel($id);
		if ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('recruitment/recruitment_channel'));
	}
	/**
	 * get recruitment channel data ajax
	 * @param  int $id
	 * @return view
	 */
	public function get_recruitment_channel_data_ajax($id) {
		$data['id'] = $id;
		$data['total_cv_form'] = $this->recruitment_model->count_cv_from_recruitment_channel($id, 1);
		$data['recruitment_channel'] = $this->recruitment_model->get_recruitment_channel($id);
		$this->load->view('recruitment_channel/recruitment_channel_preview', $data);
	}
	/**
	 * add candidate form recruitment channel
	 * @param redirect
	 */
	public function add_candidate_form_recruitment_channel($form_key) {
		$data = $this->input->post();
		if ($data) {
			$ids = $this->recruitment_model->add_candidate_forms($data, $form_key);
			if ($ids) {
				handle_rec_candidate_file_form($ids);
				handle_rec_candidate_avar_file($ids);
				$success = true;
				$message = _l('added_successfully', _l('candidate_profile'));
				set_alert('success', $message);
				redirect(site_url('recruitment/forms/wtl/' . $form_key));
			}
		}
	}
	/**
	 * calendar interview schedule
	 * @return view 
	 */
	public function calendar_interview_schedule() {
       	$data['staffs'] = $this->staff_model->get();
		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['list_cd'] = $this->recruitment_model->get_list_cd();
		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();
		$data['title'] = _l('interview_schedule');
        $data['google_calendar_api']  = get_option('google_calendar_api_key');
        $data['title']                = _l('calendar');
        add_calendar_assets();
        $this->load->view('interview_schedule/calendar', $data);
    }
    /**
     * get calendar interview schedule data
     * @return json 
     */
    public function get_calendar_interview_schedule_data() {
        if ($this->input->is_ajax_request()) {
            $data = $this->recruitment_model->get_calendar_interview_schedule_data(
                $this->input->post('start'),
                $this->input->post('end'),
                '',
                '',
                $this->input->post()
            );
            echo json_encode($data);
            die();
        }
    }
    /**
     * switch kanban, recruitment switch kan ban
     * @param  integer $set    
     * @param  boolean $manual 
     * @return redirect         
     */
    public function switch_kanban_candidate($set = 0, $manual = false) {
        if ($set == 1) {
            $set = 'false';
        } else {
            $set = 'true';
        }
        $this->session->set_userdata([
            'candidate_profile_kanban_view' => $set,
        ]);
        if ($manual == false) {
            // clicked on VIEW KANBAN from projects area and will redirect again to the same view
            if (strpos($_SERVER['HTTP_REFERER'], 'project_id') !== false) {
                redirect(admin_url('recruitment'));
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
    /**
     * kanban
     * @return [type] 
     */
    public function kanban_candidate() {	
        echo html_entity_decode($this->load->view('candidate_profile/kan_ban_candidate', [], true));
    }
    /**
     * recruitment tasks kanban load more
     * 
     */
    public function recruitment_kanban_load_more() {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');
        $candidates = $this->recruitment_model->do_kanban_query($status, $this->input->get('search'), $page, false, []);
        foreach ($candidates as $candidate) {
            $this->load->view('candidate_profile/_kan_ban_card_candidate', [
                'candidate'   => $candidate,
                'status' => $status,
            ]);
        }
    }
    /**
     * candidate change status
     * @param  integer $status 
     * @param  integer $id     
     *          
     */
    public function candidate_change_status($status, $id) {
		$change = $this->recruitment_model->change_status_candidate($status, $id);
		if ($change == true) {
			$message = _l('change_recruitment_candidate_status') . ' ' . _l('successfully');
			echo json_encode([
				'success'=> 'true',
				'message' => $message,
			]);
		} else {
			$message = _l('change_recruitment_candidate_status') . ' ' . _l('fail');
			echo json_encode([
				'success'=>'false',
				'message' => $message,
			]);
		}
	}
	/**
	 * skill
	 * @return redirect
	 */
	public function skill() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_skill($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=skills'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_skill($data, $id);
				if ($success) {
					$message = _l('updated_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=skills'));
			}
			die;
		}
	}
	/**
	 * delete job_position
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_skill($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=skills'));
		}
		$response = $this->recruitment_model->delete_skill($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced'));
		} elseif ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('recruitment/setting?group=skills'));
	}
	 /**
     * get position fill data
     * @return html 
     */
    public function get_position_fill_data() {
        $data = $this->input->post();
        $position = $this->recruitment_model->list_position_by_campaign($data['campaign']);
        echo json_encode(['position' => $position]);
    }
     /**
     * recruitment campaign setting
     * @return  json
     */
    public function recruitment_campaign_setting() {
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->recruitment_model->recruitment_campaign_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }
	public function default_approver() {
        $data = $this->input->post();
        if($data != 'null') {
            $value = $this->recruitment_model->default_approver($data);
            if($value) {
                $success = true;
                $message = _l('updated_successfully');
            } else {
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }
    /**
     * company add edit
     * @param  string $id 
     * @return json     
     */
    public function company_add_edit($id = '') {
		$data = $this->input->post();
		if ($data) {
			if (!isset($data['id'])) {
				$ids = $this->recruitment_model->add_company($data);
				if ($ids) {
					// handle commodity list add edit file
					$success = true;
					$message = _l('added_successfully');
					set_alert('success', $message);
					/*upload multifile*/
					echo json_encode([
						'url' => admin_url('recruitment/setting?group=company_list'),
						'companyid' => $ids,
					]);
					die;

				}
				echo json_encode([
					'url' => admin_url('recruitment/commodity_list'),
				]);
				die;
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_company($data, $id);
				/*update file*/
				if ($success == true) {
					$message = _l('updated_successfully');
					set_alert('success', $message);
				}
				echo json_encode([
					'url' => admin_url('recruitment/setting?group=company_list'),
					'companyid' => $id,
				]);
				die;
			}
		}
	}
	/**
	 * add company attachment
	 * @param integer $id 
	 */
	public function add_company_attachment($id) {
		handle_company_attachments($id);
		echo json_encode([
			'url' => admin_url('recruitment/setting?group=company_list'),
		]);
	}
	/**
	 * get company file url
	 * @param  integer $company_id 
	 * @return json             
	 */
	public function get_company_file_url($company_id) {
		$arr_company_file = $this->recruitment_model->get_company_attachments($company_id);
		/*get images old*/
		$images_old_value = '';
		if (count($arr_company_file) > 0) {
			foreach ($arr_company_file as $key => $value) {
				$images_old_value .= '<div class="dz-preview dz-image-preview image_old' . $value["id"] . '">';
				$images_old_value .= '<div class="dz-image">';
				if (file_exists(RECRUITMENT_COMPANY_UPLOAD . $value["rel_id"] . '/' . $value["file_name"])) {
					$images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/recruitment/uploads/company_images/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
				} else {
					$images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/purchase/uploads/company/company_images/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
				}
				$images_old_value .= '</div>';
				$images_old_value .= '<div class="dz-error-mark">';
				$images_old_value .= '<a class="dz-remove" data-dz-remove>Remove file';
				$images_old_value .= '</a>';
				$images_old_value .= '</div>';
				$images_old_value .= '<div class="remove_file">';
				$images_old_value .= '<a href="#" class="text-danger" onclick="delete_company_attachment(this,' . $value["id"] . '); return false;"><i class="fa fa fa-times"></i></a>';
				$images_old_value .= '</div>';
				$images_old_value .= '</div>';
			}
		}
		echo json_encode(['arr_images' => $images_old_value,]);
		die();
	}
	/**
	 * delete company file
	 * @param  integer $attachment_id 
	 * @return json                
	 */
	public function delete_company_file($attachment_id) {
		if (!has_permission('recruitment', '', 'delete') && !is_admin()) {
			access_denied('recruitment');
		}
		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->recruitment_model->delete_company_file($attachment_id),
		]);
	}
	/**
	 * delete company
	 * @param  integer $id 
	 * @return redirect     
	 */
	public function delete_company($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=company_list'));
		}
		$response = $this->recruitment_model->delete_company($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('company')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('company')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('company')));
		}
		redirect(admin_url('recruitment/setting?group=company_list'));
	}
	/**
	 * industry
	 * @return redirect 
	 */
	public function industry() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$id = $this->recruitment_model->add_industry($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=industry_list'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_industry($data, $id);
				if ($success) {
					$message = _l('updated_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('recruitment/setting?group=industry_list'));
			}
			die;
		}
	}
	/**
	 * delete job_position
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_industry($id) {
		if (!$id) {
			redirect(admin_url('recruitment/setting?group=industry_list'));
		}
		$response = $this->recruitment_model->delete_industry($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced'));
		} elseif ($response == true) {
			set_alert('success', _l('deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('recruitment/setting?group=industry_list'));
	}
}