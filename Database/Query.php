<?php
namespace Database;
use Database\Query\Delete;
use Database\Query\Insert;
use Database\Query\Select;
use Database\Query\Update;

abstract class Query{
  public function __construct(protected ?string $table = null, protected ?array $columns = []){}

  public function name(): string{
    return strtolower(substr(static::class, strrpos(static::class, '\\') + 1));
  }

  public function isAssoc(): bool{
    return is_array($this->columns) & !array_is_list($this->columns);
  }

  public function isList(): bool{
    return is_array($this->columns) & array_is_list($this->columns);
  }

  public static function select(?string $table, ?array $columns = ['*']): Select{
    return new Select($table, $columns);
  }

  public static function insert(?string $table, ?array $columns = null): Insert{
    return new Insert($table, $columns);
  }

  public static function delete(?string $table): Delete{
    return new Delete($table, []);
  }

  public static function update(?string $table, array $columns): Update{
    return new Update($table, $columns);
  }

  public abstract function values(): array;

  public abstract function __toString(): string;
}