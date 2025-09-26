<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Vacancy;
use app\models\Employee;

$this->title = 'Candidate Selection List';

$connection = Yii::$app->getDb();
$empsql  = "SELECT user_level from employee WHERE id ='" . $_SESSION['user_array']['id'] . "'";
$empresult  = $connection->createCommand($empsql)->queryOne();
$uid = $empresult['user_level'];
?>
<div class="candidate-form">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a class="ajaxlink" href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=dashboard">Home</a>
            </li>
            <li>
                <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=candidate/index"
                    class="ajaxlink">Selection List</a>
            </li>
            <li class="active"><?= Html::encode($this->title) ?></li>
        </ul><!-- /.breadcrumb -->
    </div>
    <div class="page-content">
        <div class="page-header">
            <h1>
                <?= Html::encode($this->title) ?>
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                </small>
            </h1>
        </div><!-- /.page-header -->
        <div class="row">
            <?php echo $this->render('reportsearch'); ?>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <input type="hidden" id="search_nav_link"
                    value="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=candidate/sidebarinput">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'frminput', 'enctype' => 'multipart/form-data']]); ?>
                <table class="table  table-bordered table-hover" id="formattribute">
                    <thead>
                        <th>Sr</th>
                        <th>Picture</th>
                        <th>Candidate Name</th>
                        <th>CNIC</th>
                        <th>Applied For</th>
                        <th>School</th>
                        <th>Section</th>
                        <th>Subject</th>
                        <th>Applying Date</th>

                        <th>Final Interview Date</th>
                        <th>Current Date Decision</th>
                        <th>Written Test Marks</th>
                        <th>Demo Obtain Marks</th>
                        <th>Total Demo Marks</th>
                        <th>Total Recommend</th>
                        <th>Total Non Recommend</th>
                        <th>Total Recommendation Marks</th>
                        <?php if ($uid == 42 || $uid == 769) { ?>
                            <th>PEC Decision</th>
                        <?php  } ?>
                        <th>Final Remarks</th>
                        <?php if ($uid == 42) { ?>
                            <th>Reject Date</th>
                            <th>Reject Remarks</th>
                        <?php  } ?>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody class="tabledata">
                        <?php
                        $i = 1;
                        $conn = Yii::$app->getDb();
                        //AND Candidateactivity.rec_status NOT IN (2)
                        $today = date('Y-m-d');

                        // Step 1: Check if there are interviews today
                        // echo $uid;

                        if ($uid === 771) {
                            $uid = 1;
                        }

                        // echo ' - ' . $uid;

                        //771,1

                        // If session id is not 11, apply the interview check
                        if ($uid != 42 && $uid === 771) {
                            $checkQuery = Yii::$app->db->createCommand("
                                SELECT COUNT(*) 
                                FROM candidate 
                                WHERE DATE(f_interview_date) = '$today'
                            ")->queryScalar();

                            if ($checkQuery > 0) {
                                // Step 2: Show only candidates with today's interview
                                $sqlt = "
                                    SELECT  
                                        *, candidate.cand_id, candidate.profile_pic as profile_pic, 
                                        SUM(Candidateactivity.activity_marks) AS sum_remarks,
                                        Candidateactivity.written_marks as written_marks, 
                                        Candidateactivity.interview_date as interview_date, 
                                        department.name AS subject, 
                                        projects.project_name as school, 
                                        candidate.selection_status as selection_status, 
                                        sections.section_name as section, 
                                        candidate.final_remarks as final_remarks,
                                        CASE 
                                            WHEN candidate.status = '0' THEN 'Initiated'
                                            WHEN candidate.status = '1' THEN 'Shortlisted'
                                            WHEN candidate.status = '2' THEN 'Interview Conducted'
                                            WHEN candidate.status = '3' THEN 'Rejected'
                                            WHEN candidate.status = '4' THEN 'Interview Passed'
                                            WHEN candidate.status = '5' THEN 'Interview Failed'
                                            WHEN candidate.status = '6' THEN 'Hired'
                                            WHEN candidate.status = '7' THEN 'Declined by Candidate'
                                            WHEN candidate.status = '8' THEN 'Reserved'
                                            WHEN candidate.status = '9' THEN 'Absent'
                                        END AS status1,
                                        candidate.status AS status_value
                                    FROM candidate
                                    JOIN Candidateactivity ON candidate.cand_id = Candidateactivity.employee_id 
                                    JOIN vacancy ON candidate.vacancy_id = vacancy.id
                                    JOIN department ON vacancy.hiringmanager = department.id
                                    JOIN designation ON vacancy.designation_id = designation.id
                                    LEFT JOIN projects ON projects.id = candidate.school_id
                                    LEFT JOIN sections ON sections.id = candidate.section_id
                                    WHERE candidate.fowarded = 1 
                                    AND (candidate.active = 1 OR candidate.active IS NULL)
                                    AND applyingdate LIKE '2025%'
                                    AND candidate.f_interview_date IS NULL
                                    GROUP BY candidate.cand_id, department.name
                                    ORDER BY department.name ASC,
                                        CASE 
                                            WHEN designation = 'section head' THEN 1
                                            WHEN designation = 'Academic coordinator' THEN 2
                                            WHEN designation = 'CCA Coordinator' THEN 3
                                            ELSE 4
                                        END ASC
                                ";
                            } else {
                                // Step 3: Show full data (as before)
                                $sqlt = "
                                    SELECT  
                                        *, candidate.cand_id, candidate.profile_pic as profile_pic, 
                                        SUM(Candidateactivity.activity_marks) AS sum_remarks,
                                        Candidateactivity.written_marks as written_marks, 
                                        Candidateactivity.interview_date as interview_date, 
                                        department.name AS subject, 
                                        projects.project_name as school, 
                                        candidate.selection_status as selection_status, 
                                        sections.section_name as section, 
                                        candidate.final_remarks as final_remarks,
                                        CASE 
                                            WHEN candidate.status = '0' THEN 'Initiated'
                                            WHEN candidate.status = '1' THEN 'Shortlisted'
                                            WHEN candidate.status = '2' THEN 'Interview Conducted'
                                            WHEN candidate.status = '3' THEN 'Rejected'
                                            WHEN candidate.status = '4' THEN 'Interview Passed'
                                            WHEN candidate.status = '5' THEN 'Interview Failed'
                                            WHEN candidate.status = '6' THEN 'Hired'
                                            WHEN candidate.status = '7' THEN 'Declined by Candidate'
                                            WHEN candidate.status = '8' THEN 'Reserved'
                                            WHEN candidate.status = '9' THEN 'Absent'
                                        END AS status1,
                                        candidate.status AS status_value
                                    FROM candidate
                                    JOIN Candidateactivity ON candidate.cand_id = Candidateactivity.employee_id 
                                    JOIN vacancy ON candidate.vacancy_id = vacancy.id
                                    JOIN department ON vacancy.hiringmanager = department.id
                                    JOIN designation ON vacancy.designation_id = designation.id
                                    LEFT JOIN projects ON projects.id = candidate.school_id
                                    LEFT JOIN sections ON sections.id = candidate.section_id
                                    WHERE candidate.fowarded = 1 
                                    AND (candidate.active = 1 OR candidate.active IS NULL)
                                    AND applyingdate LIKE '2025%'
                                    AND candidate.f_interview_date IS NULL
                                    GROUP BY candidate.cand_id, department.name
                                    ORDER BY department.name ASC,
                                        CASE 
                                            WHEN designation = 'section head' THEN 1
                                            WHEN designation = 'Academic coordinator' THEN 2
                                            WHEN designation = 'CCA Coordinator' THEN 3
                                            ELSE 4
                                        END ASC
                                ";
                            }
                        } else {
                            // If session id is 11, skip the date condition and show full data
                            $sqlt = "
                                SELECT  
                                    *, candidate.cand_id, candidate.profile_pic as profile_pic, 
                                    SUM(Candidateactivity.activity_marks) AS sum_remarks,
                                    Candidateactivity.written_marks as written_marks, 
                                    Candidateactivity.interview_date as interview_date, 
                                    department.name AS subject, 
                                    projects.project_name as school, 
                                    candidate.selection_status as selection_status, 
                                    sections.section_name as section, 
                                    candidate.final_remarks as final_remarks,
                                    CASE 
                                        WHEN candidate.status = '0' THEN 'Initiated'
                                        WHEN candidate.status = '1' THEN 'Shortlisted'
                                        WHEN candidate.status = '2' THEN 'Interview Conducted'
                                        WHEN candidate.status = '3' THEN 'Rejected'
                                        WHEN candidate.status = '4' THEN 'Interview Passed'
                                        WHEN candidate.status = '5' THEN 'Interview Failed'
                                        WHEN candidate.status = '6' THEN 'Hired'
                                        WHEN candidate.status = '7' THEN 'Declined by Candidate'
                                        WHEN candidate.status = '8' THEN 'Reserved'
                                        WHEN candidate.status = '9' THEN 'Absent'
                                    END AS status1,
                                    candidate.status AS status_value
                                FROM candidate
                                JOIN Candidateactivity ON candidate.cand_id = Candidateactivity.employee_id 
                                JOIN vacancy ON candidate.vacancy_id = vacancy.id
                                JOIN department ON vacancy.hiringmanager = department.id
                                JOIN designation ON vacancy.designation_id = designation.id
                                LEFT JOIN projects ON projects.id = candidate.school_id
                                LEFT JOIN sections ON sections.id = candidate.section_id
                                WHERE candidate.fowarded = 1 
                                AND (candidate.active = 1 OR candidate.active IS NULL)
                                AND Candidateactivity.rec_status IS NULL
                                AND NOT (
                                    (candidate.fname LIKE 'Tehmina%' AND candidate.lname LIKE 'Mukhtiar%') OR
                                    (candidate.fname LIKE 'Muhammad%' AND candidate.lname LIKE 'Zafar%') OR
                                    (candidate.fname LIKE 'Sughra%' AND candidate.lname LIKE 'Begum%') OR
                                    (candidate.fname LIKE 'Aqsa%' AND candidate.lname LIKE 'Bibi%') OR
                                    (candidate.fname LIKE 'Naghmana%' AND candidate.lname LIKE 'Irfan%') OR
                                    (candidate.fname LIKE 'Hira bibi%' AND candidate.lname LIKE 'Bibi%') OR
                                    (candidate.fname LIKE 'Husnain%' AND candidate.lname LIKE 'Mehdi%') OR
                                    (candidate.fname LIKE 'Farzana atif%' AND candidate.lname LIKE 'Janjua%') OR
                                    (candidate.fname LIKE 'Ayesha%' AND candidate.lname LIKE 'Abid%') OR
                                    (candidate.fname LIKE 'Tehreem%' AND candidate.lname LIKE 'Nawaz%') OR
                                    (candidate.fname LIKE 'Muhammad%' AND candidate.lname LIKE 'Ehtisham%') OR
                                    (candidate.fname LIKE 'Isma%' AND candidate.lname LIKE 'Gul%') OR
                                    (candidate.fname LIKE 'MARYAM%' AND candidate.lname LIKE 'FAHEEM%') OR
                                    (candidate.fname LIKE 'Kashifa%' AND candidate.lname LIKE 'Mehreen%') OR
                                    (candidate.fname LIKE 'Uzma%' AND candidate.lname LIKE 'Latif%') OR
                                    (candidate.cnic = '1330273799706') OR
                                    (candidate.cnic = '1120133799389') OR
                                    (candidate.cnic = '5440076946447')
                                    
                                  )
                                GROUP BY candidate.cand_id, department.name
                                ORDER BY department.name ASC,
                                    CASE 
                                        WHEN designation = 'section head' THEN 1
                                        WHEN designation = 'Academic coordinator' THEN 2
                                        WHEN designation = 'CCA Coordinator' THEN 3
                                        ELSE 4
                                    END ASC
                            ";
                        }

                        $resultt = Yii::$app->db->createCommand($sqlt)->queryAll();

                        // echo count($resultt);
                        $subjectwise = '';
                        foreach ($resultt as $row) {

                            // $sqlrec = "SELECT * FROM emp_recommendation WHERE flag=2 AND emp_recommendation.emp_id = '" . $row['cand_id'] . "'   ";

                            // $rec_detail = $conn->createCommand($sqlrec)->queryAll();
                            $show = true;
                            // $logged_in_user_level = $uid;

                            // foreach ($rec_detail as $rec) {
                            //     $recommend_by_id = $rec['recommend_by'];

                            //     $sql_user = "SELECT user_level FROM employee WHERE id = '$recommend_by_id'";
                            //     $user_level = $conn->createCommand($sql_user)->queryScalar();

                            //     if ($user_level == $logged_in_user_level) {
                            //         $show = false;
                            //         break;
                            //     }
                            // }

                            if ($show) {
                                if ($row['selection_status'] != 1) {
                                    if ($i === 1) {
                                        $subjectwise = $row['subject'];
                                    }
                                    $totalr = 0;
                                    $totalnr = 0;
                                    $recremarks = '';
                                    $totalrecmarks = 0;


                                    foreach ($rec_detail as $rec_details) {
                                        $sqluser = "SELECT *,role_types.role_name
                                            FROM employee
                                            LEFT JOIN role_types ON (role_types.role_id = employee.user_level) 
                                            WHERE employee.id = '" . $rec_details['recommend_by'] . "' ";
                                        $resultuser = $conn->createCommand($sqluser)->queryOne();

                                        if ($rec_details['status'] == 1) {
                                            $recremarks = "Not Recommend";
                                            $totalrecmarks = $totalrecmarks + (int)$rec_details['remarks'] + $rec_details['remarks1'];
                                            $totalnr = $totalnr + 1;
                                        } else if ($rec_details['status'] == 2) {
                                            $recremarks = "Recommend";
                                            $totalrecmarks = $totalrecmarks + (int)$rec_details['remarks'] + $rec_details['remarks1'];
                                            $totalr = $totalr + 1;
                                        }
                                    }

                                    $sqlt11 = "SELECT sum(activity_marks) as total_obtain,sum(total_marks) as total_marks  from Candidateactivity
                        where  Candidateactivity.employee_id = '" . $row['cand_id'] . "'";
                                    $resultt11 = $conn->createCommand($sqlt11)->queryOne();

                                    $sqlt_marks = "SELECT sum(marks) as total_marks  from activitytype
                        where  1=1";
                                    $result_tmarks = $conn->createCommand($sqlt_marks)->queryOne();

                                    $totalrecomsql = "SELECT COUNT(*)as total FROM emp_recommendation WHERE flag=2 AND emp_recommendation.emp_id = '" . $row['cand_id'] . "' AND recommend_by='" . $_SESSION['user_array']['id'] . "'";
                                    $totalrecomresult = $conn->createCommand($totalrecomsql)->queryOne();
                                    // print_r($totalrecomresult); 
                                    if (empty($resultt11['total_obtain'])) {
                                        $marks = 'No Activity';
                                    } else {
                                        $marks = $resultt11['total_obtain'];
                                    }

                                    $sqlstatus = "SELECT * from employee where cnic= '" . $row['cnic'] . "' ";
                                    $result_status = $conn->createCommand($sqlstatus)->queryOne();

                                    // if($result_status['cnic']==$row['cnic'])
                                    if (!empty($result_status)) {
                                        $cand_status = '1';
                                        //hired
                                    } else {
                                        $cand_status = '2'; //in process 
                                    }

                        ?>
                                    <?php if ($uid == 771 || $row['final_status'] === NULL) {  ?>

                                        <?php if ($subjectwise != $row['subject']) { ?>
                                            <tr>
                                                <td colspan=13><br> <?= $row['subject'] ?></td>
                                            </tr>
                                        <?php $subjectwise = $row['subject'];
                                        } ?>
                                        <tr>

                                            <td><?php echo $i;
                                                $i++; ?></td>
                                            <td>
                                                <img src="img/candidate_pics/<?php echo htmlspecialchars($row['profile_pic']); ?>"
                                                    alt="Candidate Picture" width="40" height="40">
                                            </td>
                                            <td><?php echo $row['fname'] . ' ' . $row['lname']; ?></td>
                                            <td><?php echo $row['cnic']; ?></td>
                                            <td><?php echo $row['designation']; ?></td>
                                            <td><?php echo $row['school']; ?></td>
                                            <td><?php echo $row['section']; ?></td>
                                            <td><?php echo $row['subject']; ?></td>
                                            <td><?php echo $row['applyingdate']; ?></td>


                                            <td><?php echo $row['f_interview_date']; ?></td>
                                            <td><?php echo $row['f_interview_curr_date']; ?></td>
                                            <td><?php echo $row['written_marks']; ?></td>
                                            <td><a
                                                    onclick="upload_candidate_documents(<?php echo $row['cand_id']; ?>);"><?php echo $marks; ?></a>
                                            </td>
                                            <td><?php echo $resultt11['total_marks']; ?></td>
                                            <?php if ($totalr > 0) { ?>
                                                <td align='center'><a
                                                        onclick="view_Recommendation(<?php echo $row['cand_id']; ?>);"><?= $totalr ?></a>
                                                </td>
                                            <?php } else { ?>
                                                <td align='center'><?= $totalr ?></td>
                                            <?php } ?>
                                            <?php if ($totalnr > 0) { ?>
                                                <td align='center'><a
                                                        onclick="view_nonRecommendation(<?php echo $row['cand_id']; ?>);"><?= $totalnr ?></a>
                                                </td>
                                            <?php } else { ?>
                                                <td align='center'><?= $totalnr ?></td>
                                            <?php } ?>
                                            <?php if ($totalr > 0) { ?>
                                                <td align='center'><?= $totalrecmarks ?></td>
                                            <?php } else { ?>
                                                <td align='center'>0</td>
                                            <?php } ?>
                                            <td> <?php echo $row['final_remarks']; ?></td>
                                            <?php if ($uid == 42) { ?>
                                                <td> <?php echo $row['reject_date']; ?></td>
                                                <td> <?php echo $row['remarks_rej']; ?></td>
                                            <?php } ?>
                                            <td>
                                                <?php

                                                echo $row['status1'];

                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=candidate/candidate_view&id=<?php echo $row['cand_id'] ?>"
                                                    class="btn btn-minier btn-primary" target="_blank">View</a>
                                                <?php if ($uid == 42) {
                                                ?>
                                                    <a href="?r=employee/candidate_emp&id=<?php echo $row['cand_id'] ?>"
                                                        class="btn btn-minier btn-warning" target="_blank">Hiring</a>
                                                    <a class="btn btn-minier btn-success"
                                                        onclick="final_status_rej(<?php echo $row['cand_id']; ?>);">Final Status</a>
                                                <?php  }
                                                ?>
                                                <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/tcpdf/print/recruitment.php?id=<?php echo $row['cand_id'] ?>"
                                                    class="btn btn-minier btn-info" target="_blank">Print</a>

                                                <?php if ($uid == 1 or $uid == 49 or $uid == 764 or $uid == 766 or $uid == 768 or $uid == 769 or $uid == 770) {
                                                ?>
                                                    <a class="btn btn-minier btn-success"
                                                        onclick="upadate_Recommendation(<?php echo $row['cand_id']; ?>);">Recommendations</a>
                                                <?php  }
                                                ?>

                                                <?php if ($uid == 771 && $row['final_status'] != 3) {
                                                ?>
                                                    <a onclick="updateStatusModal('<?php echo $row['cand_id'] ?>');"
                                                        class="btn btn-minier btn-warning">Final Decision

                                                    </a>
                                                <?php  } ?>
                                            </td>
                                        </tr>
                                    <?php } else if ($uid != 771 && $totalrecomresult['total'] === 0) { ?>
                                        <?php if ($subjectwise != $row['subject']) { ?>
                                            <tr>
                                                <td colspan=13><?= $row['subject'] ?> <br></td>
                                            </tr>
                                        <?php $subjectwise = $row['subject'];
                                        } ?>
                                        <?php if ($row['reject_status'] == 1) { ?>
                                            <tr style="background-color: crimson; color: white !important;"><?php } else { ?>
                                            <tr>
                                            <?php } ?>

                                            <td><?php echo $i;
                                                $i++; ?></td>
                                            <td>
                                                <img src="img/candidate_pics/<?php echo htmlspecialchars($row['profile_pic']); ?>"
                                                    alt="Candidate Picture" width="40" height="40">
                                            </td>
                                            <td><?php echo $row['fname'] . ' ' . $row['lname']; ?></td>
                                            <td><?php echo $row['cnic']; ?></td>
                                            <td><?php echo $row['designation']; ?></td>
                                            <td><?php echo $row['school']; ?></td>
                                            <td><?php echo $row['section']; ?></td>
                                            <td><?php echo $row['subject']; ?></td>
                                            <td><?php echo $row['applyingdate']; ?></td>


                                            <td><?php echo $row['f_interview_date']; ?></td>
                                            <td><?php echo $row['f_interview_curr_date']; ?></td>
                                            <td><?php echo $row['written_marks']; ?></td>
                                            <!--<td align='center'><?php echo $marks; ?></td>-->
                                            <td align='center'><a
                                                    onclick="upload_candidate_documents(<?php echo $row['cand_id']; ?>);"><?php echo $marks; ?></a>
                                            </td>
                                            <td align='center'><?php echo $resultt11['total_marks']; ?></td>
                                            <?php if ($totalr > 0) { ?>
                                                <td align='center'><a
                                                        onclick="view_Recommendation(<?php echo $row['cand_id']; ?>);"><?= $totalr ?></a>
                                                </td>
                                            <?php } else { ?>
                                                <td align='center'><?= $totalr ?></td>
                                            <?php } ?>
                                            <?php if ($totalnr > 0) { ?>
                                                <td align='center'><a
                                                        onclick="view_nonRecommendation(<?php echo $row['cand_id']; ?>);"><?= $totalnr ?></a>
                                                </td>
                                            <?php } else { ?>
                                                <td align='center'><?= $totalnr ?></td>
                                            <?php } ?>
                                            <?php if ($totalr > 0) { ?>
                                                <td align='center'><?= $totalrecmarks ?></td>
                                            <?php } else { ?>
                                                <td align='center'>0</td>
                                            <?php } ?>
                                            <?php if ($uid == 42 || $uid == 769) { ?>
                                                <td><?php if ($row['final_status'] === 0) {
                                                        echo 'Not Approved';
                                                    } else if ($row['final_status'] === 1) {
                                                        echo 'Approved';
                                                    } else if ($row['final_status'] === 2) {
                                                        echo 'Reserved';
                                                    } else if ($row['final_status'] === 3) {
                                                        echo 'Not Appeared';
                                                    } else {
                                                        echo 'Pending';
                                                    }
                                                    ?>
                                                </td>
                                            <?php  } ?>
                                            <td> <?php echo $row['final_remarks']; ?></td>
                                            <?php if ($uid == 42) { ?>
                                                <td> <?php echo $row['reject_date']; ?></td>
                                                <td> <?php echo $row['remarks_rej']; ?></td>
                                            <?php } ?>
                                            <td>
                                                <?php

                                                echo $row['status1'];

                                                ?>
                                            </td>
                                            <td>
                                                <a href="?r=candidate/candidate_view&id=<?php echo $row['cand_id'] ?>"
                                                    class="btn btn-minier btn-primary" target="_blank">View</a>
                                                <?php if ($uid == 42) { ?>
                                                    <a href="?r=employee/candidate_emp&id=<?php echo $row['cand_id'] ?>"
                                                        class="btn btn-minier btn-warning" target="_blank">Hiring</a>
                                                    <a class="btn btn-minier btn-success"
                                                        onclick="final_status_rej(<?php echo $row['cand_id']; ?>);">Final Status</a>
                                                    <?php if ($row['final_status'] === 0 && $row['reject_status'] === 0) { ?>
                                                        <a onclick="updateStatusModal('<?php echo $row['cand_id'] ?>');"
                                                            class="btn btn-minier btn-danger">Reject </a>
                                                <?php  }
                                                }
                                                ?>
                                                <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/tcpdf/print/recruitment.php?id=<?php echo $row['cand_id'] ?>"
                                                    class="btn btn-minier btn-info" target="_blank">Print</a>

                                                <?php if ($uid == 1 or $uid == 49 or $uid == 764 or $uid == 766 or $uid == 768 or $uid == 769 or $uid == 770) {
                                                ?>
                                                    <a class="btn btn-minier btn-success"
                                                        onclick="upadate_Recommendation(<?php echo $row['cand_id']; ?>);">Recommendations</a>
                                                <?php  }
                                                ?>

                                                <?php if ($uid == 771) {
                                                ?>
                                                    <!--<a onclick="updateStatusModal('<?php echo $row['cand_id'] ?>');"  class="btn btn-minier btn-primary" >Final Decision-->

                                                    <!--</a>-->
                                                <?php  } ?>
                                            </td>
                                            </tr>
                            <?php }
                                }
                            }
                        }
                            ?>
                    </tbody>
                </table>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog">
        <div class="modal-dialog">
            <form id="finaldecision" name="finaldecision" method="post"
                action="/index.php?r=candidate/update_candidate_finalstatus" onsubmit="return finalizer()">
                <div class="modal-content">
                    <div class="modal-header" style="text-align: center;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <?php if ($uid == 42) { ?>
                            <h4 class="modal-title">Reject Candidate</h4>
                        <?php } else { ?> <h4 class="modal-title">Update Final Status</h4><?php } ?>
                    </div>
                    <style>
                        #aid_chosen {
                            width: 100% !important;
                        }
                    </style>

                    <div class="modal-body" style="width: 50%;">

                        <input type="hidden" id="modalCand_id" name="modalCand_id" value="">
                        <select name="final_status" id="final_status" class="chzn-select">
                            <?php if ($uid == 42) { ?>
                                <option value="3">Reject</option>
                            <?php } else { ?>
                                <option value="">Select Status</option>
                                <option value="0">Not Approved</option>
                                <option value="1">Approved</option>
                                <option value="2">Reserved</option>
                                <option value="3">Not Appeared</option>
                            <?php } ?>

                        </select>
                    </div>
                    <div class="modal-body" style="width: 100%;">
                        <label>Remarks</label>
                        <textarea type="text" class="form-control for_input_height" rows="2" name="remarks"></textarea>
                    </div>
                    <div class="modal-body" style="width: 40%;">
                        <label for="interview_date">Final Interview Date</label>
                        <input type="text" class="form-control date-picker1" name="f_interview_date"
                            id="f_interview_date" value="">
                    </div>
                    <div class="modal-body" style="width: 40%;">
                        <!--<label for="interview_date">Final Interview Current Date</label>-->
                        <input type="hidden" name="f_interview_curr_date" id="f_interview_curr_date">
                        <!-- Hidden input field -->
                    </div>


                    <div class="modal-footer">
                        <?php if ($uid == 42) { ?>
                            <input type="button" class="btn btn-default" value="Submit" onclick="rejected()" />
                        <?php } else { ?> <input type="button" class="btn btn-default" value="Submit"
                                onclick="finalizer()" /><?php } ?>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- Recommendation Form Modal Start -->
