<?php

class User extends JObject
{
    public int $id;
    public string $timestamp;
    protected string $email;
    public string $pseudo;
    protected string $saltPassword;
    public int $emailVerified = 0;

    public int $role = 0;
    public ?string $image;
    public ?string $about;
}

class UserMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('ziedelth.users', 'User');
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

    function isTokenExists(?PDO $pdo, string $token): bool
    {
        $request = $pdo->prepare("SELECT * FROM tokens WHERE token = :token");
        $request->execute(array('token' => $token));
        return $request->rowCount() > 0;
    }

    function registerUser(?PDO $pdo, string $email, string $pseudo, string $password): JSONResponse
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return new JSONResponse(400, array('error' => 'Bad email format'));

        if ($this->isEmailExists($pdo, $email))
            return new JSONResponse(409, array('error' => 'Email already exists'));

        if (strlen($pseudo) < 4 || strlen($pseudo) > 16)
            return new JSONResponse(400, array('error' => 'Bad pseudo format'));

        if ($this->isPseudoExists($pdo, $pseudo))
            return new JSONResponse(409, array('error' => 'Pseudo already exists'));

        $salt = Utils::generateRandomString();
        $request = $pdo->prepare("INSERT INTO $this->tableName VALUES (NULL, CURRENT_TIMESTAMP, :email, :pseudo, :password, 0, 0, NULL, NULL)");
        $request->execute(array('email' => $email, 'pseudo' => $pseudo, 'password' => "$salt|" . hash('sha512', "$salt$password")));

        // TODO: SEND EMAIL

        return new JSONResponse(201, array('success' => 'OK'));
    }

    function loginUser(?PDO $pdo, string $pseudo, string $password): JSONResponse
    {
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
        $request = $pdo->prepare("DELETE FROM tokens WHERE user_id = :user_id");
        $request->execute(array('userId' => $user->id));
        $request = $pdo->prepare("INSERT INTO tokens VALUES (NULL, CURRENT_TIMESTAMP, :userId, :token)");
        $request->execute(array('userId' => $user->id, 'token' => $token));

        return new JSONResponse(200, array('token' => $token));
    }

    function getUserByToken(?PDO $pdo, string $token): JSONResponse
    {
        if (!$this->isTokenExists($pdo, $token))
            return new JSONResponse(404, array('error' => 'Token doesn\'t exists'));

        $request = $pdo->prepare("SELECT u.* FROM $this->tableName u INNER JOIN tokens t ON t.user_id = u.id WHERE token = :token");
        $request->execute(array('token' => $token));
        $user = $request->fetchObject($this->className);

        if ($user->emailVerified != 1)
            return new JSONResponse(400, array('error' => 'Email is not verified'));

        return new JSONResponse(200, $user);
    }

    function getUserByPseudo(?PDO $pdo, string $pseudo): JSONResponse
    {
        if (!$this->isPseudoExists($pdo, $pseudo))
            return new JSONResponse(404, array('error' => 'Pseudo doesn\'t exists'));

        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $user = $request->fetchObject($this->className);

        if ($user->emailVerified != 1)
            return new JSONResponse(400, array('error' => 'Email is not verified'));

        return new JSONResponse(200, $user);
    }
}