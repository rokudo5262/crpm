<?php

// Javascript to handle Advance Kanban Filter
hooks()->add_action('app_admin_assets', 'init_assets_kanban_task_advance_filter');

function init_assets_kanban_task_advance_filter() {
	$CI = &get_instance();

	// Javascript
	$CI->app_scripts->add('kanban-task-advance-js', 'assets/js/kanban-task-advance.js');
}

function generate_task_status_name($task_status_id) {
	switch ($task_status_id) {
        case '1':
            $task_status_name = "Not started";
            break;
        case '2':
            $task_status_name = "Awaiting Feedback";
            break;
        case '3':
        	$task_status_name = "Testing";
            break;
        case '4':
            $task_status_name = "In Progress";
            break;
        case '5':
            $task_status_name = "Complete";
            break;
        default:
            $task_status_name = "Unknown";
            break;
    }
    return $task_status_name;
}