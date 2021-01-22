let filter_called = false;
$( document ).ready(function() {
    if(localStorage.getItem("kanban_filter")) {
        $.each($('li.task-statuses-filter'), function() {
            $(this).removeClass('active');
        });
    }
    $(document).ajaxComplete(function () {
        $('body.kan-ban-body .dt-loader').hide();
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

    let project_filter_select_options = {
        liveSearch: true,
        actionsBox: true,
        noneSelectedText: 'Projects Filter',
        style: '',
        styleBase: 'form-control'
    };
    $('#project-filter').selectpicker(project_filter_select_options);
    $('.bs-select-all').on('click', function() {
        let option_el = $('#project-filter > option');
        option_el.addClass('selected');
    });
    
    $('.bs-deselect-all').on('click', function() {
        let option_el = $('#project-filter > option');
        option_el.removeClass('selected');
    });

    $('#project-filter').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let option_el = $('#project-filter > option.display-order-' + clickedIndex);
        if(isSelected)
            option_el.addClass("selected");
        else
            option_el.removeClass("selected");
        update_storage_filter();
        tasks_kanban_advance();
    });
});

function load_saved_filter() {
    let filters = JSON.parse(localStorage.getItem("kanban_filter"));
    $.each(filters, function(index, value) {
        if(typeof(value) == 'object') {
            if(index == 'task_statuses')
                $.each(value, function() {
                    $('li.' + $(this)[0]).addClass('active');
                });
            if(index == 'departments')
                $('li.department-filter').addClass('active');
            else if(index == 'assigned')
                $('li.assigned-filter').addClass('active');
            else if(index == 'my_following_tasks')
                $('li.my_following_tasks').addClass('active');
            else if(index == 'projects') {
                $('#project-filter').selectpicker('val', value);
                $.each(value, function() {
                    let option_el = $('select#project-filter option[value=' + $(this)[0] + ']');
                    option_el.addClass("selected");
                });
            }
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

    // Update my following task filter
    let my_following_tasks = $('.my_following_tasks.active').find('a').attr('data-cview');
    if(typeof (my_following_tasks) != 'undefined') {
        filters["my_following_tasks"] = my_following_tasks;
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

    // Update project filter
    let projects = $('select#project-filter option.selected');
    if(typeof (projects) != 'undefined' && projects.length > 0) {
        projects_arr = [];
        $.each(projects, function() {
            projects_arr.push($(this).val());
        });
        filters["projects"] = projects_arr;
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

    // Remove active class of "All" if one or more status is hidden
    if($('li.task-statuses-filter').not('.active').length > 0)
        $('li.all_tasks').removeClass("active");
    // Add "active" class to "All" filter if condition met
    if($('li.task-statuses-filter').not('.active').length == 0
        && $('._filter_data li.active').not('.task-statuses-filter').length == 0)
         $('li.all_tasks').addClass("active");
    update_storage_filter();
}

function kb_custom_view(value, custom_input_name, clear_other_filters) {
	var name = typeof (custom_input_name) == 'undefined' ? 'custom_view' : custom_input_name;
    if (typeof (clear_other_filters) != 'undefined') {
        var filters = $('._filter_data li.active');
        $.each(filters, function () {
            if(!$(this).hasClass('task-statuses-filter')) {
                $(this).removeClass('active');
            }
            var input_name = $(this).find('a').attr('data-cview');
            $('._filters input[name="' + input_name + '"]').val('');
        });
        // Display all "Status"
        $.each($('.task-statuses-filter'), function() {
            $(this).addClass('active');
        });
    }
    var _cinput = kanban_do_filter_active(name);
    if (_cinput != name) {
        value = "";
    }
    $('input[name="' + name + '"]').val(value);
    update_storage_filter();
    // Add "active" class to "All" filter if condition met
    if($('li.task-statuses-filter').not('.active').length == 0
        && $('._filter_data li.active').not('.task-statuses-filter').length == 0)
         $('li.all_tasks').addClass("active");
    // Reload Kanban
    tasks_kanban_advance();
}

function kanban_do_filter_active(value, parent_selector) {
    if (value !== '' && typeof (value) != 'undefined') {
        $('[data-cview="all"]').parents('li').removeClass('active');
        var selector = $('[data-cview="' + value + '"]');
        if (typeof (parent_selector) != 'undefined') {
            selector = $(parent_selector + ' [data-cview="' + value + '"]');
        }
        var parent = selector.parents('li');
        if (parent.hasClass('filter-group')) {
            var group = parent.data('filter-group');
            $('[data-filter-group="' + group + '"]').not(parent).removeClass('active');
            $.each($('[data-filter-group="' + group + '"]').not(parent), function () {
                $('input[name="' + $(this).find('a').attr('data-cview') + '"]').val('');
            });
            $('input[name="' + value + '"]').val('');
        }
        if (!parent.not('.dropdown-submenu').hasClass('active')) {
            parent.addClass('active');

        } else {
            parent.not('.dropdown-submenu').removeClass('active');
            // Remove active class from the parent dropdown if nothing selected in the child dropdown
            var parents_sub = selector.parents('li.dropdown-submenu');
            if (parents_sub.length > 0) {
                if (parents_sub.find('li.active').length === 0) {
                    parents_sub.removeClass('active');
                }
            }
            value = "";
        }
        return value;
    } else {
        $('._filters input').val('');
        $('._filter_data li.active').not('.task-statuses-filter').removeClass('active');
        $('[data-cview="all"]').parents('li').addClass('active');
        return "";
    }
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

    var is_filter_my_following_tasks = $('li.my_following_tasks.active').val();
    if (typeof (is_filter_my_following_tasks) != 'undefined' && is_filter_my_tasks !== '') {
        parameters['my_following_task_filter'] = true;
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

    var project_ids = [];
    $.each($('select#project-filter option.selected'), function() {
        project_ids.push($(this).val());
    });
    if(project_ids.length > 0) {
        parameters['projects'] = project_ids.join();
    }

    var task_status_ids = [];
    $.each($('li.task-statuses-filter.active'), function() {
        task_status_ids.push($(this).attr('data-id'));
    });
    if(task_status_ids.length > 0) {
        parameters['task_statuses'] = task_status_ids.join();
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