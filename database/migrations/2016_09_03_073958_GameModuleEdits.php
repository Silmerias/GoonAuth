<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GameModuleEdits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change the module names.
        DB::transaction(function ()
        {
            DB::table('Game')
                ->where('GAbbr', 'sc')
                ->update(array('GModulePHP' => 'StarCitizen'));

            DB::table('Game')
                ->where('GAbbr', 'ee')
                ->update(array('GModulePHP' => 'EveEchoes'));

            DB::table('Game')
                ->where('GAbbr', 'sc')
                ->update(array('GModulePHP' => 'StarCitizen'));

            DB::table('Game')
                ->where('GAbbr', 'mwo')
                ->update(array('GModulePHP' => 'MWO'));

            DB::table('GameOrg')
                ->where('GOAbbr', 'GOON')
                ->update(array('GOModulePHP' => 'GOON'));

            DB::table('GameOrg')
                ->where('GOAbbr', 'WAFFE')
                ->update(array('GOModulePHP' => 'WAFFE'));

            DB::table('GameOrg')
                ->where('GOAbbr', 'KRMA')
                ->update(array('GOModulePHP' => 'KRMA'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore the old names.
        DB::transaction(function ()
        {
            DB::table('Game')
                ->where('GAbbr', 'sc')
                ->update(array('GModulePHP' => 'StarCitizenGame'));

            DB::table('Game')
                ->where('GAbbr', 'mwo')
                ->update(array('GModulePHP' => 'MWOGame'));

            DB::table('Game')
                ->where('GAbbr', 'ee')
                ->update(array('GModulePHP' => 'EveEchoesGame'));

            DB::table('GameOrg')
                ->where('GOAbbr', 'GOON')
                ->update(array('GOModulePHP' => 'GOONOrg'));

            DB::table('GameOrg')
                ->where('GOAbbr', 'WAFFE')
                ->update(array('GOModulePHP' => 'WAFFEOrg'));

            DB::table('GameOrg')
                ->where('GOAbbr', 'KRMA')
                ->update(array('GOModulePHP' => 'KRMAOrg'));
        });
    }
}
