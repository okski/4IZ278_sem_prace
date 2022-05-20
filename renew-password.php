<?php
//načteme připojení k databázi a inicializujeme session
require_once 'application/inc/user.php';

if (!empty($_SESSION['IdUser'])){
    //uživatel už je přihlášený, nemá smysl, aby se přihlašoval znovu
    header('Location: index.php');
    exit();
}

$invalidCode=false;
$invalidPassword=false;

if (!empty($_REQUEST) && !empty($_REQUEST['code'])){
    #region kontrola, jestli se daný kód shoduje s údaji v databázi
    $query=$db->prepare('SELECT * FROM hosj03.forgotten_password WHERE forgotten_password_id=:id AND code=:code AND IdUser=:user LIMIT 1;');
    $query->execute([
        ':user'=>$_REQUEST['user'],
        ':code'=>$_REQUEST['code'],
        ':id'=>$_REQUEST['request'],
    ]);
    if ($existingRequest=$query->fetch(PDO::FETCH_ASSOC)){
        //zkontrolujeme, jestli je kód ještě platný
        if (strtotime($existingRequest['created'])<(time()-3600)){//kontrola, jestli není kód starší než 24 hodin
            $invalidCode=true;
        }
    }else{
        $invalidCode=true;
    }
    #endregion kontrola, jestli se daný kód shoduje s údaji v databázi

    if (!empty($_POST) && !$invalidCode){
        #region změna zapomenutého hesla
        //kontrola dat z formuláře
        if (empty($_POST['password']) || (strlen($_POST['password'])<5)){
            $invalidPassword='Musíte zadat heslo o délce alespoň 5 znaků.';
        }
        if ($_POST['password']!=$_POST['password2']){
            $invalidPassword='Zadaná hesla se neshodují.';
        }

        //uložení dat
        if (!$invalidPassword){
            //aktualizujeme heslo u daného uživatele
            $saveQuery=$db->prepare('UPDATE hosj03.user SET Password=:password WHERE IdUser=:user LIMIT 1;');
            $saveQuery->execute([
                ':user'=>$existingRequest['IdUser'],
                ':password'=>password_hash($_POST['password'], PASSWORD_DEFAULT)
            ]);

            //smažeme požadavky na obnovu hesla
            $forgottenDeleteQuery=$db->prepare('DELETE FROM hosj03.forgotten_password WHERE IdUser=:user;');
            $forgottenDeleteQuery->execute([':user'=>$existingRequest['IdUser']]);

            //načteme údaje o aktuálním uživateli
            $userQuery=$db->prepare('SELECT * FROM hosj03.user WHERE IdUser=:user LIMIT 1;');
            $userQuery->execute([
                ':user'=>$existingRequest['IdUser']
            ]);
            $user=$userQuery->fetch(PDO::FETCH_ASSOC);

            //uživatele rovnou přihlásíme
            $_SESSION['IdUser']=$user['IdUser'];
            $_SESSION['Username']=$user['Username'];

            //přesměrování na homepage
            header('Location: index.php');
            exit();
        }
        #endregion změna zapomenutého hesla
    }
}

//vložíme do stránek hlavičku
include __DIR__.'/application/inc/header.php';

echo '<div class="renew"><h2>Obnova zapomenutého hesla</h2>';

if ($invalidCode){
    echo '<p class="text-danger">Kód pro obnovu hesla již není platný.</p>';
    echo '<a href="index.php" class="btn btn-light">zpět na homepage</a>';
}else{
    echo '<form method="post">
            <div class="form-group">
              <label for="password">Nové heslo:</label>
              <input type="password" name="password" id="password" required class="form-control '.($invalidPassword?'is-invalid':'').'" />
              '.($invalidPassword?'<div class="invalid-feedback">'.$invalidPassword.'</div>':'').'
            </div>
            <div class="form-group">
              <label for="password2">Potvrzení hesla:</label>
              <input type="password" name="password2" id="password2" required class="form-control '.($invalidPassword?'is-invalid':'').'" />
            </div>
            
            <input type="hidden" name="code" value="'.htmlspecialchars($_REQUEST['code']).'" />
            <input type="hidden" name="user" value="'.htmlspecialchars($_REQUEST['user']).'" />
            <input type="hidden" name="request" value="'.htmlspecialchars($_REQUEST['request']).'" />
            
            <button type="submit" class="btn btn-primary">změnit heslo</button>
            <a href="index.php" class="btn btn-light">zrušit</a>
          </form></div>';
}

//vložíme do stránek patičku
include __DIR__.'/application/inc/footer.php';