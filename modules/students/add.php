<?php
/**
 * Add New Student
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

$pageTitle = 'Add New Student';
$activeMenu = 'students';

$error = '';
$success = '';

// Get dropdown data
$classes = $conn->query("SELECT * FROM classes WHERE status = 1 ORDER BY class_name_numeric");
$sections = $conn->query("SELECT * FROM sections WHERE status = 1 ORDER BY section_name");
$parents = $conn->query("SELECT p.*, u.email FROM parents p JOIN users u ON p.user_id = u.user_id WHERE p.status = 1 ORDER BY p.father_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Get form data
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
        $admissionDate = sanitize($_POST['admission_date'] ?? date('Y-m-d'));
        $previousSchool = sanitize($_POST['previous_school'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $state = sanitize($_POST['state'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $medicalNotes = sanitize($_POST['medical_notes'] ?? '');
        
        // Validation
        if (empty($firstName) || empty($lastName) || empty($gender) || $classId == 0) {
            $error = 'Please fill in all required fields (marked with *).';
        } elseif (!empty($email) && !isValidEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Generate admission number and roll number
            $admissionNo = generateCode('ADM', 'students', 'admission_no');
            $rollNo = 'R-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            // Generate password
            $plainPassword = strtolower($firstName) . '123';
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
            
            // Generate username
            $username = strtolower($firstName . $lastName . mt_rand(10, 99));
            
            // Create user account
            $conn->begin_transaction();
            
            try {
                // Insert into users table
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone) VALUES (?, ?, ?, 'student', ?)");
                $stmt->bind_param("ssss", $username, $email, $hashedPassword, $phone);
                $stmt->execute();
                $userId = $conn->insert_id;
                
                // Handle photo upload
                $photoName = 'default_student.png';
                if (isset($_FILES['photo']) && $_FILES['photo']['tmp_name']) {
                    $upload = uploadFile($_FILES['photo'], 'students', ['jpg', 'jpeg', 'png', 'gif'], 2097152);
                    if ($upload['success']) {
                        $photoName = $upload['filename'];
                    }
                }
                
                // Insert into students table
                $stmt = $conn->prepare("INSERT INTO students (user_id, admission_no, roll_no, first_name, last_name, gender, date_of_birth, cnic_bform, religion, blood_group, phone, email, class_id, section_id, parent_id, admission_date, previous_school, photo, address, city, state, country, medical_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssssssssssiissssssss", $userId, $admissionNo, $rollNo, $firstName, $lastName, $gender, $dob, $cnic, $religion, $bloodGroup, $phone, $email, $classId, $sectionId, $parentId, $admissionDate, $previousSchool, $photoName, $address, $city, $state, $country, $medicalNotes);
                $stmt->execute();
                $studentId = $conn->insert_id;
                
                $conn->commit();
                
                // Log activity
                logActivity(getCurrentUserId(), 'Student Added', "Added student: $firstName $lastName (Admission: $admissionNo)");
                
                redirect(BASE_URL . 'modules/students/', 'success', "Student added successfully! Admission No: $admissionNo, Password: $plainPassword");
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Error adding student: ' . $e->getMessage();
            }
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
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-plus me-2 text-primary"></i>Add New Student
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                <?php echo csrfField(); ?>
                
                <div class="row g-3">
                    <!-- Personal Information -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" required value="<?php echo $_POST['first_name'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" required value="<?php echo $_POST['last_name'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="Male" <?php echo ($_POST['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($_POST['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo ($_POST['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control datepicker" name="date_of_birth" value="<?php echo $_POST['date_of_birth'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">CNIC / B-Form</label>
                                        <input type="text" class="form-control" name="cnic_bform" value="<?php echo $_POST['cnic_bform'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Religion</label>
                                        <input type="text" class="form-control" name="religion" value="<?php echo $_POST['religion'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Blood Group</label>
                                        <select class="form-select" name="blood_group">
                                            <option value="">Select</option>
                                            <option value="A+" <?php echo ($_POST['blood_group'] ?? '') == 'A+' ? 'selected' : ''; ?>>A+</option>
                                            <option value="A-" <?php echo ($_POST['blood_group'] ?? '') == 'A-' ? 'selected' : ''; ?>>A-</option>
                                            <option value="B+" <?php echo ($_POST['blood_group'] ?? '') == 'B+' ? 'selected' : ''; ?>>B+</option>
                                            <option value="B-" <?php echo ($_POST['blood_group'] ?? '') == 'B-' ? 'selected' : ''; ?>>B-</option>
                                            <option value="AB+" <?php echo ($_POST['blood_group'] ?? '') == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                            <option value="AB-" <?php echo ($_POST['blood_group'] ?? '') == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                            <option value="O+" <?php echo ($_POST['blood_group'] ?? '') == 'O+' ? 'selected' : ''; ?>>O+</option>
                                            <option value="O-" <?php echo ($_POST['blood_group'] ?? '') == 'O-' ? 'selected' : ''; ?>>O-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Photo</label>
                                        <input type="file" class="form-control file-input-preview" name="photo" accept="image/*" data-preview="photoPreview">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control phone-input" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Medical Notes</label>
                                        <textarea class="form-control" name="medical_notes" rows="2"><?php echo $_POST['medical_notes'] ?? ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Academic Information -->
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Class <span class="text-danger">*</span></label>
                                        <select class="form-select class-select" name="class_id" required>
                                            <option value="">Select Class</option>
                                            <?php $classes->data_seek(0); while ($class = $classes->fetch_assoc()): ?>
                                            <option value="<?php echo $class['class_id']; ?>" <?php echo ($_POST['class_id'] ?? '') == $class['class_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($class['class_name']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Section</label>
                                        <select class="form-select section-select" name="section_id">
                                            <option value="">Select Section</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Admission Date</label>
                                        <input type="date" class="form-control datepicker" name="admission_date" value="<?php echo $_POST['admission_date'] ?? date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Previous School</label>
                                        <input type="text" class="form-control" name="previous_school" value="<?php echo $_POST['previous_school'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Assign Parent</label>
                                        <select class="form-select select2" name="parent_id">
                                            <option value="">Select Parent</option>
                                            <?php while ($parent = $parents->fetch_assoc()): ?>
                                            <option value="<?php echo $parent['parent_id']; ?>" <?php echo ($_POST['parent_id'] ?? '') == $parent['parent_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($parent['father_name']); ?> (<?php echo htmlspecialchars($parent['father_phone']); ?>)
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2"><?php echo $_POST['address'] ?? ''; ?></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="<?php echo $_POST['city'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="state" value="<?php echo $_POST['state'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Country</label>
                                        <input type="text" class="form-control" name="country" value="<?php echo $_POST['country'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Photo Preview -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-camera me-2"></i>Photo Preview</h6>
                            </div>
                            <div class="card-body text-center">
                                <img id="photoPreview" src="../../assets/images/default-user.png" alt="Photo Preview" 
                                     class="img-fluid rounded mb-3" style="max-width:200px;max-height:250px;object-fit:cover;">
                                <div class="small text-muted">Upload student photo</div>
                                <div class="small text-muted">Max size: 2MB (JPG, PNG, GIF)</div>
                            </div>
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Instructions</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small text-muted ps-3 mb-0">
                                    <li class="mb-2">Fields marked with <span class="text-danger">*</span> are required</li>
                                    <li class="mb-2">Admission number will be auto-generated</li>
                                    <li class="mb-2">Student login credentials will be created automatically</li>
                                    <li class="mb-2">Default password: firstname123 (lowercase)</li>
                                    <li>Parent can be assigned later if not available</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Student
                            </button>
                            <button type="reset" class="btn btn-secondary btn-reset">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>