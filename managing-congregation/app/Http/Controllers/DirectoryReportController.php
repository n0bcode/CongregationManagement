<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DirectoryReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MemberIndexExport;
use App\Exports\BirthdayExport;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class DirectoryReportController extends Controller
{
    public function __construct(
        private DirectoryReportService $reportService
    ) {}

    /**
     * Generate full directory communion report
     */
    public function communion(string $format)
    {
        $data = $this->reportService->getDirectoryCommunion();

        return match($format) {
            'pdf' => $this->generateCommunionPdf($data),
            'docx' => $this->generateCommunionDocx($data),
            default => abort(404, 'Format not supported')
        };
    }

    /**
     * Generate member index report
     */
    public function index(string $format)
    {
        $data = $this->reportService->getMemberIndex();

        return match($format) {
            'pdf' => $this->generateIndexPdf($data),
            'excel' => Excel::download(new MemberIndexExport($data), 'member-index.xlsx'),
            default => abort(404, 'Format not supported')
        };
    }

    /**
     * Generate birthdays report
     */
    public function birthdays(string $format)
    {
        $data = $this->reportService->getBirthdaysByMonth();

        return match($format) {
            'pdf' => $this->generateBirthdaysPdf($data),
            'excel' => Excel::download(new BirthdayExport($data), 'birthdays.xlsx'),
            default => abort(404, 'Format not supported')
        };
    }

    /**
     * Generate deceased members report
     */
    public function deceased(string $format)
    {
        $data = $this->reportService->getDeceasedMembers();

        return match($format) {
            'pdf' => $this->generateDeceasedPdf($data),
            'docx' => $this->generateDeceasedDocx($data),
            default => abort(404, 'Format not supported')
        };
    }

    /**
     * Generate single community report
     */
    public function community(int $id, string $format)
    {
        $data = $this->reportService->getCommunityDetail($id);

        return match($format) {
            'pdf' => $this->generateCommunityPdf($data),
            'docx' => $this->generateCommunityDocx($data),
            default => abort(404, 'Format not supported')
        };
    }

    // PDF Generation Methods

    private function generateCommunionPdf($data)
    {
        $pdf = Pdf::loadView('reports.directory.communion', ['communities' => $data]);
        return $pdf->download('directory-communion-' . now()->format('Y-m-d') . '.pdf');
    }

    private function generateIndexPdf($data)
    {
        $pdf = Pdf::loadView('reports.directory.index', ['members' => $data]);
        return $pdf->download('member-index-' . now()->format('Y-m-d') . '.pdf');
    }

    private function generateBirthdaysPdf($data)
    {
        $pdf = Pdf::loadView('reports.directory.birthdays', ['months' => $data]);
        return $pdf->download('birthdays-' . now()->format('Y-m-d') . '.pdf');
    }

    private function generateDeceasedPdf($data)
    {
        $pdf = Pdf::loadView('reports.directory.deceased', ['members' => $data]);
        return $pdf->download('deceased-members-' . now()->format('Y-m-d') . '.pdf');
    }

    private function generateCommunityPdf($data)
    {
        $pdf = Pdf::loadView('reports.directory.community', ['community' => $data]);
        return $pdf->download('community-' . $data['code'] . '-' . now()->format('Y-m-d') . '.pdf');
    }

    // DOCX Generation Methods

    private function generateCommunionDocx($data)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Title
        $section->addTitle('AFE SALESIAN PROVINCE DIRECTORY ' . now()->year, 1);
        $section->addTitle('COMMUNION', 2);
        $section->addTextBreak(2);

        foreach ($data as $community) {
            // Community header
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

        // Save to temp file and download
        $tempFile = tempnam(sys_get_temp_dir(), 'directory_communion_');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, 'directory-communion-' . now()->format('Y-m-d') . '.docx')
            ->deleteFileAfterSend(true);
    }

    private function generateDeceasedDocx($data)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addTitle('Deceased Salesians', 1);
        $section->addTextBreak();

        foreach ($data as $member) {
            $text = $member['role_code'] . '. ' . $member['surname'] . ' ' . $member['given_name'];
            $section->addText($text, ['bold' => true]);
            
            $details = '   Died: ' . $member['date_of_death']->format('d-m-Y');
            if ($member['age_at_death']) {
                $details .= ' (Age: ' . $member['age_at_death'] . ')';
            }
            $details .= '  |  House: ' . $member['house_code'];
            
            $section->addText($details);
            $section->addTextBreak();
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'deceased_');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, 'deceased-members-' . now()->format('Y-m-d') . '.docx')
            ->deleteFileAfterSend(true);
    }

    private function generateCommunityDocx($data)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addTitle($data['code'] . ' - ' . $data['name'], 1);
        $section->addTextBreak();

        if ($data['location']) {
            $section->addText('Address: ' . $data['location']);
        }
        if ($data['phone']) {
            $section->addText('Tel: ' . $data['phone']);
        }
        if ($data['email']) {
            $section->addText('Email: ' . $data['email']);
        }

        $section->addTextBreak(2);
        $section->addText('Members (' . $data['member_count'] . '):', ['bold' => true]);
        $section->addTextBreak();

        foreach ($data['members'] as $member) {
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
            
            if ($member['phone']) {
                $section->addText('   Phone: ' . $member['phone']);
            }
            if ($member['email']) {
                $section->addText('   Email: ' . $member['email']);
            }
            
            $section->addTextBreak();
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'community_');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, 'community-' . $data['code'] . '-' . now()->format('Y-m-d') . '.docx')
            ->deleteFileAfterSend(true);
    }
}
