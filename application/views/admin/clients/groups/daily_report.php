<?php defined('BASEPATH') or exit('No direct script access allowed');
if(isset($client)){ ?>
<?php if(has_permission('customers', '', 'edit')) { ?>
<style type="text/css">
    @media screen and (min-device-width: 1024px)  and (max-device-width: 1399px) { 
        .responsive iframe {
            width: 650px !important;
            height:900px !important;
        }
        .footer {
            width: 95% !important;
            height: 5% !important;
        }      
    }
    @media screen and (min-device-width: 1400px)  and (max-device-width: 1599px) { 
        .responsive iframe {
            width: 850px !important;
            height:1200px !important;
        }
        .footer {
            width: 95% !important;
            height: 5% !important;
        }        
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) { 
        .responsive iframe {
            width: 100% !important;
            height:450px !important;
        }
        .footer {
            width: 90% !important;
            height: 6.6% !important;
        }
    }
    @media only screen and (min-device-width: 411px) and (max-device-width: 665px) { 
        .responsive iframe {
            width: 100% !important;
            height:810px !important;
        }
        .footer {
            height: 4.3% !important;
            width: 95% !important;
        }
    }
    @media only screen and (min-device-width: 666px) and (max-device-width: 767px) { 
        .responsive iframe {
            width: 100% !important;
            height:950px !important;
        }
        .footer {
            height: 4% !important;
            width: 95% !important;
        }
    }
    @media only screen and (min-device-width: 375px) and (max-device-width: 410px) { 
        .responsive iframe {
            width: 100% !important;
            height:550px !important;
        }
        .footer {
            height:6% !important;
            width:90% !important;
        }
    }
    @media only screen and (min-device-width: 768px) and (max-device-width: 1023px){
        .responsive iframe {
            width: 100% !important;
            height:1200px !important;
        }
        .footer {
            height:6% !important;
            width:97% !important;
        }
    }
    @media screen and (min-width: 4001px) {
        .responsive iframe {
            width: 1700px !important; height:2400px !important;
        }
        .footer {
            height:6% !important;
            width:97% !important;
        }
    }
    @media screen and (min-width: 2500px) and (max-width: 4000px) {
        .responsive iframe {
            width: 1500px !important; height:2300px !important;
        }
        .footer {
            height:6% !important;
            width:97% !important;
        }
    }
    @media screen and (min-width: 1600px) and (max-width: 2499px) {
        .responsive iframe {
            width: 1000px !important; height:1500px !important;
        }
        .footer {
            height:5% !important;
            width:90% !important;
        }
    }
</style>
<h4 class="customer-profile-group-heading">Daily Report</h4>
<div class="col-md-4">
    <div class="form-group">
        <label for="website">Embed Code</label>
        <div class="input-group">
        <input type="text" name="ifame_report" id="ifame_report" value="<?php echo $client->daily_report_embed; ?>" class="form-control">
        <button class="btn btn-info label-margin" onclick="save_ifame_report(<?php echo $client->userid; ?>); return false;">
    <?php echo _l('submit'); ?></button>
        </div>
    </div>
</div>
<div class="responsive">
<iframe src="<?php echo $client->daily_report_embed; ?>" frameborder="0" style="border:0" allowfullscreen>
</iframe>
<div class="footer" style="position:absolute; bottom:4px; height:25px; width:500px; background-color:#fff; z-index:10px"> </div>
</div>
<div>
<?php } ?>
<?php
}