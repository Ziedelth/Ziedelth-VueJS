<?php

class User extends JObject
{
    public int $id;
    public string $timestamp;
    public string $pseudo;
    public int $emailVerified = 0;
    public int $role = 0;
    public ?string $image;
    public ?string $about;
    protected string $email;
    protected string $saltPassword;
}

class UserMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('ziedelth.users', 'User');
    }

    function registerUser(?PDO $pdo, string $email, string $pseudo, string $password): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return new JSONResponse(400, array('error' => 'Bad email format'));

        if ($this->isEmailExists($pdo, $email))
            return new JSONResponse(409, array('error' => 'Email already exists'));

        if (strlen($pseudo) < 4 || strlen($pseudo) > 16)
            return new JSONResponse(400, array('error' => 'Bad pseudo format'));

        if ($this->isPseudoExists($pdo, $pseudo))
            return new JSONResponse(409, array('error' => 'Pseudo already exists'));

        $salt = Utils::generateRandomString();
        $pdo->beginTransaction();
        $request = $pdo->prepare("INSERT INTO $this->tableName VALUES (NULL, CURRENT_TIMESTAMP, :email, :pseudo, :password, 0, 0, NULL, NULL)");
        $request->execute(array('email' => $email, 'pseudo' => $pseudo, 'password' => "$salt|" . hash('sha512', "$salt$password")));
        $id = $pdo->lastInsertId();
        $hash = Utils::generateRandomString(15);
        $request = $pdo->prepare("INSERT INTO ziedelth.actions VALUES (NULL, CURRENT_TIMESTAMP, :userId, :hash, :action)");
        $request->execute(array('userId' => $id, 'hash' => $hash, 'action' => 'VERIFY_EMAIL'));

        $body = '<div style="margin: 0;">
    <div style="display: flex">
        <img src="https://ziedelth.fr/images/favicon.jpg" style="width: 64px; border-radius: 8px" alt="Icon">

        <div style="margin-left: 0.5rem">
            <p style="margin-bottom: 0; font-weight: bold">' . $pseudo . ',</p>
            <p style="margin-top: 0">Merci de votre inscription sur <b>Ziedelth.fr !</b></p>
        </div>
    </div>

    <div style="margin-top: 1vh">
        <p style="margin-top: 0; margin-bottom: 10px">Veuillez cliquez sur le lien suivant pour terminer votre inscription :</p>
        <a href="https://ziedelth.fr/a/' . $hash . '" style="text-decoration: underline; text-decoration-color: black; color: black">Confirmer mon inscription</a>

        <p style="margin-bottom: 0">Votre inscription ne sera effective que si vous cliquez sur le lien de confirmation ci-dessus.</p>
        <i>Vous ne pourrez vous connecter que lorsque votre adresse mail sera confirmée.</i>

        <p style="margin-bottom: 0">Cordialement,</p>
        <p style="margin-top: 0">Ziedelth.fr</p>
        <div>
            <i>Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce mail.</i>
            <br>
            <i>Cette action n\'est valable que 10 minutes.</i>
        </div>
        
        <div style="margin-top: 0.5vh"><i>Ce mail est envoyé automatiquement, merci de ne pas y répondre.</i></div>
    </div>
