<?php
/*
 * Copyright (c) 2009, EasyORM
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Cesar Rodas nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY CESAR RODAS ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL CESAR RODAS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

define("EORM_DRIVER_DIR","drivers/");

EasyORM::import("sql.php");

abstract class EasyORM {
    private static $drivers;

    function __construct() {
    }

    public static function Connect($param) {
        extract(parse_url($param));
        if (!isset($scheme)) {
            throw new Exception("$param is an invalid connection URI");
        }
        if (!EasyORM::import(EORM_DRIVER_DIR."/$scheme.php")) {
            throw new Exception("There is not a driver for {$scheme}");
        }
        if (!EasyORM::isDriver($scheme)) {
            throw new Exception("The driver $scheme is not working well");
        }
    }

    public static function registerDriver($driver,$dbc,$sql) {
        self::$drivers[$driver] = array("dbc"=>$dbc,"sql"=>$sql);
    }

    public static function isDriver($driver) {
        return isset(self::$drivers[$driver]);
    }

    public static function import($file){
        static $loaded=array();
        $file=dirname(__FILE__)."/$file";
        if (is_file($file) && !isSet($loaded[$file])) {
            include($file);
            $loaded[$file] = true;
        }
        return isset($loaded[$file]);
    }

    function __call($name,$params) {
        var_dump($params);
        die();
    }
}

class DB {
    const ONE='one';
    const MANY='many';
    public $type;
    public $size;
    public $rel;

    function __construct($type,$size=0,$rel=null) {
        $this->type=$type;
        $this->size=$size;
        $this->rel =$rel;
    }

    public static function String($length) {
        return new DB("string",$length);
    }

    public static function Integer($length=11) {
        return new DB("integer",$length);
    }

    public static function Relation($class,$rel=DB::ONE) {
        if (!is_subclass_of($class,"EasyORM")) {
            throw new Exception("$class is not an EasyORM subclass");
        }
        return new DB("relation:$class",0,$rel);
    }
}


?>
