<?php

namespace Lib;

class WebRedirect extends Exception implements IHeaderContainer
{
    protected string $location;

    public function __construct($location, $code = 302)
    {
        parent::__construct('Performing redirect: ' . $location, $code);
        $this->location = $location;
    }

    public function getHeaders(): array
    {
        return [
            'Location' => $this->location,
        ];
    }
}