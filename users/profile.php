<?php

/*
 * We're going to include our session
 * controller to check for an active
 * session.
 */
include '../common/session.php';

/*
 * We're going to include our header which
 * is going to be common throughout our
 * entire application.
 */
include '../common/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update') {
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        if ($query = $mysql->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?")) {
            if ($query->bind_param("si", $password, $_SESSION['user']['id'])) {
                $query->execute();
            }
        }
    }

    if ($query = $mysql->prepare("UPDATE `users` SET `first_name` = ?, `last_name` = ?, `phone` = ?, `email` = ?, `active` = 1 WHERE `id` = ?")) {
        if ($query->bind_param("ssssi", $_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['email'], $_SESSION['user']['id'])) {
            if ($query->execute()) {
                if ($query->affected_rows === -1) {
                    $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Error occurred when trying to save profile!';
                } elseif ($query->affected_rows === 0) {
                    $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Failed to update profile! Were any changes made?</div>';
                } else {
                    $_SESSION['flash'] = '<div class="alert alert-success" role="alert">Your profile has been updated successfully!</div>';
                }
            } else {
                $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Error occurred when trying to save profile!</div>';
            }
        } else {
            $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Error occurred when trying to save profile!</div>';
        }
    } else {
        $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Error occurred when trying to save profile!</div>';
    }
}

if (!($query = $mysql->prepare("SELECT * FROM users WHERE id = ?"))) {
    $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Error occurred when trying to prepare query!</div>';
} else {
    if (!$query->bind_param("i", $_SESSION['user']['id'])) {
        $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Error occurred when trying to bind parameters to query!</div>';
    } else {
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows === 0) {
            $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Unable to find your profile as referenced!</div>';
        } else {
            $user = $result->fetch_assoc();
        }
    }
}

?>

<div class="header">
    <div class="row">
        <div class="col-md-6">
            <h1><i class="fa fa-user"></i> My Profile</h1>
        </div>
        <div class="col-md-6">
            <div class="float-right"></div>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['flash'])) echo $_SESSION['flash']; unset($_SESSION['flash']); ?>

<?php if (isset($user)) { ?>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="box">
                <div class="box-body">
                    <form action="" method="post">
                        <input name="action" value="update" type="hidden">

                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input name="first_name" type="text" class="form-control" id="first_name" aria-describedby="firstnameHelp" placeholder="First Name" value="<?php echo $user['first_name']; ?>">
                            <small id="firstnameHelp" class="form-text text-muted">Enter the users first name.</small>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input name="last_name" type="text" class="form-control" id="last_name" aria-describedby="lastnameHelp" placeholder="Last Name" value="<?php echo $user['last_name']; ?>">
                            <small id="lastnameHelp" class="form-text text-muted">Enter the users last name.</small>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input name="phone" type="text" class="form-control" id="phone" aria-describedby="phoneHelp" placeholder="(000) 000-0000" value="<?php echo $user['phone']; ?>">
                            <small id="phoneHelp" class="form-text text-muted">Enter the interviewees phone number in the proper format. <span class="text-info">Formatting happens automatically!</span></small>
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail Address</label>
                            <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="username@domain.com" value="<?php echo $user['email']; ?>">
                            <small id="emailHelp" class="form-text text-muted">Enter the users e-mail address.</small>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input name="password" type="password" class="form-control" id="password" aria-describedby="passwordHelp" placeholder="Password">
                            <small id="passwordHelp" class="form-text text-muted">Enter a password for the user. <span class="text-danger">LEAVE BLANK TO NOT CHANGE!</span></small>
                        </div>

                        <button type="submit" class="btn btn-block btn-primary"><i class="fas fa-save"></i> Save Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#phone').mask('(000) 000-0000', {placeholder: "(000) 000-0000"});
        });
    </script>
<?php } ?>

<?php

/*
 * Here, we're including our footer which
 * is going to be common throughout our
 * entire application just like the header.
 */
include '../common/footer.php';

?>
