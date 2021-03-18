<?php defined('BASEPATH') or exit('No direct script access allowed');
if (isset($client) && has_permission('customers', '', 'edit')) { ?>
<style type="text/css">
    .daily-report-preview iframe {
        width: 100%;
        height: 1500px;
    }
    .daily-report-preview .footer {
        position: absolute;
        z-index: 1;
        height: 35px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #fff;
    }
</style>
<h4 class="customer-profile-group-heading"><?php echo _l('Daily Report'); ?></h4>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="ifame_report">Embed URL</label>
            <input type="text" class="form-control" name="ifame_report" id="ifame_report" value="<?php echo $client->daily_report_embed; ?>" class="form-control">
            <button class="btn btn-info label-margin" onclick="save_ifame_report(<?php echo $client->userid; ?>); return false;">
        <?php echo _l('submit'); ?></button>
        </div>
    </div>
</div>
<hr/>
<div class="row daily-report-preview">
    <div class="col-md-12">
        <iframe src="<?php echo $client->daily_report_embed; ?>" frameborder="0" allowfullscreen></iframe>
        <div class="footer"></div>
    <div>
</div>
<?php } ?>