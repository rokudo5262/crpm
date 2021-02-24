<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

   <div class="content">

   <div class="row">
   <div class="col-md-12">

      <div class="panel_s">
         <div class="panel-heading text-uppercase open-ticket-subject">
            <?php echo 'Feedback Form'; ?>
			<a href="<?= admin_url('feedback/feedback_received' )  ?>" class="btn btn-info pull-right" data-form="#open-new-ticket-form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo "Back List"; ?></a>
         </div>
         <div class="panel-body">
            <div class="row">
			
               <div class="col-md-12">
			    <div class="row">
					<?php   
						foreach($listFields as $key =>  $value) {	
							if (!in_array($key , $fieldNotShow)) {
								if ($key == 'comments') {
									?>
									<div class="col-md-12 "> 
								<div class="form-group open-ticket-message-group">
									<label for="">Comments:</label>
									<textarea name="<?=$key?>" id="message" class="form-control" rows="15" readonly><?=$value?> </textarea>
								</div>
								</div>
<?php
								} else {
					?>
						<div class="col-md-6">
							<div class="form-group open-ticket-message-group">
								<label for="" class="text-capitalize"><?=str_replace('_', ' ', $key)?>:</label>
								<input class="form-control" name="<?=$key?>" value="<?=$value?>" readonly >
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
  
   
</div>


<!-- content -->
</div> 

</div>

</div>



</div>

</div>

</div>

<?php init_tail(); ?>



</body>

</html>

