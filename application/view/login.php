<?php
//načteme připojení k databázi a inicializujeme session
require_once __DIR__ . '/../inc/user.php';
require_once __DIR__ . '/../inc/facebook.php';


if (!empty($_SESSION['IdUser'])){
    //uživatel už je přihlášený, nemá smysl, aby se přihlašoval znovu
    header('Location: ../../index.php');
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

        if (password_verify($_POST['password'],$user['Password'])){
            //heslo je platné => přihlásíme uživatele
            $_SESSION['IdUser']=$user['IdUser'];
            $_SESSION['Username']=$user['Username'];
            $_SESSION['Admin']=$user['Admin'];

            //smažeme požadavky na obnovu hesla
//            $forgottenDeleteQuery=$db->prepare('DELETE FROM hosj03.forgotten_passwords WHERE IdUser=:user;');
//            $forgottenDeleteQuery->execute([':user'=>$user['IdUser']]);

            header('Location: ../../index.php');
            exit();
        }else{
            $errors=true;
        }

    }else{
        $errors=true;
    }
    #endregion zpracování formuláře
}

$fbHelper = $fb->getRedirectLoginHelper();

//nastavení parametrů pro vyžádání oprávnění a odkaz na přesměrování po přihlášení
$permissions = ['email'];
$callbackUrl = htmlspecialchars('https://eso.vse.cz/~hosj03/sem_prace/fb-callback.php');

$fbLoginUrl = $fbHelper->getLoginUrl($callbackUrl, $permissions);


include __DIR__ . '/../inc/header.php';
?>

<div class="login">
    <h2>User login</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="Email" id="email" required class="form-control <?php echo ($errors?'is-invalid':''); ?>" value="<?php echo htmlspecialchars(@$_POST['email'])?>"/>
            <?php
            echo ($errors?'<div class="invalid-feedback">Combination of this email and password is incorrect.</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo ($errors?'is-invalid':''); ?>" />
        </div>
        <button type="submit" class="btn btn-primary">login</button>
        <a href="'.<?php echo $fbLoginUrl?>.'" class="btn btn-primary">přihlásit se pomocí Facebooku</a>
        <a href="forgotten-password.php" class="btn btn-light">forgot password</a>
        <a href="registration.php" class="btn btn-light">register</a>
        <a href="../../index.php" class="btn btn-light">cancel</a>
    </form>
</div>


<?php
include __DIR__ . '/../inc/footer.php';
