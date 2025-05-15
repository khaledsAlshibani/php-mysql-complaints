<?php
require_once '../../config.php';

$user = new User();
$complaint = new Complaint();
$user->requireLogin();

$complaint_id = Req::retrieveGetValue('id');
if (!$complaint_id) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

$complaintData = $complaint->getComplaintById($complaint_id);
if (!$complaintData) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once INC_DIR . 'head-include.php' ?>
    <title>Update Complaint</title>
</head>

<body class="bg-light">
    <div class="mb-4">
        <?php
        $navbar = true;
        include(TEMPLATES_DIR . 'components/header.php');
        ?>

        <div class="container">
            <div class="row justify-content-center align-items-center" style="margin-top: 100px; margin-bottom: 100px;">
                <div class="col-md-6">
                    <h2 class="mb-4 fw-bold" style="max-width: 80%;">Update Complaint</h2>

                    <form method="post" action="<?php echo BASE_URL; ?>core/process.php">
                        <input type="hidden" name="complaint_id" value="<?php echo Utility::escape($complaint_id); ?>">
                        <div class="input-group mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control rounded-2" id="title" placeholder="Title" name="complaint_title" value="<?php echo Utility::escape($complaintData['title']); ?>" required>
                                <label for="title">Title</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-floating">
                                <textarea class="form-control" id="description" name="complaint_description" placeholder="Enter description" rows="5" style="min-height: 150px;" required><?php echo Utility::escape($complaintData['description']); ?></textarea>
                                <label for="description">Description</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block rounded-2">Update Complaint</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once INC_DIR . 'foot-include.php' ?>
</body>

</html>
