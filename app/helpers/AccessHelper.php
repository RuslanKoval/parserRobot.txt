<?php
namespace app\helpers;


use app\services\user\User;

class accessHelper
{
    /**
     *
     */
    public static function checkLoginUser()
    {
        $user = User::getUser();

        if ($user) {
            header("Location: /");
        }

    }

    /**
     *
     */
    public static function checkLogoutUser()
    {
        $user = User::getUser();

        if (!$user) {
            header("Location: /");
        }

    }
}