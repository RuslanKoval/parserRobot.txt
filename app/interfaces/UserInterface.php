<?php
namespace app\interfaces;


interface UserInterface
{
    public function generateHash($password = '');
    public function saveModel();
    public function checkUserByName($name);
    public function checkUserByEmail($email);
    public function getUserById($id);

}