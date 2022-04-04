<?php

use Slim\Http\UploadedFile;

class MemberMapper
{
    static function pseudoExists(PDO $pdo, ?string $pseudo): bool
    {
        if ($pseudo == null && empty($pseudo))
            return false;

        $request = $pdo->prepare("SELECT id
FROM ziedelth.users
WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        return $request->rowCount() >= 1;
    }

    static function emailExists(PDO $pdo, ?string $email): bool
    {
        if ($email == null && empty($email))
            return false;

        $request = $pdo->prepare("SELECT id
FROM ziedelth.users
WHERE email = :email");
        $request->execute(array('email' => $email));
        return $request->rowCount() >= 1;
    }

    static function tokenExists(PDO $pdo, ?string $token): bool
    {
        if ($token == null && empty($token))
            return false;

        $request = $pdo->prepare("SELECT id
FROM ziedelth.tokens
WHERE token = :token");
        $request->execute(array('token' => $token));
        return $request->rowCount() >= 1;
    }

    static function isAdminToken(PDO $pdo, ?string $token): bool
    {
        if (!self::tokenExists($pdo, $token))
            return false;

        $request = $pdo->prepare("SELECT tokens.id
FROM ziedelth.tokens
INNER JOIN ziedelth.users ON tokens.user_id = users.id
WHERE token = :token AND users.role = 100");
        $request->execute(array('token' => $token));
        return $request->rowCount() >= 1;
    }

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

    static function deleteOldActions(PDO $pdo)
    {
        $request = $pdo->prepare("SELECT *
FROM ziedelth.actions
WHERE timestamp < NOW() - INTERVAL 10 MINUTE");
        $request->execute(array());
        $objects = $request->fetchAll(PDO::FETCH_ASSOC);

        foreach ($objects as $object) {
            switch ($object['action']) {
                case 'VERIFY_EMAIL':
                    $request = $pdo->prepare("DELETE
FROM ziedelth.users
WHERE id = :userId");
                    $request->execute(array('userId' => $object['user_id']));
                    break;
                default:
                    $request = $pdo->prepare("DELETE
FROM ziedelth.actions
WHERE id = :id");
                    $request->execute(array('id' => $object['id']));
                    break;
            }
        }
    }

    static function deleteOldTokens(PDO $pdo)
    {
        $request = $pdo->prepare("DELETE
FROM ziedelth.tokens
WHERE timestamp < NOW() - INTERVAL 1 MONTH");
        $request->execute(array());
    }

    static function deleteOld(PDO $pdo)
    {
        self::deleteOldActions($pdo);
        self::deleteOldTokens($pdo);
    }

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
        $hash = self::generateRandomString(15);

        if (!EmailMapper::sendEmail("Inscription sur Ziedelth.fr", EmailTemplate::getEmailRegisterTemplate($pseudo, $hash), $email))
            return array('error' => "Can not send email");

        $pdo->beginTransaction();
        $request = $pdo->prepare("INSERT INTO ziedelth.users
VALUES (NULL, CURRENT_TIMESTAMP, :pseudo, :email, :password, 0, 0, NULL, NULL)");
        $request->execute(array('pseudo' => $pseudo, 'email' => $email, 'password' => "$salt|" . hash('sha512', "$salt$password")));
        $id = $pdo->lastInsertId();
        $request = $pdo->prepare("INSERT INTO ziedelth.actions
VALUES (NULL, CURRENT_TIMESTAMP, :userId, :hash, :action)");
        $request->execute(array('userId' => $id, 'hash' => $hash, 'action' => 'VERIFY_EMAIL'));
        $pdo->commit();

        return array('success' => "OK");
    }

    static function validateAction(PDO $pdo, string $hash): array
    {
        self::deleteOld($pdo);

        $request = $pdo->prepare("SELECT *
FROM ziedelth.actions
WHERE hash = :hash");
        $request->execute(array('hash' => $hash));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "No action");

        $object = $request->fetch(PDO::FETCH_ASSOC);

        switch ($object['action']) {
            case 'VERIFY_EMAIL':
                $request = $pdo->prepare("UPDATE ziedelth.users
SET email_verified = 1
WHERE email_verified = 0
  AND id = :userId");
                $request->execute(array('userId' => $object['user_id']));
                $count = $request->rowCount();

                if ($count != 1)
                    return array('error' => "No update");

                $request = $pdo->prepare("DELETE
FROM ziedelth.actions
WHERE id = :id");
                $request->execute(array('id' => $object['id']));
                break;
            case 'PASSWORD_RESET':
                $newHash = self::generateRandomString(15);
                $request = $pdo->prepare("UPDATE ziedelth.actions
SET timestamp = CURRENT_TIMESTAMP,
    hash      = :hash,
    action    = 'CONFIRM_PASSWORD_RESET'
WHERE id = :id");
                $request->execute(array('hash' => $newHash, 'id' => $object['id']));

                $request = $pdo->prepare("SELECT *
FROM ziedelth.actions
WHERE id = :id");
                $request->execute(array('id' => $object['id']));
                $count = $request->rowCount();

                if ($count != 1)
                    return array('error' => "No action");

                $object = $request->fetch(PDO::FETCH_ASSOC);

                return array('object' => $object, 'success' => 'OK');
            case 'DELETE_ACCOUNT':
                $request = $pdo->prepare("DELETE
FROM ziedelth.users
WHERE id = :id");
                $request->execute(array('id' => $object['user_id']));
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

        $request = $pdo->prepare("SELECT id, timestamp, pseudo, role, image, about
FROM ziedelth.users
WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $user = $request->fetch(PDO::FETCH_ASSOC);
        $user['statistics'] = StatisticsMapper::getStatisticsForUser($pdo, $user);
        return $user;
    }

    static function getMemberWithToken(PDO $pdo, string $token)
    {
        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("SELECT u.id, u.timestamp, u.pseudo, u.role, u.image, u.about
FROM ziedelth.users u
         INNER JOIN ziedelth.tokens t ON t.user_id = u.id
WHERE t.token = :token");
        $request->execute(array('token' => $token));
        $user = $request->fetch(PDO::FETCH_ASSOC);
        $user['statistics'] = StatisticsMapper::getStatisticsForUser($pdo, $user);
        return $user;
    }

    static function getPrivateMemberWithToken(PDO $pdo, string $token)
    {
        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("SELECT u.*
FROM ziedelth.users u
         INNER JOIN ziedelth.tokens t ON t.user_id = u.id
WHERE t.token = :token");
        $request->execute(array('token' => $token));
        return $request->fetch(PDO::FETCH_ASSOC);
    }

    static function getPrivateMemberWithEmail(PDO $pdo, string $email)
    {
        if (!self::emailExists($pdo, $email))
            return array('error' => "Email does not exists");

        $request = $pdo->prepare("SELECT *
FROM ziedelth.users
WHERE email = :email");
        $request->execute(array('email' => $email));
        return $request->fetch(PDO::FETCH_ASSOC);
    }

    static function loginWithCredentials(PDO $pdo, string $email, string $password): array
    {
        self::deleteOld($pdo);

        if (!self::emailExists($pdo, $email))
            return array('error' => "Email does not exists");
        if (!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email))
            return array('error' => "Email invalid pattern");

        $request = $pdo->prepare("SELECT *
FROM ziedelth.users
WHERE email = :email");
        $request->execute(array('email' => $email));
        $member = $request->fetch(PDO::FETCH_ASSOC);

        if ($member['email_verified'] != 1)
            return array('error' => "Email is not verified");

        $exploded = explode('|', $member['salt_password']);
        $salt = $exploded[0];
        $saltPassword = hash('sha512', "$salt$password");

        if ($saltPassword != $exploded[1])
            return array('error' => "Invalid credentials");


        $request = $pdo->prepare("SELECT *
FROM ziedelth.tokens
WHERE user_id = :userId");
        $request->execute(array('userId' => $member['id']));
        $count = $request->rowCount();

        if ($count == 0) {
            $token = self::generateRandomString(100);
            $request = $pdo->prepare("INSERT INTO ziedelth.tokens
VALUES (NULL, CURRENT_TIMESTAMP, :userId, :token)");
            $request->execute(array('userId' => $member['id'], 'token' => $token));
        } else {
            $token = $request->fetch(PDO::FETCH_ASSOC)['token'];
        }

        return array('token' => $token, 'user' => self::getMemberWithPseudo($pdo, $member['pseudo']));
    }

    static function loginWithToken(PDO $pdo, string $token): array
    {
        self::deleteOld($pdo);

        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("SELECT *
FROM ziedelth.users u
         INNER JOIN ziedelth.tokens t on u.id = t.user_id
WHERE t.token = :token");
        $request->execute(array('token' => $token));
        $member = $request->fetch(PDO::FETCH_ASSOC);

        if ($member['email_verified'] != 1)
            return array('error' => "Email is not verified");

        return array('token' => $token, 'user' => self::getMemberWithPseudo($pdo, $member['pseudo']));
    }

    static function update(PDO $pdo, string $token, string $about): array
    {
        self::deleteOld($pdo);

        if (!self::tokenExists($pdo, $token))
            return array('error' => "Token does not exists");

        $request = $pdo->prepare("UPDATE ziedelth.users u INNER JOIN ziedelth.tokens t on u.id = t.user_id
SET about = :about
WHERE t.token = :token");
        $request->execute(array('about' => $about, 'token' => $token));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "Can not update member");

        return self::getMemberWithToken($pdo, $token);
    }

    static function updateImage(PDO $pdo, string $token, string $directory, ?UploadedFile $file): array
    {
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
        $sfolder = "$directory$localFolder";

        if (!file_exists($sfolder))
            mkdir($sfolder);

        $key = self::generateRandomString(50);
        $a = "$key.$ext";
        $file->moveTo("$sfolder/$a");

        if ($member['image'] != null && file_exists("$directory/" . $member['image']))
            unlink("$directory/" . $member['image']);

        $request = $pdo->prepare("UPDATE ziedelth.users u INNER JOIN ziedelth.tokens t on u.id = t.user_id
SET image = :image
WHERE t.token = :token");
        $request->execute(array('image' => "$localFolder/$a", 'token' => $token));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "Can not update member");

        return self::getMemberWithToken($pdo, $token);
    }

    static function delete(PDO $pdo, string $token): array
    {
        self::deleteOld($pdo);

        if (!self::tokenExists($pdo, $token))
            return array('error' => 'Token does not exists');

        $member = self::getPrivateMemberWithToken($pdo, $token);
        $pdo->beginTransaction();

        $hash = self::generateRandomString(15);
        $request = $pdo->prepare("INSERT INTO ziedelth.actions
VALUES (NULL, CURRENT_TIMESTAMP, :userId, :hash, :action)");
        $request->execute(array('userId' => $member['id'], 'hash' => $hash, 'action' => 'DELETE_ACCOUNT'));

        if (!EmailMapper::sendEmail("Suppression de compte sur Ziedelth.fr", EmailTemplate::getAccountDeletedTemplate($member['pseudo'], $hash), $member['email'])) {
            $pdo->rollBack();
            return array('error' => "Can not send email");
        }

        $pdo->commit();
        return array('success' => "OK");
    }

    static function passwordReset(PDO $pdo, string $email): array
    {
        self::deleteOld($pdo);

        if (!self::emailExists($pdo, $email))
            return array('error' => 'Email does not exists');

        $member = self::getPrivateMemberWithEmail($pdo, $email);
        $pdo->beginTransaction();

        $hash = self::generateRandomString(15);
        $request = $pdo->prepare("INSERT INTO ziedelth.actions
VALUES (NULL, CURRENT_TIMESTAMP, :userId, :hash, :action)");
        $request->execute(array('userId' => $member['id'], 'hash' => $hash, 'action' => 'PASSWORD_RESET'));

        if (!EmailMapper::sendEmail("Changement de mot de passe sur Ziedelth.fr", EmailTemplate::getPasswordResetTemplate($member['pseudo'], $hash), $member['email'])) {
            $pdo->rollBack();
            return array('error' => "Can not send email");
        }

        $pdo->commit();
        return array('success' => "OK");
    }

    static function confirmPasswordReset(PDO $pdo, string $hash, string $password): array
    {
        self::deleteOld($pdo);

        $request = $pdo->prepare("SELECT *
FROM ziedelth.actions
WHERE hash = :hash");
        $request->execute(array('hash' => $hash));
        $count = $request->rowCount();

        if ($count != 1)
            return array('error' => "No action");

        $object = $request->fetch(PDO::FETCH_ASSOC);
        $request = $pdo->prepare("DELETE
FROM ziedelth.actions
WHERE id = :id
  AND action = 'CONFIRM_PASSWORD_RESET'");
        $request->execute(array('id' => $object['id']));
        $request = $pdo->prepare("SELECT *
FROM ziedelth.users
WHERE id = :userId");
        $request->execute(array('userId' => $object['user_id']));
        $user = $request->fetch(PDO::FETCH_ASSOC);

        $salt = self::generateRandomString(10);

        if (!EmailMapper::sendEmail("Confirmation de changement de mot de passe sur Ziedelth.fr", EmailTemplate::getConfirmationPasswordResetTemplate($user['pseudo']), $user['email']))
            return array('error' => "Can not send email");

        $request = $pdo->prepare("UPDATE ziedelth.users
SET salt_password = :password
WHERE id = :id");
        $request->execute(array('password' => "$salt|" . hash('sha512', "$salt$password"), 'id' => $user['id']));

        return array('success' => "OK");
    }
}