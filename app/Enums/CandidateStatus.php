<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case INTERVIEW = 'interview';
}