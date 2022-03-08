<?php

use Slim\Http\UploadedFile;

class MemberMapper
{
    /**
     * @param PDO $pdo
     * @param string|null $pseudo
     * @return bool
     */
    static function pseudoExists(PDO $pdo, ?string $pseudo): bool
    {
        if ($pseudo == null && empty($pseudo))
            return false;

        $request = $pdo->prepare("SELECT id FROM ziedelth.users WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        return $request->rowCount() >= 1;
    }

    /**
     * @param PDO $pdo
     * @param string|null $email
     * @return bool
     */
    static function emailExists(PDO $pdo, ?string $email): bool
    {
        if ($email == null && empty($email))
            return false;

        $request = $pdo->prepare("SELECT id FROM ziedelth.users WHERE email = :email");
        $request->execute(array('email' => $email));
        return $request->rowCount() >= 1;
    }

    static function tokenExists(PDO $pdo, ?string $token): bool
    {
        if ($token == null && empty($token))
            return false;

        $request = $pdo->prepare("SELECT id FROM ziedelth.tokens WHERE token = :token");
        $request->execute(array('token' => $token));
        return $request->rowCount() >= 1;
    }

    /**
     * @param int $length
     * @return string
     */
    static function generateRandomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param PDO $pdo
     * @return void
     */
    static function deleteOldActions(PDO $pdo)
    {
        $request = $pdo->prepare("SELECT * FROM ziedelth.actions WHERE timestamp < NOW() - INTERVAL 10 MINUTE");
        $request->execute(array());
        $objects = $request->fetchAll(PDO::FETCH_ASSOC);

        foreach ($objects as $object) {
            switch ($object['action']) {
                case 'VERIFY_EMAIL':
                    $request = $pdo->prepare("DELETE FROM ziedelth.users WHERE id = :userId");
                    $request->execute(array('userId' => $object['user_id']));
                    break;
                default:
                    $request = $pdo->prepare("DELETE FROM ziedelth.actions WHERE id = :id");
                    $request->execute(array('id' => $object['id']));
                    break;
            }
        }
    }

    /**
     * @param PDO $pdo
     * @return void
     */
    static function deleteOldTokens(PDO $pdo)
    {
        $request = $pdo->prepare("DELETE FROM ziedelth.tokens WHERE timestamp < NOW() - INTERVAL 1 MONTH");
        $request->execute(array());
    }

    /**
     * @param PDO $pdo
     * @return void
     */
    static function deleteOld(PDO $pdo)
    {
        self::deleteOldActions($pdo);
        self::deleteOldTokens($pdo);
    }

    /**
     * @param PDO $pdo
     * @param string $pseudo
     * @param string $email
     * @param string $password
     * @return array
     */
    static function register(PDO $pdo, string $pseudo, string $email, string $password): array
    {
        self::deleteOld($pdo);

        if (self::pseudoExists($pdo, $pseudo))
            return array('error' => "Pseudo already exists");
        if (self::emailExists($pdo, $email))
            return array('error' => "Email already exists");

        if (!preg_match('/^\w{4,16}$/', $pseudo))
            return array('error' => "Pseudo invalid pattern");
        if (!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email))
            return array('error' => "Email invalid pattern");

        $salt = self::generateRandomString(10);
        $pdo->beginTransaction();

        $request = $pdo->prepare("INSERT INTO ziedelth.users VALUES (NULL, CURRENT_TIMESTAMP, :pseudo, :email, :password, 0, 0, NULL, NULL)");
        $request->execute(array('pseudo' => $pseudo, 'email' => $email, 'password' => "$salt|" . hash('sha512', "$salt$password")));
        $id = $pdo->lastInsertId();

        $hash = self::generateRandomString(15);
        $request = $pdo->prepare("INSERT INTO ziedelth.actions VALUES (NULL, CURRENT_TIMESTAMP, :userId, :hash, :action)");
        $request->execute(array('userId' => $id, 'hash' => $hash, 'action' => 'VERIFY_EMAIL'));

        if (!EmailMapper::sendEmail("Inscription sur Ziedelth.fr", EmailTemplate::getEmailRegisterTemplate($pseudo, $hash), $email)) {
            $pdo->rollBack();
            return array('error' => "Can not send email");
        }

        $pdo->commit();
        return array('success' => "OK");
    }

    /**
     * @param PDO $pdo
     * @param string $hash
     * @return string[]
     */
    static function validateAction(PDO $pdo, string $hash): array
    {
        self::deleteOld($pdo);

        $request = $pdo->prepare("SELECT * FROM ziedelth.actions WHERE hash = :hash");
        $request->execute(array('hash' => $hash));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "No action");

        $object = $request->fetch(PDO::FETCH_ASSOC);

        switch ($object['action']) {
            case 'VERIFY_EMAIL':
                $request = $pdo->prepare("UPDATE ziedelth.users SET email_verified = 1 WHERE email_verified = 0 AND id = :userId");
                $request->execute(array('userId' => $object['user_id']));
                $count = $request->rowCount();

                if ($count != 1)
                    return array('error' => "No update");

                $request = $pdo->prepare("DELETE FROM ziedelth.actions WHERE id = :id");
                $request->execute(array('id' => $object['id']));
                break;
            case 'PASSWORD_RESET':
                break;
            default:
                return array('error' => "Invalid action");
        }

