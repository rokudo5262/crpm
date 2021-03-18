<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    @media screen and (min-device-width: 1024px)  and (max-device-width: 1399px) { 
        .responsive iframe {
            width: 100% !important;
            height:1700px !important;
        }
        .footer_hide {
            width: 97% !important;
            height: 5% !important;
            }
    }
    @media screen and (min-device-width: 1400px)  and (max-device-width: 1599px) { 
        .responsive iframe {
            width: 99.9% !important;
            height:1600px !important;
        }
        .footer_hide {
            width: 98% !important;
            height: 5% !important;
            }
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) { 
        .responsive iframe {
            width: 98% !important;
            height:500px !important;
        }
        .footer_hide {
            width: 90% !important;
            height: 8% !important;
            }
    }
    
    @media only screen and (min-device-width: 411px) and (max-device-width: 665px) { 
        .responsive iframe {
            width: 98% !important;
            height: 800px !important;
        }
        .footer_hide {
            width: 93% !important;
            height: 5% !important;
            }
    }
    @media only screen and (min-device-width: 666px) and (max-device-width: 767px) { 
        .responsive iframe {
            width: 98% !important;
            height: 950px !important;
        }
        .footer_hide {
            width: 94% !important;
            height: 5% !important;
            }
    }
    @media only screen and (min-device-width: 375px) and (max-device-width: 410px) { 
        .responsive iframe {
            width: 98% !important;
            height: 800px !important;
        }
        .footer_hide {
            width: 93% !important;
            height: 5% !important;
            }
    }
    @media only screen and (min-device-width: 768px) and (max-device-width: 1023px){
        .responsive iframe {
            width: 100% !important;
            height:1300px !important;
        }
        .footer_hide {
            height: 5% !important;
            width: 96% !important; 
            }
    }
    @media screen and (min-width: 4001px) {
        .responsive iframe {
            width: 100% !important;
            height:1700px !important;
        }
        .footer_hide {
            height: 5% !important;
            width: 97% !important; 
            }
    }
    @media screen and (min-width: 2500px) and (max-width: 4000px) {
        .responsive iframe {
            width: 100% !important; height:1700px !important;
        }
        .footer_hide {
            height: 5% !important;
            width: 97% !important;  
            }
    }
    @media screen and (min-width: 1600px) and (max-width: 2499px) {
        .responsive iframe {
            width: 100% !important; height:1700px !important;
        }
        .footer_hide {
            height: 5% !important;
            width: 97% !important; 
            }
    }
    
</style>
<div class="panel_s">
<div class="panel_s section-heading section-estimates">
    <div class="panel-body">
        <h4 class="no-margin section-text">Daily Report</h4>
    </div>
    <div class="panel-body">
    <div class="responsive">
        <iframe width= "100%" src="<?php echo $client->daily_report_embed; ?>" frameborder="0" style="border:0" allowfullscreen> </iframe>
    </div>
    <div class="footer_hide" style="position:absolute; bottom:10px ;height:25px; width:500px; background-color:#fff; z-index:10px"> </div>
    </div>
</div>
</div>


