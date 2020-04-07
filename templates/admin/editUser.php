<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

<?php
    print_r($_POST);
?>

<h1><?php echo $results['pageTitle']?></h1>

<form action="admin.php?action=<?php echo $results['formAction']?>" method="post">
    <!-- Обработка формы будет направлена файлу admin.php ф-ции newCategory либо editCategory в зависимости от formAction, сохранённого в result-е -->
    <input type="hidden" name="userId" value="<?php echo $results['user']->id ?>"/>

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
        <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>

    <ul>

        <li>
            <label for="login">Login</label>
            <input type="text" name="login" id="login" placeholder="Login" required autofocus maxlength="60" value="<?php echo htmlspecialchars( $results['user']->login )?>" />
        </li>

        <li>
            <label for="password">Set New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" maxlength="60" />
        </li>
        <li>
            <label for="blocked">Blocked status: </label>
            <input type="checkbox" name="blocked" id="blocked" <?php if (($results['user']->blocked)) echo 'checked' ?>>
        </li>

    </ul>

    <div class="buttons">
        <input type="submit" name="saveChanges" value="Save Changes" />
        <input type="submit" formnovalidate name="cancel" value="Cancel" />
    </div>

</form>

<?php if ( $results['user']->id ) { ?>
    <p><a href="admin.php?action=deleteUser&amp;userId=<?php echo $results['user']->id ?>" onclick="return confirm('Delete This User?')">Delete This User</a></p>
<?php } ?>

<?php include "templates/include/footer.php" ?>

