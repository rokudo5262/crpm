<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Task filter
Description: Module of status management, work issues
Version: 1.0.0
Requires at least: 2.3.*
*/

define('TASK_FILTER_MODULE_NAME', 'task_filter');

hooks()->add_action('admin_init', 'task_filter_permissions');
hooks()->add_action('admin_init', 'task_filter_module_init_menu_items');
hooks()->add_filter('get_dashboard_widgets', 'task_filter_add_dashboard_widget');



/**
* Register activation module hook
*/
register_activation_hook(TASK_FILTER_MODULE_NAME, 'task_filter_module_activation_hook');

function task_filter_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function task_filter_add_dashboard_widget($widgets)
{
    $widgets[] = [
            'path'      => 'task_filter/task_filter_widget',
            'container' => 'top-12',
        ];

    return $widgets;
}
/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(TASK_FILTER_MODULE_NAME, [TASK_FILTER_MODULE_NAME]);


$CI = & get_instance();
/*$CI->load->helper(TASK_FILTER_MODULE_NAME . '/task_filter');*/

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function task_filter_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('task_filter', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('task_filter', [
                'name'     =>_l('task_filter'),
                'href'     => admin_url('task_filter'),
                'icon'     => 'fa fa-fa',
                'position' => 6,
            ]);
    }
}

function task_filter_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view'),
    ];

    register_staff_capabilities('task_filter', $capabilities, _l('task_filter'));
}
/**
 * Get goal types for the goals feature
 * @return array
 */

