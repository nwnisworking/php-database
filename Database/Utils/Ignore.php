<?php
namespace Database\Utils;

class Ignore{
  private function __construct(public readonly string $key){}

  public static function key(string $key): self{
    return new self($key);
  }
}