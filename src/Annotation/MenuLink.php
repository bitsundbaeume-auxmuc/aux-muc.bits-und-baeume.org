<?php

namespace App\Annotation;

#[\Attribute]
class MenuLink
{
    public string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

}