<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * courts.type is the real, live tier field that the Court add/edit
     * forms already write to (dropdown-driven from court_types) — not
     * Grade_levels, which the create_courts_table migration file names
     * today but which was edited in place after it had already run, so
     * it never existed on the live table. Every seeded court still holds
     * an old-scheme Somali ordinal value (Kowaad/Labaad/.../KowaadDegmo)
     * from before court_types gained its proper 5-tier English set
     * (2026_08_05_...add_court_types.php equivalent seeding); this
     * reclassifies each by courtcode — stable/unique, unlike free-text
     * longName — to the matching new tier.
     *
     * CRT016 (Hodan) is the one district court that was entered as
     * "Darajada Labaad" instead of "KowaadDegmo" like its 12 siblings;
     * its name and role are an ordinary district court, so it's folded
     * in as District Court rather than left stranded on a value nothing
     * else uses.
     *
     * CRT006 (Administrator), CRT020 (AGO), and CRT021 (CID) are
     * non-judicial placeholder rows in the courts table (used only so
     * non-court staff have a courtID) and are deliberately left alone.
     */
    public function up(): void
    {
        $legacyToNewType = [
            'District Court'  => ['KowaadDegmo', 'Darajada Labaad'],
            'Regional Court'  => ['Kowaad'],
            'Appellate Court' => ['Labaad'],
            'High Court'      => ['Afaraad'],
            'Supreme Court'   => ['Sadaxaad'],
        ];

        $districtCourtcodes = [
            'CRT007', 'CRT008', 'CRT009', 'CRT010', 'CRT011', 'CRT012',
            'CRT013', 'CRT014', 'CRT015', 'CRT016', 'CRT017', 'CRT018', 'CRT019',
        ];

        DB::table('courts')->whereIn('courtcode', $districtCourtcodes)->update(['type' => 'District Court']);

        DB::table('courts')->where('courtcode', 'CRT002')->update(['type' => 'Regional Court']);
        DB::table('courts')->where('courtcode', 'CRT003')->update(['type' => 'Appellate Court']);
        DB::table('courts')->where('courtcode', 'CRT004')->update(['type' => 'Supreme Court']);
        DB::table('courts')->where('courtcode', 'CRT005')->update(['type' => 'High Court']);

        // Retire (not delete) the old 3-value Somali ordinal set now that
        // every real court has been moved onto the 5-value English set,
        // so the Court add/edit dropdown stops offering both at once.
        DB::table('court_types')
            ->whereIn('name', ['Darajada Koobaad', 'Darajada Labaad', 'Darajada Sadaxaad'])
            ->update(['status' => 'inactive']);
    }

    public function down(): void
    {
        DB::table('court_types')
            ->whereIn('name', ['Darajada Koobaad', 'Darajada Labaad', 'Darajada Sadaxaad'])
            ->update(['status' => 'active']);

        // Original legacy values aren't recoverable per-row (several
        // district courts shared one value already before this ran) —
        // down() restores the pre-5-tier grouping, not byte-for-byte.
        $districtCourtcodes = [
            'CRT007', 'CRT008', 'CRT009', 'CRT010', 'CRT011', 'CRT012',
            'CRT013', 'CRT014', 'CRT015', 'CRT017', 'CRT018', 'CRT019',
        ];
        DB::table('courts')->whereIn('courtcode', $districtCourtcodes)->update(['type' => 'KowaadDegmo']);
        DB::table('courts')->where('courtcode', 'CRT016')->update(['type' => 'Darajada Labaad']);
        DB::table('courts')->where('courtcode', 'CRT002')->update(['type' => 'Kowaad']);
        DB::table('courts')->where('courtcode', 'CRT003')->update(['type' => 'Labaad']);
        DB::table('courts')->where('courtcode', 'CRT004')->update(['type' => 'Sadaxaad']);
        DB::table('courts')->where('courtcode', 'CRT005')->update(['type' => 'Afaraad']);
    }
};
