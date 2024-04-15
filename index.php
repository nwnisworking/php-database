<?php
use Database\Drivers\MySQL;
use Database\Drivers\SQLite;
use Database\Utils\Ignore;
use Database\Query;
use Database\Condition;

spl_autoload_register(fn($e)=>include_once "$e.php");

// echo "BETWEEN test: ".assert((string)($c = Condition::between('Price', 10, 20)) === 'Price BETWEEN ? AND ?', 'Does not match BETWEEN');
// echo PHP_EOL;
// echo "IN test: ".assert((string)($c = Condition::in('Country', ['Germany', 'France', 'UK'])) === 'Country IN (?, ?, ?)', 'Does not match IN');
// echo PHP_EOL;
// echo "Add test: ".assert((string)($c = Condition::add(6, 9)) === '? + ?', 'Does not match ADD');
// echo PHP_EOL;
// echo "Enclose with alias Add test: ".assert((string)($c = Condition::add(6, 9)->enclose(true)->alias('total')) === '(? + ?) total', 'Does not match ADD with enclose and alias total');
// echo PHP_EOL;
// echo "Bit test: ".assert((string)($c = Condition::bit(Condition::bit(1, '<<', 2), '&', 0b111)) === '1 << ? & ?', 'Does not match BIT');
// echo PHP_EOL;
// echo "Eq test: ".assert((string)($c = Condition::eq('Price', 18)) === 'Price = ?', 'Does not match Eq');
// echo PHP_EOL;
// echo "Fn test: ".assert((string)($c = Condition::fn('JSON_SEARCH', Ignore::key('users'), 'one', 1, null, "$[*].id")) === 'JSON_SEARCH(users, ?, ?, NULL, ?)', 'Does not match Fn');
// echo PHP_EOL;
// echo "LIKE test: ".assert((string)($c = Condition::like('name', '%s')->not(true)) === 'name NOT LIKE ?', 'Does not match LIKE');
// echo PHP_EOL;
// echo "NOT NULL test: ".assert((string)($c = Condition::isNotNull('Address')) === 'Address IS NOT NULL', 'Does not match NOT NULL');
// echo PHP_EOL;
// echo "Concat condition test: ".assert((string)($c = Condition::concat(Condition::eq('id', 1, 'OR'), Condition::eq('id', 11))) === '(id = ? OR id = ?)', 'Does not match concat string');
// echo PHP_EOL;
// echo PHP_EOL;

// echo $e = Query::select('users', [Condition::add(10, 20)]);
// echo PHP_EOL;
// echo 'column_values is false: '.json_encode($e->values()).PHP_EOL;
// echo 'column_values is true: '.json_encode($e->columnValues(true)->values()).PHP_EOL;
// echo PHP_EOL;
// echo $e = Query::select('users', [Query::select('test')->eq('id', 1)->alias('example')]);
// echo PHP_EOL;
// echo 'column_values is false: '.json_encode($e->values()).PHP_EOL;
// echo 'column_values is true: '.json_encode($e->columnValues(true)->values()).PHP_EOL;

// $user_id = 2;
// echo ($e = Query::select('users', [
//   Ignore::key('*'),
//   Query::select('user_project_access', [
//     Condition::fn('JSON_ARRAYAGG', 
//       Condition::fn('JSON_OBJECT', 
//         Ignore::key('"id"'), Ignore::key('id'),
//         Ignore::key('"title"'), Ignore::key('title'),
//         Ignore::key('"user_id"'), Ignore::key('user_id'),
//         Ignore::key('"key"'), Ignore::key('key'),
//         Ignore::key('"users"'), Ignore::key('users'),
//       )
//     )
//   ])
//   ->enclose(true)
//   ->isNotNull(Condition::fn('JSON_SEARCH', Ignore::key('invited_users'), 'one', $user_id, null, "$[*].user_id"))
// ])
// ->columnValues(true)
// ->eq('id', $user_id)
// ).PHP_EOL.PHP_EOL.json_encode($e->values()).PHP_EOL;

// var_dump(Query::select('users')
// ->exists(Query::select('customers')).'');

// $insert = Query::insert('users', [
//   'name'=>'test', 
//   'email'=>'test@gmail.com'
// ])
// ->toSelect(true)
// ->exists(Query::select('users', [1])
// ->eq('name','test', 'OR')
// ->eq('email','test@gmail.com')
// );

// echo $insert;

// var_dump($insert->values());

// $insert = Query::insert('users', ['name', 'email', 'password', 'public_key'])
// ->data(
//   ['test', 'test@gmail.com', 'testpass', '23442342354'], 
//   ['bob', 'bo@gmail.com', Ignore::key('UUID()'), 'orih3ep9uirh']
// )
// ->toSelect(true);

// var_dump($insert.'');

// $sql = new MySQL;

// $sql->config('dbname', 'swol_shit');

// $select = $sql->connect();

// var_dump($select->select('users'));
// var_dump($select->fetchAssoc());

$query = Query::insert('users', ['name', 'value']);
var_dump($query.'');
$query->data(['test', 'bar'], ['test', 'bar']);
var_dump($query.'');
$query = Query::insert('users', ['name'=>'test', 'value'=>'bar']);
$query->data(['test', 'bar'], ['test', 'bar']);
var_dump($query.'');