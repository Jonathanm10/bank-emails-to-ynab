<?php

namespace App\Data;

abstract class AbstractData
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
