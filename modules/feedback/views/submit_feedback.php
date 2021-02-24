<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_open('feedback/client/submit_project',array('id'=>'project-submit-form')); ?>
<input type="hidden" value="<?php echo $id;?>" id="id" name="id">
<div class="row">
   <div class="col-md-12">

      <div class="panel_s">
         <div class="panel-heading text-uppercase open-ticket-subject">
            <?php echo 'Feedback Form'; ?>
         </div>
         <div class="panel-body">
            <div class="row">
			
               <div class="col-md-12">
			    <div class="row">
					<?php   
						foreach($listFields as $field) {	
							if (!in_array($field , $fieldNotShow)) {
								if ($field == 'comments') {
									?>
									<div class="col-md-12 "> 
								<div class="form-group open-ticket-message-group">
									<label for="">Comments:</label>
									<textarea name="<?=$field?>" id="message" class="form-control" rows="15"></textarea>
								</div>
								</div>
<?php
								} else {
					?>
						<div class="col-md-6">
							<div class="form-group open-ticket-message-group">
								<label for="" class="text-capitalize"><?=str_replace('_', ' ', $field)?>:</label>
								<input class="form-control" name="<?=$field?>" >
							</div>
						</div>
					<?php 
								}	} 
						 } 
					?>
					
                  </div>
                
               </div>
            </div>
         </div>
      </div>
   </div>
  
   <div class="col-md-12 text-center mtop20">
      <button type="submit" class="btn btn-info" data-form="#open-new-ticket-form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
   </div>
</div>
<?php echo form_close(); ?>
