<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\CreateContactCommand;
use LBHurtado\Missive\Repositories\ContactRepository;

class CreateContactHandler
{
    /** @var ContactRepository */
    protected $contacts;

    /**
     * @param ContactRepository $contacts.
     */
    public function __construct(ContactRepository $contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * @param SendCommand $command
     */
    public function handle(CreateContactCommand $command)
    {
        $this->contacts->firstOrCreate([
            'mobile' => $command->mobile
        ]);
    }
}
