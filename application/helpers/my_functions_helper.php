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

// Add new status for Tasks
hooks()->add_filter('before_get_task_statuses','add_custom_task_status');

function add_custom_task_status($current_statuses){
    // Push new status to the current statuses
    $current_statuses[] = array(
    'id' => 6, // new status with id 50
    'color' => '#000',
    'name' => _l('task_status_6'),
    'order' => 4,
    'filter_default' => true, // true or false

    );
    // Return the statuses
    return $current_statuses;
}

function get_user_telegram_id($staff_id) {
    return get_custom_field_value($staff_id, 'staff_telegram_user_id', 'staff');
}
function get_telegram_url(){
    $url = 'https://api.telegram.org/bot1605810631:AAEK-7MQK1VVNkJq334IeQgOCfIi-OhmKZM/sendMessage';
    return $url;
}