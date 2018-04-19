<?php

namespace core;

class ClassFactory
{

    public static function factory($type) {
        $nameSpace = "app\\controllers\\";
        $className = $nameSpace.$type;
        return new $className();
    }
}