        return array('object' => $object, 'success' => 'OK');
    }

    static function getMemberWithPseudo(PDO $pdo, string $pseudo)
    {
        if (!self::pseudoExists($pdo, $pseudo))
            return array('error' => "Pseudo does not exists");

        $request = $pdo->prepare("SELECT timestamp, pseudo, role, image, about FROM ziedelth.users WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        return $request->fetch(PDO::FETCH_ASSOC);
    }

    static function getMemberWithToken(PDO $pdo, string $token)
    {
        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("SELECT u.timestamp, u.pseudo, u.role, u.image, u.about FROM ziedelth.users u INNER JOIN ziedelth.tokens t ON t.user_id = u.id WHERE t.token = :token");
        $request->execute(array('token' => $token));
        return $request->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param PDO $pdo
     * @param string $email
     * @param string $password
     * @return string[]
     */
    static function loginUser(PDO $pdo, string $email, string $password): array {
        self::deleteOld($pdo);

        if (!self::emailExists($pdo, $email))
            return array('error' => "Email does not exists");
        if (!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email))
            return array('error' => "Email invalid pattern");

        $request = $pdo->prepare("SELECT * FROM ziedelth.users WHERE email = :email");
        $request->execute(array('email' => $email));
        $member = $request->fetch(PDO::FETCH_ASSOC);

        if ($member['email_verified'] != 1)
            return array('error' => "Email is not verified");

        $exploded = explode('|', $member['salt_password']);
        $salt = $exploded[0];
        $saltPassword = hash('sha512', "$salt$password");

        if ($saltPassword != $exploded[1])
            return array('error' => "Invalid credentials");

        $token = self::generateRandomString(100);
        $request = $pdo->prepare("SELECT id FROM ziedelth.tokens WHERE user_id = :userId");
        $request->execute(array('userId' => $member['id']));
        $count = $request->rowCount();

        if ($count == 0)
            $request = $pdo->prepare("INSERT INTO ziedelth.tokens VALUES (NULL, CURRENT_TIMESTAMP, :userId, :token)");
        else
            $request = $pdo->prepare("UPDATE ziedelth.tokens SET token = :token WHERE user_id = :userId");

        $request->execute(array('userId' => $member['id'], 'token' => $token));

        return array('token' => $token, 'user' => self::getMemberWithPseudo($pdo, $member['pseudo']));
    }

    /**
     * @param PDO $pdo
     * @param string $token
     * @return string[]
     */
    static function loginToken(PDO $pdo, string $token): array {
        self::deleteOld($pdo);

        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("SELECT * FROM ziedelth.users u INNER JOIN ziedelth.tokens t on u.id = t.user_id WHERE t.token = :token");
        $request->execute(array('token' => $token));
        $member = $request->fetch(PDO::FETCH_ASSOC);

        if ($member['email_verified'] != 1)
            return array('error' => "Email is not verified");

        return array('token' => $token, 'user' => self::getMemberWithPseudo($pdo, $member['pseudo']));
    }

    /**
     * @param PDO $pdo
     * @param string $token
     * @param string $about
     * @return string[]
     */
    static function update(PDO $pdo, string $token, string $about): array {
        self::deleteOld($pdo);

        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("UPDATE ziedelth.users u INNER JOIN ziedelth.tokens t on u.id = t.user_id SET about = :about WHERE t.token = :token");
        $request->execute(array('about' => $about, 'token' => $token));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "Can not update member");

        return self::getMemberWithToken($pdo, $token);
    }

    /**
     * @param PDO $pdo
     * @param string $token
     * @param UploadedFile|null $file
     * @return array|string[]
     */
    static function updateImage(PDO $pdo, string $token, ?UploadedFile $file): array {
        self::deleteOld($pdo);

        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $member = self::getMemberWithToken($pdo, $token);
        $ext_array = $member['role'] >= 100 ? array('png', 'jpg', 'jpeg', 'gif') : array('png', 'jpg', 'jpeg');
        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);

        if (!in_array($ext, $ext_array))
            return array('error' => "Invalid extension");

        if ($file->getSize() > 2 * 1024 * 1024)
            return array('error' => "Invalid size for file, max: 2 MiB");

        if ($file->getError() != UPLOAD_ERR_OK)
            return array('error' => $file['error']);

        $localFolder = 'images/members';
//        $folder = "/var/www/html/";
        $folder = "/mnt/c/Users/watte/Documents/Development/Ziedelth.fr/public/";
        $sfolder = "$folder$localFolder";

        if (!file_exists($sfolder))
            mkdir($sfolder);

        $key = self::generateRandomString(50);
        $a = "$key.$ext";
        $file->moveTo("$sfolder/$a");

        if ($member['image'] != null && file_exists("$folder/" . $member['image']))
            unlink("$folder/" . $member['image']);

        $request = $pdo->prepare("UPDATE ziedelth.users u INNER JOIN ziedelth.tokens t on u.id = t.user_id SET image = :image WHERE t.token = :token");
        $request->execute(array('image' => "$localFolder/$a", 'token' => $token));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "Can not update member");

        return self::getMemberWithToken($pdo, $token);
    }
}