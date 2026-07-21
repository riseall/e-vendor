<?php

namespace App\Console\Commands;

use App\Models\VendorAuditQuestionnaireForm;
use Illuminate\Console\Command;

/**
 * Cetak isi master questionnaire ke terminal (untuk review / debug / export).
 *
 *   php artisan audit:list-questionnaire             # semua form aktif
 *   php artisan audit:list-questionnaire --form=kemas_primer
 *   php artisan audit:list-questionnaire --form=kemas_primer --json
 */
class ListAuditQuestionnaire extends Command
{
    protected $signature = 'audit:list-questionnaire
        {--form= : Filter by form code}
        {--json : Output as JSON}';

    protected $description = 'List master questionnaire forms & questions';

    public function handle(): int
    {
        $formCode = $this->option('form');

        $forms = VendorAuditQuestionnaireForm::query()
            ->when($formCode, fn($q) => $q->where('code', $formCode))
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        if ($forms->isEmpty()) {
            $this->warn('Tidak ada form aktif' . ($formCode ? " dengan code '{$formCode}'" : '') . '.');
            $this->line('Jalankan: php artisan db:seed --class=VendorAuditQuestionnaireKemasPrimerSeeder');
            return self::FAILURE;
        }

        $payload = $forms->map(function (VendorAuditQuestionnaireForm $form) {
            return [
                'code'         => $form->code,
                'name'         => $form->name,
                'material'     => $form->materialTypeLabel(),
                'document'     => $form->document_number,
                'total_q'      => $form->questions()->count(),
                'questions'    => $form->questions()->get()->map(fn($q) => [
                    'section'  => $q->section,
                    'question' => $q->question,
                    'type'     => $q->answer_type,
                    'required' => $q->is_required,
                    'depends'  => $q->depends_on_question_code,
                    'order'    => $q->order,
                ])->values(),
            ];
        });

        if ($this->option('json')) {
            $this->line($payload->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        }

        foreach ($payload as $f) {
            $this->line('');
            $this->line(str_repeat('═', 70));
            $this->info(sprintf('[%s] %s', strtoupper($f['code']), $f['name']));
            $this->line("Material : {$f['material']}");
            $this->line("Document : " . ($f['document'] ?? '-'));
            $this->line("Total    : {$f['total_q']} pertanyaan");
            $this->line(str_repeat('─', 70));

            $bySection = collect($f['questions'])->groupBy('section');
            foreach ($bySection as $section => $rows) {
                $this->line('');
                $this->comment("── {$section}");
                foreach ($rows as $r) {
                    $flag = $r['required'] ? ' <required>' : '';
                    $dep  = $r['depends'] ? " (jika Q{$r['depends']}=YES)" : '';
                    $this->line(sprintf(
                        '  #%-3d [%s]%s%s — %s',
                        $r['order'],
                        strtoupper($r['type']),
                        $flag,
                        $dep,
                        mb_substr($r['question'], 0, 70) . (mb_strlen($r['question']) > 70 ? '…' : '')
                    ));
                }
            }
        }

        return self::SUCCESS;
    }
}
