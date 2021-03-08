function new_job_position(){
"use strict";
    $('#job_position').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');

    $('#job_position input[name="position_name"]').val('');
    $('#job_position select[name="industry_id"]').val('').change();
    $('#job_position textarea[name="position_description"]').val('').change();

    $('#additional').html('');
}
function edit_job_position(invoker,id){
    "use strict";
    $('#additional').append(hidden_input('id',id));
    $('#job_position input[name="position_name"]').val($(invoker).data('name'));

    if($(invoker).data('industry_id') != 0){
        $('#job_position select[name="industry_id"]').val($(invoker).data('industry_id')).change();

    }else{

        $('#job_position select[name="industry_id"]').val('').change();
    }


    var job_skill_str = $(invoker).data('job_skill');
    if(typeof(job_skill_str) == "string"){
        $('#job_position select[name="job_skill[]"]').val( ($(invoker).data('job_skill')).split(',')).change();
    }else{
       $('#job_position select[name="job_skill[]"]').val($(invoker).data('job_skill')).change();

    }

    $.get(admin_url + 'recruitment/get_lastest_job_position_jd_file_ajax/' + id, function(data) {
        if('' !== data) {
            data = JSON.parse(data);
            $('.job_jd_file_wrapper .lastest_jd_file_download_url').html('<p><a href="' + data.url + '" target="_blank" download>' + data.name + '</a> <a href="#" onclick="remove_jd_files(' + id + ')"><i class="text-danger fa fa-remove"></i></a></p>');
        }
    });

    $('#job_position textarea[name="position_description"]').val($(invoker).data('position_description'));
    $('#job_position').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}

function remove_jd_files(id) {
    if(!confirm("Are you sure about that?")) {
        return false;
    } else {
        $.post(admin_url + 'recruitment/remove_job_jd_files/' + id, function(data) {
            if(data == 1) {
                alert_float('success', 'Job JD removed');
                $('.job_jd_file_wrapper .lastest_jd_file_download_url').html('');
                $('#download_' + id).remove();
            } else {
                alert_float('danger', 'Can not remove JD files!');
            }
        });
    }
}