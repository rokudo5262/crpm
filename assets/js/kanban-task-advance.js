var filter_called = 0;
$( document ).ready(function() {
    if(localStorage.getItem("kanban_filter")) {
        $.each($('li.task-statuses-filter'), function() {
            $(this).removeClass('active');
        });
    }
    $(document).ajaxComplete(function () {
        $('body.kan-ban-body .dt-loader').hide();
        if(filter_called >= 2) {
            var task_statuses_li = $('li.task-statuses-filter').not('.active');
            $.each(task_statuses_li, function() {
                task_status_li = $(this).attr('data-id');
                $('ul[data-col-status-id=' + task_status_li + ']').hide();
            });
            return;
        }
        // Load saved filter after last ajax has been loaded
        load_saved_filter();
        tasks_kanban_advance();
        // Flag to stop ajax.done loop
        filter_called++;
     });

    // Initialize "Project select" filter (Bootstrap Select)
    const project_filter_select_options = {
        liveSearch: true,
        actionsBox: true,
        noneSelectedText: 'Projects Filter',
        style: '',
        styleBase: 'form-control'
    };
    const project_filter = $('#project-filter');
    project_filter.selectpicker(project_filter_select_options);

    $('.bs-select-all').on('click', function() {
        var option_el = $('#project-filter > option');
        option_el.addClass('selected');
        update_storage_filter();
    });
    
    $('.bs-deselect-all').on('click', function() {
        var option_el = $('#project-filter > option');
        option_el.removeClass('selected');
        update_storage_filter();
    });

    // Event on project select and Project filter reload after page refresh
    project_filter.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var option_el = $('#project-filter > option.display-order-' + clickedIndex);
        if(isSelected)
            option_el.addClass("selected");
        else
            option_el.removeClass("selected");
        // If changed by saved filter (clickedIndex == null) then do not update storage filter
        // If changed by click then update
        if(clickedIndex != null) {
            update_storage_filter();
        }
        tasks_kanban_advance();
    });
});

/** Load saved filter from localStorage */
function load_saved_filter(reload = false) {
    var filters = JSON.parse(localStorage.getItem("kanban_filter"));
    $.each(filters, function(index, value) {
        if(typeof(value) == 'object') {
            if(index === 'task_statuses')
                $.each(value, function() {
                    $('li.' + $(this)[0]).addClass('active');
                });
            if(index === 'departments') {
                var department_ids = [];
                $('li.department-filter').addClass('active');
                for(var i = 0; i < value.length; i++) {
                    $('li.' + value[i]).addClass('active');
                    var tmp = value[i][0];
                    var department_id = tmp.replace('department_', '');
                    department_ids.push(department_id);
                }
                if(!reload)
                    load_assignee_list_by_departments(department_ids, false);
            }
            else if(index === 'assigned') {
                $('li.assigned-filter').addClass('active');
                for(var i = 0; i < value.length; i++) {
                    $('li.' + value[i]).addClass('active');
                }
            }
            else if(index === 'my_following_tasks')
                $('li.my_following_tasks').addClass('active');
            else if(index === 'projects') {
                $('#project-filter').selectpicker('val', value);
                for(var i = 0; i < value.length; i++) {
                    var option_el = $('select#project-filter option[value=' + value[i] + ']');
                    option_el.addClass("selected");
                }
            }
        } else {
            $('li.' + value).addClass('active');
        }
    });
}

