<?php

namespace App\Events;

use App\Models\Anak;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnakCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Anak $anak;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Anak $anak)
    {
        $this->anak = $anak;
    }
}