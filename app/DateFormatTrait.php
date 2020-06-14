<?php

namespace App;

trait DateFormatTrait
{
    public function getDateFormat()
    {
        return $this->dateFormat ?: $this->getConnection()->getQueryGrammar()->getDateFormat();
    }
}