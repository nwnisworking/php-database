<?php
namespace Database\Drivers;

use Database\Driver;
use Exception;

class SQLite extends Driver{
  public function config(?string $key = null, int|string|null $value): self|int|string|null{
    if(!in_array($key, ['path']))
      return $this;

    return parent::config($key, $value);
  }

  public function connect(): ?self{
    try{
      # Too stupid to know how to connect to sqlite
    }
    catch(Exception $err){}
  }
}