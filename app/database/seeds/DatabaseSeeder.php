<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
                
                $artists = array(
                  'John Smith','Nicolas Provost','Hannelore Frank','Godard'  
                );
                
                $countries = array(
                    'Switzerland','Deutschland','Österreich','Frankreich','Spanien','Venezuela','Belgien','Niederlande','Grossbritannien'
                );
                
                for($i = 0; $i < 10; $i++){
                    $film = new Film();
                    $film->nr = $i;
                    $film->title = 'Titel '.$i;
                    $film->artist = $artists[rand(0,count($artists)-1)];
                    $film->country = $countries[rand(0,count($artists)-1)];
                    
                    $film->poster = rand(0,1) ? null : 'http://video-js.zencoder.com/oceans-clip.png';
                    $film->video =  rand(0,1) ? null : 'http://video-js.zencoder.com/oceans-clip.mp4';
//                    $film->
                    $film->save();
                }
	}

}
