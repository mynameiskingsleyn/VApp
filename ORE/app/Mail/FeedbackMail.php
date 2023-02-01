<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;
	public $feedback;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     *
     * @return $th""
     */
    public function build()
    {
		 $this->buildAddress();
		 return $this->view('emails.confirmation',['feedback' => $this->feedback])->subject('DriveFCA: Request Received');

    }

    public function buildAddress()
    {
        $feedback = $this->feedback;
        $keys = ['dealerAddress1','dealerCity','dealerState','dealerZip'];
        $address = "";
        if(!empty($this->feedback['dealerAddress1'] ) && !empty($feedback['dealerName'])){
            foreach($keys as $add){
                if($add == 'dealerZip'){
                    $address .= substr($feedback['dealerZip'],0,5);
                }else{
                    if(isset($feedback[$add]) && !empty($feedback[$add])){
                        $address .= $feedback[$add] .", <br>";
                    }
                }
            }
        }
        $this->feedback['fullAddress'] = $address;
    }
}
