<?php

namespace Remp\MailerModule\Components;

use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;
use Remp\MailerModule\Repository\BatchesRepository;
use Remp\MultiArmedBandit\Lever;
use Remp\MultiArmedBandit\Machine;

class BatchExperimentEvaluation extends Control
{
    /** @var ActiveRow */
    private $batchesRepository;

    public function __construct(BatchesRepository $batchesRepository)
    {
        parent::__construct();
        $this->batchesRepository = $batchesRepository;
    }

    public function render($batchId)
    {
        $batch = $this->batchesRepository->find($batchId);
        $jobBatchTemplates = $batch->related('mail_job_batch_templates');

        $openProbabilities = $this->run($jobBatchTemplates, 'opened');
        $clickProbabilities = $this->run($jobBatchTemplates, 'clicked');

        $this->template->jobBatchTemplates = $jobBatchTemplates;
        $this->template->openProbabilities = $openProbabilities;
        $this->template->clickProbabilities = $clickProbabilities;

        $this->template->setFile(__DIR__ . '/batch_experiment_evaluation.latte');
        $this->template->render();
    }

    private function run($jobBatchTemplates, string $conversionField): array
    {
        $machine = new Machine(10000);
        $zeroStat = [];

        foreach ($jobBatchTemplates as $jobBatchTemplate) {
            if (!$jobBatchTemplate->$conversionField) {
                $zeroStat[$jobBatchTemplate->mail_template->code] = 0;
                continue;
            }
            $machine->addLever(new Lever($jobBatchTemplate->mail_template->code, $jobBatchTemplate->$conversionField, $jobBatchTemplate->sent));
        }

        return $machine->run() + $zeroStat;
    }
}
