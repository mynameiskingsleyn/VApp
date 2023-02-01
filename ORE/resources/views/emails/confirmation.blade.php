 @php
  $makeName = $feedback['make'];
  $appreciate_text = 'We appreciate your patience and your passion for style and race-inspired performance.';
  switch(strtolower($feedback['make'])){
    case 'chrysler':
      $makeName = 'CHRYSLER®';
      $appreciate_text = 'We appreciate your patience and your taste for sophisticated innovation.';
      break;
    case 'jeep':
      $makeName = 'JEEP®';
      $appreciate_text = 'We appreciate your patience and your desire for new adventures, either on or off-road.';
      break;
    case 'dodge':
      $makeName = 'DODGE®';
      $appreciate_text = 'We appreciate your patience and your pursuit of high-octane performance.';
      break;
    case 'fiat':
      $makeName = 'FIAT®';
      $appreciate_text = 'We appreciate your patience and your eye for trend-setting design and excitement.';
      break;
    case 'ram':
      $makeName = 'RAM®';
      $appreciate_text = 'We appreciate your patience and your drive, whether the job is work, play or just your everyday.';
      break;
    case 'alfa romeo':
      $makeName = 'ALFA ROMEO®';
      $appreciate_text = 'We appreciate your patience and your passion for style and race-inspired performance.';
      break;
  }
 @endphp
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="650px" style="font-family: arial; font-size: 17px; line-height: 34px; border: 1px solid #05485e; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
  <tr>
    <td style="background: #05485e; text-align: center; padding: 10px; color: #ffffff; padding: 20px;" valign="top"> </td>
  </tr>
  <tr>
    <td style="padding: 20px; font-size: 14px; line-height: 1.5" valign="top">
        <p style="margin: 0px 0px 10px 0px;"> Hello {{ strtoupper($feedback['name']) }},</p>
        <p style="padding-bottom: 2px; margin: 0px; ">Thank you for your interest in our legendary {{ $makeName }} vehicles.</p>

        <p style="margin: 10px 0px">Your submission has been received and a representative from the dealership will be contacting you shortly to confirm your preferences and vehicle availability.</p>

        <p style="padding-bottom: 10px; margin: 0px 0px 15px 0px;">{{ $appreciate_text }}</p>


    <p style="text-align: center; margin: 0px; line-height: 1.5;">{{ $feedback['dealerName'] }}</p>

    <p style="text-align: center;  margin: 0px; line-height: 1.5;">
{{--    @if(!empty($feedback['dealerAddress1'])) {{ $feedback['dealerAddress1'] }}, @endif--}}
{{--    @if(!empty($feedback['dealerCity'])) {{ $feedback['dealerCity'] }},  @endif--}}
{{--    @if(!empty($feedback['dealerState'])) {{ $feedback['dealerState'] }},  @endif--}}
{{--    {{ substr($feedback['dealerZip'],0,5) }}--}}
        {!! $feedback['fullAddress'] !!}
  	</p>

    <p style="text-align: center;  margin: 0px; line-height: 1.5;">{{$feedback['phoneNumber']}}</p>

        <p style="text-align: center;  margin: 0px; line-height: 1.5;" >{{ $feedback['year'] }}/{{ $feedback['make'] }}/{{ $feedback['model'] }}</p>
        <p style="text-align: center;  margin: 0px; line-height: 1.5;" >{{ $feedback['vin'] }}</p>

        <p style="padding-top: 10px; padding-bottom: 10px; margin: 15px 0px 10px 0px;"><a href="{{ $feedback['qrcodemail'] }}">Click Here</a> to learn more about the amazing features of this vehicle by exploring our engaging video content.</p>

        <p style="padding-bottom: 10px; margin: 0px 0px 10px 0px;">Your dealer can scan the QR code below in <span style="color:red;">iShowroomPRO</span> to quickly identify your submission and vehicle preferences upon visiting the dealership.</p>

	{{-- {!! QrCode::format('png')->size(230)->generate($feedback['qrcode']); !!}

	<img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(230)->generate($feedback['qrcode'])) !!} " > --}}

	  <img src="{!! $message->embedData(QrCode::format('png')->size(230)->generate($feedback['qrcode']), 'QrCode.png', 'image/png')!!}">



    </td>
  </tr>
  <tr>
    <td valign="middle" style="background: #05485e;padding: 20px;color: #ffffff; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; font-size: 14px; line-height: 20px;">
       <p style="font-size: 14px; line-height: 22px">
        Sincerely,<BR/>
          Your {{ $makeName }} Brand Team
      </p>
    </td>
  </tr>
</table>


</body>
</html>
