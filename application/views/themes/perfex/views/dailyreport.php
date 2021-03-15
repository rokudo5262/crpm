<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    @media screen and (min-device-width: 1024px)  and (max-device-width: 1399px) { 
        .responsive iframe {
            width: 100% !important;
            height:1350px !important;
        }        
    }
    @media screen and (min-device-width: 1400px)  and (max-device-width: 1599px) { 
        .responsive iframe {
            width: 100% !important;
            height:1600px !important;
        }        
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) { 
        .responsive iframe {
            width: 100% !important;
            height:400px !important;
        } 
    }
    @media only screen and (min-device-width: 411px) and (max-device-width: 665px) { 
        .responsive iframe {
            width: 100% !important;
            height:540px !important;
        } 
    }
    @media only screen and (min-device-width: 375px) and (max-device-width: 410px) { 
        .responsive iframe {
            width: 100% !important;
            height:550px !important;
        } 
    }
    @media only screen and (min-device-width: 768px) and (max-device-width: 1023px){
        .responsive iframe {
            width: 100% !important;
            height:1050px !important;
        }
    }
    @media screen and (min-width: 4001px) {
        .responsive iframe {
            width: 100% !important;
            height:1700px !important;
        } 
    }
    @media screen and (min-width: 2501px) and (max-width: 4000px) {
        .responsive iframe {
            width: 100% !important; height:1700px !important;
        } 
    }
    @media screen and (min-width: 1600px) and (max-width: 2500px) {
        .responsive iframe {
            width: 100% !important; height:1600px !important;
        } 
    }
</style>
<div class="panel_s">
    <!-- <div class="panel-body"> -->
    <h4 class="no-margin section-text"> Daily Report</h4><br/>
    <!-- </div> -->
    <div class="responsive">
        <iframe width= "100%" src="<?php echo $client->daily_report_embed; ?>" frameborder="0" style="border:0" allowfullscreen> </iframe>
    </div>
</div>


