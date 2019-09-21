<?php

namespace App\Contracts;

interface CommandTicketable
{
	function getTicket();

	function getSMS();
}
