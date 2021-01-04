<script>    
(function($) {
    "use strict";
    rec_chart_by_status('password_by_cate',<?php echo html_entity_decode($password_by_cate); ?>, <?php echo json_encode(_l('password_by_cate')); ?>);
    rec_chart_by_status('share_by_type',<?php echo html_entity_decode($share_by_type); ?>, <?php echo json_encode(_l('share_by_type')); ?>);
    //declare function variable radius chart
    function rec_chart_by_status(id, value, title_c){
     

        Highcharts.setOptions({
          chart: {
              style: {
                  fontFamily: 'inherit !important',
                  fontWeight:'normal',
                  fill: 'black'
              }
          },
          colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
         });

        Highcharts.chart(id, {
            chart: {
                backgroundcolor: '#fcfcfc8a',
                type: 'variablepie'
            },
            accessibility: {
                description: null
            },
            title: {
                text: title_c
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">'+<?php echo json_encode(_l('invoice_table_quantity_heading')); ?>+'</span>: <b>{point.y}</b> <br/> <span>'+<?php echo json_encode(_l('ratio')); ?>+'</span>: <b>{point.percentage:.0f}%</b><br/>',
                shared: true
            },
             plotOptions: {
                variablepie: {
                    dataLabels: {
                        enabled: false,
                        },
                    showInLegend: true        
                }
            },
            series: [{
                minPointSize: 10,
                innerSize: '20%',
                zMin: 0,
                name: <?php echo json_encode(_l('invoice_table_quantity_heading')); ?>,
                data: value,
                point:{
                      events:{
                          click: function (event) {
                             if(this.statusLink !== undefined)
                             { 
                               window.location.href = this.statusLink;

                             }
                          }
                      }
                  }
            }]
        });
    }
})(jQuery);
</script>