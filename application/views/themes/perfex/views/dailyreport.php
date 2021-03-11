<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    @media screen and (min-device-width: 1024px)  and (max-device-width: 1600px) and (-webkit-min-device-pixel-ratio: 1) { 
        .responsive iframe {
            height:1600px !important;
        }        
    }
    @media only screen and (min-device-width: 320px) and (max-device-width: 667px) { 
        .responsive iframe {
            height:500px !important;
        } 
    }
    @media only screen and (min-device-width: 411px) and (max-device-width: 665px) { 
        .responsive iframe {
            height:500px !important;
        } 
    }
    @media only screen and (min-device-width: 375px) and (max-device-width: 667px) { 
        .responsive iframe {
            height:550px !important;
        } 
    }
    @media only screen and (min-device-width: 768px) and (max-device-width: 1024px){
        .responsive iframe {
            height:1300px !important;
        }
    }
    
</style>
<div class="panel_s">
    <!-- <div class="panel-body"> -->
    <h4 class="no-margin section-text"> Daily Report</h4><br/>
    <!-- </div> -->
    <div class="responsive">
        <iframe width= "100%" src="<?php echo $client->ifame_report; ?>" frameborder="0" style="border:0" allowfullscreen> </iframe>
    </div>
</div>


