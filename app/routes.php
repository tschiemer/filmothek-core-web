<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('public');
});

Route::model('film', 'Film');

Route::get('/film/id/{film}',function(Film $film){
    return $film->toJson();
});

//Route::get('/film/{search?}',function($search = NULL){
//    
//    $query = DB::table('films');
//            
//    if (Input::get('title')){
//        $query = $query->where('title','like','%'.Input::get('title').'%');
//    }
//    if (Input::get('artist')){
//        $query = $query->where('artist','like','%'.Input::get('artist').'%');
//    }
//    if (Input::get('country')){
//        $query = $query->where('country','like','%'.Input::get('country').'%');
//    }
//    if (Input::get('technique')){
//        $query = $query->where('technique','like','%'.Input::get('technique').'%');
//    }
//    
//    if ( ! empty($search)){
//        $query = $query->where(function($q)use($search){
//            $q->where('nr','like',"%{$search}%");
//            if (!Input::get('title')){
//                $q->orWhere('title','like',"%{$search}%");
//            }
//            if (!Input::get('artist')){
//                $q->orWhere('artist','like',"%{$search}%");
//            }
//            if (!Input::get('country')){
//                $q->orWhere('country','like',"%{$search}%");
//            }
//            if (!Input::get('year')){
//                $q->orWhere('year','like',"%{$search}%");
//            }
//            if (!Input::get('technique')){
//                $q->orWhere('technique','like',"%{$search}%");
//            }
//        });
//    }
//    
//    return $query->get();
//});


Route::post('/film',function(){
    
    $search = Input::get('search');
    
    $query = DB::table('films');
            
    if (Input::get('title')){
        $query = $query->where('title','like','%'.Input::get('title').'%');
    }
    if (Input::get('artist')){
        $query = $query->where('artist','like','%'.Input::get('artist').'%');
    }
    if (Input::get('country')){
        $query = $query->where('country','like','%'.Input::get('country').'%');
    }
    if (Input::get('technique')){
        $query = $query->where('technique','like','%'.Input::get('technique').'%');
    }
    
    if ( ! empty($search)){
        $query = $query->where(function($q)use($search){
            $q->where('nr','like',"%{$search}%");
//            if (!Input::get('title'))
                {
                $q->orWhere('title','like',"%{$search}%");
            }
//            if (!Input::get('artist'))
                {
                $q->orWhere('artist','like',"%{$search}%");
            }
//            if (!Input::get('country'))
                {
                $q->orWhere('country','like',"%{$search}%");
            }
//            if (!Input::get('year'))
                {
                $q->orWhere('year','like',"%{$search}%");
            }
//            if (!Input::get('technique'))
                {
                $q->orWhere('technique','like',"%{$search}%");
            }
        });
    }
    
    return $query->get();
});


Route::get('/category/{subcat?}/{search?}',function($subcat = NULL, $search = NULL){
    
    if (in_array($subcat,array('artist','country','technique'))){
        if (empty($search)){
            $result =  Film::distinct()->lists($subcat);
        } else {
            $result = Film::where($subcat,'like',"%{$search}%")->distinct()->lists($subcat);
        }
//        $i = 1;
        return array_map(function($label)use($i){
            static $i = 1;
            return array(
                'key' => $i++,
                'label' => $label
            );
        }, $result);
    }
        
        
    if ($subcat == NULL){
        return Response::json(
                array(
                    array('key'=>'title','label'=>'Titel'),
                    array('key'=>'artist','label'=>'Regie'),
                    array('key'=>'country','label'=>'Land'),
                )
         );
    }
    
    App::abort(404);
});

Route::get('/settings',function(){
    return View::make('settings');
});

Route::get('/settings/ajax',function(){ 
//    if (Request::ajax()){
        return Setting::all()->toJson();
//    }
});

Route::post('/settings/ajax',function(){
   
    
//    if (Request::ajax()){
        $settings = Input::get();
        foreach($settings as $key => $value){
            if (preg_match('/^image/',$key)){
                //delete upload
                
                $filename = Setting::get($key,NULL);
                if (!empty($filename)){
                    $filePath = public_path('uploads/'.$filename);
                    unlink($filePath);
                }
            }
            Setting::set($key,$value);
        }
        return;
//    }
    
    
//    Setting::set('page-title',Input::get('page-title'));
    
//    return Redirect::to('settings');
});

Route::post('/settings/upload/{key}',function($key){
//    return Input::get();
//    return var_dump($_FILES);
    
//    unlink(public_path('uploads/jpeg/php1nd5Fy'));
//    unlink(public_path('uploads/jpeg/phpLzDEat'));
//    unlink(public_path('uploads/jpeg'));
//    unlink(public_path('uploads/toy love.jpeg/phpN4BEDo'));
//    unlink(public_path('uploads/toy love.jpeg/phpmOhkvi'));
//    rmdir(public_path('uploads/toy love.jpeg'));
    
    if (!Input::hasFile('file') or !Input::file('file')->isValid()){
        App::abort(500);
    }
    
    
    $filename = Setting::get($key,NULL);
    if (!empty($filename)){
        $filePath = public_path('uploads/'.$filename);
        if (file_exists($filePath)){
            unlink($filePath);
        }
    }
    
    $destinationPath = public_path('uploads/');
    Input::file('file')->move($destinationPath,Input::file('file')->getClientOriginalName());
    Setting::set($key,Input::file('file')->getClientOriginalName());
});

//Route::get('/uploads',function(){
//    return;
//});