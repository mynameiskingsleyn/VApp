<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class DealerInventoryMail extends Mailable
{
    use Queueable, SerializesModels;
	 
	private $currenttime;
	private $DealerList;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dealerlist_jobschedule)
    {
        $this->DealerList = $dealerlist_jobschedule;
		$this->currenttime =  Carbon::now('America/Vancouver')->format('F d, Y H:i:s');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
		return $this->view('emails.dealer_inventory_monitor',['DealerList' => $this->DealerList, 'uptime' => $this->currenttime])->subject('DriveFCA: Dealer Inventory Monitor');
    }
}
