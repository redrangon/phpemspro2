<?php
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2018/2/10
 * Time: 14:44
 */

namespace PHPEMSPRO\user\model;

class elem
{
    static $fields = array(
        'user' => array("userface","userrealname","userpassport"),
        'agent' => array("userface","userrealname","userpassport"),
        'teacher' => array("userface","userrealname","userpassport"),
        'webmaster' => array("userface","userrealname","userpassport")
    );
}