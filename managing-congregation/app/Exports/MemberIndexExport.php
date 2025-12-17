<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MemberIndexExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private Collection $members
    ) {}

    public function collection()
    {
        return $this->members;
    }

    public function headings(): array
    {
        return [
            'Surname',
            'Given Name',
            'Role',
            'Date of Birth',
            '1st Profession',
            'Ordination',
            'House',
        ];
    }

    public function map($member): array
    {
        return [
            $member['surname'],
            $member['given_name'],
            $member['role_code'],
            $member['dob']?->format('d.m.Y'),
            $member['first_profession']?->format('d.m.Y'),
            $member['ordination']?->format('d.m.Y'),
            $member['house_code'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Member Index';
    }
}
