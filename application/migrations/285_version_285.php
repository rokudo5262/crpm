<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_285 extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {

        if (!$this->db->field_exists('type', 'tasks')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `type` TEXT ;');
        }

    }
}
