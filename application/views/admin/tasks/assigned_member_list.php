<?php foreach ($tasks_filter_assignees as $as) { ?>
<li class="task_assigned_<?php echo $as['assigneeid']; ?>">
    <a href="#" data-cview="task_assigned_<?php echo $as['assigneeid']; ?>"
       onclick="kb_custom_view(<?php echo $as['assigneeid']; ?>,'task_assigned_<?php echo $as['assigneeid']; ?>'); return false;"><?php echo $as['full_name']; ?></a>
</li>
<?php } ?>