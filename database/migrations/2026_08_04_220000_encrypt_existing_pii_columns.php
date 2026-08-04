<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Encrypts existing plaintext PII now covered by an `encrypted` cast
     * on the relevant models. Encrypted ciphertext (base64 JSON envelope
     * with iv/value/mac) is far longer than the original varchar(50)
     * columns, so those are widened to TEXT first. Skips values that
     * already look encrypted, so re-running never double-encrypts.
     */
    private array $targets = [
        'appeal_civil_parties'       => ['pk' => 'PID', 'columns' => ['national_id', 'passport_number']],
        'attorney_case_accused'      => ['pk' => 'id',  'columns' => ['id_number', 'custodian_id_number']],
        'attorney_case_parties'      => ['pk' => 'id',  'columns' => ['national_id']],
        'attorney_case_victims'      => ['pk' => 'id',  'columns' => ['id_number', 'custodian_id_number']],
        'attorney_case_witnesses'    => ['pk' => 'id',  'columns' => ['id_number']],
        'district_civil_parties'     => ['pk' => 'PID', 'columns' => ['national_id', 'passport_number']],
        'district_criminal_parties'  => ['pk' => 'PID', 'columns' => ['national_id', 'passport_number']],
        'district_execution_parties' => ['pk' => 'PID', 'columns' => ['national_id', 'passport_number']],
        'district_family_parties'    => ['pk' => 'PID', 'columns' => ['national_id', 'passport_number']],
    ];

    public function up(): void
    {
        foreach ($this->targets as $table => $config) {
            Schema::table($table, function (Blueprint $blueprint) use ($config) {
                foreach ($config['columns'] as $column) {
                    $blueprint->text($column)->nullable()->change();
                }
            });
        }

        foreach ($this->targets as $table => $config) {
            $pk = $config['pk'];

            foreach ($config['columns'] as $column) {
                $rows = DB::table($table)->whereNotNull($column)->select($pk, $column)->get();

                foreach ($rows as $row) {
                    $value = $row->$column;

                    if ($value === '' || $this->looksEncrypted($value)) {
                        continue;
                    }

                    DB::table($table)->where($pk, $row->$pk)->update([
                        $column => Crypt::encryptString($value),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->targets as $table => $config) {
            $pk = $config['pk'];

            foreach ($config['columns'] as $column) {
                $rows = DB::table($table)->whereNotNull($column)->select($pk, $column)->get();

                foreach ($rows as $row) {
                    $value = $row->$column;

                    if (!$this->looksEncrypted($value)) {
                        continue;
                    }

                    try {
                        $decrypted = Crypt::decryptString($value);
                        DB::table($table)->where($pk, $row->$pk)->update([$column => $decrypted]);
                    } catch (\Throwable $e) {
                        // leave as-is if it doesn't decrypt cleanly
                    }
                }
            }
        }

        foreach ($this->targets as $table => $config) {
            Schema::table($table, function (Blueprint $blueprint) use ($config) {
                foreach ($config['columns'] as $column) {
                    $blueprint->string($column, 50)->nullable()->change();
                }
            });
        }
    }

    private function looksEncrypted(string $value): bool
    {
        $decoded = base64_decode($value, true);

        if ($decoded === false) {
            return false;
        }

        $json = json_decode($decoded, true);

        return is_array($json) && isset($json['iv'], $json['value'], $json['mac']);
    }
};
