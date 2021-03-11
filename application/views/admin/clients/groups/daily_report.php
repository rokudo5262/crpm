<?php defined('BASEPATH') or exit('No direct script access allowed');
if(isset($client)){ ?>
<?php if(has_permission('customers', '', 'edit')) { ?>
<style type="text/css">
    @media screen and (min-device-width: 1024px)  and (max-device-width: 1600px) and (-webkit-min-device-pixel-ratio: 1) { 
        .responsive iframe {
            width: 700px; height:950px;
        }        
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 3) { 
        .responsive iframe {
            width: 280px; height:350px;
        } 
    }
    @media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2) { 
        .responsive iframe {
            width: 280px; height:350px;
        } 
    }
    @media only screen and (min-device-width: 768px) and (max-device-width: 1024px){
        .responsive iframe {
            width: 600px; height:1200px;
        }
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2) {
        .responsive iframe {
            width: 230px; height:320px;
        }
    }
    @media only screen and (min-device-width: 411px) and (max-device-width: 665px) { 
        .responsive iframe {
            width: 100% !important;
            height:500px !important;
        } 
    }
</style>
<div class="col-md-4">
        <label for="website">Daily Report Embed</label>
        <div class="input-group">
        <input type="text" name="ifame_report" id="ifame_report" value="<?php echo $client->daily_report_embed; ?>" class="form-control">
        <button class="btn btn-info label-margin" onclick="save_ifame_report(<?php echo $client->userid; ?>); return false;">
    <?php echo _l('submit'); ?></button>
</div>
<br/>
<div class="responsive">
<iframe src="<?php echo $client->daily_report_embed; ?>" frameborder="0" style="border:0" allowfullscreen>
</iframe>
</div>
<?php } ?>
<?php
}