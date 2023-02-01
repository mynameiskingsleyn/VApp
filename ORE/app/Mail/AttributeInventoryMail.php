<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AttributeInventoryMail extends Mailable
{
    use Queueable, SerializesModels;
	 
	private $currenttime,$currentdate;
	private $Attribute;
    private $AuditDetails;
    /**
     * Create a new message instance.
     *  DealerInventoryMail
     * @return void
     */
    public function __construct($Attribute,$audit_details)
    {
        $this->Attribute = $Attribute;
        $this->AuditDetails = $audit_details;
		$this->currenttime =  Carbon::now('America/Vancouver')->format('F d, Y H:i:s');
        $this->currentdate =  Carbon::now('America/Vancouver')->format('d-m-Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 

		return $this->view('emails.attribute_inventory_monitor',['Attribute' => $this->Attribute, 'uptime' => $this->currenttime, 'AuditDetails' => $this->AuditDetails])->subject('DriveFCA: Audit Information on '.$this->currentdate);
    }
}
