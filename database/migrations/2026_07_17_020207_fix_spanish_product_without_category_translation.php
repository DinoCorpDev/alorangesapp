<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->translations() as $langKey => $langValue) {
            DB::table('translations')->updateOrInsert(
                ['lang' => 'es', 'lang_key' => $langKey],
                [
                    'lang_value' => $langValue,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        Artisan::call('optimize:clear');
    }

    public function down(): void
    {
        // Keep corrected Spanish translations in place on rollback.
    }

    private function translations(): array
    {
        return [
            'without_category' => 'Sin categoría',
        ];
    }
};