<div class="modal fade" id="recommendationModal" tabindex="-1" role="dialog" aria-labelledby="recommendationModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 4px !important;">
            <div class="modal-header" style="padding: 8px 15px 4px 15px !important;">
                <h3 class="modal-title" id="decisionModalLabel" style="float: left;">Recommendation</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: right;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="add_recommendations" id="add_recommendations"
                    action="index.php?r=employee/add_recommendation" method="POST" onsubmit="return checker()">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="remarks">Decision</label>
                            <select id="recommendation" class="chzn-select" name="recommendation">
                                <option value="">Select an Option</option>
                                <option value="1">Not Recommend</option>
                                <option value="2">Recommend</option>
                                <option value="3">Not Appeared</option>
                            </select>
                            <input type="hidden" name="emp_id" id="emp_id" value="">
                            <input type="hidden" name="flag" id="flag" value="2">
                        </div>
                        <div class="col-sm-4">
                            <label for="remarks">Marks <small>for Professional Skills</small></label>
                            <select id="recommendation" class="chzn-select" name="remarks">
                                <option value="">Select Marks</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="remarks">Marks <small>for Life Skills</small></label>
                            <select id="recommendation" class="chzn-select" name="remarks1">
                                <option value="">Select Marks</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group" style="float: right; margin-right: 13px;">
                                <button type="button" onClick="checker()" class="btn btn-sm btn-success">Add
                                    Recommendation</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Recommendation Form Modal End -->

