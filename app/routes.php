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

if (!Schema::hasTable('settings'))
{
    Route::get('/{any?}',function($any=NULL){
        return 'No database detected. Please migrate first.';
    });
    
    return;
}


Route::get('/', function()
{
	return View::make('public');
});

Route::model('film', 'Film');

Route::post('/film',function(){
    
    $search = Input::get('search');
    
    $query = DB::table('films');
            
    if (Input::get('title')){
        $query = $query->where('title','like','%'.Input::get('title').'%')
                        ->orWhere('title_en','like','%'.Input::get('title').'%');
    }
  //  if (Input::get('title_en')){
//	$query = $query->where
    //}
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
		$q->orWhere('title_en','like',"%{$search}%");
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
            return Setting::all()->toJson(JSON_NUMERIC_CHECK);
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

        if (Input::get('nr',NULL) !== NULL){
            $film->nr = Input::get('nr');
        }
        if (Input::get('title',NULL) !== NULL){
            $film->title = Input::get('title');
        }
        if (Input::get('title_en',NULL) !== NULL){
            $film->title_en = Input::get('title_en');
        }
        if (Input::get('artist',NULL) !== NULL){
            $film->artist = Input::get('artist');
        }
        if (Input::get('country',NULL) !== NULL){
            $film->country = Input::get('country');
        }
        if (Input::get('year',NULL) !== NULL){
            $film->year = Input::get('year');
        }
        if (Input::get('length',NULL) !== NULL){
            $film->length = Input::get('length');
        }
        if (Input::get('technique',NULL) !== NULL){
            $film->technique = Input::get('technique');
        }

        $film->save();

    });

    Route::delete('/film/{$film}',function(Film $film){
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
                
//                try {
//                    $f = $dirPath . '/' . $matches[0];
//                    $image = Intervention\Image\Facades\Image::make($f);
//                    $image->fit(500,400);
//                    $image->save();
//                } catch(Exception $e){
//                    // do nothing
//                }
            } else {
                $film->poster = null;
            }

            // scan for poster
            if (preg_match("/(?:.*){$film->nr}(?:.*)\.(mov|mp4|mv4|m4v)/",$files,$matches)){
    //            echo "Matches for video {$film->nr}<br/>";
                $film->video = $matches[0];
            } else {
                $film->video = null;
            }

            $film->update();
        }
    });
});

//if (Setting::get('dirFilesPublic',false)){
//    Route::get(Setting::get('dirFilesPublic').'/{filename}',function($filename = NULL){
//        $filepath = Setting::get('dirFilesReal',  public_path('films')) . '/' . $filename;
//        
//        if (!file_exists($filepath)){
//            App::abort(404);
//        }
//        
//        return readfile($filepath);
//    });
//}

//Route::get('/uploads',function(){
//    return;
//});
