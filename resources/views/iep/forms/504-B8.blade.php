<?php

$splits = ['impaired', 'some-degree', 'take-into-account', 'identification', 'services'];

?>

@foreach ($responses->responses as $response)
	@if ($response['type'] == 'checkbox')
		@if (in_array($response['field'], $splits))
			@include('iep._partials.checkbox', ['split' => '/,\s+/'])
		@else
			@if (!empty($response['value']))
				<?php $pdf->setField($response['field'], 'Yes'); ?>
			@endif
		@endif
	@else
		@include('iep._partials.text')
	@endif
@endforeach