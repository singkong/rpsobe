<?php

namespace App\Enums;

enum RPSStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Revision = 'revision';
    case Approved = 'approved';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'Dalam Review',
            self::Revision => 'Revisi',
            self::Approved => 'Disetujui',
            self::Published => 'Dipublikasi',
            self::Archived => 'Diarsipkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Review => 'yellow',
            self::Revision => 'orange',
            self::Approved => 'green',
            self::Published => 'blue',
            self::Archived => 'red',
        };
    }

    /**
     * @return RPSStatus[]
     */
    public function transitions(): array
    {
        return match ($this) {
            self::Draft => [self::Review, self::Archived],
            self::Review => [self::Approved, self::Revision],
            self::Revision => [self::Draft, self::Review],
            self::Approved => [self::Published, self::Archived],
            self::Published => [self::Archived],
            self::Archived => [],
        };
    }

    public function canTransitionTo(RPSStatus $target): bool
    {
        return in_array($target, $this->transitions(), true);
    }
}
