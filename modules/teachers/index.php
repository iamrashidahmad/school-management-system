<?php
/**
 * Teachers Module - List All Teachers
 */
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireLogin();

$pageTitle = 'All Teachers';
$activeMenu = 'teachers';

$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$where = "1=1";
$params = [];
$types = "";

if (!empty($statusFilter)) {
    $where .= " AND t.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}
if (!empty($search)) {
    $where .= " AND (t.first_name LIKE ? OR t.last_name LIKE ? OR t.teacher_code LIKE ? OR t.email LIKE ?)";
    $s = "%$search%";
    $params = array_merge($params, [$s, $s, $s, $s]);
    $types .= "ssss";
}

$sql = "SELECT t.* FROM teachers t WHERE $where ORDER BY t.created_at DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$teachers = $stmt->get_result();

include '../../includes/header.php';
?>

<div id="wrapper">
    <?php include '../../includes/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <?php include '../../includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>All Teachers</h1>
                <a href="add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Teacher</a>
            </div>
            <?php showFlashMessage(); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search" value="<?php echo $search; ?>" placeholder="Search by name, code, email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <?php foreach(['active','inactive','on_leave','retired'] as $st): ?>
                                <option value="<?php echo $st; ?>" <?php echo $statusFilter==$st?'selected':''; ?>><?php echo ucfirst(str_replace('_',' ',$st)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter me-1"></i>Filter</button>
                            <a href="index.php" class="btn btn-secondary"><i class="fas fa-undo me-1"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover datatable mb-0">
                            <thead><tr><th>Photo</th><th>Code</th><th>Name</th><th>Gender</th><th>Phone</th><th>Qualification</th><th>Experience</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php while($t=$teachers->fetch_assoc()): ?>
                                <tr>
                                    <td><img src="../../assets/uploads/teachers/<?php echo $t['photo']; ?>" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" onerror="this.src='../../assets/images/default-user.png'"></td>
                                    <td class="font-monospace fw-bold"><?php echo $t['teacher_code']; ?></td>
                                    <td><?php echo $t['first_name'].' '.$t['last_name']; ?><div class="small text-muted"><?php echo $t['email']; ?></div></td>
                                    <td><?php echo $t['gender']; ?></td>
                                    <td><?php echo $t['phone']; ?></td>
                                    <td><?php echo $t['qualification']; ?></td>
                                    <td><?php echo $t['experience_years']; ?> years</td>
                                    <td><?php echo getStatusBadge($t['status']); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo $t['teacher_id']; ?>" class="btn btn-sm btn-info btn-action"><i class="fas fa-eye"></i></a>
                                        <a href="edit.php?id=<?php echo $t['teacher_id']; ?>" class="btn btn-sm btn-primary btn-action"><i class="fas fa-edit"></i></a>
                                        <a href="delete.php?id=<?php echo $t['teacher_id']; ?>" class="btn btn-sm btn-danger btn-action btn-delete"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($teachers->num_rows==0): ?>
                                <tr><td colspan="9" class="text-center py-4 text-muted">No teachers found</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
       
    </div>
</div>
 <?php include '../../includes/footer.php'; ?>