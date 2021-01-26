<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('feedback_model');
		$this->load->helper('feedback_helper');
		hooks()->do_action('after_clients_area_init', $this);
    }

	public function client_feedback()
    {
         $client_id=$this->session->userdata('client_user_id');
		 $data['feedbacks'] = $this->feedback_model->get_projects($client_id);
         $data['title']='Customer Feedback';
         $this->data($data);
		 $this->view('client_feedback', $data);
		 $this->layout();
    }
	
	public function project()
    {
		$listFields=$this->feedback_model ->list_fields();
		$comentsIndex = array_search('comments', $listFields);
		if (sizeof($listFields) > 0 && !empty($comentsIndex)) {
			$listFields = $this->moveElement($listFields, $comentsIndex  ,sizeof($listFields));
		}
		$fieldNotShow = array('id', 'type','customer_id', 'date', 'project_id', 'feedback_submitted');
		$data['listFields'] = $listFields;
		$data['fieldNotShow'] = $fieldNotShow;
        $data['id']=$this->uri->segment(4);
		$data['title']='Submit Feedback';
        $this->data($data);
		$this->view('submit_feedback', $data);
		$this->layout();
    
	}
	
	function moveElement(&$array, $a, $b) {
		
		$out = array_splice($array, $a, 1);
		
		array_splice($array, $b, 0, $out);
		return $array;
		
	}
	
	public function submit_project()
    {
		if ($this->input->post()) {
			$client_id=$this->session->userdata('client_user_id');
			$data= $this->input->post();
			$data['customer_id'] = $client_id;
//			$listFields = $this->feedback_model ->addFeedback($data);
			$data['feedback_submitted']   = '1';
			
			$id=$this->feedback_model->feedback_update($data);
			
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('Feedback')));
                    redirect(site_url('feedback/client/client_feedback'));
                }
		}	
    
    }
	
}
