<?php
/**
 * View Student Profile
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

$pageTitle = 'Student Profile';
$activeMenu = 'students';

$studentId = intval($_GET['id'] ?? 0);

// Get student data
$stmt = $conn->prepare("SELECT s.*, c.class_name, sec.section_name, p.father_name, p.mother_name, p.father_phone, p.father_email, p.father_occupation, p.address as parent_address
                        FROM students s 
                        LEFT JOIN classes c ON s.class_id = c.class_id 
                        LEFT JOIN sections sec ON s.section_id = sec.section_id 
                        LEFT JOIN parents p ON s.parent_id = p.parent_id 
                        WHERE s.student_id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    redirect(BASE_URL . 'modules/students/', 'error', 'Student not found.');
}

// Get attendance summary
$attSummary = getAttendanceSummary($studentId);
$attPercentage = $attSummary['total_days'] > 0 ? round(($attSummary['present'] / $attSummary['total_days']) * 100, 1) : 0;

// Get fee summary
$feeSummary = getFeeSummary($studentId);

// Get documents
$docs = $conn->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY upload_date DESC");
$docs->bind_param("i", $studentId);
$docs->execute();
$documents = $docs->get_result();

// Get results
$results = $conn->prepare("SELECT r.*, e.exam_name, et.exam_name as type_name FROM results r 
                           JOIN exams e ON r.exam_id = e.exam_id 
                           JOIN exam_types et ON e.exam_type_id = et.exam_type_id 
                           WHERE r.student_id = ? ORDER BY r.created_at DESC");
$results->bind_param("i", $studentId);
$results->execute();
$studentResults = $results->get_result();

include '../../includes/header.php';
?>

<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-graduate me-2 text-primary"></i>Student Profile
                </h1>
                <div>
                    <a href="edit.php?id=<?php echo $studentId; ?>" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            
            <div class="row g-3">
                <!-- Left Column -->
                <div class="col-lg-4">
                    <!-- Profile Card -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-body text-center">
                            <img src="../../assets/uploads/students/<?php echo $student['photo']; ?>" 
                                 class="rounded-circle mb-3 border" style="width:150px;height:150px;object-fit:cover;"
                                 onerror="this.src='../../assets/images/default-user.png'">
                            <h4 class="mb-1"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h4>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($student['admission_no']); ?></p>
                            <p class="text-muted mb-3">Roll No: <?php echo htmlspecialchars($student['roll_no']); ?></p>
                            <?php echo getStatusBadge($student['status']); ?>
                            <hr>
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div class="fw-bold text-primary h5 mb-0"><?php echo $attPercentage; ?>%</div>
                                    <div class="small text-muted">Attendance</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-success h5 mb-0"><?php echo formatCurrency($feeSummary['total_paid'] ?? 0); ?></div>
                                    <div class="small text-muted">Fees Paid</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Basic Info</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Gender:</span>
                                <span class="fw-bold"><?php echo $student['gender']; ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Date of Birth:</span>
                                <span class="fw-bold"><?php echo formatDate($student['date_of_birth']); ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">CNIC/B-Form:</span>
                                <span class="fw-bold"><?php echo $student['cnic_bform'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Religion:</span>
                                <span class="fw-bold"><?php echo $student['religion'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Blood Group:</span>
                                <span class="fw-bold"><?php echo $student['blood_group'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Admission Date:</span>
                                <span class="fw-bold"><?php echo formatDate($student['admission_date']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Parent Info -->
                    <div class="card shadow-sm">
                        <div class="card-header py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Parent/Guardian</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Father:</span>
                                <span class="fw-bold"><?php echo $student['father_name'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Mother:</span>
                                <span class="fw-bold"><?php echo $student['mother_name'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Phone:</span>
                                <span class="fw-bold"><?php echo $student['father_phone'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between small">
                                <span class="text-muted">Email:</span>
                                <span class="fw-bold"><?php echo $student['father_email'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Occupation:</span>
                                <span class="fw-bold"><?php echo $student['father_occupation'] ?? 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header py-3">
                            <ul class="nav nav-tabs card-header-tabs" id="studentTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button">Academic</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button">Attendance</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button">Results</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button">Fees</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">Documents</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="studentTabsContent">
                                <!-- Academic Tab -->
                                <div class="tab-pane fade show active" id="academic">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Class</label>
                                            <p><?php echo $student['class_name'] ?? 'N/A'; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Section</label>
                                            <p><?php echo $student['section_name'] ?? 'N/A'; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Previous School</label>
                                            <p><?php echo $student['previous_school'] ?: 'N/A'; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Previous Class</label>
                                            <p><?php echo $student['previous_class'] ?: 'N/A'; ?></p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Address</label>
                                            <p><?php echo nl2br(htmlspecialchars($student['address'] ?: 'N/A')); ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">City</label>
                                            <p><?php echo $student['city'] ?: 'N/A'; ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">State</label>
                                            <p><?php echo $student['state'] ?: 'N/A'; ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Country</label>
                                            <p><?php echo $student['country'] ?: 'N/A'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Attendance Tab -->
                                <div class="tab-pane fade" id="attendance">
                                    <div class="row g-3">
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-soft-success">
                                                <div class="card-body">
                                                    <div class="h4 text-success mb-0"><?php echo $attSummary['present']; ?></div>
                                                    <div class="small text-muted">Present</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-soft-danger">
                                                <div class="card-body">
                                                    <div class="h4 text-danger mb-0"><?php echo $attSummary['absent']; ?></div>
                                                    <div class="small text-muted">Absent</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-soft-warning">
                                                <div class="card-body">
                                                    <div class="h4 text-warning mb-0"><?php echo $attSummary['late']; ?></div>
                                                    <div class="small text-muted">Late</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-soft-info">
                                                <div class="card-body">
                                                    <div class="h4 text-info mb-0"><?php echo $attSummary['total_days']; ?></div>
                                                    <div class="small text-muted">Total Days</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="../attendance/report.php?student_id=<?php echo $studentId; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-chart-bar me-1"></i>View Detailed Report
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Results Tab -->
                                <div class="tab-pane fade" id="results">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead><tr><th>Exam</th><th>Type</th><th>Total</th><th>Obtained</th><th>%</th><th>Grade</th><th>Status</th></tr></thead>
                                            <tbody>
                                                <?php while ($result = $studentResults->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $result['exam_name']; ?></td>
                                                    <td><?php echo $result['type_name']; ?></td>
                                                    <td><?php echo $result['total_marks']; ?></td>
                                                    <td><?php echo $result['total_obtained']; ?></td>
                                                    <td><?php echo $result['percentage']; ?>%</td>
                                                    <td><?php echo $result['grade']; ?></td>
                                                    <td><?php echo getStatusBadge($result['status']); ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                                <?php if ($studentResults->num_rows == 0): ?>
                                                <tr><td colspan="7" class="text-center text-muted">No results available</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Fees Tab -->
                                <div class="tab-pane fade" id="fees">
                                    <div class="row g-3">
                                        <div class="col-md-4 text-center">
                                            <div class="card bg-soft-primary">
                                                <div class="card-body">
                                                    <div class="h4 text-primary mb-0"><?php echo formatCurrency($feeSummary['total_amount'] ?? 0); ?></div>
                                                    <div class="small text-muted">Total Fees</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="card bg-soft-success">
                                                <div class="card-body">
                                                    <div class="h4 text-success mb-0"><?php echo formatCurrency($feeSummary['total_paid'] ?? 0); ?></div>
                                                    <div class="small text-muted">Paid</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="card bg-soft-danger">
                                                <div class="card-body">
                                                    <div class="h4 text-danger mb-0"><?php echo formatCurrency($feeSummary['total_balance'] ?? 0); ?></div>
                                                    <div class="small text-muted">Balance</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Documents Tab -->
                                <div class="tab-pane fade" id="documents">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Uploaded Documents</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead><tr><th>Name</th><th>Type</th><th>Date</th><th>Actions</th></tr></thead>
                                            <tbody>
                                                <?php while ($doc = $documents->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $doc['document_name']; ?></td>
                                                    <td><?php echo $doc['document_type']; ?></td>
                                                    <td><?php echo formatDate($doc['upload_date']); ?></td>
                                                    <td>
                                                        <a href="../../assets/uploads/documents/<?php echo $doc['document_file']; ?>" target="_blank" class="btn btn-sm btn-info btn-action"><i class="fas fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                                <?php if ($documents->num_rows == 0): ?>
                                                <tr><td colspan="4" class="text-center text-muted">No documents uploaded</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
      
    </div>
</div>
  <?php include '../../includes/footer.php'; ?>