<?php
//načteme připojení k databázi a inicializujeme session
require_once 'application/inc/user.php';

if (!empty($_SESSION['IdUser'])){
    //uživatel už je přihlášený, nemá smysl, aby se přihlašoval znovu
    header('Location: index.php');
    exit();
}

$errors=false;
if (!empty($_POST)){
    #region zpracování formuláře
    $userQuery=$db->prepare('SELECT * FROM hosj03.user WHERE Email=:Email LIMIT 1;');
    $userQuery->execute([
        ':Email'=>trim($_POST['Email'])
    ]);
    if ($user=$userQuery->fetch(PDO::FETCH_ASSOC)){

        if (password_verify($_POST['Password'],$user['Password'])){
            //heslo je platné => přihlásíme uživatele
            $_SESSION['IdUser']=$user['IdUser'];
            $_SESSION['Username']=$user['name'];

            //smažeme požadavky na obnovu hesla
            $forgottenDeleteQuery=$db->prepare('DELETE FROM hosj03.forgotten_passwords WHERE IdUser=:user;');
            $forgottenDeleteQuery->execute([':user'=>$user['IdUser']]);

            header('Location: index.php');
            exit();
        }else{
            $errors=true;
        }

    }else{
        $errors=true;
    }
    #endregion zpracování formuláře
}

//vložíme do stránek hlavičku
include __DIR__.'/application/inc/header.php';
?>

<div class="login">
    <h2>Přihlášení uživatele</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="Email" id="email" required class="form-control <?php echo ($errors?'is-invalid':''); ?>" value="<?php echo htmlspecialchars(@$_POST['email'])?>"/>
            <?php
            echo ($errors?'<div class="invalid-feedback">Neplatná kombinace přihlašovacího e-mailu a hesla.</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="Password" id="password" required class="form-control <?php echo ($errors?'is-invalid':''); ?>" />
        </div>
        <button type="submit" class="btn btn-primary">přihlásit se</button>
        <a href="forgotten-password.php" class="btn btn-light">zapomněl(a) jsem heslo</a>
        <a href="registration.php" class="btn btn-light">registrovat se</a>
        <a href="index.php" class="btn btn-light">zrušit</a>
    </form>
</div>


<?php
//vložíme do stránek patičku
include __DIR__.'/application/inc/footer.php';