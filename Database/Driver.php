<?php
namespace Database;

use Database\Query\Select;
use Database\Query\Delete;
use Database\Query\Insert;
use Database\Query\Update;
use PDO;
use PDOStatement;

abstract class Driver{
  protected static ?PDO $pdo;
  
  protected Query $query;

  private array $config = [];

  public function select(string $table, ?array $columns = ['*']): Select{
    return $this->query = new Select($table, $columns);
  }

  public function insert(string $table, ?array $columns = null): Insert{
    return $this->query = new Insert($table, $columns);
  }

  public function delete(string $table): Delete{
    return $this->query = new Delete($table, null);
  }

  public function update(string $table, array $columns): Update{
    return $this->query = new Update($table, $columns);
  }

  public function driverName(): ?string{
    return @self::$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
  }

  public function config(?string $key = null, ?string $value = null): self|string|int|null{
    if(is_null($value))
      return $key === '*' ? $this->config :  @$this->config[$key] ;

    $this->config[$key] = $value;
    
    return $this;
  }

  public function name(): string{
    return strtolower(substr(static::class, strrpos(static::class, '\\') + 1));
  }

  public function execute(): bool|PDOStatement{
    $prepare = self::$pdo->prepare($this->query);

    if(!$prepare->execute($this->query->values()))
      return false;

    return $prepare;
  }

  public function fetchObject(): ?array{
    return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_OBJ);
  }

  public function fetchColumn(int $index): ?array{
    return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_COLUMN, $index);
  }

  public function fetchAssoc(): ?array{
    return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_ASSOC);
  }

  public function fetchClass(string $class): ?array{
    return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_CLASS, $class);
  }

  public function fetchFunc(callable $fn): ?array{
    return !($res = $this->execute()) ?: $res->fetchAll(PDO::FETCH_FUNC, $fn);
  }

  public function beginTransaction(): self{
    self::$pdo->beginTransaction();
    return $this;
  }

  public function rollback(): self{
    self::$pdo->rollBack();
    return $this;
  }

  public abstract function connect(): ?self;
}