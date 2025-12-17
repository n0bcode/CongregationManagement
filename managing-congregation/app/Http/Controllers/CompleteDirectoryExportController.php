<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DirectoryReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class CompleteDirectoryExportController extends Controller
{
    public function __construct(
        private DirectoryReportService $reportService
    ) {}

    /**
     * Export complete directory as single PDF
     */
    public function exportPdf()
    {
        $data = [
            'communion' => $this->reportService->getDirectoryCommunion(),
            'index' => $this->reportService->getMemberIndex(),
            'birthdays' => $this->reportService->getBirthdaysByMonth(),
            'deceased' => $this->reportService->getDeceasedMembers(),
            'stats' => $this->reportService->getStatistics(),
        ];

        $pdf = Pdf::loadView('reports.directory.complete', $data);
        $pdf->setPaper('a4');
        
        $filename = 'AFE_Directory_Complete_' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export complete directory as single DOCX
     */
    public function exportDocx()
    {
        $communion = $this->reportService->getDirectoryCommunion();
        $index = $this->reportService->getMemberIndex();
        $birthdays = $this->reportService->getBirthdaysByMonth();
        $deceased = $this->reportService->getDeceasedMembers();
        $stats = $this->reportService->getStatistics();

        $phpWord = new PhpWord();
        
        // Cover Page
        $section = $phpWord->addSection();
        $section->addTitle('AFE SALESIAN PROVINCE', 1);
        $section->addTitle('DIRECTORY ' . now()->year . '-' . (now()->year + 1), 1);
        $section->addTextBreak(3);
        $section->addText('Total Members: ' . $stats['total_members'], ['size' => 14]);
        $section->addText('Total Communities: ' . $stats['total_communities'], ['size' => 14]);
        $section->addTextBreak(2);
        $section->addText('Generated: ' . now()->format('d F Y'), ['size' => 12, 'color' => '666666']);
        
        // Table of Contents
        $section->addPageBreak();
        $section->addTitle('TABLE OF CONTENTS', 2);
        $section->addTextBreak();
        $section->addText('1. HOUSES/COMMUNITIES (Communion)');
        $section->addText('2. INDEX CONFRERES (Alphabetical)');
        $section->addText('3. BIRTHDAYS (By Month)');
        $section->addText('4. DECEASED SALESIANS');
        
        // Section 1: Communion
        $section->addPageBreak();
        $section->addTitle('HOUSES/COMMUNITIES', 1);
        $section->addTextBreak();
        
        foreach ($communion as $community) {
            $section->addTitle($community['code'] . ' - ' . $community['name'], 3);
            
            if ($community['location']) {
                $section->addText('Address: ' . $community['location']);
            }
            if ($community['phone']) {
                $section->addText('Tel: ' . $community['phone']);
            }
            if ($community['email']) {
                $section->addText('Email: ' . $community['email']);
            }
            
            $section->addTextBreak();
            $section->addText('Members:', ['bold' => true]);

            foreach ($community['members'] as $member) {
                $text = $member['role_code'] . '. ' . $member['full_name'];
                $section->addText($text, ['bold' => true]);
                
                $details = '   DOB: ' . $member['dob']->format('d.m.Y');
                if ($member['first_profession']) {
                    $details .= '  |  1st PR: ' . $member['first_profession']->format('d.m.Y');
                }
                if ($member['ordination']) {
                    $details .= '  |  ORD: ' . $member['ordination']->format('d.m.Y');
                }
                $section->addText($details);
                $section->addTextBreak();
            }

            $section->addTextBreak(2);
        }
        
        // Section 2: Index
        $section->addPageBreak();
        $section->addTitle('INDEX CONFRERES', 1);
        $section->addTextBreak();
        
        foreach ($index as $member) {
            $text = $member['surname'] . ' ' . $member['given_name'] . ' (' . $member['role_code'] . ')';
            $section->addText($text, ['bold' => true]);
            
            $details = '   DOB: ' . $member['dob']->format('d.m.Y');
            if ($member['first_profession']) {
                $details .= '  |  1st PR: ' . $member['first_profession']->format('d.m.Y');
            }
            if ($member['ordination']) {
                $details .= '  |  ORD: ' . $member['ordination']->format('d.m.Y');
            }
            $details .= '  |  House: ' . $member['house_code'];
            
            $section->addText($details);
            $section->addTextBreak();
        }
        
        // Section 3: Birthdays
        $section->addPageBreak();
        $section->addTitle('BIRTHDAYS', 1);
        $section->addTextBreak();
        
        foreach ($birthdays as $monthData) {
            $section->addTitle(strtoupper($monthData['month_name']), 3);
            $section->addTextBreak();
            
            foreach ($monthData['members'] as $member) {
                $text = $member['day'] . ' - ' . $member['surname'] . ' ' . $member['given_name'];
                $section->addText($text, ['bold' => true]);
                $section->addText('   ' . $member['dob']->format('d.m.Y') . ' (Age: ' . $member['age'] . ') | House: ' . $member['house_code']);
                $section->addTextBreak();
            }
            
            $section->addTextBreak();
        }
        
        // Section 4: Deceased
        $section->addPageBreak();
        $section->addTitle('DECEASED SALESIANS', 1);
        $section->addTextBreak();
        
        foreach ($deceased as $member) {
            $text = $member['role_code'] . '. ' . $member['surname'] . ' ' . $member['given_name'];
            $section->addText($text, ['bold' => true]);
            
            $details = '   Died: ' . $member['date_of_death']->format('d-m-Y');
            if ($member['age_at_death']) {
                $details .= ' (Age: ' . $member['age_at_death'] . ')';
            }
            $details .= '  |  House: ' . $member['house_code'];
            
            $section->addText($details);
            if ($member['dob']) {
                $section->addText('   Born: ' . $member['dob']->format('d.m.Y'), ['color' => '666666']);
            }
            $section->addTextBreak();
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'directory_complete_');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        $filename = 'AFE_Directory_Complete_' . now()->format('Y-m-d') . '.docx';
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
