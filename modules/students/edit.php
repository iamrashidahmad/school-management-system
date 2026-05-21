<?php
/**
 * Edit Student
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

$pageTitle = 'Edit Student';
$activeMenu = 'students';

$studentId = intval($_GET['id'] ?? 0);
$error = '';

// Get student data
$stmt = $conn->prepare("SELECT s.* FROM students s WHERE s.student_id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    redirect(BASE_URL . 'modules/students/', 'error', 'Student not found.');
}

// Get dropdown data
$classes = $conn->query("SELECT * FROM classes WHERE status = 1 ORDER BY class_name_numeric");
$sections = $conn->query("SELECT * FROM sections WHERE class_id = {$student['class_id']} ORDER BY section_name");
$parents = $conn->query("SELECT p.* FROM parents p WHERE p.status = 1 ORDER BY p.father_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $gender = sanitize($_POST['gender'] ?? '');
        $dob = sanitize($_POST['date_of_birth'] ?? '');
        $cnic = sanitize($_POST['cnic_bform'] ?? '');
        $religion = sanitize($_POST['religion'] ?? '');
        $bloodGroup = sanitize($_POST['blood_group'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $classId = intval($_POST['class_id'] ?? 0);
        $sectionId = intval($_POST['section_id'] ?? 0);
        $parentId = intval($_POST['parent_id'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'active');
        $address = sanitize($_POST['address'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $state = sanitize($_POST['state'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $medicalNotes = sanitize($_POST['medical_notes'] ?? '');
        
        $photoName = $student['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['tmp_name']) {
            $upload = uploadFile($_FILES['photo'], 'students', ['jpg','jpeg','png','gif'], 2097152);
            if ($upload['success']) {
                deleteFile($student['photo'], 'students');
                $photoName = $upload['filename'];
            }
        }
        
        $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, gender=?, date_of_birth=?, cnic_bform=?, religion=?, blood_group=?, phone=?, email=?, class_id=?, section_id=?, parent_id=?, status=?, photo=?, address=?, city=?, state=?, country=?, medical_notes=? WHERE student_id=?");
        $stmt->bind_param("sssssssssiissssssssi", $firstName, $lastName, $gender, $dob, $cnic, $religion, $bloodGroup, $phone, $email, $classId, $sectionId, $parentId, $status, $photoName, $address, $city, $state, $country, $medicalNotes, $studentId);
        
        if ($stmt->execute()) {
            logActivity(getCurrentUserId(), 'Student Updated', "Updated student: $firstName $lastName");
            redirect(BASE_URL . 'modules/students/', 'success', 'Student updated successfully.');
        } else {
            $error = 'Error updating student: ' . $conn->error;
        }
    }
}

include '../../includes/header.php';
?>

<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-primary"></i>Edit Student</h1>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
            <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <?php echo csrfField(); ?>
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" required value="<?php echo $student['first_name']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" required value="<?php echo $student['last_name']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Gender *</label>
                                        <select class="form-select" name="gender" required>
                                            <option value="Male" <?php echo $student['gender']=='Male'?'selected':''; ?>>Male</option>
                                            <option value="Female" <?php echo $student['gender']=='Female'?'selected':''; ?>>Female</option>
                                            <option value="Other" <?php echo $student['gender']=='Other'?'selected':''; ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo $student['date_of_birth']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Blood Group</label>
                                        <select class="form-select" name="blood_group">
                                            <option value="">Select</option>
                                            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                            <option value="<?php echo $bg; ?>" <?php echo $student['blood_group']==$bg?'selected':''; ?>><?php echo $bg; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <?php foreach(['active','inactive','graduated','transferred','suspended'] as $st): ?>
                                            <option value="<?php echo $st; ?>" <?php echo $student['status']==$st?'selected':''; ?>><?php echo ucfirst($st); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone" value="<?php echo $student['phone']; ?>"></div>
                                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo $student['email']; ?>"></div>
                                    <div class="col-md-4"><label class="form-label">Photo</label><input type="file" class="form-control file-input-preview" name="photo" accept="image/*" data-preview="photoPreview"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Info</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Class *</label>
                                        <select class="form-select class-select" name="class_id" required>
                                            <?php $classes->data_seek(0); while($c=$classes->fetch_assoc()): ?>
                                            <option value="<?php echo $c['class_id']; ?>" <?php echo $student['class_id']==$c['class_id']?'selected':''; ?>><?php echo $c['class_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Section</label>
                                        <select class="form-select section-select" name="section_id">
                                            <option value="">Select</option>
                                            <?php while($s=$sections->fetch_assoc()): ?>
                                            <option value="<?php echo $s['section_id']; ?>" <?php echo $student['section_id']==$s['section_id']?'selected':''; ?>><?php echo $s['section_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Parent</label>
                                        <select class="form-select select2" name="parent_id">
                                            <option value="">Select</option>
                                            <?php while($p=$parents->fetch_assoc()): ?>
                                            <option value="<?php echo $p['parent_id']; ?>" <?php echo $student['parent_id']==$p['parent_id']?'selected':''; ?>><?php echo $p['father_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white"><h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="2"><?php echo $student['address']; ?></textarea></div>
                                    <div class="col-md-4"><label class="form-label">City</label><input type="text" class="form-control" name="city" value="<?php echo $student['city']; ?>"></div>
                                    <div class="col-md-4"><label class="form-label">State</label><input type="text" class="form-control" name="state" value="<?php echo $student['state']; ?>"></div>
                                    <div class="col-md-4"><label class="form-label">Country</label><input type="text" class="form-control" name="country" value="<?php echo $student['country']; ?>"></div>
                                    <div class="col-12"><label class="form-label">Medical Notes</label><textarea class="form-control" name="medical_notes" rows="2"><?php echo $student['medical_notes']; ?></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera me-2"></i>Photo</h6></div>
                            <div class="card-body text-center">
                                <img id="photoPreview" src="../../assets/uploads/students/<?php echo $student['photo']; ?>" class="img-fluid rounded mb-3" style="max-width:200px;max-height:250px;object-fit:cover;" onerror="this.src='../../assets/images/default-user.png'">
                                <div class="small text-muted">Upload new photo to replace</div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Update Student</button>
                            <a href="view.php?id=<?php echo $studentId; ?>" class="btn btn-info"><i class="fas fa-eye me-2"></i>View Profile</a>
                            <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      
    </div>
</div>
  <?php include '../../includes/footer.php'; ?>