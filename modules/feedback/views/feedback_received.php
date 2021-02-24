<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        
        
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
			   <?php
                $table_data = [
                   'id',
				   'Customer Name',
				   'Project Name',
				   'Comments',
                   'Action',
                  ];
                  render_datatable($table_data, ('feedback_received_table')); ?>
			   
               </div>
            </div>
         </div>
        <?php echo form_close(); ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>

 <?php init_tail(); ?>
<script type="text/javascript">
  $(function(){
    initDataTable('.table-feedback_received_table', window.location.href,'undefined','undefined','');
  });     
</script>

</body>
</html>