<!-- Final status Form Modal Start -->
<div class="modal fade" id="finalModal" tabindex="-1" role="dialog" aria-labelledby="finalModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 4px !important;">
            <div class="modal-header" style="padding: 8px 15px 4px 15px !important;">
                <h3 class="modal-title" id="decisionModalLabel" style="float: left;">Final Status</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: right;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="final_status" id="final_status" action="index.php?r=employee/add_final_rej" method="POST"
                    onsubmit="return checker1()">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="remarks">Reject Date</label>
                            <input type="date" name="reject_date" id="reject_date" class="form-control">

                            <input type="hidden" name="emp_id1" id="emp_id1" value="">
                            <input type="hidden" name="flag" id="flag" value="2">
                        </div>
                        <div class="col-sm-4">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks_rej" name="remarks_rej" class="form-control" rows="3"
                                placeholder="Enter remarks here..."></textarea>
                        </div>

                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group" style="float: right; margin-right: 13px;">
                                <button type="submit" onClick="checker1()" class="btn btn-sm btn-success">Submit Final
                                    Status</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Recommendation View -->
<div class="modal fade" id="recommendationviewModal" tabindex="-1" role="dialog"
    aria-labelledby="recommendationviewModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 4px !important;">
            <div class="modal-header" style="padding: 8px 15px 4px 15px !important;">
                <h3 class="modal-title" id="decisionModalLabel" style="float: left;">View Recommendation</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: right;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ViewrecommendationModalbody">
            </div>
        </div>
    </div>
