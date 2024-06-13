<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script>alert('You must fill generate form fields first'); location.replace(document.referrer);</script>";
    exit;
}
extract($_POST);
if (isset($_POST['id']) && $_POST['id'] > 0) {
    $qry = $conn->query("SELECT q.*,CONCAT(cc.name,' - ',c.name) as `class`, c.course_code, c.session, c.semester from `question_paper_list` q inner join course_list c on q.class_id = c.id inner join programme_list cc on c.course_id = cc.id where q.id = '{$_POST['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
$question_paper_id = isset($_POST['id']) ? $_POST['id'] : ''; // Assuming question paper ID is available in your code
if (!empty($question_paper_id)) {
    $programme_query = $conn->query("SELECT cc.name AS programme_name 
                                     FROM programme_list cc 
                                     INNER JOIN course_list c ON cc.id = c.course_id 
                                     INNER JOIN question_paper_list q ON c.id = q.class_id 
                                     WHERE q.id = '$question_paper_id'");
    if ($programme_query->num_rows > 0) {
        $programme_row = $programme_query->fetch_assoc();
        $programme_name = $programme_row['programme_name'];
    } else {
        $programme_name = ''; // Default value if program name is not found
    }
} else {
    $programme_name = ''; // Default value if question paper ID is not set
}

$category = ["A.", "B.", "C."];
$current_category = 0;

// Calculate total marks for each section
$total_single_marks = 0;
$total_multiple_marks = 0;
$total_text_marks = 0;

$single_marks_query = $conn->query("SELECT SUM(mark) as total_marks FROM `question_list` WHERE question_paper_id = '{$id}' AND `type` = 1");
if ($single_marks_query->num_rows > 0) {
    $total_single_marks = $single_marks_query->fetch_assoc()['total_marks'];
}

$multiple_marks_query = $conn->query("SELECT SUM(mark) as total_marks FROM `question_list` WHERE question_paper_id = '{$id}' AND `type` = 2");
if ($multiple_marks_query->num_rows > 0) {
    $total_multiple_marks = $multiple_marks_query->fetch_assoc()['total_marks'];
}

$text_marks_query = $conn->query("SELECT SUM(mark) as total_marks FROM `question_list` WHERE question_paper_id = '{$id}' AND `type` = 3");
if ($text_marks_query->num_rows > 0) {
    $total_text_marks = $text_marks_query->fetch_assoc()['total_marks'];
}
?>
<div class="content py-3">
    <div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
            <h5 class="card-title"><b>Generated Question Paper</b></h5>
           
        </div>
        <div class="card-body">
            <div id="outprint">
                <style>
                    .first-page {
                        border: 1px solid #000;
                        margin: 10px;
                        padding: 20px;
                    }
                    .no-break {
                        page-break-inside: avoid;
                    }
                    .choice-label {
                        font-weight: bold;
                    }
                    
                    .check-choice {
                        display: inline-block;
                        height: 15px;
                        width: 15px;
                        border: 1px solid #000;
                    }
                    .text-field {
                        height: 10em;
                        width: 100%;
                    }
                    .question-options div {
                        margin-bottom: 5px;
                    }
                    .mark {
                        text-align: right;
                        font-weight: bold;
                    }
                    .question-item {
                        position: relative;
                        padding-bottom: 30px; /* Add space for the mark to appear below */
                    }
                    .mark {
                        position: absolute;
                        right: 0;
                        bottom: 0;
                        text-align: right;
                    }
                    @media print {
                        body, html {
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .page-break {
                            page-break-after: always;
                        }
                    }
                </style>
                <!-- First Page Layout -->
                <div class="first-page">
                    <div class="text-center mb-4 no-break">
                        <h6 class="text-left" style="margin-bottom: 175px;">CONFIDENTIAL</h6>
                        <img src="../uploads/uthmlogo.jpg" alt="UTHM Logo" style="width: 500px; margin-bottom: 70px;">
                        <h4 style="margin-bottom: 50px;"><b>UNIVERSITI TUN HUSSEIN ONN MALAYSIA</b></h4>
                        <h5 style="margin-bottom: 20px;"><b>FINAL EXAMINATION</b></h5>
                        <h5 style="margin-bottom: 20px;"><b>SEMESTER  <?= isset($semester) ? strtoupper($semester) : "" ?></b></h5>
                        <h5 style="margin-bottom: 20px;"><b>SESSION <?= isset($session) ? strtoupper($session) : "" ?></b></h5>
                        <div class="text-left" style="line-height: 1.6; margin-top: 100px;">
                            <h6 style="margin-bottom: 40px;"><b>COURSE NAME:</b> <?= isset($title) ? strtoupper($title) : "" ?></h6>
                            <h6 style="margin-bottom: 40px;"><b>COURSE CODE:</b> <?= isset($course_code) ? strtoupper($course_code) : "" ?></h6>
                            <h6 style="margin-bottom: 40px;"><b>PROGRAMME CODE:</b> <?= isset($programme_name) ? strtoupper($programme_name) : "" ?></h6>
                            <h6 style="margin-bottom: 40px;"><b>EXAMINATION DATE:</b> <?= isset($exam_date) ? strtoupper($exam_date) : "" ?></h6>
                            <h6 style="margin-bottom: 40px;"><b>DURATION:</b> <?= isset($duration) ? strtoupper($duration) : "" ?></h6>
                            <h6 style="margin-bottom: 20px;"><b>INSTRUCTIONS:</b></h6>
                            <ol class="no-break">
                                <li style="margin-bottom: 10px;">ANSWER ALL QUESTIONS</li>
                                <li style="margin-bottom: 10px;">THIS FINAL EXAMINATION IS CONDUCTED VIA CLOSED BOOK</li>
                                <li style="margin-bottom: 10px;">STUDENTS ARE PROHIBITED TO CONSULT THEIR OWN MATERIAL OR ANY EXTERNAL RESOURCES DURING THE EXAMINATION CONDUCTED VIA CLOSED BOOK</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- End of First Page Layout -->
                <div class="page-break">
                    <!-- Add another page break here -->
                </div>

                <?php if ($single > 0): ?>
                    <div class="page-break">
                        <hr>
                        <h5><b>SECTION <?= $category[$current_category++] . " (" . $total_single_marks . " MARKS)"; ?></b></h5>
                        <h5>Instructions: Select the correct answer.</h5>
                        <hr>
                        <?php 
                        $i = 1;
                        $questions = $conn->query("SELECT * from `question_list` where question_paper_id = '{$id}' and `type` = 1 order by RAND() limit {$single}");
                        while ($row = $questions->fetch_assoc()):
                        ?>
                        <div class="question-item mb-5">
                            <div class="d-flex align-items-start mb-1">
                                <div class="col-auto pr-1"><b><?= "Q" . $i++ ?>.</b></div>
                                <div class="col flex-grow-1"><?=$row['question'] ?></div>
                            </div>
                            <div class="mx-2 mb-3 question-options">
                                <?php 
                                $options = $conn->query("SELECT * FROM choice_list where question_id = '{$row['id']}'");
                                $option_labels = ['A.', 'B.', 'C.', 'D.'];
                                $j = 0;
                                while ($orow = $options->fetch_array()):
                                ?>
                                <div>
                                    <span class="choice-label"><?= $option_labels[$j++] ?></span> 
                                    <span class="radio-choice"></span>
                                    <span><?=$orow['choice'] ?></span>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <?php if ($multiple > 0): ?>
    <div class="page-break">
        <hr>
        <h5><b>SECTION <?= $category[$current_category++] . " (" . $total_multiple_marks . " MARKS)"; ?></b></h5>
        <h5>Instructions: Identify whether each of the following statements is TRUE or FALSE.</h5>
        <hr>
        <?php 
        $i = 1;
        $questions = $conn->query("SELECT * from `question_list` where question_paper_id = '{$id}' and `type` = 2 order by RAND() limit {$multiple}");
        while ($row = $questions->fetch_assoc()):
        ?>
        <div class="question-item mb-3">
            <div class="d-flex w-100 mb-1">
                <div class="col-auto pr-1"><b><?= "Q" . $i++ ?>.</b></div>
                <div class="col-auto flex-shrink-1 flex-grow-1"><?=$row['question'] ?></div>
            </div>
            <?php /* No choices for multiple choice questions */ ?>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

                <?php if ($text > 0): ?>
                <div class="page-break">
                    <hr>
                    <h5><b>SECTION <?= $category[$current_category++]. " (" . $total_text_marks . " MARKS)"; ?></b></h5>
                    <h5>Instructions: Answer All Questions.</h5>
                    <hr>
                    <?php 
                    $i = 1;
                    $questions = $conn->query("SELECT * from `question_list` where question_paper_id = '{$id}' and `type` = 3 order by RAND() limit {$text}");
                    while ($row = $questions->fetch_assoc()):
                    ?>
                    <div class="question-item mb-3">
                        <div class="d-flex w-100 mb-1">
                            <div class="col-auto pr-1"><b><?= "Q" . $i++ ?>.</b></div>
                            <div class="col-auto flex-shrink-1 flex-grow-1"><?=$row['question'] ?></div>
                        </div>
                        <div class="mx-2 mb-3">
                            <div class="text-field"></div>
                        </div>
                        <div class="mark">Marks: <?=$row['mark'] ?></div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#print').click(function(){
            var headClone = $('head').clone();
            var outprintClone = $('#outprint').clone();
            var printContainer = $('<div>');

            headClone.find('title').text("Generated Question Paper - Print View");
            headClone.append("<style>html,body{min-height:unset !important;}</style>");
            printContainer.append(headClone);
            printContainer.append(outprintClone);

            start_loader();
            var printWindow = window.open("", "_blank", "width=1000,height=800,left=200,top=50");
            printWindow.document.write(printContainer.html());
            printWindow.document.close();

            setTimeout(() => {
                printWindow.print();
                setTimeout(() => {
                    printWindow.close();
                    end_loader();
                }, 200);
            }, 300);
        });
    });
</script>
