<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.ijaza_types', [
            'hafs' => 'إجازة برواية حفص',
            'warsh' => 'إجازة برواية ورش',
            'qalun' => 'إجازة برواية قالون',
            'duri' => 'إجازة برواية الدوري',
            'susi' => 'إجازة برواية السوسي',
            'shuba' => 'إجازة برواية شعبة',
            'khalaf' => 'إجازة برواية خلف عن حمزة',
            'ibn_kathir' => 'إجازة برواية ابن كثير'
        ]);

        $this->migrator->add('general.qualifications', [
            'high_school' => 'ثانوية عامة',
            'bachelor'    => 'بكالوريوس',
            'master'      => 'ماجستير',
            'phd'         => 'دكتوراه',
        ]);
    }
};
