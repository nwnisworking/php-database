<?php
namespace Database\Query;
use Database\Query;
use Database\Where;

class Insert extends Query{
  use Where;
  
  private array $values = [];

  private bool $select = false;

  public function __construct(?string $table = null, ?array $columns = null){
    parent::__construct($table, $columns);

    if($this->isAssoc()){
      $this->columns = array_keys($columns);
      $this->values[] = array_values($columns);
    }
  }

  public function toSelect(bool $value): self{
    $this->select = $value;

    return $this;
  }

  public function data(?array ...$values): self{
    $columns = $this->columns;

    foreach($values as $value){
      if(is_array($columns) && (count($columns) === count($value)) || is_null($columns))
        $this->values[] = $value;

      else
        continue;
    }

    return $this;
  }

  public function values(): array{
    return array_merge(...$this->values, ...array_map(fn($e)=>$e->values(), $this->where));
  }

  public function __toString(): string{
    $str = "INSERT INTO $this->table";

    if(count($this->columns))
      $str.= '('.join(', ', $this->columns).')';

    if($this->select){
      $str.= ' '.join(' UNION ', array_map(fn($e)=>(Query::select(null, $e))->columnValues(true)->where(...$this->where), $this->values));
    }
    else{
      if(count($this->values))
        $str.= ' VALUES ';

      foreach($this->values as $value)
        $str.= '('.join(',', array_fill(0, count($value), '?')).'), ';
      
      $str = rtrim($str, ', ');
    }
    
    return $str;
  }
}