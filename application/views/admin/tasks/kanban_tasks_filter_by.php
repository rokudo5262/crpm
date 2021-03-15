<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="_hidden_inputs _filters _tasks_filters">
    <?php
    $this->load->model('departments_model');
    $this->load->model('projects_model');
    $tasks_filter_assignees = $this->misc_model->get_tasks_distinct_assignees();
    $tasks_filter_departments = $this->departments_model->get();
    $tasks_filter_projects = $this->projects_model->get_undone_projects();
    hooks()->do_action('tasks_filters_hidden_html');
    echo form_hidden('my_tasks', (!has_permission('tasks', '', 'view') ? 'true' : ''));

    if (has_permission('tasks', '', 'view')) {
        foreach ($tasks_filter_assignees as $tf_assignee) {
            echo form_hidden('task_assigned_' . $tf_assignee['assigneeid']);
        }
        foreach ($tasks_filter_departments as $tf_department) {
            echo form_hidden('department_' . $tf_department['departmentid']);
        }
    }
    foreach ($task_statuses as $status) {
        $val = 'true';
        if ($status['filter_default'] == false) {
            $val = '';
        }
        echo form_hidden('task_status_' . $status['id'], $val);
    }

    if(!empty($this->input->get('task_statuses'))) {
      $saved_statuses_arr = explode(',', $where['task_statuses']);
      foreach($saved_statuses_arr as $status) {
        $saved_task_statuses[] = get_task_status_by_id($status);
      }
    }

    ?>
</div>

<div class="project-filter-wrapper btn-group pull-left mleft10 mbot25">
    <select id="project-filter" data-width="300px" multiple>
        <option class="none_project_related display-order-0" value="-1"><?php echo strtoupper(_l('task_none_project_related')) ?></option>
        <?php foreach($tasks_filter_projects as $index => $tf_project) {
        ?>
        <option class="display-order-<?php echo $index+1 ?>" value="<?php echo $tf_project['id'] ?>"><?php echo $tf_project['name'] ?></option>
        <?php } ?>
    </select>
</div>

<div class="btn-group pull-left mleft10 mbot25 btn-with-tooltip-group _filter_data" data-toggle="tooltip"
     data-title="<?php echo _l('filter_by'); ?>">

    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu width300">
        <li class="all_tasks">
            <a href="#" data-cview="all" onclick="kb_custom_view('','',true); return false;">All</a>
        </li>
        <div class="clearfix"></div>
        <li class="divider"></li>
        <?php foreach ($task_statuses as $status) { ?>
            <li class="clear-all-prevent task-statuses-filter task-statuses-filter-<?php echo $status['id']; ?> task_status_<?php echo $status['id']; ?> active"
                data-id="<?php echo $status['id']; ?>">
                <a href="#" data-cview="task_status_<?php echo $status['id']; ?>"
                   onclick="kb_status_visibility(<?php echo $status['id']; ?>)">
                    <?php echo $status['name']; ?>
                </a>
            </li>
        <?php } ?>
        <div class="clearfix"></div>
        <li class="divider"></li>
        <li class="<?php echo(!has_permission('tasks', '', 'view') ? ' active' : ''); ?> my_tasks" data-filter-group="assigned-following-unassigned">
            <a href="#" data-cview="my_tasks" onclick="kb_custom_view('my_tasks','my_tasks'); return false;">
                <?php echo _l('tasks_view_assigned_to_user'); ?>
            </a>
        </li>
        <li class="my_following_tasks" data-filter-group="assigned-following-unassigned">
            <a href="#" data-cview="my_following_tasks" onclick="kb_custom_view('my_following_tasks','my_following_tasks'); return false;">
                <?php echo _l('tasks_view_follower_by_user'); ?>
            </a>
        </li>
        <li class="not_assigned" data-filter-group="assigned-following-unassigned">
            <a href="#" data-cview="not_assigned" onclick="kb_custom_view('not_assigned','not_assigned'); return false;">
                <?php echo _l('task_list_not_assigned'); ?>
            </a>
        </li>
        <div class="clearfix"></div>
        <li class="divider"></li>
        <?php if (has_permission('tasks', '', 'view')) { ?>
            <?php if (count($tasks_filter_departments)) { ?>
                <li class="dropdown-submenu department-filter pull-left">
                    <a href="#" tabindex="-1"><?php echo _l('by_department'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left">
                        <?php foreach ($tasks_filter_departments as $department) { ?>
                            <li class="department_<?php echo $department['departmentid']; ?>">
                                <a href="#" data-cview="department_<?php echo $department['departmentid']; ?>"
                                   onclick="kb_custom_view(<?php echo $department['departmentid']; ?>,'department_<?php echo $department['departmentid']; ?>'); return false;"><?php echo $department['name']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } ?>
        <div class="clearfix"></div>
        <?php if (has_permission('tasks', '', 'view')) { ?>
            <?php if (count($tasks_filter_assignees)) { ?>
                <li class="dropdown-submenu assigned-filter pull-left">
                    <a href="#" tabindex="-1"><?php echo _l('filter_by_assigned'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left" id="assigned_member_list">
                        <li class="task_assigned_all">
                            <a href="#" data-cview="task_assigned_all"
                               onclick="kb_custom_view('task_assigned_all','task_assigned_all'); return false;">All</a>
                        </li>
                        <?php foreach ($tasks_filter_assignees as $as) { ?>
                            <li class="task_assigned_<?php echo $as['assigneeid']; ?>">
                                <a href="#" data-cview="task_assigned_<?php echo $as['assigneeid']; ?>"
                                   onclick="kb_custom_view(<?php echo $as['assigneeid']; ?>,'task_assigned_<?php echo $as['assigneeid']; ?>'); return false;"><?php echo $as['full_name']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

        <?php } ?>

    </ul>
</div>

<div class="pull-left mleft10 mbot25" data-toggle="tooltip" data-title="<?php echo _l('kanban_refresh'); ?>">
    <button class="btn btn-primary" onclick="tasks_kanban_advance();">
        <i class="fa fa-refresh"></i>
    </button>
</div>