<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_274 extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $CI = &get_instance();

        add_option('bitly_access_token', '');

        if (!$CI->db->field_exists('daily_report_embed', db_prefix() . 'clients')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `daily_report_embed` TEXt DEFAULT NULL');
        }
    }
}
