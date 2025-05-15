<?php
$user = new User();
$suggestion = new Suggestion();
?>
<section class="mb-5">
    <div class="d-flex gap-4 align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Suggestions</h2>
        <?php if ($user->getUserType() == 'user') : ?>
            <a href="<?php echo BASE_URL; ?>templates/pages/create.php" class="btn btn-outline-primary">New Suggestion</a>
        <?php endif; ?>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        $i = 1;

        if ($user->getUserType() == 'admin') {
            $suggestions = $suggestion->getAllSuggestions();
        } else {
            $suggestions = $suggestion->getSuggestionsByUser($user->getUserId());
        }

        foreach ($suggestions as $suggestionItem) : ?>
            <div class="col mb-4">
                <div class="card d-flex flex-column h-100 border-gray rounded-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $suggestionItem['title']; ?></h5>
                        <p class="card-text"><?php echo $suggestionItem['description']; ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php if ($user->getUserType() == 'admin') : ?>
                            <li class="list-group-item border-top-0 border-start-0 border-end-0">
                                <strong>By: </strong>
                                <span class="text-capitalize"><?php echo $user->getFullNameById($suggestionItem['user_id']); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item border-top-0 border-start-0 border-end-0">
                            <strong>Status: </strong>
                            <span class="badge <?php echo $suggestionItem['status'] ? 'bg-success text-white' : 'bg-warning text-white'; ?>">
                                <?php echo $suggestionItem['status'] ? 'Resolved' : 'Pending'; ?>
                            </span>
                        </li>
                        <li class="list-group-item border-top-0 border-start-0 border-end-0">
                            <strong>Feedback: </strong>
                            <?php if (isset($suggestionItem['feedback'])) : ?>
                                <?php echo $suggestionItem['feedback']; ?>
                            <?php else : ?>
                                No feedback added
                            <?php endif; ?>
                        </li>
                    </ul>
                    <div class="card-body d-flex align-items-end">
                        <!-- Admin Buttons -->
                        <?php if ($user->getUserType() == 'admin') : ?>
                            <?php if (!$suggestionItem['feedback']) : ?>
                                <a class="btn btn-success me-2" href="<?php echo BASE_URL; ?>templates/pages/suggestion-feedback.php?id=<?php echo $suggestionItem['id']; ?>">Add Feedback</a>
                            <?php else : ?>
                                <a class="btn btn-primary me-2" href="<?php echo BASE_URL; ?>templates/pages/suggestion-feedback.php?id=<?php echo $suggestionItem['id']; ?>">Update Feedback</a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#sugFeedbackDeleteModal<?php echo $suggestionItem['id']; ?>">Delete Feedback</button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Users Buttons -->
                        <?php if ($user->getUserType() == 'user') : ?>
                            <?php if (!$suggestionItem['status']) : ?>
                                <a class="btn btn-primary me-2" href="<?php echo BASE_URL; ?>templates/pages/update-suggestion.php?id=<?php echo $suggestionItem['id']; ?>">Update</a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#sugDeleteModal<?php echo $suggestionItem['id']; ?>">Delete</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-primary me-2" disabled>Update</button>
                                <button type="button" class="btn btn-danger" disabled>Delete</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php
            if ($user->getUserType() == 'user') {
                include(MODALS_DIR . 'delete-suggestion-modal.php');
            } elseif ($user->getUserType() == 'admin') {
                include(MODALS_DIR . 'delete-suggestion-feedback-modal.php');
            }
            ?>
        <?php endforeach; ?>
    </div>
</section>