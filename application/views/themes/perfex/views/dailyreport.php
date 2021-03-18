<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .daily-report-preview iframe {
        width: 100%;
        height: 1700px;
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
<div class="panel_s section-heading">
    <div class="panel-body">
        <h4 class="no-margin section-text"><?php echo _l('Daily Report'); ?></h4>
    </div>
</div>
<div class="panel_s">
 <div class="panel-body">
    <div class="row daily-report-preview">
        <div class="col-md-12">
            <iframe src="<?php echo $client->daily_report_embed; ?>" frameborder="0" allowfullscreen></iframe>
            <div class="footer"></div>
        <div>
    </div>
</div>
</div>
