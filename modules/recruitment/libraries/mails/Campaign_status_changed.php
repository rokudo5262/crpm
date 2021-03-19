<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Campaign_status_changed extends App_mail_template
{
    protected $for = 'recruitment';

    protected $campaign;

    public $slug = 'campaign_status_changed';

    public function __construct($campaign)
    {
        parent::__construct();

        $this->campaign = $campaign;

        // For SMS and merge fields for email
    }
    public function build()
    {
        $this->set_merge_fields('recruitment_merge_fields', $this->campaign->id);
    }
}
