<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbltask_filter.id',
    'filter_name',
    'creator',
    ];
$sIndexColumn = 'id';
$sTable       = 'tbltask_filter';
$join = ['Left join tbllist_widget on tbllist_widget.rel_id = tbltask_filter.id and tbllist_widget.rel_type = "task_filter"'];
$where = ['where tbltask_filter.creator = '.get_staff_user_id()];   
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbltask_filter.id','tbllist_widget.id as id','tbllist_widget.add_from']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'filter_name') {
            $_data = '<a href="#" onclick="edit_task_filter(this,' . $aRow['tbltask_filter.id'] . '); return false" data-filter_name="' . $aRow['filter_name'] . '">' . $_data . '</a>';
        }
        elseif($aColumns[$i] == 'creator'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['creator']) . '">' . staff_profile_image($aRow['creator'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/member/' . $aRow['creator']) . '">' . get_staff_full_name($aRow['creator']) . '</a>';
        }
        $row[] = $_data;
    }
    $options = icon_btn('task_filter/view_data_filter/' . $aRow['tbltask_filter.id'], 'eye', 'btn-default', ["data-toggle"=>"tooltip", "title"=>"View data"]);
    $options .= icon_btn('task_filter/task_filters/' . $aRow['tbltask_filter.id'], 'pencil-square-o', 'btn-default', [
        'onclick' => 'edit_task_filter(this,' . $aRow['tbltask_filter.id'] . '); return false', 'data-filter_name' => $aRow['filter_name'],
        ]);
    $options .= icon_btn('task_filter/delete_task_filter/' . $aRow['tbltask_filter.id'], 'remove', 'btn-danger _delete');

    if(is_numeric($aRow['id']) && $aRow['add_from'] == get_staff_user_id()){
        $row[] = $options .= icon_btn('task_filter/remove_task_filter_widget/' . $aRow['id'], 'compress', 'btn-danger', ["data-toggle"=>"tooltip", "title"=>"Remove widget"]);
    }else{
        $row[] = $options .= icon_btn('task_filter/add_task_filter_widget/' . $aRow['tbltask_filter.id'], 'external-link', 'btn-success', ["data-toggle"=>"tooltip", "title"=>"Add to Dashboard"]);
    }
    $output['aaData'][] = $row;
}
