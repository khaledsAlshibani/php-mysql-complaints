<?php
$user = new User();
$complaint = new Complaint();
?>
<section class="mb-5">
    <div class="d-flex gap-4 align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Complaints</h2>
        <?php if ($user->getUserType() == 'user') : ?>
            <a href="<?php echo BASE_URL; ?>templates/pages/create.php" class="btn btn-outline-primary">New Complaint</a>
        <?php endif; ?>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        $i = 1;

        if ($user->getUserType() == 'admin') {
            $complaints = $complaint->getAllComplaints();
        } else {
            $complaints = $complaint->getComplaintsByUser($user->getUserId());
        }

        foreach ($complaints as $complaintItem) : ?>
            <div class="col mb-2">
                <div class="card d-flex flex-column h-100 border-gray rounded-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $complaintItem['title']; ?></h5>
                        <p class="card-text"><?php echo $complaintItem['description']; ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php if ($user->getUserType() == 'admin') : ?>
                            <li class="list-group-item border-top-0 border-start-0 border-end-0">
                                <strong>By: </strong>
                                <span class="text-capitalize"><?php echo $user->getFullNameById($complaintItem['user_id']); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item border-top-0 border-start-0 border-end-0">
                            <strong>Status: </strong>
                            <span class="badge <?php echo $complaintItem['status'] ? 'bg-success text-white' : 'bg-warning text-white'; ?>">
                                <?php echo $complaintItem['status'] ? 'Resolved' : 'Pending'; ?>
                            </span>
                        </li>
                        <li class="list-group-item border-top-0 border-start-0 border-end-0">
                            <strong>Feedback: </strong>
                            <?php if ($complaintItem['feedback']) : ?>
                                <?php echo $complaintItem['feedback']; ?>
                            <?php else : ?>
                                No feedback added
                            <?php endif; ?>
                        </li>
                    </ul>
                    <div class="card-body d-flex align-items-end">
                        <!-- Admin Buttons -->
                        <?php if ($user->getUserType() == 'admin') : ?>
                            <?php if (!$complaintItem['feedback']) : ?>
                                <a class="btn btn-success me-2" href="<?php echo BASE_URL; ?>templates/pages/complaint-feedback.php?id=<?php echo $complaintItem['id']; ?>">Add Feedback</a>
                            <?php else : ?>
                                <a class="btn btn-primary me-2" href="<?php echo BASE_URL; ?>templates/pages/complaint-feedback.php?id=<?php echo $complaintItem['id']; ?>">Update Feedback</a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#compFeedbackDeleteModal<?php echo $complaintItem['id']; ?>">Delete Feedback</button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Users Buttons -->
                        <?php if ($user->getUserType() == 'user') : ?>
                            <a class="btn btn-primary me-2" href="<?php echo BASE_URL; ?>templates/pages/update-complaint.php?id=<?php echo $complaintItem['id']; ?>">Update</a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#compDeleteModal<?php echo $complaintItem['id']; ?>">Delete</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Delete Modals -->
            <?php
            if ($user->getUserType() == 'user') {
                include(MODALS_DIR . 'delete-complaint-modal.php');
            } elseif ($user->getUserType() == 'admin') {
                include(MODALS_DIR . 'delete-complaint-feedback-modal.php');
            }
            ?>
        <?php endforeach; ?>
    </div>
</section>