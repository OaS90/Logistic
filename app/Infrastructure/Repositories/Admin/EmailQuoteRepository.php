<?php

namespace App\Infrastructure\Repositories\Admin;
use App\Models\EmailQuote;


class EmailQuoteRepository
{
    public function update($emails)
    {
        foreach ($emails as $email) {
            if (isset($email['id'])) {
                $emailEntity = EmailQuote::find($email['id']);
                if ($emailEntity)
                    $emailEntity->update(['active' => $email['active']]);
                else
                    $this->create($email);
            }
        }
    }

    public function create($email)
    {
        EmailQuote::create(['email' => $email['email'], 'active' => $email['active']]);
    }

    public function getAll()
    {
        return EmailQuote::all();
    }

    public function getAllActive()
    {
        return EmailQuote::where('active', true)->get()->all();
    }

    public function getAllActiveEmails()
    {
        return EmailQuote::where('active', true)->get()->pluck('email')->all();
    }
}