/** Save current state of filter to localStorage */
function update_storage_filter() {
    localStorage.removeItem("kanban_filter");
    var filters = {};
    // Update task statues filter
    var task_statuses = $('.task-statuses-filter.active');
    if(task_statuses.length > 0) {
        var task_status_arr = [];
        $.each(task_statuses, function() {  
            task_status_arr.push([$(this).find('a').attr('data-cview')]);
        });
        filters["task_statuses"] = task_status_arr;
    }
    
    // Update task assigned to me filter
    var my_tasks = $('.my_tasks.active').find('a').attr('data-cview');
    if(typeof (my_tasks) != 'undefined') {
        filters["my_tasks"] = my_tasks;
    }

    // Update my following task filter
    var my_following_tasks = $('.my_following_tasks.active').find('a').attr('data-cview');
    if(typeof (my_following_tasks) != 'undefined') {
        filters["my_following_tasks"] = my_following_tasks;
    }

    // Update department filter
    var departments = $('.department-filter li.active');
    if(typeof (departments) != 'undefined' && departments.length > 0) {
        departments_arr = [];
        $.each(departments, function() {
            departments_arr.push([$(this).find('a').attr('data-cview')]);
        });
        filters["departments"] = departments_arr;
    }

    // Update assigned member filter
    var assigned = $('.assigned-filter li.active');
    if(typeof (assigned) != 'undefined' && assigned.length > 0) {
        assigned_arr = [];
        $.each(assigned, function() {
            assigned_arr.push([$(this).find('a').attr('data-cview')]);
        });
        filters["assigned"] = assigned_arr;
    }

    // Update unassigned tasks filter
    var unassigned = $('.not_assigned.active').val();
    if(typeof (unassigned) != 'undefined' && assigned.length !== '') {
        filters["not_assigned"] = 'not_assigned';
    }

    // Update project filter
    var projects = $('select#project-filter option.selected');
    if(typeof (projects) != 'undefined' && projects.length > 0) {
        projects_arr = [];
        $.each(projects, function() {
            if($(this).hasClass('none_project_related'))
                projects_arr.push(-1);
            else
                projects_arr.push($(this).val());
        });
        filters["projects"] = projects_arr;
    }
    localStorage.setItem("kanban_filter", JSON.stringify(filters));
}

/** Event when clicked on Kanban Status Columns filter */
function kb_status_visibility(status_id) {
    var status_li = $('.task-statuses-filter-' + status_id);
    var status_column = $('ul[data-col-status-id=' + status_id + ']');
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

    if(custom_input_name == 'task_assigned_all') {
        $('ul#assigned_member_list li.active').not('.task_assigned_all').removeClass('active');
    } else if(custom_input_name.includes('task_assigned_')) {
        $('ul#assigned_member_list .task_assigned_all').removeClass('active');
        if($('ul#assigned_member_list li.active').length == 0) {
            $('ul#assigned_member_list .task_assigned_all a').click();
        }
    }

    // Reload assignee list according to Departments selected
    if(custom_input_name.includes('department_')) {
        var department_ids = [];
        $.each($('._filter_data .department-filter ul li.active'), function() {
            var department_li = $(this).find('a');
            var department_id = department_li.attr('data-cview');
            department_id = department_id.replace("department_", "");
            department_ids.push(department_id);
        });
        load_assignee_list_by_departments(department_ids);
    }

    // Only active one filter in "assigned-following-unassigned" (afu) filter group
    if(custom_input_name !== '') {
        var afu_filter_group_lis = $('li[data-filter-group=assigned-following-unassigned].active').not('.' + custom_input_name);
        afu_filter_group_lis.removeClass('active');
    }
    
    // Add "active" class to "All" filter if condition met
    if($('li.task-statuses-filter').not('.active').length == 0
        && $('._filter_data li.active').not('.task-statuses-filter').length == 0)
         $('li.all_tasks').addClass("active");

    // Reload Kanban
    tasks_kanban_advance();
    update_storage_filter();
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
    if (typeof (is_filter_my_following_tasks) != 'undefined' && is_filter_my_following_tasks !== '') {
        parameters['my_following_task_filter'] = true;
    }

    var not_assigned = $('li.not_assigned.active').val();
    if (typeof (not_assigned) != 'undefined' && not_assigned !== '') {
        parameters['not_assigned'] = true;
    }

    var department_ids = [];
    $.each($('._filter_data .department-filter ul li.active'), function() {
        var department_li = $(this).find('a');
        var department_id = department_li.attr('data-cview');
        department_id = department_id.replace("department_", "");
        department_ids.push(department_id);
    });
    if(department_ids.length > 0) {
        parameters['departments'] = department_ids.join();
    }

    var assigned_ids = [];
    $.each($('._filter_data .assigned-filter ul li.active'), function() {
        var assigned_li = $(this).find('a');
        var assigned_id = assigned_li.attr('data-cview');
        assigned_id = assigned_id.replace("task_assigned_", "");
        if(assigned_id == 'all')
            return;
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

function load_assignee_list_by_departments(department_ids = [], isClicked = true) {
    var url = admin_url + 'tasks/kanban_load_assigned_member/';
    if(department_ids.length > 0) {
        url += encodeURIComponent(department_ids.join());
    }

    $('#assigned_member_list').load(url, function() {
        if(isClicked) {
            $('ul#assigned_member_list .task_assigned_all a').click();
        }
        load_saved_filter(true);
    });
}