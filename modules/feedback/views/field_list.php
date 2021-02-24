<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
      <?php echo form_open('feedback/addFieldName',array('id'=>'meeting-submit-form')); ?>
    

               <div class="col-md-6">
               <?php 
               
               echo render_input('fieldName','New Field Name','','text',array('required'=>'true')); ?>
               </div>   
       
      <div class=" col-md-6">
   
            <button type="submit" class="btn btn-info" style="margin-top: 25px;" >ADD</button>
         </div>
      <?php echo form_close(); ?>

        </div>
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
               <table id="example" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
               
                <tbody>
                    <?php
                    
                    foreach($data as $dt) {
                     if (!in_array($dt , $fieldNotShow)) {
                  ?>
                        <tr>
                        <td><?php echo $dt;?></td>
                        <td>
                            <a  href="feedback/deleteFieldName?fieldName=<?php echo $dt;?>">Delete </a>
                        </td>
                        </tr>
                    <?php 
                        }
                     }
                 ?>    
                </tbody>
               
              </table>
               </div>
            </div>
         </div>
        <?php echo form_close(); ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>

<?php init_tail(); ?>

</body>
</html>

