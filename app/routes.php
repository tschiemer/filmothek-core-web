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

Route::group(array('prefix'=>'settings'),function(){
    
    Route::get('/',function(){
        return View::make('settings');
    });

    Route::get('/ajax',function(){ 
    //    if (Request::ajax()){
            return Setting::all()->toJson();
    //    }
    });

    Route::post('/ajax',function(){


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

    Route::post('/upload/{key}',function($key){

        if (!Input::hasFile('file') or !Input::file('file')->isValid()){
            App::abort(500,'invalid file');
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




    Route::get('/film',function(){
        return Film::all()->toJson();
    });

    Route::delete('/film',function(){
        Film::truncate();
    });

    Route::get('/film/{film}',function(Film $film){
        return $film->toJson();
    });

    Route::post('/film/{film}',function(Film $film){

        if (Input::get('title')){
            $film->title = Input::get('title');
        }
        if (Input::get('title_en')){
            $film->title_en = Input::get('title_en');
        }
        if (Input::get('artist')){
            $film->artist = Input::get('artist');
        }
        if (Input::get('country')){
            $film->country = Input::get('country');
        }
        if (Input::get('length')){
            $film->country = Input::get('length');
        }
        if (Input::get('technique')){
            $film->country = Input::get('technique');
        }

        $film->save();

    });

    Route::post('/film/{$film}',function(Film $film){
        $film->delete();
    });

    Route::post('/import-films',function(){
        $fields = array(
            'nr','title','title_en','artist','country','year','length','technique'
        );
        $existingNumbers = Film::lists('nr');
        Excel::load(Input::file('file')->getRealPath(), function($reader)use($fields,$existingNumbers) {
            foreach($reader->all() as $row){
                foreach($fields as $f){
                    if (!$row->has($f)){
                        App::abort('500','Spaltennamen nicht definiert: '.$f . print_r($row,true));
                    }
                }
                if ($row->nr == null){
                    continue;
                }
                $film = null;
                if (in_array(strval($row->nr),$existingNumbers)){
                    $film = Film::where('nr','=',$row->nr)->first();
                }
                if (empty($film)){
                    $film = new Film();
                    $film->save();
                }

                foreach($fields as $f){
                    $film->$f = $row->$f;
                }

                $film->update();
            }
        });
    });

    Route::get('/scan-for-files',function(){
        $dirPath = Setting::get('dirFilesReal',  public_path('films'));
        if (empty($dirPath)){
            App::abort(500,'No path set for files');
        }
        if (!file_exists($dirPath)){
            App::abort(500, 'File-path does not exist.');
        }
        if (filetype($dirPath) != 'dir'){
            App::abort(500, 'Filepath is not a directory.');
        }
        $files = scandir($dirPath);
        $files = implode("\n",$files);

    //    echo nl2br($files);

        foreach(Film::all() as $film){
    //        echo "{$film->nr}<br/>";
            // scan for poster
            if (preg_match("/(?:.*){$film->nr}(?:.*)\.(jpg|jpeg|png|gif)/",$files,$matches)){
    //            echo "Matches for poster {$film->nr}<br/>";
                $film->poster = $matches[0];
            } else {
                $film->poster = null;
            }

            // scan for poster
            if (preg_match("/(?:.*){$film->nr}(?:.*)\.(mov|mp4|mv4)/",$files,$matches)){
    //            echo "Matches for video {$film->nr}<br/>";
                $film->video = $matches[0];
            } else {
                $film->video = null;
            }

            $film->update();
        }
    });
});


//Route::get('/uploads',function(){
//    return;
//});