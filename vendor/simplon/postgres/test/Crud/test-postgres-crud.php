<?php

require __DIR__ . '/../../vendor/autoload.php';

class UserCrudVo extends \Simplon\Postgres\Crud\PgSqlCrudVo
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pubToken;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $passwordHash;

    /**
     * @return string
     */
    public static function crudGetSource()
    {
        return 'users_user';
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email !== null ? (string)$this->email : null;
    }

    /**
     * @param string $email
     *
     * @return UserCrudVo
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash !== null ? (string)$this->passwordHash : null;
    }

    /**
     * @param string $passwordHash
     *
     * @return UserCrudVo
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPubToken()
    {
        return $this->pubToken !== null ? (string)$this->pubToken : null;
    }

    /**
     * @param string $pubToken
     *
     * @return UserCrudVo
     */
    public function setPubToken($pubToken)
    {
        $this->pubToken = $pubToken;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id !== null ? (int)$this->id : null;
    }

    /**
     * @param int $id
     *
     * @return UserCrudVo
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name !== null ? (string)$this->name : null;
    }

    /**
     * @param string $name
     *
     * @return UserCrudVo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @var int
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $updatedAt;

    /**
     * @param $isCreateEvent
     *
     * @return bool
     */
    public function crudBeforeSave($isCreateEvent)
    {
        if ($isCreateEvent === true)
        {
            $this->setCreatedAt(time());
        }

        $this->setUpdatedAt(time());

        return true;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return (new DateTime())->format('c');
    }

    /**
     * @param int $createdAt
     *
     * @return static
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return (new DateTime())->format('c');
    }

    /**
     * @param int $updatedAt
     *
     * @return static
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

// ----------------------------------------------

$config = [
    'server'   => 'localhost',
    'username' => 'fightbulc',
    'password' => '',
    'database' => 'pushcast_devel_app',
];

$dbh = new \Simplon\Postgres\Postgres(
    $config['server'],
    $config['username'],
    $config['password'],
    $config['database']
);

(new UserCrudVo())->setId(1);
$pgSqlCrudManager = new \Simplon\Postgres\Crud\PgSqlCrudManager($dbh);
$userCrudVo = $pgSqlCrudManager->create(
    (new UserCrudVo())
        ->setId(40)
        ->setPubToken('PUB1233445')
        ->setPasswordHash('HASH12345')
        ->setName('Johnny')
        ->setEmail('johnny@foo.com')
);

die(var_dump($userCrudVo));
//$pgSqlCrudManager->update(
//    (new UserCrudVo())
//        ->setPubToken('PUB1233445')
//        ->setPasswordHash('HASH12345')
//        ->setName('Johnny')
//        ->setEmail('johnny@foo.com')
//    ,
//    ['id' => 3]
//);