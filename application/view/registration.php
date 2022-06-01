<?php
//načteme připojení k databázi a inicializujeme session
require_once __DIR__ . '/../inc/user.php';

if (!empty($_SESSION['IdUser'])){
    //uživatel už je přihlášený, nemá smysl, aby se registroval
    header('Location: ../../index.php');
    exit();
}

$errors=[];
if (!empty($_POST)){
    #region zpracování formuláře
    #region kontrola jména
    $name=trim(@$_POST['name']);
    if (empty($name)){
        $errors['name']='You have to type your name or nickname.';
    }
    #endregion kontrola jména

    #region kontrola emailu
    $email=trim(@$_POST['email']);
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['name']='You have to type valid email address.';
    }else{
        //kontrola, jestli již není e-mail registrovaný
        $mailQuery=$db->prepare('SELECT * FROM hosj03.user WHERE Email=:email LIMIT 1;');
        $mailQuery->execute([
            ':email'=>$email
        ]);
        if ($mailQuery->rowCount()>0){
            $errors['name']='User account with this email address already exists.';
        }
    }
    #endregion kontrola emailu

    #region kontrola hesla
    if (empty($_POST['password']) || (strlen($_POST['password'])<10)){
        $errors['password']='Minimal length of password is ten characters.';
    }
    if ($_POST['password']!=$_POST['password2']){
        $errors['password2']='Typed passwords are not the same.';
    }
    #endregion kontrola hesla

    if (empty($errors)){
        //zaregistrování uživatele
        $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

        $query=$db->prepare('INSERT INTO hosj03.user (Username, Email, Password) VALUES (:name, :email, :password);');
        $query->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':password'=>$password
        ]);

        //uživatele rovnou přihlásíme
        $_SESSION['IdUser']=$db->lastInsertId();
        $_SESSION['Username']=$name;

        //přesměrování na homepage
        header('Location: ../../index.php');
        exit();
    }
    #endregion zpracování formuláře
}

include __DIR__ . '/../inc/header.php';
?>
<div class="registration">
    <h2>Registration of new user</h2>
    <form method="post">
        <div class="form-group">
            <label for="name">Name or Nickname:</label>
            <input type="text" name="name" id="name" required class="form-control <?php echo (!empty($errors['name'])?'is-invalid':''); ?>"
                   value="<?php echo htmlspecialchars(@$name);?>" />
            <?php
            echo (!empty($errors['name'])?'<div class="invalid-feedback">'.$errors['name'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo (!empty($errors['email'])?'is-invalid':''); ?>"
                   value="<?php echo htmlspecialchars(@$email);?>"/>
            <?php
            echo (!empty($errors['email'])?'<div class="invalid-feedback">'.$errors['email'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo (!empty($errors['password'])?'is-invalid':''); ?>" />
            <?php
            echo (!empty($errors['password'])?'<div class="invalid-feedback">'.$errors['password'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password2">Password confirmation:</label>
            <input type="password" name="password2" id="password2" required class="form-control <?php echo (!empty($errors['password2'])?'is-invalid':''); ?>" />
            <?php
            echo (!empty($errors['password2'])?'<div class="invalid-feedback">'.$errors['password2'].'</div>':'');
            ?>
        </div>
        <button type="submit" class="btn btn-primary">register</button>
        <a href="login.php" class="btn btn-light">login</a>
        <a href="../../index.php" class="btn btn-light">cancel</a>
    </form>
</div>
<?php
include __DIR__ . '/../inc/footer.php';