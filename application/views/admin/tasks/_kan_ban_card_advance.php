<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<li data-task-id="<?php echo $task['id']; ?>" class="task<?php if($task['current_user_is_assigned']){echo ' current-user-task';} if((!empty($task['duedate']) && $task['duedate'] < date('Y-m-d')) && $task['status'] != Tasks_model::STATUS_COMPLETE){ echo ' overdue-task'; } ?><?php if(!$task['current_user_is_assigned'] && $task['current_user_is_creator'] == '0' && !is_admin()){echo ' not-sortable';} ?>">
  <div class="panel-body" style="border-top: 2px solid <?php echo task_priority_color($task['priority']); ?>;">
    <div class="row">
      <div class="col-md-12 task-name">
        <a href="<?php echo admin_url('tasks/view/' . $task['id']); ?>" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
          <span class="inline-block full-width mtop10"><span style="color:#000;">#<?php echo $task['id']; ?></span> - <?php echo $task['name']; ?></span>
        </a>
        <?php
          if ($task['rel_name']) {
              $relName = task_rel_name($task['rel_name'], $task['rel_id'], $task['rel_type']);
              $relShortName = substr($relName, 0, 70) . '...';
              $link = task_rel_link($task['rel_id'], $task['rel_type']);

              echo '<a class="text-muted" data-toggle="tooltip" title="' . _l('task_related_to') . ': ' . $relName . '" href="' . $link . '">' . $relShortName . '</a>';
          }
        ?>
      </div>

      <div class="col-md-4 text-muted mtop10" style="padding-right:0; font-size:12px;">
        <span data-toggle="tooltip" data-title="Start date"><i class="fa fa-circle text-success"></i> <?php echo empty($task["startdate"]) ? "---" : $task["startdate"] ?></span>
     </div>
     <div class="col-md-4 text-muted mtop10" style="padding-right:0; padding-left:10px; font-size:12px;">
      <span data-toggle="tooltip" data-title="Due date"><i class="fa fa-flag text-warning"></i> <?php echo empty($task["duedate"]) ? "---" : $task["duedate"] ?></span>
     </div>
     <div class="col-md-4 text-right text-muted mtop10">
      <span class="align-middle" data-toggle="tooltip" data-title="Priority">
        <?php 
          switch ($task["priority"]) {
            case 1:
              echo '<i class="fa fa-arrow-up"></i> Low';
              break;
            case 2:
              echo '<font class="text-primary"><i class="fa fa-arrow-up"></i> Medium</font>';
              break;
            case 3:
              echo '<font class="text-warning"><i class="fa fa-arrow-up"></i> High</font>';
              break;
            case 4:
              echo '<font class="text-danger"><i class="fa fa-arrow-up"></i> Urgent</font>';
              break;
            default:
              break;
          }
        ?>
      </span>
     </div>
     <div style="clear: both">

      <div class="col-md-6 text-muted mtop10">
       <?php
       echo format_members_by_ids_and_names($task['assignees_ids'],$task['assignees'],false,'staff-profile-image-xs');
       ?>
     </div>
     <div class="col-md-6 text-right text-muted mtop10">
      <?php if($task['total_checklist_items'] > 0){ ?>
        <span class="mright5 inline-block text-muted" data-toggle="tooltip" data-title="<?php echo _l('task_checklist_items'); ?>">
          <i class="fa fa-check-square-o" aria-hidden="true"></i>
          <?php echo $task['total_finished_checklist_items']; ?>
          /
          <?php echo $task['total_checklist_items']; ?>
        </span>
      <?php } ?>
      <span class="mright5 inline-block text-muted" data-toggle="tooltip" data-title="<?php echo _l('task_comments'); ?>">
        <i class="fa fa-comments"></i> <?php echo $task['total_comments']; ?>
      </span>
      <span class="inline-block text-muted" data-toggle="tooltip" data-title="<?php echo _l('task_view_attachments'); ?>">
       <i class="fa fa-paperclip"></i>
       <?php echo $task['total_files']; ?>
     </span>
   </div>
   <?php $tags = get_tags_in($task['id'],'task');
   if(count($tags) > 0){ ?>
     <div class="col-md-12">
      <div class="mtop5 kanban-tags">
        <?php echo render_tags($tags); ?>
      </div>
    </div>
  <?php } ?>

</div>
</div>
</li>
