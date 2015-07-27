<?php

$pdf->setField('your-school-district', config('iep.district.name'));
$pdf->setfield('your-city', config('iep.district.city'));

?>

@foreach ($responses->responses as $response)
  @if ($response['type'] == 'text')
    @include('iep._partials.text')
  @elseif ($response['type'] == 'checkbox')
    @include('iep._partials.checkbox', ['split' => '/,\s+/'])
  @endif
@endforeach