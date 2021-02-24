<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s section-heading section-projects">
   <div class="panel-body">
      <h4 class="no-margin section-text">Project Feedbacks</h4>
   </div>
</div>
<div class="panel_s">
   <div class="panel-body">
      <div class="row mbot15">
         <div class="col-md-12">
            <h3 class="text-success projects-summary-heading no-mtop mbot15">Projects</h3>
         </div>
        
      </div>
      <hr />
         <table class="table dt-table table-projects" data-order-col="2" >
            <thead>
               <tr>
                  <th class="th-project-name"><?php echo _l('project_name'); ?></th>
                  <th class="th-project-start-date">Provide Feedback</th>
                  
               </tr>
            </thead>
            <tbody>
               <?php foreach($feedbacks as $feedback){  ?>
               <tr>
                  <td><?php echo project_name_by_id($feedback['project_id']); ?></td>
                  <td ><a href="<?php echo site_url('feedback/client/project/'.$feedback['id']); ?>">Submit</a></td>
                  
               </tr>
               <?php } ?>
            </tbody>
         </table>
   </div>
</div>
