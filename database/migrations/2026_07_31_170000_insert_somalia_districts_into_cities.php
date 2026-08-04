<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Districts of Somalia's 18 administrative regions, keyed by the exact
     * state_name values already present in state_regions.
     */
    private array $districtsByRegion = [
        'Awdal'              => ['Baki', 'Borama', 'Lughaya', 'Zeylac'],
        'Bakool'             => ['Ceel Barde', 'Rab Dhuure', 'Tayeeglow', 'Waajid', 'Xudur'],
        'Banaadir'           => ['Abdiaziz', 'Bondhere', 'Dayniile', 'Dharkeenley', 'Hamar Jajab', 'Hamar Weyne', 'Hodan', 'Howlwadaag', 'Heliwaa', 'Kaaraan', 'Shangaani', 'Shibis', 'Waberi', 'Wadajir', 'Yaaqshiid'],
        'Bari'               => ['Bosaso', 'Bandarbeyla', 'Caluula', 'Iskushuban', 'Qandala', 'Qardho', 'Ufeyn'],
        'Bay'                => ['Baydhaba', 'Buur Hakaba', 'Diinsoor', 'Qansax Dheere'],
        'Galguduud'          => ['Cabudwaaq', 'Cadaado', 'Ceel Buur', 'Ceel Dheer', 'Dhuusamareeb'],
        'Gedo'               => ['Baardheere', 'Beled Xaawo', 'Ceel Waaq', 'Doolow', 'Garbahaarey', 'Luuq'],
        'Hiiraan'            => ['Beledweyne', 'Buulo Burte', 'Jalalaqsi', 'Matabaan', 'Maxaas'],
        'Jubbada Hoose'      => ['Afmadow', 'Badhaadhe', 'Jamaame', 'Kismaayo'],
        'Jubbada Dhexe'      => ["Bu'aale", 'Jilib', 'Saakow'],
        'Shabeellaha Hoose'  => ['Afgooye', 'Baraawe', 'Kurtunwaarey', 'Marka', 'Qoryooley', 'Sablaale', 'Wanlaweyn'],
        'Shabeellaha Dhexe'  => ['Aadan Yabaal', 'Cadale', 'Jowhar', 'Balcad', 'Mahadaay'],
        'Mudug'              => ['Gaalkacyo', 'Galdogob', 'Hobyo', 'Jariiban', 'Xarardheere'],
        'Nugaal'             => ['Burtinle', 'Eyl', 'Garowe'],
        'Sanaag'             => ['Badhan', 'Ceerigaabo', 'Dhahar', 'Laasqoray'],
        'Sool'               => ['Caynabo', 'Laascaanood', 'Taleex', 'Xudun'],
        'Togdheer'           => ['Buuhoodle', 'Burco', 'Oodweyne', 'Sheekh'],
        'Woqooyi Galbeed'    => ['Gabiley', 'Hargeysa', 'Berbera', 'Wajaale'],
    ];

    public function up(): void
    {
        $now = now();
        $regionIds = DB::table('state_regions')->pluck('id', 'state_name');

        $rows = [];
        foreach ($this->districtsByRegion as $regionName => $cities) {
            $regionId = $regionIds[$regionName] ?? null;

            if (! $regionId) {
                continue;
            }

            foreach ($cities as $city) {
                $rows[] = [
                    'city_name'       => $city,
                    'state_region_id' => $regionId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        DB::table('cities')->insertOrIgnore($rows);
    }

    public function down(): void
    {
        $cityNames = collect($this->districtsByRegion)->flatten()->all();
        DB::table('cities')->whereIn('city_name', $cityNames)->delete();
    }
};
