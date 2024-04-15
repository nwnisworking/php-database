<?php
namespace Database\Query;

use Database\Condition;
use Database\Exceptions\ColumnException;
use Database\Exceptions\JoinTypeException;
use Database\Exceptions\OrderException;
use Database\Query;
use Database\Utils\Ignore;
use Database\Where;

class Select extends Query{
  use Where;

  public const JOIN = ['INNER', 'FULL', 'LEFT', 'RIGHT', 'CROSS'];

  public const ORDER = ['DESC', 'ASC'];

  private array $join = [];

  private array $having = [];

  private array $group = [];

  private array $order = [];

  private ?int $limit = null;

  private ?int $offset = null;

  private bool $enclose = false;

  private bool $distinct = false;

  private ?string $with = null;

  private ?string $alias = null;

  private bool $column_values = false; # This will assume column data is not a value

  public function __construct(?string $table = null, ?array $columns = ['*']){
    parent::__construct($table, $columns);

    if($this->isAssoc())
      throw new ColumnException;
  }

  public function enclose(?bool $value = null): self|bool{
    if(is_null($value))
      return $this->enclose;

    $this->enclose = $value;

    return $this;
  }

  public function distinct(?bool $value = null): self|bool{
    if(is_null($value))
      return $this->distinct;

    $this->distinct = $value;

    return $this;
  }

  public function with(?string $value = null): self|string{
    if(is_null($value))
      return $this->with;

    $this->with = $value;

    return $this;
  }

  public function alias(?string $value = null): self|string{
    if(is_null($value))
      return $this->alias;

    $this->alias = $value;

    return $this;
  }

  public function columnValues(?bool $value = null): self|bool{
    if(is_null($value))
      return $this->column_values;

    $this->column_values = $value;

    return $this;
  }

  public function join(string $type, string $table, Condition $condition): self{
    if(!in_array($type, self::JOIN))
      throw new JoinTypeException;

    if(!isset($this->join[$table]))
      $this->join[$table] = ['type'=>$type, 'condition'=>$condition];

    else
      $this->join[$table]['condition'][] = $condition;

    return $this;
  }

  public function inner(string $table, Condition $condition): self{
    return $this->join('INNER', $table, $condition);
  }

  public function left(string $table, Condition $condition): self{
    return $this->join('LEFT', $table, $condition);
  }
  
  public function right(string $table, Condition $condition): self{
    return $this->join('RIGHT', $table, $condition);
  }

  public function full(string $table, Condition $condition): self{
    return $this->join('FULL', $table, $condition);
  }

  public function group(string ...$columns): self{
    array_push($this->group, ...$columns);
    return $this;
  }

  public function having(Condition ...$condition): self{
    array_push($this->having, ...$condition);
    return $this;
  }

  public function order(string $column, string $order = 'DESC'): self{
    if(!in_array($order = strtoupper($order), self::ORDER))
      throw new OrderException;

    array_push($this->order, "$column $order");
    return $this;
  }

  public function limit(int $limit, ?int $offset = null): self{
    $this->limit = $limit;
    $this->offset = $offset;
    return $this;
  }

  public function values(): array{
    $values = [];

    if($this->column_values){
      foreach($this->columns as $v){
        if(is_a($v, Ignore::class) || is_null($v))
          continue;
    
        else if(is_a($v, Condition::class) || is_a($v, self::class))
          array_push($values, ...$v->values());

        else
          $values[] = $v;
      }
    }

    foreach(array_merge($this->where, $this->having) as $v){
      array_push($values, ...$v->values());
    }

    return $values;
  }

  public function format(mixed $value): string{
    if(is_null($value))
      return "NULL";

    else if(is_a($value, Ignore::class))
      return $value->key;

    else if(is_a($value, self::class) || is_a($value, Condition::class))
      return $value;
    
    else
      return '?';
  }

  public function __toString(): string{
    $str = "SELECT ";

    if($this->distinct)
      $str.= "DISTINCT ";

    $str.= join(', ', $this->column_values ? array_map([$this, 'format'], $this->columns) : $this->columns);

    if(!empty($this->table))
      $str.= " FROM $this->table";

    if(count($this->join))
      foreach($this->join as $k=>$v)
        $str.= " $v[type] JOIN $k ON ".Condition::concat(...$v['condition'])->enclose(false);

    if(count($this->where))
      $str.= ' WHERE '.Condition::concat(...$this->where)->enclose(false);

    if(count($this->group))
      $str.= ' GROUP BY '.join(',', array_unique($this->group));

    if(count($this->having))
      $str.= ' HAVING '.Condition::concat(...$this->having)->enclose(false);

    if(count($this->order))
      $str.= ' ORDER BY '.join(',', $this->order);

    if(isset($this->limit))
      $str.= " LIMIT $this->limit";

    if(isset($this->offset))
      $str.= " OFFSET $this->offset";

    $str = trim($str);

    if(isset($this->with))
      return "WITH $this->with ($str)";

    if($this->enclose || ($this->alias && $this->enclose(true)))
      return "($str)".($this->alias ? " $this->alias" : '');
  
    return $str;
  }
}