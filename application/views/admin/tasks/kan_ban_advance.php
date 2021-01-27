<?php defined('BASEPATH') or exit('No direct script access allowed');

$where = array();
if($this->input->get('project_id')){
  $where['rel_id'] = $this->input->get('project_id');
  $where['rel_type'] = 'project';
}

if($this->input->get('is_my_task_filter')) {
  $where['is_my_task_filter'] = get_staff_user_id();
}

if($this->input->get('my_following_task_filter')) {
  $where['my_following_task_filter'] = get_staff_user_id();
}

if($this->input->get('departments')) {
  $where['departments'] = $this->input->get('departments');
}

if($this->input->get('assigned')) {
  $where['assigned'] = $this->input->get('assigned');
}

if($this->input->get('not_assigned')) {
  $where['not_assigned'] = $this->input->get('not_assigned');
}

if($this->input->get('projects')) {
  $where['projects'] = $this->input->get('projects');
}

if($this->input->get('task_statuses')) {
  $where['task_statuses'] = $this->input->get('task_statuses');
}

$this->session->set_userdata("kanban_filters", serialize($where));

if(!empty($this->input->get('task_statuses'))) {
  $save_task_statuses_ids = explode(',', $where['task_statuses']);
  foreach($task_statuses as $index => $status) {
    if(in_array($status['id'], $save_task_statuses_ids))
      $task_statuses[$index]['active'] = true;
    else
      $task_statuses[$index]['active'] = false;
  }
} else {
  foreach($task_statuses as $index => $status) {
    $task_statuses[$index]['active'] = true;
  }
}

foreach ($task_statuses as $status) {
  $total_pages = ceil($this->tasks_model->do_kanban_advance_query($status['id'],$this->input->get('search'),1,true,$where)/get_option('tasks_kanban_limit'));
  ?>
  <ul class="kan-ban-col tasks-kanban" data-col-status-id="<?php echo $status['id']; ?>" data-total-pages="<?php echo $total_pages; ?>">
    <li class="kan-ban-col-wrapper">
      <div class="border-right panel_s">
        <div class="panel-heading-bg" style="background:<?php echo $status['color']; ?>;border-color:<?php echo $status['color']; ?>;color:#fff; ?>" data-status-id="<?php echo $status['id']; ?>">
          <div class="kan-ban-step-indicator<?php if($status['id'] == Tasks_model::STATUS_COMPLETE){ echo ' kan-ban-step-indicator-full'; } ?>"></div>
          <span class="heading"><?php echo format_task_status($status['id'],false,true); ?>
          </span>
          <a href="#" onclick="return false;" class="pull-right color-white">
          </a>
        </div>
        <div class="kan-ban-content-wrapper">
          <div class="kan-ban-content">
            <ul class="status tasks-status sortable relative" data-task-status-id="<?php echo $status['id']; ?>">
              <?php
              $tasks = $this->tasks_model->do_kanban_advance_query($status['id'],$this->input->get('search'),1,false,$where);
              $total_tasks = count($tasks);
              foreach ($tasks as $task) {
                if ($task['status'] == $status['id']) {
                  $this->load->view('admin/tasks/_kan_ban_card_advance',array('task'=>$task,'status'=>$status['id']));
                } } ?>
                <?php if($total_tasks > 0 ){ ?>
                <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status['id']; ?>">
                 <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} ?>" data-page="1" onclick="kanban_load_more(<?php echo $status['id']; ?>,this,'tasks/tasks_kanban_load_more',265,360); return false;";><?php echo _l('load_more'); ?></a>
               </li>
               <?php } ?>
               <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_tasks > 0){echo ' hide';} ?>">
                <h4>
                  <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                  <?php echo _l('no_tasks_found'); ?></h4>
                </li>
              </ul>
            </div>
          </div>
        </li>
      </ul>
      <?php } ?>
