<?php

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
     * @param string $pseudo
     * @param string $email
     * @param string $password
     * @return array
     */
    static function register(PDO $pdo, string $pseudo, string $email, string $password): array
    {
        self::deleteOldActions($pdo);

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
        self::deleteOldActions($pdo);

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
}