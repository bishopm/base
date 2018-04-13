<?php

namespace Bishopm\Connexion\Mail;

use Illuminate\Mail\Mailable;
use Bishopm\Connexion\Models\Setting;

class GenericMail extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $emaildata;

    protected $theme = '';

    public function __construct($emaildata)
    {
        $this->emaildata=$emaildata;
        dd($this);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->emaildata->hasFile('attachment')) {
            return $this->subject($this->emaildata->subject)
                    ->from($this->emaildata->sender)
                    ->attach($this->emaildata->file('attachment'), array('as' => $this->emaildata->file('attachment')->getClientOriginalName(), 'mime' => $this->emaildata->file('attachment')->getMimeType()))
                    ->markdown('connexion::emails.generic');
        } else {
            return $this->subject($this->emaildata->subject)
                    ->from($this->emaildata->sender)
                    ->markdown('connexion::emails.generic');
        }
    }
}