</div>';

        if (!Utils::sendEmail("Inscription sur Ziedelth.fr", $body, $email)) {
            $pdo->rollBack();
            return new JSONResponse(500, array('error' => 'Can not send confirmation email'));
        }

        $pdo->commit();
        return new JSONResponse(201, array('success' => 'OK'));
    }

    function deleteOldActions(?PDO $pdo)
    {
        $request = $pdo->prepare("SELECT * FROM ziedelth.actions WHERE timestamp < NOW() - INTERVAL 10 MINUTE");
        $request->execute(array());
        $objects = $request->fetchAll(PDO::FETCH_ASSOC);

        foreach ($objects as $object) {
            switch ($object['action']) {
                case 'VERIFY_EMAIL':
                    $request = $pdo->prepare("DELETE FROM $this->tableName WHERE id = :userId");
                    $request->execute(array('userId' => $object['user_id']));
                    break;
                default:
                    $request = $pdo->prepare("DELETE FROM ziedelth.actions WHERE id = :id");
                    $request->execute(array('id' => $object['id']));
                    break;
            }
        }
    }

    function isEmailExists(?PDO $pdo, string $email): bool
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE email = :email");
        $request->execute(array('email' => $email));
        return $request->rowCount() > 0;
    }

    function isPseudoExists(?PDO $pdo, string $pseudo): bool
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        return $request->rowCount() > 0;
    }

    function loginUser(?PDO $pdo, string $pseudo, string $password): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (strlen($pseudo) < 4 || strlen($pseudo) > 16)
            return new JSONResponse(400, array('error' => 'Bad pseudo format'));

        if (!$this->isPseudoExists($pdo, $pseudo))
            return new JSONResponse(404, array('error' => 'Pseudo doesn\'t exists'));

        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $user = $request->fetchObject($this->className);

        $reflectionClass = new ReflectionClass($user);
        $reflectionProperty = $reflectionClass->getProperty('saltPassword');
        $reflectionProperty->setAccessible(true);

        $exploded = explode('|', $reflectionProperty->getValue($user));
        $salt = $exploded[0];
        $saltPassword = hash('sha512', "$salt$password");

        if ($saltPassword != $exploded[1])
            return new JSONResponse(400, array('error' => 'Invalid credentials'));

        if ($user->emailVerified != 1)
            return new JSONResponse(400, array('error' => 'Email is not verified'));

        $token = Utils::generateRandomString(100);
        $request = $pdo->prepare("SELECT id FROM ziedelth.tokens WHERE user_id = :userId");
        $request->execute(array('userId' => $user->id));
        $count = $request->rowCount();

        if ($count == 0)
            $request = $pdo->prepare("INSERT INTO ziedelth.tokens VALUES (NULL, CURRENT_TIMESTAMP, :userId, :token)");
        else
            $request = $pdo->prepare("UPDATE ziedelth.tokens SET token = :token WHERE user_id = :userId");
        $request->execute(array('userId' => $user->id, 'token' => $token));

        return new JSONResponse(200, array('token' => $token));
    }

    function action(?PDO $pdo, string $hash): JSONResponse
    {
        $this->deleteOldActions($pdo);

        $request = $pdo->prepare("SELECT * FROM ziedelth.actions WHERE hash = :hash");
        $request->execute(array('hash' => $hash));
        $count = $request->rowCount();

        if ($count != 1)
            return new JSONResponse(400, array('error' => 'Can not find action'));

        $object = $request->fetch(PDO::FETCH_ASSOC);

        switch ($object['action']) {
            case 'VERIFY_EMAIL':
                $request = $pdo->prepare("UPDATE $this->tableName SET email_verified = 1 WHERE email_verified = 0 AND id = :userId");
                $request->execute(array('userId' => $object['user_id']));
                $count = $request->rowCount();

                if ($count != 1)
                    return new JSONResponse(400, array('error' => 'Email already confirmed or hash invalid'));

                $request = $pdo->prepare("DELETE FROM ziedelth.actions WHERE id = :id");
                $request->execute(array('id' => $object['id']));
                break;
            case 'PASSWORD_RESET':
                break;
            default:
                return new JSONResponse(400, array('success' => 'Invalid action'));
        }

        return new JSONResponse(200, array('object' => $object, 'success' => 'OK'));
    }

    function passwordReset(?PDO $pdo, string $email): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (!$this->isEmailExists($pdo, $email))
            return new JSONResponse(400, array('error' => 'Email does not exists'));

        $user = $this->getUserByEmail($pdo, $email);
        $hash = Utils::generateRandomString(15);
        $pdo->beginTransaction();
        $request = $pdo->prepare("INSERT INTO ziedelth.actions VALUES (NULL, CURRENT_TIMESTAMP, :userId, :hash, :action)");
        $request->execute(array('userId' => $user->id, 'hash' => $hash, 'action' => 'PASSWORD_RESET'));

        $body = '<div style="margin: 0;">
    <div style="display: flex">
        <img src="https://ziedelth.fr/images/favicon.jpg" style="width: 64px; border-radius: 8px" alt="Icon">

        <div style="margin-left: 0.5rem">
            <p style="margin-bottom: 0; font-weight: bold">' . $user->pseudo . ',</p>
            <p style="margin-top: 0">Nous avons compris votre oublie de mot de passe sur <b>Ziedelth.fr</b></p>
        </div>
    </div>

    <div style="margin-top: 1vh">
        <p style="margin-top: 0; margin-bottom: 10px">Veuillez cliquez sur le lien suivant pour pouvoir changer votre mot de passe :</p>
        <a href="https://ziedelth.fr/action/' . $hash . '" style="text-decoration: underline; text-decoration-color: black; color: black">Mot de passe oublié</a>

        <p style="margin-bottom: 0">Cordialement,</p>
        <p style="margin-top: 0">Ziedelth.fr</p>
        <div>
            <i>Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer ce mail.</i>
            <br>
            <i>Cette action n\'est valable que 10 minutes.</i>
        </div>
        
        <div style="margin-top: 0.5vh"><i>Ce mail est envoyé automatiquement, merci de ne pas y répondre.</i></div>
    </div>
