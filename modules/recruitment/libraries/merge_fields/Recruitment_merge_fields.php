<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recruitment_merge_fields extends App_merge_fields {
    public function build() {
        return [
            [
                'name'      => 'id',
                'key'       => '{cp_id}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Campaign Code',
                'key'       => '{campaign_code}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Campaign Name',
                'key'       => '{campaign_name}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Position',
                'key'       => '{position}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Department',
                'key'       => '{department}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Reason Recruitment',
                'key'       => '{reason_recruitment}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Campaign Status',
                'key'       => '{campaign_status}',
                'available' => ['recruitment'],
            ],
            [
                'name'      => 'Recruitment Campaign Link',
                'key'       => '{recruitment_campaign_link}',
                'available' => ['recruitment'],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($id) {
        $this->ci->load->model('recruitment/recruitment_model');
        $fields = [];
        $this->ci->db->where('cp_id', $id);
        $campaign = $this->ci->db->get(db_prefix() . 'rec_campaign')->row();
        if (!$campaign) {
            return $fields;
        }
        $fields['{cp_id}'] = $campaign->cp_id;
        $fields['{campaign_code}'] = $campaign->campaign_code;
        $fields['{campaign_name}'] = $campaign->campaign_name; 
        $fields['{position}'] = $campaign->cp_position;
        $fields['{department}'] = $campaign->cp_department; 
        $fields['{reason_recruitment}'] = $campaign->cp_reason_recruitment;
        $fields['{campaign_status}'] = $campaign->cp_status;
        $fields['{recruitment_campaign_link}'] = admin_url('recruitment/recruitment_campaign#'.$campaign->cp_id);
        return $fields;
    }
}
