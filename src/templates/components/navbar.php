<?php
if ($navbar) :
    $user = new User();
    $userId = $user->getUserId();
?>
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand text-capitalize"><?php echo '👋 Hello, ' . $user->getFullNameById($userId); ?></span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Dashboard</a>
                    <?php if ($user->getUserType() == 'user') : ?>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>templates/pages/create.php">Create</a>
                    <?php endif; ?>
                </div>

                <div class="navbar-nav ms-auto">
                    <a class="btn btn-outline-light" href="<?php echo BASE_URL; ?>logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>