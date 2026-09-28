<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../includes/db.php");

$message = "";
$current_admin_id = (int)$_SESSION['admin_id'];

// =========================================================
// DETERMINE SUPER ADMIN (needed before ADD / UPDATE / DELETE)
// =========================================================
$min_result = mysqli_query($conn, "SELECT MIN(admin_id) AS min_id FROM admins");
$min_row = $min_result ? mysqli_fetch_assoc($min_result) : null;
$super_admin_id = (int)($min_row['min_id'] ?? 0);
$is_super_admin = ($current_admin_id === $super_admin_id);

// =========================================================
// CREATE ADMIN
// =========================================================
if (isset($_POST['add'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$is_super_admin) {
        $message = "<div class='alert alert-danger'><i class='fa fa-ban'></i> Only the super admin can add new admins.</div>";
    } elseif ($full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $message = "<div class='alert alert-danger'><i class='fa fa-circle-exclamation'></i> Please complete all fields and enter a valid email.</div>";
    } else {
        $email_esc = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT admin_id FROM admins WHERE email='$email_esc' LIMIT 1");

        if ($check && mysqli_num_rows($check) > 0) {
            $message = "<div class='alert alert-danger'><i class='fa fa-envelope'></i> This email is already registered to an admin.</div>";
        } else {
            $name_esc = mysqli_real_escape_string($conn, $full_name);
            $pass_esc = mysqli_real_escape_string($conn, $password);
            $sql = "INSERT INTO admins (full_name, ic_number, email, password) VALUES ('$name_esc', '', '$email_esc', '$pass_esc')";

            if (mysqli_query($conn, $sql)) {
                $message = "<div class='alert alert-success'><i class='fa fa-circle-check'></i> Admin successfully added.</div>";
            } else {
                $message = "<div class='alert alert-danger'><i class='fa fa-circle-exclamation'></i> Failed to add admin: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
            }
        }
    }
}

// =========================================================
// UPDATE ADMIN
// =========================================================
if (isset($_POST['update'])) {
    $edit_id = (int)($_POST['admin_id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$is_super_admin && $edit_id !== $current_admin_id) {
        $message = "<div class='alert alert-danger'><i class='fa fa-ban'></i> You can only edit your own admin account.</div>";
    } elseif ($edit_id <= 0 || $full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'><i class='fa fa-circle-exclamation'></i> Please enter a valid name and email.</div>";
    } else {
        $name_esc = mysqli_real_escape_string($conn, $full_name);
        $email_esc = mysqli_real_escape_string($conn, $email);

        $check = mysqli_query($conn, "SELECT admin_id FROM admins WHERE email='$email_esc' AND admin_id!='$edit_id' LIMIT 1");

        if ($check && mysqli_num_rows($check) > 0) {
            $message = "<div class='alert alert-danger'><i class='fa fa-envelope'></i> This email is already used by another admin.</div>";
        } else {
            if ($password !== '') {
                $pass_esc = mysqli_real_escape_string($conn, $password);
                $sql = "UPDATE admins SET full_name='$name_esc', email='$email_esc', password='$pass_esc' WHERE admin_id='$edit_id'";
            } else {
                $sql = "UPDATE admins SET full_name='$name_esc', email='$email_esc' WHERE admin_id='$edit_id'";
            }

            if (mysqli_query($conn, $sql)) {
                $message = "<div class='alert alert-success'><i class='fa fa-circle-check'></i> Admin details updated successfully.</div>";

                if ($edit_id === $current_admin_id) {
                    $_SESSION['admin_name'] = $full_name;
                    $_SESSION['admin_email'] = $email;
                }
            } else {
                $message = "<div class='alert alert-danger'><i class='fa fa-circle-exclamation'></i> Failed to update admin: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
            }
        }
    }
}

// =========================================================
// DELETE ADMIN
// =========================================================
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    if (!$is_super_admin) {
        $message = "<div class='alert alert-danger'><i class='fa fa-ban'></i> Only the super admin can delete admins.</div>";
    } elseif ($delete_id === $current_admin_id) {
        $message = "<div class='alert alert-danger'><i class='fa fa-ban'></i> You cannot delete your own account while logged in.</div>";
    } else {
        $count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM admins");
        $count_row = $count_result ? mysqli_fetch_assoc($count_result) : ['total' => 0];

        if ((int)$count_row['total'] <= 1) {
            $message = "<div class='alert alert-danger'><i class='fa fa-ban'></i> At least one admin account must remain.</div>";
        } else {
            if (mysqli_query($conn, "DELETE FROM admins WHERE admin_id='$delete_id' LIMIT 1")) {
                $message = "<div class='alert alert-success'><i class='fa fa-circle-check'></i> Admin deleted successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'><i class='fa fa-circle-exclamation'></i> Failed to delete admin.</div>";
            }
        }
    }
}

$result = mysqli_query($conn, "SELECT admin_id, full_name, ic_number, email FROM admins ORDER BY admin_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Admins - JPP EMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;font-family:'DM Sans',sans-serif}body{margin:0;background:#f6f4f1;color:#292124;overflow-x:hidden}.main{margin-left:250px;padding:30px;min-height:100vh}.topbar{background:linear-gradient(135deg,#fff,#fbf7ef);border:1px solid #eee6dc;border-radius:22px;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;gap:15px;box-shadow:0 15px 35px rgba(46,0,12,.08);margin-bottom:22px}.topbar h3{margin:0;color:#65001b;font-weight:700}.add-btn,.btn-save{border:0;border-radius:14px;padding:11px 18px;background:#800020;color:#fff;font-weight:800}.admin-notice{display:inline-flex;align-items:center;gap:7px;background:#eef0f2;color:#4f5963;border-radius:10px;padding:8px 11px;font-size:11px;font-weight:600;line-height:1.35;max-width:390px}.add-btn:hover,.btn-save:hover{background:#5c0017;color:#fff;transform:translateY(-1px)}.table-card{background:#fff;border-radius:22px;padding:22px;box-shadow:0 15px 35px rgba(46,0,12,.07);border:1px solid #eee6dc}.table thead th{background:#65001b;color:#fff;border:0;white-space:nowrap}.table tbody td{vertical-align:middle}.table{min-width:620px}.email-cell{font-size:14px;color:#5f5558}.badge-you{display:inline-block;background:#198754;color:#fff;font-size:10px;padding:4px 8px;border-radius:20px;margin-left:5px}.badge-super{display:inline-block;background:#800020;color:#fff;font-size:10px;padding:4px 8px;border-radius:20px;margin-left:5px;font-weight:800}.btn-edit-sm,.btn-delete-sm{display:inline-flex;align-items:center;gap:6px;border-radius:10px;padding:7px 11px;text-decoration:none;border:0;font-size:13px;font-weight:700}.btn-edit-sm{background:#efe8d3;color:#65001b}.btn-delete-sm{background:#f8d7da;color:#842029}.form-control{border-radius:12px;height:46px}.form-control:focus{border-color:#d4af37;box-shadow:0 0 0 3px rgba(212,175,55,.15)}.modal-content{border:0;border-radius:20px;overflow:hidden}.modal-header{background:#65001b;color:#fff}.modal-header .btn-close{filter:invert(1)}.name-cell{display:flex;flex-wrap:wrap;align-items:center;gap:6px;row-gap:4px}.name-text{font-weight:600;word-break:break-word}.badge-wrap{display:inline-flex;flex-wrap:wrap;gap:5px}.badge-you,.badge-super{white-space:nowrap}@media(max-width:991px){.main{margin-left:0;padding:80px 18px 25px}.topbar{align-items:flex-start;flex-direction:column}}@media(max-width:576px){.main{padding:72px 12px 20px}.table-card{padding:12px}.admin-notice{max-width:100%;font-size:10px}.name-cell{flex-direction:column;align-items:flex-start;gap:4px}.badge-you,.badge-super{margin-left:0}.table thead th,.table tbody td{font-size:13px;padding:10px 8px}}
</style>
</head>
<body>
<?php include("admin_menu.php"); ?>
<div class="main">
<div class="topbar">
<div><h3><i class="fa fa-user-shield"></i> Manage Admins</h3><div class="text-muted small mt-1">Admin accounts now use email for login.</div></div>
<?php if($is_super_admin): ?><button class="add-btn" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-user-plus"></i> Add Admin</button><?php else: ?><span class="admin-notice"><i class="fa fa-lock"></i> Super admin only: manage other admins. You can edit your own.</span><?php endif; ?>
</div>
<?= $message ?>
<div class="table-card">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead><tr><th>#</th><th>Full Name</th><th>Email</th><th>Action</th></tr></thead>
<tbody>
<?php if($result && mysqli_num_rows($result)>0): $no=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $no++ ?></td>
<td>
<div class="name-cell">
<span class="name-text"><?= htmlspecialchars($row['full_name']) ?></span>
<span class="badge-wrap">
<?php if((int)$row['admin_id']===$current_admin_id): ?><span class="badge-you">YOU</span><?php endif; ?>
<?php if((int)$row['admin_id']===$super_admin_id): ?><span class="badge-super">SUPER ADMIN</span><?php endif; ?>
</span>
</div>
</td>
<td class="email-cell"><i class="fa fa-envelope text-secondary"></i> <?= htmlspecialchars($row['email'] ?? '') ?></td>
<td>
<?php if($is_super_admin || (int)$row['admin_id']===$current_admin_id): ?>
<button class="btn-edit-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?= (int)$row['admin_id'] ?>" data-name="<?= htmlspecialchars($row['full_name'],ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($row['email'] ?? '',ENT_QUOTES) ?>"><i class="fa fa-pen"></i> Edit</button>
<?php else: ?>
<span class="text-muted small">—</span>
<?php endif; ?>
<?php if($is_super_admin): ?>
<a href="manage_admins.php?delete=<?= (int)$row['admin_id'] ?>" class="btn-delete-sm" onclick="return confirm('Are you sure you want to delete this admin?')"><i class="fa fa-trash"></i> Delete</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="4" class="text-center text-muted py-4">No admin accounts found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<form method="POST">
<div class="modal-header"><h5 class="modal-title"><i class="fa fa-user-plus"></i> Add Admin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="mb-3"><label class="form-label fw-bold">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
<div class="mb-3"><label class="form-label fw-bold">Admin Email</label><input type="email" name="email" class="form-control" placeholder="admin@example.com" required></div>
<div class="mb-3"><label class="form-label fw-bold">Password</label><input type="password" name="password" class="form-control" required></div>
<div class="alert alert-light border small mb-0"><i class="fa fa-circle-info"></i> IC Number is no longer required for admin login.</div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="add" class="btn-save"><i class="fa fa-floppy-disk"></i> Save Admin</button></div>
</form></div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<form method="POST">
<input type="hidden" name="admin_id" id="edit_admin_id">
<div class="modal-header"><h5 class="modal-title"><i class="fa fa-user-pen"></i> Edit Admin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="mb-3"><label class="form-label fw-bold">Full Name</label><input type="text" name="full_name" id="edit_full_name" class="form-control" required></div>
<div class="mb-3"><label class="form-label fw-bold">Admin Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
<div class="mb-3"><label class="form-label fw-bold">New Password</label><input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password"></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="update" class="btn-save"><i class="fa fa-floppy-disk"></i> Save Changes</button></div>
</form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function(event){const b=event.relatedTarget;document.getElementById('edit_admin_id').value=b.getAttribute('data-id');document.getElementById('edit_full_name').value=b.getAttribute('data-name');document.getElementById('edit_email').value=b.getAttribute('data-email');});
</script>
</body>
</html>
