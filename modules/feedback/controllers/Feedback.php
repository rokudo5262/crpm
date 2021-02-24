<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Feedback extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('feedback_model');
    }
   
    public function index()
    {
        $data['title']='Customer Feedback';
        $this->load->view('feedback', $data);
		 
        if ($this->input->post()) {
            
			$data= $this->input->post();
			$data['customer_id']   = html_purify($this->input->post('clientid', false));
            $data['project_id']   = html_purify($this->input->post('project_id', false));
			$id=$this->feedback_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('Feedback')));
                    redirect(admin_url('feedback'));
                }else{
					
				    set_alert('warning', _l('Feedback request already exist'));	
					redirect(admin_url('feedback'));
				}	
		}	
    }
	
	
	
    public function feedback_received()
    {
       
		if ($this->input->is_ajax_request()) {
                $this->app->get_table_data(module_views_path('feedback', 'feedback_received_table'));
            } 
		  $data['title']='Feedback Received';
        $this->load->view('feedback_received', $data);	 
    }
    
    public function field_list()
    {
        $listFields=$this->feedback_model ->list_fields();
        $fieldNotShow = array('id', 'type','customer_id', 'date', 'project_id', 'comments','feedback_submitted');
		$data['fieldNotShow'] = $fieldNotShow;
        $data['data']= $listFields;
        $data['title']= "Field List";
        $this->load->view('field_list', $data);	 
    }

    public function addFieldName()
    {
        $fieldName = $this->input->post('fieldName');
        if (isset($fieldName) && !empty($fieldName) ) {
            $listFields = $this->feedback_model ->addFieldName($fieldName);
        }
        redirect(admin_url('feedback/field_list')); 
    }

    public function deleteFieldName()
    {
        $fieldName = $this->input->get('fieldName');
        if (isset($fieldName) && !empty($fieldName) ) {
            $listFields = $this->feedback_model ->deleteFieldName($this->input->get('fieldName'));
        }
        redirect(admin_url('feedback/field_list')); 
    }

    public function viewDetails()
    {
        $id=$this->uri->segment(4);
        if (isset($id) && !empty($id) ) {
           $feedbackRow =  $this->feedback_model ->get_feedback_by_id($id);
           foreach($feedbackRow as $key => $val) {
               if($key == 'comments') {
                $item = $feedbackRow->comments;
                    unset( $feedbackRow->{$key});
                    $feedbackRow->comments = $item ;
                    break;
                }
            }
           $data['title']= "Feedback view";
           $fieldNotShow = array('id', 'type','customer_id', 'date', 'project_id');
           $data['listFields'] = $feedbackRow;
           $data['fieldNotShow'] = $fieldNotShow;
           $this->load->view('feedback_view', $data);	 
        } else {
            redirect(admin_url('feedback/field_list')); 
        }
    }
	
}
