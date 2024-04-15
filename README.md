# PHP-Database
Version 3.0 of SQL library.

This library solves the following use case below:

- [x] Write conditions easily
- [x] String multiple conditions 
- [x] Converts SQL function arguments into a placeholder/non-value
- [x] Ignore non-value from being converted into a placeholder
- [x] Fetch rows without calling execute. **SELECT statement only**
- [x] Ignores order of execution
- [x] Increase speed in generating SQL queries
- [ ] Open a secure connection to database server
- [ ] Include set options

#### Table of Contents
1. [Usages](#Usages)
2. [SELECT Class](#SELECT-Class)
3. [INSERT Class](#INSERT-Class)
4. [UPDATE / DELETE Class](#UPDATE/DELETE-Class)
5. [Condition Class](#Condition-Class)

<br>

# Usages

To utilize the library, simple declare the driver of your choice. 

```php
<?php
$driver = new Drivers\MySQL;
$driver
->config('dbname', 'w3schools')
->config('host', 'localhost')
->config('prefix', '')
->config('user', 'root')
->config('pass', '')
->connect();
```
The moment <code>connect</code> method runs, a static PDO will be declared in the Driver class. This is to prevent uneeded initialization of another PDO from being created. 

By using one of the following methods <code>select</code>, <code>insert</code>, <code>delete</code>, <code>update</code>, you will be able to access the Query Class. 
```php
<?php
/**
 * The following example uses w3school database 
 * @link https://github.com/AndrejPHP/w3schools-database/ SQL database
 */
$driver
# Second parameter can be omitted since it returns ['*'] on empty
->select('customers c', ['*', 'COUNT(c.customerid) as total'])
# You can create multiple joins with similar types. As long as the table matches, the conditions will be collated with the previous declared join.
#
# You can also do the following without re-declaring
#->innerJoin('orders o', Condition::multiple(
#	Condition::eq('c.customerid', new Ignore('o.customerid')), 
#	Condition::eq('c.customerid', new Ignore('o.customerid'))
#))
->innerJoin('orders o', Condition::eq('c.customerid', new Ignore('o.customerid')))
->between('c.customerid', 1, 50)
->group('c.customerid')
->having(Condition::gt('COUNT(c.customerid)', 3))
->order('total')
->order('c.customerid', 'asc')
->limit(6);

$result = $driver->fetchAssoc();
```
For the library to discern that the value needs to be represented in its raw form, it will be declared using the **Ignore class**. Below is an example of one of its usage:

```php
$query->innerJoin('orders o', Condition::eq('c.customerid', new Ignore('o.customerid')))
```

**Before**
> INNER JOIN orders o ON c.customerid = ?

**After**

> INNER JOIN orders o ON c.customerid = o.customerid</code>

<br>

# SELECT Class 
| Methods / Properties | Comments |
|--|--|
| distinct(bool $value) | Select multiple columns with distinct values
| enclose(bool $value) | Enclose SELECT statement with curly brackets
| with(string $table_name) | Defines a temporary table 
| join(string $type, string $table, Condition $condition) | Combine rows with a secondary table
| innerJoin(string $table, Condition $condition) | Return table rows when the column of both table matches the condition  
| leftJoin(string $table, Condition $condition) | Return main table rows with matching records from the secondary table 
| rightJoin(string $table, Condition $condition) | Return secondary table rows with matching records from the main table
| fullJoin(string $table, Condition $condition) | Return all records when there is a match between both tables
| group(string ...$columns) | Group matching column values into summary rows
| having(Condition ...$condition) | Filters aggregated values
| order(string $column, string $order = 'ASC') | Sort columns in ascending or descending order
| limit(int $limit, ?int $offset) |  Limit rows from being returned 
| values() | Get placeholder values

<br>

# INSERT Class 
| Methods / Properties | Comments |
|--|--|
| select(bool $value) | Switch VALUES into a SELECT query 
| data(?array ...$values) | Insert values based on the column orders 
| values() | Get placeholder values

<br>

# UPDATE / DELETE Class 
| Methods / Properties | Comments |
|--|--|
| values() | Get placeholder values

<br>

# Condition Class
| Methods / Properties | Comments |
|--|--|
| not(?bool $value) | Set / Get NOT in the condition to return a negative result 
| and() | Filter records to match the current and the next conditions 
| or() | Filter records to match the current condition or the next conditions
| enclose() | Enclose the following condition 
| values() | Get placeholder values 
| static bit(string\|self $column, $string $op, int\|Ignore $value) |  Manipulate and evaluate specific bits within an integer
| static lt(string\|self $column, int\|Ignore\|string\|null $value) | Filter column data that has lesser than the value 
| static gt(string\|self $column, int\|Ignore\|string\|null $value) | Filter column data that has greater than the value 
| static eq(string\|self $column, int\|Ignore\|string\|null $value) | Filter column data that matches the value 
| static lte(string\|self $column, int\|Ignore\|string\|null $value) | Filter column data that has lesser than or equal to the value 
| static gte(string\|self $column, int\|Ignore\|string\|null $value) | Filter column data that has greater than or equal to the value 
| static neq(string\|self $column, int\|Ignore\|string\|null $value) | Filter column data that is not equal to the value 
| static like(string $column, Ignore\|string $value) | Filter column data to search a specific pattern
| static in(string $column, array $value) | Filter column data that matches the value in the column
| static between(string $column, string\|int\|Ignore $min, string\|int\|Ignore $max) | Filter column data that matches between a certain number / string
| static exists(Select $select) | Check the existence of any record in a subquery
| static any(string $column, string $op, Select $select) | Perform a comparison between a single value or a range of other values
| static all(string $column, string $op, Select $select) | Perform a comparison between a single value or a range of other values
| static fn(string $name, mixed ...$args) | Create an SQL function with placeholders/non-value 
| static isNotNull(string $column) | Filters for column that returns a value 
| static isNull(string $column) | Filters for column that returns a null value 
| static multiple(self ...$conditions) | String multiple conditions into one 

<br>

