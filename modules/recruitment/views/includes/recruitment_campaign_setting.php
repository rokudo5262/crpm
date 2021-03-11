<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="form-group">
  <div class="checkbox checkbox-primary">
    <input onchange="recruitment_campaign_setting(this); return false" type="checkbox" id="recruitment_create_campaign_with_plan" name="purchase_setting[recruitment_create_campaign_with_plan]" <?php if(get_recruitment_option('recruitment_create_campaign_with_plan') == 1 ){ echo 'checked';} ?> value="recruitment_create_campaign_with_plan">
    <label for="recruitment_create_campaign_with_plan"><?php echo _l('create_recruitment_campaign_not_create_plan'); ?>
    <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('recruitment_campaign_setting_tooltip'); ?>"></i></a>
    </label>
  </div>
</div>
<div class="clearfix"></div>
<div class="form-group">
    <label for="cp_approver"><?php echo _l('default_approver'); ?></label>
    
    <select name="cp_approver" onchange="default_approver(this);"  id="default_approver" class="selectpicker" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('default_approver'); ?>">
    <option <?php if($default_approver == '') { echo 'selected';};?> value=""></option>
      <?php foreach ($staffs as $staff) {?>
        <option <?php if($staff['staffid'] == $default_approver) { echo 'selected';};?> value="<?php echo $staff['staffid']; ?>"><?php echo html_entity_decode($staff['firstname'] . ' ' . $staff['lastname']); ?></option>
      <?php }?>
    </select>
</div>
<div class="clearfix"></div>
