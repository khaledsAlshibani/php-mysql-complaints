<?php
require_once '../../config.php';

$user = new User();
$suggestion = new Suggestion();
$user->requireLogin();

$suggestion_id = Req::retrieveGetValue('id');
if (!$suggestion_id) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

$suggestionData = $suggestion->getSuggestionById($suggestion_id);
if (!$suggestionData) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once INC_DIR . 'head-include.php' ?>
    <title>Add Feedback</title>
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
                    <h2 class="mb-4" style="max-width: 80%;"><?php echo $suggestionData['feedback'] ? 'Update' : 'Add' ?> Feedback For The Suggestion: <span class="fw-bold">"<?php echo Utility::escape($suggestionData['title']); ?>"</span></h2>

                    <form method="post" action="<?php echo BASE_URL; ?>core/process.php">
                        <input type="hidden" name="suggestion_id" value="<?php echo Utility::escape($suggestion_id); ?>">
                        
                        <div class="mb-3">
                            <div class="form-floating">
                                <textarea class="form-control" id="feedback" name="suggestion_feedback" placeholder="Enter Feedback" rows="5" style="min-height: 150px;" required><?php echo Utility::escape($suggestionData['feedback']); ?></textarea>
                                <label for="feedback">Feedback</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block rounded-2"><?php echo $suggestionData['feedback'] ? 'Update' : 'Add' ?> Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once INC_DIR . 'foot-include.php' ?>
</body>

</html>
