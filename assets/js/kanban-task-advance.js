let filter_called = false;

$( document ).ready(function() {
    if(localStorage.getItem("kanban_filter")) {
        $.each($('li.task-statuses-filter'), function() {
            $(this).removeClass('active');
        });
    }
    $(document).ajaxComplete(function () {
        if(filter_called) {
            let task_statuses_li = $('li.task-statuses-filter').not('.active');
            $.each(task_statuses_li, function() {
                task_status_li = $(this).attr('data-id');
                $('ul[data-col-status-id=' + task_status_li + ']').hide();
            });
            return;
        }
        load_saved_filter();
        tasks_kanban_advance();
        filter_called = true;
     });
});

function load_saved_filter() {
    let filters = JSON.parse(localStorage.getItem("kanban_filter"));
    $.each(filters, function(index, value) {
        if(typeof(value) == 'object') {
            $.each(value, function() {
                $('li.' + $(this)[0]).addClass('active');
            });
            if(index == 'departments')
                $('li.department-filter').addClass('active');
            else if(index == 'assigned')
                $('li.assigned-filter').addClass('active');
        } else {
            $('li.' + value).addClass('active');
        }
    });
    
}

function update_storage_filter() {
    localStorage.removeItem("kanban_filter");
    let filters = {};
    // Update task statues filter
    let task_statuses = $('.task-statuses-filter.active');
    if(task_statuses.length > 0) {
        let task_status_arr = [];
        $.each(task_statuses, function() {  
            task_status_arr.push([$(this).find('a').attr('data-cview')]);
        });
        filters["task_statuses"] = task_status_arr;
    }
    
    // Update task assigned to me filter
    let my_tasks = $('.my_tasks.active').find('a').attr('data-cview');
    if(typeof (my_tasks) != 'undefined') {
        filters["my_tasks"] = my_tasks;
    }

    // Update department filter
    let departments = $('.department-filter li.active');
    if(typeof (departments) != 'undefined' && departments.length > 0) {
        departments_arr = [];
        $.each(departments, function() {
            departments_arr.push([$(this).find('a').attr('data-cview')]);
        });
        filters["departments"] = departments_arr;
    }

    // Update assigned member filter
    let assigned = $('.assigned-filter li.active');
    if(typeof (assigned) != 'undefined' && assigned.length > 0) {
        assigned_arr = [];
        $.each(assigned, function() {
            assigned_arr.push([$(this).find('a').attr('data-cview')]);
        });
        filters["assigned"] = assigned_arr;
    }
    localStorage.setItem("kanban_filter", JSON.stringify(filters));
}

function kb_status_visibility(status_id) {
    let status_li = $('.task-statuses-filter-' + status_id);
    let status_column = $('ul[data-col-status-id=' + status_id + ']');
    if(status_li.hasClass('active')) {
        status_li.removeClass('active');
        status_column.hide();
    } else {

        status_li.addClass('active');
        status_column.show();
    }
    update_storage_filter();
}

function kb_custom_view(value, custom_input_name, clear_other_filters) {
	var name = typeof (custom_input_name) == 'undefined' ? 'custom_view' : custom_input_name;
    if (typeof (clear_other_filters) != 'undefined') {
        var filters = $('._filter_data li.active').not('.clear-all-prevent');
        filters.removeClass('active');
        $.each(filters, function () {
            var input_name = $(this).find('a').attr('data-cview');
            $('._filters input[name="' + input_name + '"]').val('');
        });
    }
    var _cinput = do_filter_active(name);
    if (_cinput != name) {
        value = "";
    }
    $('input[name="' + name + '"]').val(value);
    update_storage_filter();
    tasks_kanban_advance();
}

// Cloned from assets/js/main.js
// Init tasks kan ban
function tasks_kanban_advance() {
    init_kanban_advance('tasks/kanban', tasks_kanban_update, '.tasks-status', 265, 360);
}

