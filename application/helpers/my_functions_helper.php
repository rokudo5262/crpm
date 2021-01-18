<?php

// Javascript to handle Advance Kanban Filter
hooks()->add_action('app_admin_assets', 'init_assets_kanban_task_advance_filter');

function init_assets_kanban_task_advance_filter() {
	$CI = &get_instance();

	// Javascript
	$CI->app_scripts->add('kanban-task-advance-js', 'assets/js/kanban-task-advance.js');
}

function generate_task_status_name($task_status_id) {
    return _l("task_status_" . $task_status_id);
}

function generate_staff_url($user_id) {
	return site_url('admin/profile/' . $user_id);
}

function generate_task_url($task_id) {
	return site_url('admin/tasks/view/' . $task_id);
}