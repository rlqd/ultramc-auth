<?php

namespace Lib\Views;

abstract class AbstractView
{
    abstract public function render(): ?array;
}