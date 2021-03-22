<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recruitment_campaign_status_changed extends App_mail_template
{
    protected $for = 'recruitment';

    protected $campaign_id;
    protected $staff_email;
    public $staff_id;
    public $slug = 'campaign_status_changed';
    
    public $rel_type = 'recruitment';

    public function __construct($campaign_id,$staff_email,$staff_id)
    {
        parent::__construct();

        $this->campaign_id = $campaign_id;
        $this->staff_email = $staff_email;
        $this->staff_id = $staff_id;
        // For SMS and merge fields for email
        
    }
    public function build()
    {
        $this->to($this->staff_email)
            ->set_rel_id($this->staff_id)
            ->set_merge_fields('recruitment_merge_fields',$this->campaign_id);
    }
}
