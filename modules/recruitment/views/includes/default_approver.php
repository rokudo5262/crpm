<div class="form-group">
    <label for="cp_approver"><?php echo _l('default_approver'); ?></label>
    <select name="cp_approver" onchange="default_approver();"  id="default_approver" multiple="true" class="selectpicker" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('default_approver'); ?>">
      <?php foreach ($staffs as $staff) {?>
        <option <?php if(in_array($staff['staffid'],explode(',',$default_approver))) { echo 'selected';};?> value="<?php echo $staff['staffid']; ?>"><?php echo html_entity_decode($staff['firstname'] . ' ' . $staff['lastname']); ?></option>
      <?php }?>
    </select>
</div>