</div>
<!-- Recommendation View Model end -->
<!-- Non Recommendation View Modal Start -->
<div class="modal fade" id="nonrecommendationviewModal" tabindex="-1" role="dialog"
    aria-labelledby="nonrecommendationviewModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 4px !important;">
            <div class="modal-header" style="padding: 8px 15px 4px 15px !important;">
                <h3 class="modal-title" id="decisionModalLabel" style="float: left;">View Non-Recommendation</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: right;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="nonViewrecommendationModalbody">
            </div>
        </div>
    </div>
</div>
<!-- Non Recommendation View Modal End -->
<!-- Upload Documents View -->
<div class="modal fade" id="upload_documents" tabindex="-1" role="dialog" aria-labelledby="upload_documents"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 4px !important;">
            <div class="modal-header" style="padding: 8px 15px 4px 15px !important;">
                <h3 class="modal-title" id="decisionModalLabel" style="float: left;">Upload Documents</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: right;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="upload_document">
            </div>
        </div>
    </div>
</div>
<!-- Upload Documents View end -->
<script>
    function upadate_Recommendation(emp_id) {
        $('#recommendationModal').modal('show');
        $('#emp_id').val(emp_id);
    }

    function final_status_rej(emp_id) {
        $('#finalModal').modal('show');
        $('#emp_id1').val(emp_id);
    }

    function view_Recommendation(emp_id) {
        var type = 1
        $.ajax({
            url: "index.php?r=candidate/view_recommendation",
            type: "POST",
            data: {
                emp_id: emp_id,
                type: type
            },
            cache: false,
            success: function(response) {
                // alert(response); return false;
                // var data=JSON.parse(response);
                // console.log(data);
                $('#recommendationviewModal').modal('show');
                $('#ViewrecommendationModalbody').html(response);
                // data.forEach(function(item) {
                //     // console.log("Role Name:", item.role_name);
                //     // console.log("Recommendation Remarks:", item.recremarks);
                //     // $('#view_designation').val(item.role_name);
                //     // $('#view_remarks').val(item.recremarks);
                // });

            }
        });
    }

    function view_nonRecommendation(emp_id) {
        var type = 2
        $.ajax({
            url: "index.php?r=candidate/view_recommendation",
            type: "POST",
            data: {
                emp_id: emp_id,
                type: type
            },
            cache: false,
            success: function(response) {
                // alert(response); return false;
                // var data=JSON.parse(response);
                $('#nonrecommendationviewModal').modal('show');
                $('#nonViewrecommendationModalbody').html(response);
                // console.log(data);
                //   $('#nonrecommendationviewModal').modal('show');
                //     data.forEach(function(item) {
                //         // console.log("Role Name:", item.role_name);
                //         // console.log("Recommendation Remarks:", item.recremarks);
                //         $('#nonview_designation').val(item.role_name);
                //         $('#nonview_remarks').val(item.recremarks);
                //     });

            }
        });
    }

    function updateStatusModal(id) {
        $('#myModal1').modal('show');
        $('#modalCand_id').val(id);
    }

    function submitform(id) {
        var cand = $("#modalCand_id").val();

        $.ajax({
            type: "POST",
            url: "index.php?r=candidate/update_candidate_finalstatus&id=" + cand,
            contenetType: "json",
            data: $("#multi").serialize() + "&&page=1",
            success: function(response) {
                alert('Status Updated Successfully');

            }
        });
    }
