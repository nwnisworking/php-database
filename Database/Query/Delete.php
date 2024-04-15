<?php
namespace Database\Query;
use Database\Condition;
use Database\Exceptions\ColumnException;
use Database\Query;
use Database\Where;

class Delete extends Query{
  use Where;

  public function __construct(string $table, ?array $columns = null){
    parent::__construct($table, $columns);

    if($this->isList())
      throw new ColumnException;
  }

  public function values(): array{
    return array_merge(array_values($this->columns), ...array_map(fn($e)=>$e->values(), $this->where));
  }

  public function __toString(): string{
    $str = "DELETE FROM $this->table";

    if(count($this->where))
      $str.= ' WHERE '.Condition::concat(...$this->where)->enclose(false);

    return $str;
  }
}