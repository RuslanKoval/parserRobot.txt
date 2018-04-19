<?php

namespace core;

class Model
{
    const CREATE_SCENARIO = 1;
    const EDIT_SCENARIO = 2;
    const LOAD_SCENARIO = 3;

    protected $db = null;
    protected $table = "";
    protected $scenario = null;

    public function __construct()
    {
        $this->setScenario(self::CREATE_SCENARIO);
        $this->init();
    }

    public function init()
    {

    }

    public function setScenario($scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * @param $table
     */
    protected function setTable($table)
    {
        $this->table = $table;
    }

    protected function randomString($count = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randString = '';
        for ($i = 0; $i < $count; $i++) {
            $randString.= $characters[rand(0, strlen($characters))];
        }
        return $randString;
    }
}