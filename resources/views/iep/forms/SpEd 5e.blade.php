<?php

$pdf->setField('your-school-district', config('iep.district.name'));

if (!empty($responses->get('communication-on')) && !empty($responses->get('communication-by'))) {
  $pdf->setField('notice-translated', 'Yes');
}

if (!empty($responses->get('sign-of-interpreter'))) {
  $pdf->setField('adult-understands', 'Yes');
}

if (!empty($responses->get('requirements-attached'))) {
  $pdf->setField('requirements-attached', 'Yes');
}

?>

@foreach ($responses->responses as $response)
  @if ($response['type'] == 'text' || $response['type'] == 'paragraph' || $response['type'] == 'dropdown' || $response['type'] == 'hidden')
    @include('iep._partials.text')
  @elseif ($response['type'] == 'checkbox')
    @include('iep._partials.checkbox', ['split' => '/,\s+/'])
  @endif
@endforeach