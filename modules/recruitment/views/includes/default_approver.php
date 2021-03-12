<div class="form-group">
    <?php $approver = explode(',', $default_approver);?>
    <label for="cp_approver"><?php echo _l('default_approver'); ?></label>
    <select name="cp_approver"  class="selectpicker" data-live-search="true" multiple="true" onchange="default_approver(this);"  id="default_approver" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('default_approver'); ?>">
    <option <?php if($approver == '') { echo 'selected';};?> value=""></option>
      <?php foreach ($staffs as $staff) {?>
        <option <?php if(in_array($staff['staffid'],$approver)) { echo 'selected';};?> value="<?php echo $staff['staffid']; ?>"><?php echo html_entity_decode($staff['firstname'] . ' ' . $staff['lastname']); ?></option>
      <?php }?>
    </select>
</div>
<div class="clearfix"></div>
