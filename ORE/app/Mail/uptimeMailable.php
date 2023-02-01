<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class uptimeMailable extends Mailable
{
    use Queueable, SerializesModels;
	 
	private $currenttime;
	private $UpDownApiList;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($api_list_from_uptimecontroller)
    {
        $this->UpDownApiList = $api_list_from_uptimecontroller;
		$this->currenttime =  Carbon::now('America/Vancouver')->format('F d, Y H:i:s');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
		return $this->view('emails.uptime',['failure_ApiList' => $this->UpDownApiList['failure'], 'success_ApiList' => $this->UpDownApiList['success'], 'uptime' => $this->currenttime])->subject('DriveFCA: Production Monitoring');
    }
}
