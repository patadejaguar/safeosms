<?php

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'server'   => 'localhost',
    'username' => 'rootuser',
    'password' => 'rootuser',
    'database' => 'beatguide_devel_service',
];

$dbh = new \Simplon\Postgres\Postgres(
    $config['server'],
    $config['username'],
    $config['password'],
    $config['database']
);

// ############################################

$query = 'SELECT * FROM events WHERE venue_id = :venueId LIMIT 2';
$conds = array('venueId' => 23);

// ############################################

echo '<h3>fetchValue</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery($query)
    ->setConditions($conds);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);
$result = $sqlManager->fetchColumn($sqlBuilder);

var_dump($result);

// ############################################

echo '<h3>fetchValueMany</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery($query)
    ->setConditions($conds);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);
$result = $sqlManager->fetchColumnMany($sqlBuilder);

echo '<h4>total rows: ' . $sqlManager->getRowCount() . '</h4>';
var_dump($result);

// ############################################

echo '<h3>fetchValueManyCursor</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery($query)
    ->setConditions($conds);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$counter = 0;
foreach ($sqlManager->fetchColumnManyCursor($sqlBuilder) as $result)
{
    echo '<h4>#' . (++$counter) . ' cursor</h4>';
    var_dump($result);
}

// ############################################

echo '<h3>fetch</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery($query)
    ->setConditions($conds);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->fetchRow($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>fetchMany</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery($query)
    ->setConditions($conds);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->fetchRowMany($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>fetchManyCursor</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery($query)
    ->setConditions($conds);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$counter = 0;
foreach ($sqlManager->fetchRowManyCursor($sqlBuilder) as $result)
{
    echo '<h4>#' . (++$counter) . ' cursor</h4>';
    var_dump($result);
}

// ############################################

echo '<h3>execute sql: truncate</h3>';

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setQuery('TRUNCATE import_dump');

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$response = $sqlManager->executeSql($sqlBuilder);
var_dump($response);

// ############################################

echo '<h3>insert</h3>';

$data = [
    'id'   => null,
    'dump' => '{"message":"Hello"}',
];

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setTableName('import_dump')
    ->setData($data);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->insert($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>insertMany</h3>';

$data = [
    [
        'id'   => null,
        'dump' => '{"message":"Hello"}',
    ],
    [
        'id'   => null,
        'dump' => '{"message":"Foo"}',
    ],
    [
        'id'   => null,
        'dump' => '{"message":"Bar"}',
    ],
];

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setTableName('import_dump')
    ->setData($data);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->insert($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>update</h3>';

$conds = ['id' => 1];
$data = ['dump' => '{"message":"Hello BOOOOO"}'];

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setTableName('import_dump')
    ->setConditions($conds)
    ->setData($data);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->update($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>replace</h3>';
$data = [
    'id'   => 3,
    'dump' => '{"message":"Booooh!"}'
];

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setTableName('import_dump')
    ->setData($data);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->replace($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>replaceMany</h3>';
$data = [
    [
        'id'   => 2,
        'dump' => '{"message":"Hello Mum"}'
    ],
    [
        'id'   => 3,
        'dump' => '{"message":"Booooh!"}'
    ],
];

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setTableName('import_dump')
    ->setData($data);

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->replace($sqlBuilder);
var_dump($result);

// ############################################

echo '<h3>delete</h3>';

$conds = [
    'id' => 3,
];

$sqlBuilder = (new \Simplon\Postgres\Manager\PgSqlQueryBuilder())
    ->setTableName('import_dump')
    ->setConditions($conds)
    ->setConditionsQuery('id = :id');

$sqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbh);

$result = $sqlManager->delete($sqlBuilder);
var_dump($result);
