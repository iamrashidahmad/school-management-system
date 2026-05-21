<?php
/**
 * Add Teacher
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();
$pageTitle = 'Add Teacher';
$activeMenu = 'teachers';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $gender = sanitize($_POST['gender'] ?? '');
        $dob = sanitize($_POST['date_of_birth'] ?? '');
        $qualification = sanitize($_POST['qualification'] ?? '');
        $specialization = sanitize($_POST['specialization'] ?? '');
        $experience = intval($_POST['experience_years'] ?? 0);
        $salary = floatval($_POST['salary'] ?? 0);
        $phone = sanitize($_POST['phone'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $joiningDate = sanitize($_POST['joining_date'] ?? date('Y-m-d'));
        
        if (empty($firstName) || empty($lastName) || empty($gender) || empty($email)) {
            $error = 'Please fill in all required fields.';
        } else {
            $plainPass = strtolower($firstName) . '123';
            $hashPass = password_hash($plainPass, PASSWORD_DEFAULT);
            $username = strtolower($firstName.$lastName.mt_rand(10,99));
            $teacherCode = generateCode('TCH', 'teachers', 'teacher_code');
            
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone) VALUES (?, ?, ?, 'teacher', ?)");
                $stmt->bind_param("ssss", $username, $email, $hashPass, $phone);
                $stmt->execute();
                $userId = $conn->insert_id;
                
                $photo = 'default_teacher.png';
                if (isset($_FILES['photo']) && $_FILES['photo']['tmp_name']) {
                    $up = uploadFile($_FILES['photo'], 'teachers', ['jpg','jpeg','png','gif'], 2097152);
                    if ($up['success']) $photo = $up['filename'];
                }
                
                $stmt = $conn->prepare("INSERT INTO teachers (user_id, teacher_code, first_name, last_name, gender, date_of_birth, qualification, specialization, experience_years, salary, phone, email, address, joining_date, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssssiisssss", $userId, $teacherCode, $firstName, $lastName, $gender, $dob, $qualification, $specialization, $experience, $salary, $phone, $email, $address, $joiningDate, $photo);
                $stmt->execute();
                
                $conn->commit();
                logActivity(getCurrentUserId(), 'Teacher Added', "Added teacher: $firstName $lastName");
                redirect(BASE_URL.'modules/teachers/', 'success', "Teacher added! Code: $teacherCode, Password: $plainPass");
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Error: '.$e->getMessage();
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
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-plus me-2 text-primary"></i>Add Teacher</h1>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
            <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <?php echo csrfField(); ?>
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">First Name *</label><input type="text" class="form-control" name="first_name" required></div>
                                    <div class="col-md-6"><label class="form-label">Last Name *</label><input type="text" class="form-control" name="last_name" required></div>
                                    <div class="col-md-3"><label class="form-label">Gender *</label><select class="form-select" name="gender" required><option value="">Select</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
                                    <div class="col-md-3"><label class="form-label">DOB</label><input type="date" class="form-control" name="date_of_birth"></div>
                                    <div class="col-md-3"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone"></div>
                                    <div class="col-md-3"><label class="form-label">Email *</label><input type="email" class="form-control" name="email" required></div>
                                    <div class="col-md-4"><label class="form-label">Qualification</label><input type="text" class="form-control" name="qualification" placeholder="M.Ed, B.Sc"></div>
                                    <div class="col-md-4"><label class="form-label">Specialization</label><input type="text" class="form-control" name="specialization" placeholder="Mathematics"></div>
                                    <div class="col-md-4"><label class="form-label">Experience (Years)</label><input type="number" class="form-control" name="experience_years" min="0"></div>
                                    <div class="col-md-6"><label class="form-label">Salary</label><input type="number" class="form-control" name="salary" step="0.01" min="0"></div>
                                    <div class="col-md-6"><label class="form-label">Joining Date</label><input type="date" class="form-control" name="joining_date" value="<?php echo date('Y-m-d'); ?>"></div>
                                    <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="2"></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera me-2"></i>Photo</h6></div>
                            <div class="card-body text-center">
                                <img id="photoPreview" src="../../assets/images/default-user.png" class="img-fluid rounded mb-3" style="max-width:180px;">
                                <input type="file" class="form-control file-input-preview" name="photo" accept="image/*" data-preview="photoPreview">
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Teacher</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>
</div>
<?php include '../../includes/footer.php'; ?>