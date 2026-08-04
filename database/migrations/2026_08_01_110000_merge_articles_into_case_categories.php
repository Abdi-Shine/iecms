<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The standalone Articles registry (Penal Code article number + title)
     * duplicated Case Category's existing "Rule / Article" + Description
     * fields. This folds every article into its own Case Category group
     * (kept out of 'Ciqaabta' so it doesn't pollute the criminal sub-case
     * dropdowns other modules already read from that group) and retires
     * the articles table.
     */
    private const CASE_NAME = 'Sharciga Ciqaabta';

    public function up(): void
    {
        if (!Schema::hasTable('articles')) {
            return;
        }

        $now = now();
        $articles = DB::table('articles')->orderBy('article_number')->get();

        $rows = $articles->map(fn ($a) => [
            'case_name'   => self::CASE_NAME,
            'sub_case'    => null,
            'rule'        => 'Qodobka ' . $a->article_number,
            'description' => $a->title,
            'created_at'  => $now,
            'updated_at'  => $now,
        ])->all();

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('case_categories')->insert($chunk);
        }

        Schema::dropIfExists('articles');
    }

    public function down(): void
    {
        Schema::create('articles', function ($table) {
            $table->id();
            $table->unsignedInteger('article_number')->unique();
            $table->string('title');
            $table->timestamps();
        });

        $now = now();
        $restored = DB::table('case_categories')
            ->where('case_name', self::CASE_NAME)
            ->get()
            ->map(function ($c) use ($now) {
                $number = (int) str_replace('Qodobka ', '', $c->rule);

                return [
                    'article_number' => $number,
                    'title'          => $c->description,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            })
            ->all();

        foreach (array_chunk($restored, 200) as $chunk) {
            DB::table('articles')->insertOrIgnore($chunk);
        }

        DB::table('case_categories')->where('case_name', self::CASE_NAME)->delete();
    }
};
