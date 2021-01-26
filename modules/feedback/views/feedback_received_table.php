<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    db_prefix() . 'feedback.comments as comments', db_prefix() . 'feedback.customer_id as customer_id',
    db_prefix() . 'feedback.project_id as project_id',db_prefix() . 'contacts.firstname as firstname',
    db_prefix() . 'contacts.lastname as lastname',  db_prefix() . 'projects.name as project_name'

];
$sIndexColumn = 'id';
$sTable       = db_prefix().'feedback';
$filter       = [];
$where        = [];
$statusIds    = [];
$join         = [
    'LEFT JOIN ' . db_prefix() . 'contacts ON ' . db_prefix() . 'contacts.userid = ' . db_prefix() . 'feedback.customer_id',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'feedback.project_id',
];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'feedback.id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row =[];
    $row[]        = $aRow['id'];
	$row[]        = $aRow['firstname'].' '.$aRow['lastname'];
	$row[]        = $aRow['project_name'];
    $row[]        = $aRow['comments'];
	$row[]        = '<a href="'.admin_url('feedback/viewDetails/'.$aRow['id']).'">View</a>';
	
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
