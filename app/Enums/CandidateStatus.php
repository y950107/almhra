<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case INTERVIEW = 'interview';
}