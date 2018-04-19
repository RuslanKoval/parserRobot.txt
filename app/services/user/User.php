<?php
namespace app\services\user;


use app\models\UserModel;
use core\Model;

class User
{
    private static $user = null;


    public static function getUser()
    {
        if (self::$user == null) {
            $user = new UserModel();
            $user->setScenario(Model::LOAD_SCENARIO);

            if(isset($_SESSION['user'])) {
                $userId = $_SESSION['user'];
                $data = $user->getUserById($userId);

                if(!$user->validate($data))
                {
                    self::$user = $user;
                }
            }
        }

        return self::$user;

    }
}