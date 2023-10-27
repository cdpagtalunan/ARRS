<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

/*
    * THIS FILE WILL SERVE AS GLOBAL HELPERS.
    * YOU CAN ADD FUNCTION AND IT CAN BE ACCESS GLOBALLY EVEN IN BLADE
*/

class AppHelper
{
    public static function decryptId($id)
    {
        // return "increment";
        return Crypt::decryptString($id);
    }
    public static function encryptId($id)
    {
        // return "increment";
        return Crypt::encryptString($id);
    }


    //  public static function instance()
    //  {
    //      return new AppHelper();
    //  }
}