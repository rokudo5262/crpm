function default_approver() {
    var selected_value=$('#default_approver').val();
    console.log(selected_value);
    var data = {};
        data.selected_value = selected_value;
    $.post(admin_url + 'recruitment/default_approver', data).done(function(response){
        response = JSON.parse(response); 
        if (response.success == true) {
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
      });
}