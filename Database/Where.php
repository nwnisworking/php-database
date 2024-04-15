<?php
namespace Database;
use Database\Condition;
use Database\Query\Select;
use Database\Utils\Ignore;

trait Where{
  protected array $where = [];

  public function where(Condition ...$condition): self{
    array_push($this->where, ...$condition);

    return $this;
  }

  public function add(Condition|Ignore|int $a, Condition|Ignore|int $b, string $glue = 'AND'): self{
    return $this->where(Condition::add($a, $b));
  }

  public function minus(Condition|Ignore|int $a, Condition|Ignore|int $b, string $glue = 'AND'): self{
    return $this->where(Condition::minus($a, $b));
  }

  public function divide(Condition|Ignore|int $a, Condition|Ignore|int $b, string $glue = 'AND'): self{
    return $this->where(Condition::divide($a, $b));
  }

  public function multiply(Condition|Ignore|int $a, Condition|Ignore|int $b, string $glue = 'AND'): self{
    return $this->where(Condition::multiply($a, $b));
  }

  public function mod(Condition|Ignore|int $a, Condition|Ignore|int $b, string $glue = 'AND'): self{
    return $this->where(Condition::mod($a, $b));    
  }

  public function bit(string|Condition $column, string $op, Condition|int|Ignore $value, string $glue = 'AND'): self{
    return $this->where(Condition::bit($column, $op, $value, $glue));
  }

  public function neq(int|string|Condition $column, mixed $value, string $glue = 'AND'): self{
    return $this->where(Condition::neq($column, $value, $glue));
  }

  public function lt(int|string|Condition $column, mixed $value, string $glue = 'AND'): self{
    return $this->where(Condition::lt($column, $value, $glue));
  }

  public function lte(int|string|Condition $column, mixed $value, string $glue = 'AND'): self{
    return $this->where(Condition::lte($column, $value, $glue));
  }

  public function eq(int|string|Condition $column, mixed $value, string $glue = 'AND'): self{
    return $this->where(Condition::eq($column, $value, $glue));
  }

  public function gt(int|string|Condition $column, mixed $value, string $glue = 'AND'): self{
    return $this->where(Condition::gt($column, $value, $glue));
  }

  public function gte(int|string|Condition $column, mixed $value, string $glue = 'AND'): self{
    return $this->where(Condition::gte($column, $value, $glue));
  }

  public function between(string $column, string|int|Ignore $min, string|int|Ignore $max, string $glue = 'AND'): self{
    return $this->where(Condition::between($column, $min, $max, $glue));
  }

  public function in(string $column, array $value, string $glue = 'AND'): self{
    return $this->where(Condition::in($column, $value, $glue));
  }

  public function like(string $column, string $value, string $glue = 'AND'): self{
    return $this->where(Condition::like($column, $value, $glue));
  }

  public function isNull(string|Condition|self $column, string $glue = 'AND'): self{
    return $this->where(Condition::isNull($column, $glue));
  }

  public function isNotNull(string|Condition|self $column, string $glue = 'AND'): self{
    return $this->where(Condition::isNotNull($column, $glue));
  }

  public function fn(string $name, mixed ...$args): self{
    return $this->where(Condition::fn($name, ...$args));
  }

  public function exists(Select $select, string $glue = 'AND'): self{
    return $this->where(Condition::exists($select, $glue));
  }

  public function notExists(Select $select, string $glue = 'AND'): self{
    return $this->where(Condition::exists($select, $glue)->not(true));
  }

  public function any(string $column, string $op, Select $select, string $glue = 'AND'): self{
    return $this->where(Condition::any($column, $op, $select, $glue));
  }

  public function all(string $column, string $op, Select $select, string $glue = 'AND'): self{
    return $this->where(Condition::all($column, $op, $select, $glue));
  }
}