// Cloned from assets/js/main.js
// General function to init kan ban based on settings
function init_kanban_advance(url, callbackUpdate, connect_with, column_px, container_px, callback_after_load) {

    if ($('#kan-ban').length === 0) {
        return;
    }

    var parameters = [];
    var _kanban_param_val;

    $.each($('#kanban-params input'), function () {
        if ($(this).attr('type') == 'checkbox') {
            _kanban_param_val = $(this).prop('checked') === true ? $(this).val() : '';
        } else {
            _kanban_param_val = $(this).val();
        }
        if (_kanban_param_val !== '') {
            parameters[$(this).attr('name')] = _kanban_param_val;
        }
    });

    var search = $('input[name="search"]').val();
    if (typeof (search) != 'undefined' && search !== '') {
        parameters['search'] = search;
    }

    var is_filter_my_tasks = $('li.my_tasks.active').val();
    if (typeof (is_filter_my_tasks) != 'undefined' && is_filter_my_tasks !== '') {
        parameters['is_my_task_filter'] = true;
    }

    var department_ids = [];
    $.each($('._filter_data .department-filter ul li.active'), function() {
        let department_li = $(this).find('a');
        let department_id = department_li.attr('data-cview');
        department_id = department_id.replace("department_", "");
        department_ids.push(department_id);
    });
    if(department_ids.length > 0) {
        parameters['departments'] = department_ids.join();
    }

    var assigned_ids = [];
    $.each($('._filter_data .assigned-filter ul li.active'), function() {
        let assigned_li = $(this).find('a');
        let assigned_id = assigned_li.attr('data-cview');
        assigned_id = assigned_id.replace("task_assigned_", "");
        assigned_ids.push(assigned_id);
    });
    if(assigned_ids.length > 0) {
        parameters['assigned'] = assigned_ids.join();
    }

    var sort_type = $('input[name="sort_type"]');
    var sort = $('input[name="sort"]').val();
    if (sort_type.length != 0 && sort_type.val() !== '') {
        parameters['sort_by'] = sort_type.val();
        parameters['sort'] = sort;  
    }

    parameters['kanban'] = true;
    url = admin_url + url;
    url = buildUrl(url, parameters);
    delay(function () {
        $("body").append('<div class="dt-loader"></div>');
        $('#kan-ban').load(url, function () {

            fix_kanban_height(column_px, container_px);
            var scrollingSensitivity = 20,
                scrollingSpeed = 60;

            if (typeof (callback_after_load) != 'undefined') {
                callback_after_load();
            }

            $(".status").sortable({
                connectWith: connect_with,
                helper: 'clone',
                appendTo: '#kan-ban',
                placeholder: "ui-state-highlight-card",
                revert: 'invalid',
                scrollingSensitivity: 50,
                scrollingSpeed: 70,
                sort: function (event, uiHash) {
                    var scrollContainer = uiHash.placeholder[0].parentNode;
                    // Get the scrolling parent container
                    scrollContainer = $(scrollContainer).parents('.kan-ban-content-wrapper')[0];
                    var overflowOffset = $(scrollContainer).offset();
                    if ((overflowOffset.top + scrollContainer.offsetHeight) - event.pageY < scrollingSensitivity) {
                        scrollContainer.scrollTop = scrollContainer.scrollTop + scrollingSpeed;
                    } else if (event.pageY - overflowOffset.top < scrollingSensitivity) {
                        scrollContainer.scrollTop = scrollContainer.scrollTop - scrollingSpeed;
                    }
                    if ((overflowOffset.left + scrollContainer.offsetWidth) - event.pageX < scrollingSensitivity) {
                        scrollContainer.scrollLeft = scrollContainer.scrollLeft + scrollingSpeed;
                    } else if (event.pageX - overflowOffset.left < scrollingSensitivity) {
                        scrollContainer.scrollLeft = scrollContainer.scrollLeft - scrollingSpeed;

                    }
                },
                change: function () {
                    var list = $(this).closest('ul');
                    var KanbanLoadMore = $(list).find('.kanban-load-more');
                    $(list).append($(KanbanLoadMore).detach());
                },
                start: function (event, ui) {
                    $('body').css('overflow', 'hidden');

                    $(ui.helper).addClass('tilt');
                    $(ui.helper).find('.panel-body').css('background', '#fbfbfb');
                    // Start monitoring tilt direction
                    tilt_direction($(ui.helper));
                },
                stop: function (event, ui) {
                    $('body').removeAttr('style');
                    $(ui.helper).removeClass("tilt");
                    // Unbind temporary handlers and excess data
                    $("html").off('mousemove', $(ui.helper).data("move_handler"));
                    $(ui.helper).removeData("move_handler");
                },
                update: function (event, ui) {
                    callbackUpdate(ui, this);
                }
            });

            $('.status').sortable({
                cancel: '.not-sortable'
            });

        });

    }, 200);
}