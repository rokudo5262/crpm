<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="mbot15"><?php echo _l('tasks_summary'); ?></h4>
<div class="row">
  <?php foreach(tasks_summary_data((isset($rel_id) ? $rel_id : null),(isset($rel_type) ? $rel_type : null)) as $summary){ ?>
    <div class="col-md-2 col-xs-6 border-right" style="cursor: pointer">
      <a class="filter_by_each_status" data-id_status= <?php echo $summary['status_id']; ?>
      onclick="
      dt_custom_view('','.table-tasks','');
      dt_custom_view('task_status_<?php echo $summary['status_id']; ?>','.table-tasks','task_status_<?php echo $summary['status_id']; ?>'); return false;">
        <h3 class="bold no-mtop"><?php echo $summary['total_tasks']; ?></h3>
        <p style="color:<?php echo $summary['color']; ?>" class="font-medium no-mbot">
          <?php echo $summary['name']; ?>
        </p>
        <p class="font-medium-xs no-mbot text-muted">
          <?php echo _l('tasks_view_assigned_to_user'); ?>: <?php echo $summary['total_my_tasks']; ?>
        </p>
      </a>
    </div>
    <?php } ?>
  </div>
  <hr class="hr-panel-heading" />
