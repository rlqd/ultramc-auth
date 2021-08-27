<?php

namespace Lib;


interface IAction
{
    /** @throws Exception */
    public function call() : ?array;
}
