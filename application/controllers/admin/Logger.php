<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Logger extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
    }

    public function index($param = '')
    {
        $chosen_ones = [
            '41', // troy
            '43', // khane
            '47', // thomas
            '51', // wall
            '68', // leo
            '79', // pobby
            '70', // nami
            '64', // tacy
            '33', // mark
            '66', //hildemar
            '29', // vincent
            '71' // Nora

        ];
        if(!in_array(get_staff_user_id(), $chosen_ones))
            redirect(site_url('/admin'));

        close_setup_menu();

        $tasks = $this->tasks_model->get_user_tasks_assigned();
        $data['tasks'] = $tasks;
        $this->load->view('admin/logger/logger', $data);
    }

    public function isWeekend($timestamp) {
        $day = date("D", $timestamp);
        if($day == 'Sat' || $day == 'Sun'){
            return true;
        }
        return false;
    }

    public function postQuickLog()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $data["task_id"] = $this->input->post('task_id');
        $data["log_month"] = $this->input->post('log_month');
        $data["log_start"] = $this->input->post('log_start');
        $data["log_end"] = $this->input->post('log_end');
        $start_day = intval($data["log_start"]);
        $end_day = intval($data["log_end"]);
        for ($i = $start_day; $i <= $end_day; $i++) {
            $start_time_1 = $data["log_month"] . "/" . $i . "/" . date('Y') . " 08:30";
            $end_time_1 = $data["log_month"] . "/" . $i . "/" . date('Y') . " 12:00";
            $start_time_2 = $data["log_month"] . "/" . $i . "/" . date('Y') . " 13:00";
            $end_time_2 = $data["log_month"] . "/" . $i . "/" . date('Y') . " 17:30";
            if(!$this->isWeekend(strtotime($start_time_1))) {
                $this->db->insert(db_prefix() . 'taskstimers', [
                    'start_time' => strtotime($start_time_1),
                    'end_time' => strtotime($end_time_1),
                    'staff_id' => get_staff_user_id(),
                    'task_id' => $data['task_id'],
                    'hourly_rate' => 0,
                    'note' => (isset($data['note']) && $data['note'] != '' ? nl2br($data['note']) : null),
                ]);
                $this->db->insert(db_prefix() . 'taskstimers', [
                    'start_time' => strtotime($start_time_2),
                    'end_time' => strtotime($end_time_2),
                    'staff_id' => get_staff_user_id(),
                    'task_id' => $data['task_id'],
                    'hourly_rate' => 0,
                    'note' => (isset($data['note']) && $data['note'] != '' ? nl2br($data['note']) : null),
                ]);
            } else {
                continue;
            }
        }
        echo "Log successful!";
    }
}
