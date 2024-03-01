<?php

namespace App\Dto\Tasks;

use Illuminate\Support\Facades\Date;

class CreateTaskDto
{
    public int $user_id;

    public string $title;

    public string $description;

    public string $due_date;

    public string $status;

    public function __construct(int $user_id, string $title, string $description, string $due_date, string $status)
    {
        $this->user_id = $user_id;
        $this->title = $title;
        $this->description = $description;
        $this->due_date = $due_date;
        $this->status = $status;
    }
}
