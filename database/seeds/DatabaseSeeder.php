<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(App\NoteTypeSeeder::class);
		$this->call(App\UserStatusSeeder::class);
		$this->call(App\RoleSeeder::class);
		$this->call(App\GroupSeeder::class);

		$this->call(App\UserSeeder::class);

		$this->call(App\GameSeeder::class);

		if (App::environment('local'))
		{
			$this->call(App\NoteSeeder::class);
			$this->call(App\GameUserSeeder::class);
		}
	}

}
