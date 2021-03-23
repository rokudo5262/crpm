<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_117 extends App_module_migration
{
     public function up()
     {
          $new_recruitment_campaign_row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "recruitment" and slug = "new_recruitment_campaign" and language = "english";')->row();
          if(!$new_recruitment_campaign_row_exists)
          {
               $new_recruitment_campaign='Hi <br/>You are added as Manager on campaign';
               create_email_template('New Recruitment Campaign', $new_recruitment_campaign, 'recruitment', 'New Recruitment Campaign', 'new_recruitment_campaign'); 
          }
          $campaign_status_changed_row_exists = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where type = "recruitment" and slug = "new_recruitment_campaign" and language = "english";')->row();
          if(!$campaign_status_changed_row_exists)
          {
               $campaign_status_changed='Hi <br/>You are added as Manager on campaign';
               create_email_template('Campaign Status Changed', $campaign_status_changed, 'recruitment', 'Campaign Status Changed', 'campaign_status_changed');
          }
     }
}
