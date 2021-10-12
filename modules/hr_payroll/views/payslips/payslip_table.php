<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'payslip_name',
	'payslip_template_id',
	'payslip_month',
	'staff_id_created',
	'date_created',
	'payslip_status',
	'1',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'hrp_payslips';

$where = [];
$join= [];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = $aRow['id'];

		}elseif ($aColumns[$i] == 'payslip_name') {
			$code = '<a href="' . admin_url('hr_payroll/view_payslip_detail/' . $aRow['id']) . '">' . $aRow['payslip_name'] . '</a>';
			$code .= '<div class="row-options">';

			$code .= '<a href="' . admin_url('hr_payroll/view_payslip_detail/' . $aRow['id']) . '" >' . _l('view') . '</a>';

			if (has_permission('hrp_payslip', '', 'edit') || is_admin()) {

			}
			if (has_permission('hrp_payslip', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('hr_payroll/delete_payslip/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;

		}elseif($aColumns[$i] == 'payslip_template_id'){
			$_data = get_payslip_template_name($aRow['payslip_template_id']);

		}elseif($aColumns[$i] == 'payslip_month'){
			$_data =  date('m-Y', strtotime($aRow['payslip_month']));

		} elseif ($aColumns[$i] == 'staff_id_created') {
			$_data = '<a href="' . admin_url('staff/profile/' . $aRow['staff_id_created']) . '">' . staff_profile_image($aRow['staff_id_created'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staff_id_created']) . '">' . get_staff_full_name($aRow['staff_id_created']) . '</a>';

		} elseif ($aColumns[$i] == 'date_created') {
			$_data = _dt($aRow['date_created']);
		}elseif ($aColumns[$i] == 'payslip_status') {
			if($aRow['payslip_status'] == 'payslip_closing'){
				$_data = ' <span class="label label-success "> '._l($aRow['payslip_status']).' </span>';
			}else{
				$_data = ' <span class="label label-primary"> '._l($aRow['payslip_status']).' </span>';
			}

		}elseif($aColumns[$i] == '1') {

			if((has_permission('hrp_payslip','','delete')) && $aRow['payslip_status'] == 'payslip_closing' ){

				$_data = '<a class="btn btn-primary btn-xs mleft5" id="confirmDelete" data-toggle="tooltip" title="" href="'. admin_url('hr_payroll/payslip_update_status/'.$aRow['id']).'"  data-original-title="'._l('payslip_opening').'"><i class="fa fa-check"></i></a>';
			}else{
				$_data ='';
			}

		}

		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

