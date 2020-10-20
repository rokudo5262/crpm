<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_113 extends App_module_migration
{
    public function up()
    {
    	$CI = &get_instance();
    	add_option('chat_staff_can_delete_messages', 1);
    	$CI->db->query("ALTER TABLE `".db_prefix()."chatmessages` ADD `is_deleted` BOOLEAN NOT NULL DEFAULT FALSE AFTER `viewed`;");
    }
}

