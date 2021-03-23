<?php

defined('BASEPATH') or exit('No direct script access allowed');

class New_recruitment_campaign extends App_mail_template
{
    protected $for = 'recruitment';

    protected $campaign;

    public $slug = 'new_recruitment_campaign';

    public $rel_type = 'recruitment';
    
    public function __construct($campaign)
    {
        parent::__construct();

        $this->campaign = $campaign;

        // For SMS and merge fields for email
        $this->set_merge_fields('recruitment_merge_fields', $this->campaign->id);
    }
    public function build()
    {
        
    }
}