<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailController extends Mailable
{
    use Queueable, SerializesModels;

    protected $title;
    protected $text;
    public $view;
    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $text, $view, $data)
    {
      $this->title = $name;
      $this->text = $text;
      $this->view = $view;
      $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view)
                    // ->text($this->text)
                    ->subject($this->title)
                    ->with($this->data);
    }
}
