<?php

namespace App\Mail;

use App\Models\AdminUser;
use App\Models\Tariff;
use Illuminate\Mail\Mailable;

class TariffPermissionRequest extends Mailable
{
//    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected Tariff $tariff;
    protected AdminUser $user;

    public function __construct(Tariff $tariff, AdminUser $user)
    {
        $this->tariff = $tariff;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->subject('Запрос прав на операции с тарифом: ' . $this->tariff->name)
            ->view('email.quotes', ['tariffName' => $this->tariff->name, 'user' => $this->user,]);
    }
}
