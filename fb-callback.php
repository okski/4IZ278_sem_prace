<?php
  //načteme připojení k databázi a inicializujeme session
  require_once __DIR__ . '/application/inc/user.php';
  //načteme inicializaci knihovny pro Facebook
  require_once  __DIR__ . '/application/inc/facebook.php';

  #region zpracování callbacku z Facebooku
  //inicializujeme helper pro vytvoření odkazu
  $fbHelper = $fb->getRedirectLoginHelper();

  //získáme access token z aktuálního přihlášení
  try {
    $accessToken = $fbHelper->getAccessToken();
  } catch(Exception $e) {
    echo 'Login with Facebook account failed. Error: ' . $e->getMessage();
    exit();
  }

  if (!$accessToken){
    //Nebyl vrácen access token - např. uživatel odmítl oprávnění pro aplikaci atp.
    //Chyby bychom ale rozhodně mohli vypisovat v hezčí formě :)
    exit('Login with Facebook account failed. Try again.');
  }

  //OAuth 2.0 client pro správu access tokenů
  $oAuth2Client = $fb->getOAuth2Client();

  //XXX pokud bychom chtěli více pracovat s daty z Facebooku, byla by zde validace tokenu a jeho změna na dlouhodobý

  //získáme údaje k tokenu, který jsme získali z přihlášení
  $accessTokenMetadata = $oAuth2Client->debugToken($accessToken);

  //získáme ID uživatele z Facebooku
  $fbUserId = $accessTokenMetadata->getUserId();

  //získáme jméno a e-mail uživatele
  $response=$fb->get('/me?fields=name,email', $accessToken);
  $graphUser=$response->getGraphUser();

  $fbUserEmail=$graphUser->getEmail();
  $fbUserName=$graphUser->getName();

  #endregion zpracování callbacku z Facebooku

  #region registrace uživatele v DB a načtení odpovídajících údajů
  //nejprve se pokusíme daného uživatele načíst podle FB User ID
  $query=$db->prepare('SELECT * FROM hosj03.user WHERE FacebookId=:facebookId LIMIT 1;');
  $query->execute([
    ':facebookId'=>$fbUserId
  ]);

  if ($query->rowCount()>0){
    //uživatele jsme našli v DB podle jeho Facebook User ID
    $user = $query->fetch(PDO::FETCH_ASSOC);
  }else{
    //uživatel nebyl nalezen v DB - pokusíme se jej najít pomocí e-mailu
    $query = $db->prepare('SELECT * FROM hosj03.user WHERE email=:email LIMIT 1;');
    $query->execute([
      ':email'=>$fbUserEmail
    ]);

    if ($query->rowCount()>0){
      //uživatele jsme našli podle e-mailu, připíšeme k němu do DB jeho Facebook User ID
      $user = $query->fetch(PDO::FETCH_ASSOC);

      $updateQuery = $db->prepare('UPDATE hosj03.user SET FacebookId=:facebookId WHERE IdUser=:id LIMIT 1;');
      $updateQuery->execute([
        ':facebookId'=>$fbUserId,
        ':id'=>$user['user_id']
      ]);

    }else{
      //uživatele jsme vůbec nenašli, zapíšeme ho do DB jako nového
      $insertQuery = $db->prepare('INSERT INTO hosj03.user (Username, Email, FacebookId) VALUES (:name, :email, :facebookId);');
      $insertQuery->execute([
        ':name'=>$fbUserName,
        ':email'=>$fbUserEmail,
        ':facebookId'=>$fbUserId
      ]);

      //uživatele následně zpětně načteme z DB pro získání jeho user_id
      $query=$db->prepare('SELECT * FROM hosj03.user WHERE FacebookId=:facebookId LIMIT 1;');
      $query->execute([
        ':facebookId'=>$fbUserId
      ]);
      $user=$query->fetch(PDO::FETCH_ASSOC);
    }
  }

  #endregion registrace uživatele v DB a načtení odpovídajících údajů

  #region přihlášení uživatele
  if (!empty($user)){
    //přihlásíme uživatele (uložíme si jeho údaje do session)
    $_SESSION['IdUser']=$user['user_id'];
    $_SESSION['Username']=$user['name'];
  }

  //přesměrujeme uživatele na homepage
  header('Location: index.php');
  #endregion přihlášení uživatele