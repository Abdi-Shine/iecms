<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Best-effort transcription from a low-resolution source image; a few
     * entries are marked uncertain in the accompanying report and should be
     * verified/corrected via the Public Institutions registry UI.
     */
    private array $institutions = [
        'F.A.C',
        'Laanta Baadhista Canshuuraha',
        "Hay'adda Socdaalka iyo Jinsiyadda",
        'Bankiga Dhexe',
        'XXG. Koonfur Galbeed',
        'XXG. Jubaland',
        'XXG. Puntland',
        'XXG. Hirshabelle',
        'XXG. Galmudug',
        'Wasaaradda Gaashaandhigga',
        'Ciidanka Xoogga Dalka',
        'Maxkamadaha Ciidanka',
        'Wasaaradda Amniga Gudaha',
        "Hay'adda Sirdoonka Qaranka",
        'Xafiiska Madaxtooyada',
        'Xafiiska Baarlamaanka - Golaha Shacabka',
        'Xafiiska Aqalka Sare',
        "Xafiiska Ra'iisul Wasaaraha",
        'Wasaaradda Arrimaha Dibadda',
        'Safaaradaha',
        'Wasaaradda Qorsheynta',
        "Hay'adda Tirakoobka Qaranka",
        'Wasaaradda Shaqada iyo Arrimaha Bulshada',
        'Guddiga Shaqaalaha Rayidka Qaranka',
        'Wasaaradda Arrimaha Gudaha iyo Federaalka',
        "Guddiga Qaxootiga iyo Barakacayaasha Soomaaliyeed",
        "Hay'adda Diiwaangelinta Dadweynaha",
        "Hay'adda Maareynta Musiibooyinka Soomaaliyeed",
        'Wasaaradda Diinta iyo Awqaafka',
        'Wasaaradda Biyaha iyo Tamarta',
        'Wasaaradda Cadaaladda',
        'Ciidanka Asluubta Soomaaliyeed',
        'Guddiga Madaxa Bannaan ee Dib-u-eegista iyo Hirgelinta Dastuurka',
        'Wasaaradda Beeraha iyo Macdanta',
        "Hay'adda Beeraha Soomaaliyeed",
        'Wasaaradda Warfaafinta',
        'Wasaaradda Beeraha',
        'Guddiga Dib-u-heshiisiinta Qaranka',
        'Wasaaradda Isgaarsiinta iyo Tiknoolajiyadda',
        "Hay'adda Isgaarsiinta Qaranka",
        'Wasaaradda Dhalinyarada iyo Ciyaaraha',
        'Wasaaradda Gaadiidka iyo Duulista Hawada',
        "Hay'adda Duulista Rayidka Soomaaliyeed",
        'Xoghaynta Guud ee Qaranka',
        'Guriyeynta Guud',
        'Hantidhawrka Guud',
        'Wasaaradda Kalluumeysiga iyo Dhaqaalaha Badeeda',
        "Xarunta Cilmi-baarista Badda Soomaaliyeed",
        'Maamulka Horumarinta Xeebaha iyo Kalluumeysiga',
        'Wasaaradda Dekedaha iyo Gaadiidka Badda',
        'Maamulka Dekedda Muqdisho',
        'Wasaaradda Hawlaha Guud, Dib-u-dhiska iyo Guriyeynta',
        'Wasaaradda Ganacsiga iyo Warshadaha',
        "Hay'adda Tayada Soomaaliyeed",
        'Wasaaradda Deegaanka iyo Isbeddelka Cimilada',
        'Wasaaradda Caafimaadka',
        'Wasaaradda Xannaanada Xoolaha, Dhirta iyo Daaqa',
        'Wasaaradda Hawlaha iyo Horumarinta Xuquuqda Aadanaha',
        "Hay'adda Nabadda Soomaaliyeed",
        'Wasaaradda Waxbarashada, Hidaha iyo Tacliinta Sare',
        'Jaamacadda Ummadda Soomaaliyeed',
        'Akadeemiyada Cilmiga iyo Fanka Soomaaliyeed',
        'Wasaaradda Maaliyadda',
        'Akadeemiyada Gobolleedka Af-Soomaaliga',
    ];

    public function up(): void
    {
        $now = now();

        DB::table('public_institutions')->insertOrIgnore(
            array_map(fn ($name) => [
                'name'       => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ], $this->institutions)
        );
    }

    public function down(): void
    {
        DB::table('public_institutions')->whereIn('name', $this->institutions)->delete();
    }
};
