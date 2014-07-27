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
                  'John Smith','Nicolas Provost','Hannelore Frank','Godard','Pic','Art','Louis','Vuitton','Buñuel','Jonas Mekas','John Doe','Leonardo DiCaprio','François','Bill','Bob','Alice'
                );
                
                $countries = array(
                    'Switzerland','Deutschland','Österreich','Frankreich','Spanien','Venezuela','Belgien','Niederlande','Grossbritannien','Chile','Bolivien','Norwegen','Schweden','Finland','Russland','Antipoden'
                );
                
                $techniques = array(
                    'Stop Motion','Direkte Animation','Zeichenanimation','Mischtechnik'
                );
                
                for($i = 0; $i < 100; $i++){
                    $film = new Film();
                    $film->nr = $i;
                    $film->title = 'Titel '.$i;
                    $film->title_en = rand(0,1) ? null : 'Titel Eng'.$i;
                    $film->artist = $artists[rand(0,count($artists)-1)];
                    $film->country = $countries[rand(0,count($artists)-1)];
                    $film->length = rand(1,10) . '\''.(rand(0,1) ? '' : rand(0,59).'"');
                    
                    
                    $film->poster = rand(0,1) ? null : 'http://video-js.zencoder.com/oceans-clip.png';
                    $film->video =  rand(0,1) ? null : 'http://video-js.zencoder.com/oceans-clip.mp4';
//                    $film->
                    $film->save();
                }
	}

}
