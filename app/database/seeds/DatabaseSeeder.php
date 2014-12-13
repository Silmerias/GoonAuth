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

		$this->call('NoteTypeSeeder');
		$this->call('UserStatusSeeder');
		$this->call('RoleSeeder');
		$this->call('GroupSeeder');

		$this->call('UserSeeder');

		$this->call('GameSeeder');

		if (App::environment('local'))
		{
			$this->call('NoteSeeder');
			$this->call('GameUserSeeder');
		}
	}

}
