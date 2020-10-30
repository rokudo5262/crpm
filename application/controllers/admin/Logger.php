<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Logger extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }

    public function index($id = '')
    {
        echo 'asd';
    }

    public function list_expenses($id = '')
    {
        close_setup_menu();

        if (!has_permission('expenses', '', 'view') && !has_permission('expenses', '', 'view_own')) {
            access_denied('expenses');
        }

        $data['expenseid']  = $id;
        $data['categories'] = $this->expenses_model->get_category();
        $data['years']      = $this->expenses_model->get_expenses_years();
        $data['title']      = _l('expenses');

        $this->load->view('admin/expenses/manage', $data);
    }
}