</script>
<script>
    function checker() {

        $.ajax({
            url: "<?php echo Yii::$app->request->baseUrl; ?>/index.php?r=employee/checkrecommendation",
            type: "POST",
            data: $('#add_recommendations').serialize(),
            cache: false,
            success: function(response) {
                swal({
                    title: "Are you sure?",
                    //  text: response,
                    icon: "warning",
                    buttons: [
                        'No, cancel it!',
                        'Yes, I am sure!'
                    ],
                    dangerMode: true,
                }).then(function(isConfirm) {
                    if (isConfirm && response == 0) {
                        add_re();
                    } else if (isConfirm && response > 0) {
                        swal({
                            title: "Recommendation Already Exist",
                            // text: response,
                            icon: "error",
                            // location.reload();
                        })
                    }
                })
            }
        });
    }

    function checker1() {

        $.ajax({
            url: "<?php echo Yii::$app->request->baseUrl; ?>/index.php?r=employee/add_final_rej",
            type: "POST",
            data: $('#final_status').serialize(),
            cache: false,
            success: function(response) {
                swal({
                    title: "Success",
                    // text: response,
                    icon: "success",
                    // location.reload();
                }).then(function() {
                    window.location.reload();
                });
            }
        });
    }

    function add_re() {

        $.ajax({
            url: "<?php echo Yii::$app->request->baseUrl; ?>/index.php?r=employee/add_recommendation",
            type: "POST",
            data: $('#add_recommendations').serialize(),
            cache: false,
            success: function(response) {
                swal({
                    title: "Success",
                    // text: response,
                    icon: "success",
                    // location.reload();
                }).then(function() {
                    window.location.reload();
                });
            }
        });
    }
