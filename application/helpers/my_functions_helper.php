<?php

// Javascript to handle Advance Kanban Filter
hooks()->add_action('app_admin_assets', 'init_assets_kanban_task_advance_filter');

function init_assets_kanban_task_advance_filter() {
	$CI = &get_instance();

	// Javascript
	$CI->app_scripts->add('kanban-task-advance-js', 'assets/js/kanban-task-advance.js');
}