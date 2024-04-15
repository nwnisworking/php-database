<?php
namespace Database;

use Database\Exceptions\InvalidOperatorException;
use Database\Utils\Ignore;
use Database\Query\Select;

class Condition{
  public const COMPARISON = ['!=', '<', '<=', '<=>', '=', '>', '>=', 'BETWEEN', 'IN', 'IS', 'IS NOT', 'LIKE'];

  public const BITWISE = ['&', '|', '^', '<<', '>>', '~'];

  public const MATH = ['+', 'DIV', '/', 'MOD', '%', '*', '-'];

  private ?string $alias = null;
  
  private bool $enclose = false;

  private bool $not = false;

  public function __construct(
    public readonly string|null|self|Ignore $column,
    public readonly ?string $op,
    public readonly mixed $value,
    private ?string $glue = 'AND'
  ){
  }

  public function not(?bool $value = null): self|bool{
    if(is_null($value))
      return $this->not;

    $this->not = $value;

    return $this;
  }

  public function enclose(?bool $value = null): self|bool{
    if(is_null($value))
      return $this->enclose;

    $this->enclose = $value;

    return $this;
  }

  public function alias(?string $value = null): self|string{
    if(is_null($value))
      return $this->alias;

    $this->alias = $value;

    return $this;
  }

  public function and(): self{
    $this->glue = 'AND';
    
    return $this;
  }

  public function or(): self{
    $this->glue = 'OR';
    
    return $this;
  }

  public static function math(self|Ignore|int $a, string $op, self|Ignore|int $b, string $glue = 'AND'): self{
    if(!in_array($op, self::MATH))
      throw new InvalidOperatorException;

    return new self(null, $op, [$a, $b]);
  }

  public static function add(self|Ignore|int $a, self|Ignore|int $b, string $glue = 'AND'): self{
    return self::math($a, '+', $b);
  }

  public static function minus(self|Ignore|int $a, self|Ignore|int $b, string $glue = 'AND'): self{
    return self::math($a, '-', $b);
  }

  public static function divide(self|Ignore|int $a, self|Ignore|int $b, string $glue = 'AND'): self{
    return self::math($a, '/', $b);
  }

  public static function multiply(self|Ignore|int $a, self|Ignore|int $b, string $glue = 'AND'): self{
    return self::math($a, '*', $b);
  }

  public static function mod(self|Ignore|int $a, self|Ignore|int $b, string $glue = 'AND'): self{
    return self::math($a, '%', $b);
  }

  public static function bit(string|self $column, string $op, self|int|Ignore $value, string $glue = 'AND'): self{
    if(!in_array($op, self::BITWISE))
      throw new InvalidOperatorException;

    return new self($column, $op, $value, $glue);
  }

  public static function neq(int|string|self $column, mixed $value, string $glue = 'AND'): self{
    return new self($column, '<>', $value, $glue);
  }

  public static function lt(int|string|self $column, mixed $value, string $glue = 'AND'): self{
    return new self($column, '<', $value, $glue);
  }

  public static function lte(int|string|self $column, mixed $value, string $glue = 'AND'): self{
    return new self($column, '<=', $value, $glue);
  }

  public static function eq(int|string|self $column, mixed $value, string $glue = 'AND'): self{
    return new self($column, '=', $value, $glue);
  }

  public static function gt(int|string|self $column, mixed $value, string $glue = 'AND'): self{
    return new self($column, '>', $value, $glue);
  }

  public static function gte(int|string|self $column, mixed $value, string $glue = 'AND'): self{
    return new self($column, '>=', $value, $glue);
  }

  public static function between(string $column, string|int|Ignore $min, string|int|Ignore $max, string $glue = 'AND'): self{
    return new self($column, 'BETWEEN', [$min, $max], $glue);
  }

  public static function in(string $column, array $value, string $glue = 'AND'): self{
    return new self($column, 'IN', $value, $glue);
  }

  public static function like(string $column, string $value, string $glue = 'AND'): self{
    return new self($column, 'LIKE', $value, $glue);
  }

  public static function isNull(string|self $column, string $glue = 'AND'): self{
    return new self($column, 'IS', null);
  }

  public static function isNotNull(string|self $column, string $glue = 'AND'): self{
    return new self($column, 'IS NOT', null);
  }

  public static function fn(string $name, mixed ...$args): self{
    return new self($name, 'FN', $args);
  }

  public static function concat(self ...$condition): self{
    return (new self(null, null, $condition))->enclose(true);
  }

  public static function exists(Select $select, string $glue = 'AND'): self{
    return new self('', 'EXISTS', $select->enclose(true), $glue); 
  }

  public static function any(string $column, string $op, Select $select, string $glue = 'AND'): self{
    return new self($column, "$op ANY", $select->enclose(true), $glue);
  }

  public static function all(string $column, string $op, Select $select, string $glue = 'AND'): self{
    return new self($column, "$op ALL", $select->enclose(true), $glue);
  }

  public function values(): array{
    $values = [];

    if(is_a($this->column, self::class))
      $this->recursive($this->column, $values);

    $this->recursive($this->value, $values);

    return $values;
  }

  private function recursive(mixed $a, array &$res = []){
    if(is_a($a, Ignore::class) || is_null($a))
      return $res;

    else if(is_a($a, self::class) || is_a($a, Select::class))
      $res = array_merge($res, $a->values());

    else if(is_array($a))
      foreach($a as $v)
        $this->recursive($v, $res);

    else{
      $res[] = $a;
    }

    return $res;
  }

  private function format(mixed $value): string{
    if(is_null($value))
      return "NULL";
    
    else if(is_a($value, Ignore::class))
      return $value->key;
    
    else if(is_a($value, self::class) || is_a($value, Select::class))
      return $value;
    
    else
      return '?';
  }

  public function __toString(): string{
    $str = "$this->column ";
    $op = $this->op;
    $alias = $this->alias ? " $this->alias" : '';
    $not = $this->not ? 'NOT ' : '';

    $value = array_map(
      [$this, 'format'], 
      is_array($this->value) ? $this->value : [$this->value]
    );

    switch($op){
      case 'BETWEEN' : 
        $str.= $not."$op ".join(' AND ', $value);
      break;
      case 'IN' : 
        $str.= $not."$op (".join(', ', $value).')';
      break;
      case 'LIKE' : 
        $str.= $not."$op $value[0]";
      break;
      case 'FN' : 
        $str = $this->column.'('.join(', ', $value).')';
      break;
      case 'EXISTS' : 
        $str = "$not$op $value[0]";
      break;
      case '%' :
      case '+' : 
      case '/' : 
      case '*' : 
      case '-' : 
        $str = "$value[0] $op $value[1]";
      break;
      case null :
        $str = '';
        $end = $this->value[count($this->value) - 1];
        
        foreach($this->value as $v)
          $str.= $v.($end !== $v ? " $v->glue " : '');
      break;
      default : 
        $str = $not."$this->column $op $value[0]";
      break;
    }

    if($this->enclose)
      return "($str)".$alias;

    return $str.$alias;
  }  
}