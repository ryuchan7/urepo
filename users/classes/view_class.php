<?php

require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT c.*,cc.name as `course` from `course_list` c inner join `programme_list` cc  on c.course_id = cc.id where c.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
	<dl>
        <dt class="text-muted">Course</dt>
        <dd class="pl-4"><?= isset($course) ? $course : "" ?></dd>
        <dt class="text-muted">Class</dt>
        <dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
        <dt class="text-muted">Session</dt>
        <dd class="pl-4"><?= isset($session) ? $session : '' ?></dd>
        <dt class="text-muted">Status</dt>
        <dd class="pl-4">
            <?php if($status == 1): ?>
                <span class="badge badge-success px-3 rounded-pill">Active</span>
            <?php else: ?>
                <span class="badge badge-danger px-3 rounded-pill">Inactive</span>
            <?php endif; ?>
        </dd>
    </dl>
    <div class="clear-fix my-3"></div>
    <div class="text-right">
        <button class="btn btn-sm btn-dark bg-gradient-dark btn-flat" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>