<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_116 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();
          if (!$CI->db->field_exists('approver', 'rec_campaign')) {
               $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_campaign` ADD COLUMN `cp_approver` text null;');            
          }
          if (!$CI->db->field_exists('created_by', 'rec_campaign')) {
               $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_campaign` ADD COLUMN `cp_created_by` text null;');            
          }
          if (recruitment_row_options_exist('"default_approver "') == 0) {
               $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("default_approver", "2", "1");');
          }                
     }
}