</script>
<script>
    function finalizer() {

        $.ajax({
            url: "<?php echo Yii::$app->request->baseUrl; ?>/index.php?r=candidate/checkdecision",
            type: "POST",
            data: $('#finaldecision').serialize(),
            cache: false,
            success: function(response) {
                swal({
                    title: "Are you sure?",
                    //  text: response,
                    icon: "warning",
                    buttons: [
                        'No, cancel it!',
                        'Yes, I am sure!'
                    ],
                    dangerMode: true,
                }).then(function(isConfirm) {
                    if (isConfirm && response == 0) {
                        add_final();
                    } else if (isConfirm && response > 0) {
                        swal({
                            title: "Decision Already Exist",
                            // text: response,
                            icon: "error",
                            // location.reload();
                        })
                    }
                })
            }
        });
    }

    function rejected() {

        $.ajax({
            url: "<?php echo Yii::$app->request->baseUrl; ?>/index.php?r=candidate/checkrejection",
            type: "POST",
            data: $('#finaldecision').serialize(),
            cache: false,
            success: function(response) {
                swal({
                    title: "Are you sure?",
                    //  text: response,
                    icon: "warning",
                    buttons: [
                        'No, cancel it!',
                        'Yes, I am sure!'
                    ],
                    dangerMode: true,
                }).then(function(isConfirm) {
                    if (isConfirm && response == 0) {
                        add_final();
                    } else if (isConfirm && response > 0) {
                        swal({
                            title: "Decision Already Exist",
                            // text: response,
                            icon: "error",
                            // location.reload();
                        })
                    }
                })
            }
        });
    }

    function add_final() {
        var cand = $("#modalCand_id").val();

        $.ajax({
            url: "<?php echo Yii::$app->request->baseUrl; ?>/index.php?r=candidate/update_candidate_finalstatus",
            type: "POST",
            data: $('#finaldecision').serialize(),
            cache: false,
            success: function(response) {
                swal({
                    title: "Success",
                    // text: response,
                    icon: "success",
                    // location.reload();
                }).then(function() {
                    window.location.reload();
                });
            }
        });
    }
</script>
<script>
    function upload_candidate_documents(emp_id) {

        $.ajax({
            url: "index.php?r=candidate/upload_documents",
            type: "POST",
            data: {
                emp_id: emp_id
            },
            cache: false,
            success: function(response) {
                // alert(response); return false;
                // var data=JSON.parse(response);
                $('#upload_documents').modal('show');
                $('#upload_document').html(response);
                console.log(data);
                $('#nonrecommendationviewModal').modal('show');
                data.forEach(function(item) {
                    console.log("Role Name:", item.role_name);
                    console.log("Recommendation Remarks:", item.recremarks);
                    $('#nonview_designation').val(item.role_name);
                    $('#nonview_remarks').val(item.recremarks);
                });

            }
        });
    }

    function exportF(elem) {
        var table = document.getElementById("formattribute");
        var html = table.outerHTML;
        var url = 'data:application/vnd.ms-excel,' + escape(html); // Set your html table into url 
        elem.setAttribute("href", url);
        elem.setAttribute("download", "exported_table_.xls"); // Choose the file name
        return false;
    }
</script>