<?php
namespace Database\Query;
use Database\Condition;
use Database\Exceptions\ColumnException;
use Database\Query;
use Database\Where;

class Update extends Query{
  use Where;

  public function __construct(string $table, array $columns){
    parent::__construct($table, $columns);

    if($this->isList())
      throw new ColumnException;
  }

  public function values(): array{
    return array_merge(array_values($this->columns), ...array_map(fn($e)=>$e->values(), $this->where));
  }

  public function __toString(): string{
    $str = "UPDATE $this->table SET ";

    # We can make improvement here?
    foreach(array_keys($this->columns) as $v)
      $str.= "$v = ?, ";

    $str = rtrim($str, ', ');
    
    if(count($this->where))
      $str.= ' WHERE '.Condition::concat(...$this->where)->enclose(false);

    return $str;
  }
}