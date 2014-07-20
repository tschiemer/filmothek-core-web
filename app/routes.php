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
    
    if ( ! Input::get('search')){
        $search = Input::get('search');
        $query = $query->where(function($q)use($search){
            $q->where('nr','like',"%{$search}%");
            if (!Input::get('title')){
                $q->orWhere('title','like',"%{$search}%");
            }
            if (!Input::get('artist')){
                $q->orWhere('artist','like',"%{$search}%");
            }
            if (!Input::get('country')){
                $q->orWhere('country','like',"%{$search}%");
            }
            if (!Input::get('year')){
                $q->orWhere('year','like',"%{$search}%");
            }
            if (!Input::get('technique')){
                $q->orWhere('technique','like',"%{$search}%");
            }
        });
    }
    
    return $query->get();
});


Route::get('/category/{subcat?}/{search?}',function($subcat = NULL, $search = NULL){
    
    if (in_array($subcat,array('artist','country','technique'))){
        if (empty($search)){
            return Film::distinct()->lists($subcat);
        }
        return Film::where('country','like',"%{$search}%")->distinct()->lists($subcat);
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

Route::post('/settings',function(){
   
    Setting::set('page-title',Input::get('page-title'));
    
    return Redirect::to('settings');
});