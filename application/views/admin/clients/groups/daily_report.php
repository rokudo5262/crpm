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
            width:650px !important;
        }      
    }
    @media screen and (min-device-width: 1400px)  and (max-device-width: 1599px) { 
        .responsive iframe {
            width: 850px !important;
            height:1200px !important;
        }
        .footer {
            width:850px !important;
        }        
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) { 
        .responsive iframe {
            width: 100% !important;
            height:400px !important;
        }
        .footer {
            width:100% !important;
            height:25px !important;
        }
    }
    @media only screen and (min-device-width: 411px) and (max-device-width: 665px) { 
        .responsive iframe {
            width: 100% !important;
            height:800px !important;
        }
        .footer {
            height:23px !important;
            width:100% !important;
        }
    }
    @media only screen and (min-device-width: 666px) and (max-device-width: 767px) { 
        .responsive iframe {
            width: 100% !important;
            height:900px !important;
        }
        .footer {
            height:23px !important;
            width:100% !important;
        }
    }
    @media only screen and (min-device-width: 375px) and (max-device-width: 410px) { 
        .responsive iframe {
            width: 100% !important;
            height:450px !important;
        }
        .footer {
            height:25px !important;
            width:100% !important;
        }
    }
    @media only screen and (min-device-width: 768px) and (max-device-width: 1023px){
        .responsive iframe {
            width: 100% !important;
            height:1200px !important;
        }
        .footer {
            width:100% !important;
        }
    }
    @media screen and (min-width: 4001px) {
        .responsive iframe {
            width: 1700px !important; height:2400px !important;
        }
        .footer {
            width:1700px !important;
        }
    }
    @media screen and (min-width: 2501px) and (max-width: 4000px) {
        .responsive iframe {
            width: 1500px !important; height:2300px !important;
        }
        .footer {
            width:1500px !important;
        }
    }
    @media screen and (min-width: 1600px) and (max-width: 2500px) {
        .responsive iframe {
            width: 1000px !important; height:1500px !important;
        }
        .footer {
            width:1000px !important;
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
<div class="footer" style="position:absolute; bottom:4px; height:25px; width:500px; background-color:#fff; z-index:10px"> </div>
</div>
<div>
<?php } ?>
<?php
}