<?php

class User extends JObject
{
    public int $id;
    public DateTime $timestamp;
    protected string $email;
    public string $pseudo;
    protected string $saltPassword;
    protected DateTime $tokenTimestamp;
    protected string $token;
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

    function registerUser(?PDO $pdo, string $email, string $pseudo, string $password): JSONResponse
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JSONResponse(400, array('error' => 'Bad email format'));
        }

        if ($this->isEmailExists($pdo, $email)) {
            return new JSONResponse(409, array('error' => 'Email already exists'));
        }

        if (strlen($pseudo) < 4 || strlen($pseudo) > 16) {
            return new JSONResponse(400, array('error' => 'Bad pseudo format'));
        }

        if ($this->isPseudoExists($pdo, $pseudo)) {
            return new JSONResponse(409, array('error' => 'Pseudo already exists'));
        }

        $request = $pdo->prepare("INSERT INTO $this->tableName VALUES (NULL, CURRENT_TIMESTAMP, :email, :pseudo, :password, CURRENT_TIMESTAMP, :token)");
        $salt = Utils::generateRandomString(10);
        $token = Utils::generateRandomString(50);

        if ($request->execute(array('email' => $email, 'pseudo' => $pseudo, 'password' => "$salt|" . hash('sha512', "$salt$password"), 'token' => $token))) {
            return new JSONResponse(201, array('success' => 'OK'));
        }

        return new JSONResponse(409, array('error' => 'User already exists'));
    }
}