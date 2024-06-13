<?php
require_once('../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `course_list` WHERE id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="type-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="course_id" class="control-label">Programme</label>
            <select name="course_id" id="course_id" class="form-control form-control-sm rounded-0 select2" required>
                <option value="" disabled <?= !isset($course_id) ? 'selected' : "" ?>></option>
                <?php
                $course = $conn->query("SELECT * FROM `programme_list` WHERE delete_flag = '0' AND `status` = 1 ORDER BY `name` ASC");
                while ($row = $course->fetch_assoc()):
                ?>
                    <option value="<?= $row['id'] ?>" <?php echo isset($course_id) && $course_id == $row['id'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="name" class="control-label">Course Name</label>
            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="course_code" class="control-label">Course Code</label>
            <input type="text" name="course_code" id="course_code" class="form-control form-control-sm rounded-0" value="<?php echo isset($course_code) ? $course_code : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="session" class="control-label">Session</label>
            <input type="text" name="session" id="session" class="form-control form-control-sm rounded-0" value="<?php echo isset($session) ? $session : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="semester" class="control-label">Semester</label>
            <input type="text" name="semester" id="semester" class="form-control form-control-sm rounded-0" value="<?php echo isset($semester) ? $semester : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="form-control form-control-sm rounded-0" required>
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#uni_modal').on('shown.bs.modal', function () {
            $('.select2').select2({
                placeholder: 'Please Select Here',
                width: '100%',
                dropdownParent: $('#uni_modal')
            });
        });

        $('#type-form').submit(function (e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_class",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: function (err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function (resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>');
                        el.addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                        end_loader();
                    } else {
                        alert_toast("An error occurred", 'error');
                        end_loader();
                        console.log(resp);
                    }
                }
            });
        });
    });
</script>
