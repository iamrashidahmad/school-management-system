<?php
/**
 * Exams Module - List Exams
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Exams';
$activeMenu = 'exams';

$exams = $conn->query("SELECT e.*, et.exam_name as type_name, c.class_name, (SELECT COUNT(*) FROM exam_subjects WHERE exam_id=e.exam_id) as subject_count FROM exams e JOIN exam_types et ON e.exam_type_id=et.exam_type_id JOIN classes c ON e.class_id=c.class_id ORDER BY e.created_at DESC");

include '../../includes/header.php';
?>
<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-alt me-2 text-primary"></i>Examinations</h1>
                <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Create Exam</a>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover datatable mb-0">
                            <thead><tr><th>Exam Name</th><th>Type</th><th>Class</th><th>Session</th><th>Start Date</th><th>Subjects</th><th>Publish</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php while($e=$exams->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $e['exam_name']; ?><div class="small text-muted"><?php echo $e['semester_name']; ?></div></td>
                                    <td><?php echo $e['type_name']; ?></td>
                                    <td><?php echo $e['class_name']; ?></td>
                                    <td><?php echo $e['session_year']; ?></td>
                                    <td><?php echo formatDate($e['start_date']); ?></td>
                                    <td><span class="badge bg-info"><?php echo $e['subject_count']; ?></span></td>
                                    <td><?php echo $e['publish_result'] ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-secondary">Draft</span>'; ?></td>
                                    <td>
                                        <a href="subjects.php?exam_id=<?php echo $e['exam_id']; ?>" class="btn btn-sm btn-info btn-action" title="Subjects"><i class="fas fa-book"></i></a>
                                        <a href="marks.php?exam_id=<?php echo $e['exam_id']; ?>" class="btn btn-sm btn-success btn-action" title="Marks"><i class="fas fa-pen"></i></a>
                                        <a href="results.php?exam_id=<?php echo $e['exam_id']; ?>" class="btn btn-sm btn-primary btn-action" title="Results"><i class="fas fa-chart-bar"></i></a>
                                        <a href="toggle-publish.php?id=<?php echo $e['exam_id']; ?>" class="btn btn-sm btn-<?php echo $e['publish_result']?'warning':'success'; ?> btn-action" title="Toggle Publish"><i class="fas fa-<?php echo $e['publish_result']?'eye-slash':'eye'; ?>"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>