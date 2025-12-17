<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BirthdayExport implements WithMultipleSheets
{
    public function __construct(
        private Collection $months
    ) {}

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->months as $monthData) {
            $sheets[] = new BirthdayMonthSheet(
                $monthData['month_name'],
                $monthData['members']
            );
        }

        return $sheets;
    }
}

class BirthdayMonthSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private string $monthName,
        private Collection $members
    ) {}

    public function collection()
    {
        return $this->members;
    }

    public function headings(): array
    {
        return [
            'Day',
            'Surname',
            'Given Name',
            'Date of Birth',
            'Age',
            'House',
        ];
    }

    public function map($member): array
    {
        return [
            $member['day'],
            $member['surname'],
            $member['given_name'],
            $member['dob']->format('d.m.Y'),
            $member['age'],
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
        return $this->monthName;
    }
}
