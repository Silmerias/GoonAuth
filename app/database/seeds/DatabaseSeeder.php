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

		$this->call('UserStatusSeeder');

		if (App::environment('local'))
		{
			$this->call('UserSeeder');
		}
	}

}
