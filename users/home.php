<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>
<div class="row">
    <div class="col-12 col-sm-4 col-md-4 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Programme</span>
                <span class="info-box-number">
                    <?php 
                    // Query to get the total number of programmes
                    $course = $conn->query("SELECT * FROM programme_list WHERE delete_flag = 0")->num_rows;
                    echo format_num($course);
                    ?>
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-4 col-md-4 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Courses</span>
                <span class="info-box-number">
                    <?php 
                    // Retrieve the logged-in user's ID
                    $user_id = $_settings->userdata('id');
                    // Query to get the total number of courses for the logged-in user
                    $class = $conn->query("SELECT * FROM course_list WHERE user_id = '$user_id' AND delete_flag = 0")->num_rows;
                    echo format_num($class);
                    ?>
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-4 col-md-4 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-gradient-light elevation-1"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Question Paper</span>
                <span class="info-box-number">
                    <?php 
                    // Query to get the total number of question papers
                    $qp = $conn->query("SELECT * FROM question_paper_list WHERE delete_flag = 0")->num_rows;
                    echo format_num($qp);
                    ?>
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
