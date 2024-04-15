<?php
namespace Database\Drivers;

use Database\Driver;
use Exception;
use PDO;

class MySQL extends Driver{
  public function config(string|null $key = null, int|string|null $value = null): self|int|string|null{
    if(!in_array($key, ['host', 'port', 'user', 'pass', 'dbname']))
      return $this;

    return parent::config($key, $value);
  }

  public function connect(): ?self{
    try{
      if(isset(self::$pdo) && $this->driverName() === $this->name()) return $this;

      $host = $this->config('host') ?? 'localhost';
      $user = $this->config('user') ?? 'root';
      $pass = $this->config('pass') ?? '';
      $port = $this->config('port');
      $name = $this->config('dbname') ?? '';

      $query = [
        'host'=>$host,
        'port'=>$port,
        'dbname'=>$name
      ];

      self::$pdo = new PDO(
        $this->name().':'.http_build_query($query, '', ';'),
        $user,
        $pass
      );
        
      return $this;
    }
    catch(Exception $err){}

    return null;
  }
}