<?php

namespace App\Contracts;

interface HasColorAndLabel
{
    public function color(): string;
    public function label(): string;
}
