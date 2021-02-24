
<?php 
$this->load->model('task_filter/task_filter_model');
$filter_widget = $this->task_filter_model->get_filter_widget(get_staff_user_id(),'task_filter');
foreach ($filter_widget as $filter) { ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('task_filter_widget'); ?>">
<div class="panel_s user-data">
  <div class="panel-body">
    <div class="widget-dragger"></div>
     <?php $data['field'] = $this->task_filter_model->view_data_filter_helper($filter['rel_id']); 
           $data['id'] = $filter['rel_id'];
           $data['title'] = $this->task_filter_model->get_name_task_filter($filter['rel_id']);
     ?>
     <?php $this->load->view('view_filter', $data); ?>
     <!-- <?php include_once(APPPATH . 'views/admin/reports/view_filter.php') ?> -->
    </div>
  </div>
   
</div>
<?php } ?>
 <script>
   // initDataTable('.table-table_view_filter3', admin_url+'task_filter/view_filter_table/3');
  window.addEventListener('load',function(){
  <?php
  foreach ($filter_widget as $filter) {
   $id = $filter['rel_id'];
    ?>
      initDataTable('.table-table_view_filter<?php echo $id; ?>', admin_url+'task_filter/view_filter_table/'+<?php echo $id; ?>+'');
   <?php } ?>
});
</script> 


