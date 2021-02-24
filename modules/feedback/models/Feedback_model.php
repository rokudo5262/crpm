<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Feedback_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
		$projectid= $data['project_id'];
        $this->db->where('project_id', $projectid);
        $feedback = $this->db->get(db_prefix().'feedback')->row();
		if(!$feedback){
			$datecreated = date('Y-m-d H:i:s');
			$this->db->insert(db_prefix().'feedback', [
			   
				'customer_id'     => $data['customer_id'], 
				'project_id'     => $data['project_id'], 
				'date'     => $datecreated,
			   
			]);
			$feedbackid = $this->db->insert_id();
			$subject='Feedback';

			log_activity('New Feedback Added [ID: ' . $feedbackid . ', Subject: ' . $subject . ']');

			return $feedbackid;
		}else{
            return false;
        }			
    }
	
	public function feedback_add($data)
    {
		
		$projectid=$data['project_id'];
        $this->db->where('project_id', $projectid);
        $this->db->update(db_prefix() . 'feedback', $data);
			

		 return true;
				
    }
	
	public function get_projects($client_id){
		
		$this->db->where('customer_id', $client_id);
		$this->db->where('feedback_submitted =', NULL);
		$feedback_array = $this->db->get(db_prefix() . 'feedback')->result_array();
		return $feedback_array ;
	}

    public function get_project_info($projectid){	
	
	    $this->db->where('id', $projectid);
		$prj_array = $this->db->get(db_prefix() . 'projects')->result_array();
		return $prj_array ;
	
	}
	
	public function get_feedback(){
		$this->db->select('*');
		$this->db->from(db_prefix() . 'feedback f');
		$this->db->join(db_prefix() .'projects p', 'p.id = f.project_id', 'left');
		$this->db->where('f.feedback_submitted', 1);
		$query = $this->db->get()->result_array(); 
		return $query;
	}

	public function list_fields()
    {
       $array =  $this->db->list_fields(db_prefix() . 'feedback');
       return $array ;
	}
	
	public function addFieldName($feildName)
    {
		$checkField = $this->db->query('SHOW COLUMNS FROM '. db_prefix() . 'feedback' .' LIKE "'.$feildName.'"')->result_array();
		if( empty($checkField) && sizeof($checkField) == 0 ) {
			$this->db->query('ALTER TABLE '. db_prefix() . 'feedback' .' ADD '.$feildName.' TEXT  NULL');	
			return true;
		} else {
			return "Field name already exists";
		}
	}
	
	public function deleteFieldName($feildName)
    {
        // ALTER TABLE `tblfeedback` DROP `demo`
        $this->db->query('ALTER TABLE '. db_prefix() . 'feedback' .' DROP '.$feildName);
        return true;
	}
	
	public function addFeedback($data){
        $this->db->insert(db_prefix() . 'feedback', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
	}
	
	public function get_feedback_by_id($id){
		$this->db->select('f.* , p.name as project_name, c.lastname as last_name, c.firstname as first_name');
		$this->db->from(db_prefix() . 'feedback f');
		$this->db->join(db_prefix() .'projects p', 'p.id = f.project_id', 'left');
		$this->db->join(db_prefix() .'contacts c', 'c.userid = f.customer_id', 'left');
		$this->db->where('f.id', $id);
		$query = $this->db->get()->first_row(); 
		return $query;
	}

    public function feedback_update($data)
    {

        $id=$data['id'];
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'feedback', $data);
        return true;

    }
	
}
