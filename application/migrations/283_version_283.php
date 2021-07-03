<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_283 extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {

        if (!$this->db->field_exists('is_show_all_topics', 'contacts')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'contacts` ADD `is_show_all_topics` INT AFTER `is_primary`;');
        }

    }
}
