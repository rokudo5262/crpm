<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Interview_merge_fields extends App_merge_fields {
    public function build() {
        return [
            [
                'name'      => 'id',
                'key'       => '{id}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'Campaign',
                'key'       => '{campaign}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'Name',
                'key'       => '{is_name}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'Interview Day',
                'key'       => '{interview_day}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'From Time',
                'key'       => '{from_time}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'To Time',
                'key'       => '{to_time}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'from_hours',
                'key'       => '{from_hours}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'To Hours',
                'key'       => '{to_hours}',
                'available' => ['interview'],
            ],
            [
                'name'      => 'interviewer',
                'key'       => '{interviewer}',
                'available' => ['interview'],
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
        $this->ci->db->where('id', $id);
        $interview = $this->ci->db->get(db_prefix() . 'rec_interview')->row();
        if (!$interview) {
            return $fields;
        }
        $fields['{id}'] = $interview->id;
        $fields['{campaign}'] = $interview->campaign;
        $fields['{is_name}'] = $interview->is_name; 
        $fields['{interview_day}'] = $interview->interview_day;
        $fields['{from_time}'] = $interview->from_time; 
        $fields['{to_time}'] = $interview->to_time;
        $fields['{from_hours}'] = $interview->from_hours; 
        $fields['{to_hours}'] = $interview->to_hours;
        $fields['{interviewer}'] = $interview->interviewer;
        return $fields;
    }
}
