function default_approver(sel) {
    var selected_value=sel.value;
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