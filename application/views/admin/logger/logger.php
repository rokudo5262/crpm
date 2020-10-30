<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <h2>Công cụ log time tự động</h2>
            <p>Xin chào, đây là một công cụ không được phổ biến rộng rãi.</p>
            <div class="row">
                <div class="col-md-5">
                    <form method="POST">
                        <h3>Log nhanh</h3>
                        <p>Dành cho người lười hoặc tháng vừa rồi chỉ làm một task.</p>
                        <p>Chọn một task và chọn số ngày để log (tự động log mỗi ngày 8 tiếng, bỏ T7 CN).</p>
                        <p>
                            <label for="task_list">Danh sách task:</label>
                            <select id="task_list" name="task_list" class="form-control">
                                <?php
                                foreach ($tasks as $task) {
                                    ?>
                                    <option value="<?= $task['id'] ?>"><?= $task['name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </p>
                        <div class="row">
                            <div class="col-sm-4">
                                <p>
                                    <label for="log_month">Tháng:</label>
                                    <input type="number" min="1" max="12" class="form-control" name="log_month"
                                           id="log_month" value="<?= date('m') ?>" required>
                                </p></div>
                            <div class="col-sm-4">
                                <p>
                                    <label for="log_start">Ngày bắt đầu:</label>
                                    <input type="text" class="form-control" name="log_start"
                                           id="log_start" value="1" required>
                                </p>
                            </div>
                            <div class="col-sm-4">
                                <p>
                                    <label for="log_end">Ngày kết thúc:</label>
                                    <input type="text" class="form-control" name="log_end"
                                           id="log_end" value="<?= date('t') ?>" required>
                                </p>
                            </div>
                        </div>
                        <p>
                            <button type="submit" class="btn btn-primary" name="submit_quicklog" id="submit_quicklog">
                                Start Logging!
                            </button>
                        </p>
                    </form>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5" style="border-left: 2px black dotted;">
                    <h3>Log thủ công</h3>
                    <p>Dành cho người cần cù bù siêng năng.</p>
                </div>
                <div class="col-md-1"></div>
            </div>

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    $('form').submit(function (e) {
        e.preventDefault();
        var task_id = $('#task_list').val();
        var log_month = $('#log_month').val();
        var log_start = $('#log_start').val();
        var log_end = $('#log_end').val();
        $.ajax({
            url: '/admin/logger/postQuickLog',
            type: 'POST',
            data: {
                task_id: task_id,
                log_month: log_month,
                log_start: log_start,
                log_end: log_end
            },
            error: function () {
                alert('Something is wrong');
            },
            success: function (data) {
                alert(data);
            }
        });
    });
</script>
</body>
</html>
