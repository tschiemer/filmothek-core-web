<?php

class Setting extends Eloquent {
    
    protected $table = 'settings';
    
    public $timestamps = false;
    
//    protected static $cache;
//    
//    public static function boot()
//    {
//        parent::boot();
//        
//        self::$cache = array();
//        foreach(self::all() as $setting){
//            self::$cache[$setting->key] = $setting;
//            var_dump($settings);
//            die('foo');
//        }
//    }
//    
    public static function get($key, $default = NULL){
        $setting = Setting::where('key',$key)->first();
        if (empty($setting)){
            return $default;
        }
        return $setting->value;
//        if (!is_array(self::$cache[$key]) or !array_key_exists($key,self::$cache)){
//            return $default;
//        }
//        return self::$cache[$key]->value;
    }
    
    public static function set($key, $value){
//        var_dump(self::$cache);
//        exit;
        $setting = Setting::where('key',$key)->first();
        if (empty($setting)){
//        if (isset(self::$cache[$key])){
//            $setting = self::$cache[$key];
//        } else {
            $setting = new Setting();
        }
        
        $setting->key = $key;
        $setting->value = $value;
        
        $setting->save();
    }
    
    public static function int($key, $default = 0){
        return intval(self::get($key,$default));
    }
    
    public static function bool($key, $default = FALSE){
        return boolval(self::get($key,$default));
    }
    
    public static function string($key,$default = NULL){
        return self::get($key,$default);
    }
    
}