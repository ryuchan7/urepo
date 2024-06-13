<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Question Papers</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped table-bordered" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                    <col width="25%">
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Session</th>
                        <th>Course Code</th>
                        <th>Title</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Created by</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $qry = $conn->query("SELECT q.*, c.course_code, c.session, c.semester
                                         FROM `question_paper_list` q 
                                         INNER JOIN course_list c ON q.class_id = c.id 
                                         WHERE q.delete_flag = 0  
                                         ORDER BY q.`title` ASC ");
                    while($row = $qry->fetch_assoc()):
                        $uemail = $conn->query("SELECT email from lecturer_list where id = '{$row['user_id']}'")->fetch_array()[0];
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo $row['session'] ?></td>
                        <td><?php echo $row['course_code'] ?></td>
                        <td><p class="m-0 truncate-1"><?= $row['title'] ?></p></td>
                        <td><?php echo $row['semester'] ?></td>
                        <td class="text-center">
                            <?php if($row['status'] == 1): ?>
                                <span class="badge badge-success px-3 rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger px-3 rounded-pill">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><p class="m-0 truncate-1"><?= $uemail ?></p></td>
                        <td align="center">
                            <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                Action
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item view_data" href="./?page=question_papers/view_question_paper&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this Question Paper permanently?","delete_question_paper",[$(this).attr('data-id')])
        })
        $('#create_new').click(function(){
            uni_modal("<i class='fa fa-plus'></i> Add New Question Paper","question_papers/manage_question_paper.php")
        })
        $('.view_data').click(function(){
            uni_modal("<i class='fa fa-eye'></i> Question Paper Details","question_papers/view_question_paper.php?id="+$(this).attr('data-id'))
        })
        $('.edit_data').click(function(){
            uni_modal("<i class='fa fa-edit'></i> Update Question Paper Details","question_papers/manage_question_paper.php?id="+$(this).attr('data-id'))
        })
        $('.table').dataTable({
            columnDefs: [
                { orderable: false, targets: [5,6] }
            ],
            order:[0,'asc']
        });
        $('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
    })
    function delete_question_paper($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_question_paper",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
</script>