</div>';

        if (!Utils::sendEmail("Mot de passe oublié", $body, $email)) {
            $pdo->rollBack();
            return new JSONResponse(500, array('error' => 'Can not send email'));
        }

        $pdo->commit();
        return new JSONResponse(200, array('success' => 'OK'));
    }

    function getUserByEmail(?PDO $pdo, string $email): ?User
    {
        $this->deleteOldActions($pdo);

        if (!$this->isEmailExists($pdo, $email))
            return null;

        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE email = :email");
        $request->execute(array('email' => $email));
        return $request->fetchObject($this->className);
    }

    function getUserByPseudo(?PDO $pdo, string $pseudo): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (!$this->isPseudoExists($pdo, $pseudo))
            return new JSONResponse(404, array('error' => 'Pseudo doesn\'t exists'));

        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $user = $request->fetchObject($this->className);

        if ($user->emailVerified != 1)
            return new JSONResponse(400, array('error' => 'Email is not verified'));

        return new JSONResponse(200, $user);
    }

    function updateMemberImage(?PDO $pdo, string $token, $file): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (!$this->isTokenExists($pdo, $token))
            return new JSONResponse(404, array('error' => 'Token doesn\'t exists'));

        $request = $pdo->prepare("SELECT u.* FROM $this->tableName u INNER JOIN ziedelth.tokens t ON t.user_id = u.id WHERE token = :token");
        $request->execute(array('token' => $token));
        $user = $request->fetchObject($this->className);

        $ext_array = $user->role >= 100 ? array('png', 'jpg', 'jpeg', 'gif') : array('png', 'jpg', 'jpeg');
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (!in_array($ext, $ext_array))
            return new JSONResponse(500, array('error' => 'Invalid extension'));

        if ($file['size'] > 2 * 1024 * 1024)
            return new JSONResponse(500, array('error' => 'Invalid size for file, max: 2 MiB'));

        if ($file['error'] != UPLOAD_ERR_OK)
            return new JSONResponse(500, array('error' => $file['error']));

        $localFolder = 'images/members';
        $folder = "/var/www/html/";
//        $folder = "C:/Users/watte/OneDrive/Documents/Developpement/Vue/ziedelth/public/";
        $sfolder = "$folder$localFolder";

        if (!file_exists($sfolder))
            mkdir($sfolder);

        $key = Utils::generateRandomString(50);
        $a = "$key.$ext";

        if (!move_uploaded_file($file['tmp_name'], "$sfolder/$a"))
            return new JSONResponse(500, array('error' => 'Can not move uploaded file'));

        if ($user->image != null && file_exists("$folder/$user->image"))
            unlink("$folder/$user->image");

        $request = $pdo->prepare("UPDATE $this->tableName SET image = :image WHERE id = :id");
        $request->execute(array('image' => "$localFolder/$a", 'id' => $user->id));

        return $this->getUserByToken($pdo, $token);
    }

    function isTokenExists(?PDO $pdo, string $token): bool
    {
        $request = $pdo->prepare("SELECT * FROM ziedelth.tokens WHERE token = :token");
        $request->execute(array('token' => $token));
        return $request->rowCount() > 0;
    }

    function getUserByToken(?PDO $pdo, string $token): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (!$this->isTokenExists($pdo, $token))
            return new JSONResponse(404, array('error' => 'Token doesn\'t exists'));

        $request = $pdo->prepare("SELECT u.* FROM $this->tableName u INNER JOIN ziedelth.tokens t ON t.user_id = u.id WHERE token = :token");
        $request->execute(array('token' => $token));
        $user = $request->fetchObject($this->className);

        if ($user->emailVerified != 1)
            return new JSONResponse(400, array('error' => 'Email is not verified'));

        return new JSONResponse(200, $user);
    }

    function updateMemberAbout(?PDO $pdo, string $token, ?string $about): JSONResponse
    {
        $this->deleteOldActions($pdo);

        if (!$this->isTokenExists($pdo, $token))
            return new JSONResponse(404, array('error' => 'Token doesn\'t exists'));

        $request = $pdo->prepare("SELECT u.* FROM $this->tableName u INNER JOIN ziedelth.tokens t ON t.user_id = u.id WHERE token = :token");
        $request->execute(array('token' => $token));
        $user = $request->fetchObject($this->className);

        $request = $pdo->prepare("UPDATE $this->tableName SET about = :about WHERE id = :id");
        $request->execute(array('about' => $about, 'id' => $user->id));

        return $this->getUserByToken($pdo, $token);
    }
}