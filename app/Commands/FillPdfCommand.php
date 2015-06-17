<?php namespace App\Commands;


use App\Iep\Pdf;
use App\Iep\Student;
use App\Commands\Command;
use App\Events\PdfWasFilled;
use Illuminate\Contracts\Bus\SelfHandling;

class FillPdfCommand extends Command implements SelfHandling {
	public $student;
	public $responses;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($student, $responses)
	{
		$this->student = new Student($student);
		$this->responses = $responses;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		foreach ($this->responses as $response) {
			$formsPath = config('iep.forms_storage_path');
			$formsFile = str_replace('IEP: ', '', $response->form->title) . '.pdf';

			$path_to_blank = $formsPath . $formsFile;
			$renderer = str_replace('IEP: ', '', str_replace('.', '', $response->form->title));

			if (file_exists($path_to_blank)) {
				if (view()->exists("iep.forms.{$renderer}")) {
					$pdf = new Pdf($path_to_blank);

					$existing_fields = $pdf->getDataFields();

					$pdf = new Pdf($path_to_blank);
					$pdf->setFields($existing_fields);
					$pdf->setId($response->form->id);

					view("iep.forms.{$renderer}")
						->with('pdf', $pdf)
						->with('responses', $response)
						->with('student', $this->student)
						->render();

					$path_to_filled = str_random(20) . '.pdf';

					$pdf->fillForm($pdf->fields())
						->flatten()
						->needAppearances()
						->saveAs($path_to_filled);

					if (empty($pdf->getError())) {
						$files[] = $path_to_filled;
					} else {
						$error[$pdf->getId()] = $pdf->getError();
					}
				} else {
					$error[$response->form->id] = 'There is no renderer for this pdf.';
				}
			} else {
				$error[$response->form->id] = 'File does not exist: ' . $formsFile;
			}
		}

		$downloadFile = '';
		if (isset($files)) {
			$downloadFile = event(new PdfWasFilled($files))[0];
		}

		return [ 'file' => $downloadFile, 'error' => (isset($error)) ? $error : [] ];
	}

}
