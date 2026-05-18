<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DTR extends Controller
{
    public function showForm()
    {
        $internId = Session::get('intern_id');
        if (!$internId) {
            return redirect()->route('intern.login');
        }

        $intern = Intern::find($internId);
        if (!$intern) {
            Session::forget('intern_id');
            return redirect()->route('intern.login');
        }

        return view('interns.dtr', compact('intern'));
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'intern_id' => ['required', 'integer', 'min:1'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'incharge' => ['required', 'string', 'max:255'],
            'am_official_arrival' => ['required', 'date_format:H:i'],
            'am_official_departure' => ['required', 'date_format:H:i'],
            'pm_official_arrival' => ['required', 'date_format:H:i'],
            'pm_official_departure' => ['required', 'date_format:H:i'],
        ]);

        $internId = (int) $validated['intern_id'];
        $year = (int) $validated['year'];
        $month = (int) $validated['month']; // 1-12
        $incharge = $validated['incharge'];

        if ($internId <= 0 || $year <= 0 || $month < 1 || $month > 12) {
            return 'Please provide valid query parameters: intern_id, year, and month.';
        }

        $intern = Intern::find($internId);
        if (!$intern) {
            return 'Intern not found.';
        }

        $templatePath = storage_path('app/templates/dtr.docx');
        $template = new TemplateProcessor($templatePath);

        // Official working hours (provided by intern per generation)
        $am_official_arr = $validated['am_official_arrival'];
        $am_official_dep = $validated['am_official_departure'];
        $pm_official_arr = $validated['pm_official_arrival'];
        $pm_official_dep = $validated['pm_official_departure'];

        // Populate intern details in template
        $fullName = trim($intern->first_name . ' ' . ($intern->middle_name[0].'.' ?? '') . ' ' . $intern->last_name);
        $template->setValue('name', strtoupper($fullName));

        $template->setValue('ar1', date('g:i', strtotime($am_official_arr)));
        $template->setValue('dep1', date('g:i', strtotime($am_official_dep)));
        $template->setValue('ar2', date('g:i', strtotime($pm_official_arr)));
        $template->setValue('dep2', date('g:i', strtotime($pm_official_dep)));

        // In-charge / supervisor name from user input
        $template->setValue('incharge', strtoupper($incharge));

        // Build daily attendance data for the selected month from logs
        $daysDataFirstMonth = $this->buildMonthlyAttendanceData($internId, $year, $month);

        // Second half (next month) if needed by the template
        $nextMonth = $month === 12 ? 1 : $month + 1;
        $nextYear  = $month === 12 ? $year + 1 : $year;
        $daysDataSecondMonth = $this->buildMonthlyAttendanceData($internId, $nextYear, $nextMonth);

        // Fill the DTR tables
        $mergeLabelsFirst = $this->fillDtrTable(
            $template,
            $year,
            $month,
            '',
            'd',
            $am_official_arr,
            $am_official_dep,
            $pm_official_arr,
            $pm_official_dep,
            $daysDataFirstMonth
        );

        $mergeLabelsSecond = $this->fillDtrTable(
            $template,
            $nextYear,
            $nextMonth,
            '2',
            'd2',
            $am_official_arr,
            $am_official_dep,
            $pm_official_arr,
            $pm_official_dep,
            $daysDataSecondMonth
        );

        // Ensure output folder exists
        $generatedDir = storage_path('app/generated');
        if (!is_dir($generatedDir)) {
            mkdir($generatedDir, 0777, true);
        }

        $outputPath = $generatedDir . '/DTR_' . $internId . '_' . $year . '_' . $month . '_' . date('Ymd_His') . '.docx';
        $template->saveAs($outputPath);

        $this->applyNonWorkingRowMerges($outputPath, $mergeLabelsFirst, $mergeLabelsSecond);

        return response()->download($outputPath)->deleteFileAfterSend();
    }

    private function fillDtrTable(
        $template,
        int $year,
        int $monthNum,
        string $suffix,
        string $rowPlaceholder,
        string $off_a1,
        string $off_d1,
        string $off_a2,
        string $off_d2,
        array $daysData
    ): array {
        $dateObj = \DateTime::createFromFormat('Y-n-j', "$year-$monthNum-1");
        $mName = $dateObj->format('F');
        $yNum = $dateObj->format('Y');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $yNum);

        $template->setValue('month' . $suffix, $mName . ', ' . $yNum);
        $template->cloneRow($rowPlaceholder, 31);

        $holidayLabelsByDay = $this->holidayLabelsByDay($year, $monthNum);
        $mergeLabels = [];

        $grandTotalMinutes = 0;

        for ($i = 1; $i <= 31; $i++) {
            $rowRef = $rowPlaceholder . "#" . $i;
            $date = Carbon::createFromDate((int) $yNum, $monthNum, $i);
            $isWeekend = (int) $date->format('N') >= 6;
            $label = null;

            if ($i <= $daysInMonth) {
                if ($date->isSaturday()) {
                    $label = 'SATURDAY';
                } elseif ($date->isSunday()) {
                    $label = 'SUNDAY';
                } elseif (isset($holidayLabelsByDay[$i])) {
                    $label = $holidayLabelsByDay[$i];
                }
            }

            $isGray = ($i > $daysInMonth) || $isWeekend || $label !== null;

            $template->setValue($rowRef, $i);

            if ($isGray) {
                $this->clearRow($template, $i, $suffix);
                if ($label !== null) {
                    $mergeLabels[$i] = $label;
                }
                continue;
            }

            $dailyUndertime = 0;

            $actual_am_arrival   = $daysData[$i]['am_arrival'] ?? null;
            $actual_am_departure = $daysData[$i]['am_departure'] ?? null;
            $actual_pm_arrival   = $daysData[$i]['pm_arrival'] ?? null;
            $actual_pm_departure = $daysData[$i]['pm_departure'] ?? null;

            // Calculate undertime only when we have the corresponding scan
            if ($actual_am_arrival !== null && strtotime($actual_am_arrival) > strtotime($off_a1)) {
                $dailyUndertime += (strtotime($actual_am_arrival) - strtotime($off_a1)) / 60;
            }
            if ($actual_am_departure !== null && strtotime($actual_am_departure) < strtotime($off_d1)) {
                $dailyUndertime += (strtotime($off_d1) - strtotime($actual_am_departure)) / 60;
            }
            if ($actual_pm_arrival !== null && strtotime($actual_pm_arrival) > strtotime($off_a2)) {
                $dailyUndertime += (strtotime($actual_pm_arrival) - strtotime($off_a2)) / 60;
            }
            if ($actual_pm_departure !== null && strtotime($actual_pm_departure) < strtotime($off_d2)) {
                $dailyUndertime += (strtotime($off_d2) - strtotime($actual_pm_departure)) / 60;
            }

            $grandTotalMinutes += $dailyUndertime;

            $h = floor($dailyUndertime / 60);
            $m = round($dailyUndertime % 60);

            $template->setValue(
                "a1" . $suffix . "#" . $i,
                $actual_am_arrival ? date("g:i", strtotime($actual_am_arrival)) : '---'
            );
            $template->setValue(
                "d1" . $suffix . "#" . $i,
                $actual_am_departure ? date("g:i", strtotime($actual_am_departure)) : '---'
            );
            $template->setValue(
                "a2" . $suffix . "#" . $i,
                $actual_pm_arrival ? date("g:i", strtotime($actual_pm_arrival)) : '---'
            );
            $template->setValue(
                "d2" . $suffix . "#" . $i,
                $actual_pm_departure ? date("g:i", strtotime($actual_pm_departure)) : '---'
            );
            $template->setValue("h" . $suffix . "#" . $i, $h > 0 ? $h : '00');
            $template->setValue("m" . $suffix . "#" . $i, $m > 0 ? $m : '00');
        }

        $totalHours = floor($grandTotalMinutes / 60);
        $totalMins = round($grandTotalMinutes % 60);

        $template->setValue('total_h' . $suffix, $totalHours > 0 ? $totalHours : '00');
        $template->setValue('total_m' . $suffix, $totalMins > 0 ? $totalMins : '00');

        return $mergeLabels;
    }

    /**
     * Build a label map for weekday holidays in a given month.
     *
     * Weekend holidays are ignored (Sat/Sun are already non-working).
     *
     * @return array<int, string> day => label
     */
    private function holidayLabelsByDay(int $year, int $month): array
    {
        $first = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $last = $first->copy()->endOfMonth();

        /** @var \Illuminate\Support\Collection<int, \App\Models\Holiday> $holidays */
        $holidays = Holiday::query()
            ->whereBetween('holiday_date', [$first->toDateString(), $last->toDateString()])
            ->orderBy('holiday_date')
            ->get();

        $map = [];
        foreach ($holidays as $holiday) {
            $d = Carbon::parse($holiday->holiday_date)->startOfDay();
            if ($d->isWeekend()) {
                continue;
            }

            $day = (int) $d->format('j');
            $map[$day] = 'HOLIDAY';
        }

        return $map;
    }

    /**
     * Merge cells for non-working rows (HOLIDAY / SATURDAY / SUNDAY) in the generated DOCX.
     *
     * This is done post-generation using OpenTBS' ZIP capabilities to safely edit the document XML.
     *
     * @param array<int, string> $firstMonthLabels  day => label
     * @param array<int, string> $secondMonthLabels day => label
     */
    private function applyNonWorkingRowMerges(string $docxPath, array $firstMonthLabels, array $secondMonthLabels): void
    {
        if (empty($firstMonthLabels) && empty($secondMonthLabels)) {
            return;
        }

        $documentXmlPath = 'word/document.xml';

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName($documentXmlPath);
        if (!is_string($xml) || $xml === '') {
            $zip->close();
            return;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        if (@$dom->loadXML($xml) === false) {
            $zip->close();
            return;
        }

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $tables = $xpath->query('//w:tbl');
        if (!$tables) {
            $zip->close();
            return;
        }

        $dtrTables = [];
        foreach ($tables as $tbl) {
            if (!$tbl instanceof \DOMElement) {
                continue;
            }

            $dayToRow = $this->extractDayRowsFromTable($xpath, $tbl);
            if (count($dayToRow) >= 28) {
                $dtrTables[] = $dayToRow;
            }
        }

        if (count($dtrTables) >= 1) {
            $this->mergeLabeledRows($xpath, $dtrTables[0], $firstMonthLabels);
        }

        if (count($dtrTables) >= 2) {
            $this->mergeLabeledRows($xpath, $dtrTables[1], $secondMonthLabels);
        }

        $zip->deleteName($documentXmlPath);
        $zip->addFromString($documentXmlPath, $dom->saveXML());
        $zip->close();
    }

    /**
     * @return array<int, \DOMElement> day => w:tr
     */
    private function extractDayRowsFromTable(\DOMXPath $xpath, \DOMElement $tbl): array
    {
        $rows = $xpath->query('./w:tr', $tbl);
        if (!$rows) {
            return [];
        }

        $map = [];
        foreach ($rows as $tr) {
            if (!$tr instanceof \DOMElement) {
                continue;
            }

            $firstCellText = $this->getFirstCellText($xpath, $tr);
            if ($firstCellText === null) {
                continue;
            }

            if (!ctype_digit($firstCellText)) {
                continue;
            }

            $day = (int) $firstCellText;
            if ($day < 1 || $day > 31) {
                continue;
            }

            $map[$day] = $tr;
        }

        return $map;
    }

    private function getFirstCellText(\DOMXPath $xpath, \DOMElement $tr): ?string
    {
        $cell = $xpath->query('./w:tc[1]', $tr)?->item(0);
        if (!$cell instanceof \DOMElement) {
            return null;
        }

        $texts = $xpath->query('.//w:t', $cell);
        if (!$texts) {
            return null;
        }

        $out = '';
        foreach ($texts as $t) {
            if ($t instanceof \DOMElement) {
                $out .= $t->nodeValue;
            }
        }

        $out = trim($out);
        return $out === '' ? null : $out;
    }

    /**
     * @param array<int, \DOMElement> $dayToRow
     * @param array<int, string> $labels day => label
     */
    private function mergeLabeledRows(\DOMXPath $xpath, array $dayToRow, array $labels): void
    {
        if (empty($labels)) {
            return;
        }

        foreach ($labels as $day => $label) {
            if (!isset($dayToRow[$day])) {
                continue;
            }

            $this->mergeRowCellsIntoOne($xpath, $dayToRow[$day], $label);
        }
    }

    private function mergeRowCellsIntoOne(\DOMXPath $xpath, \DOMElement $tr, string $label): void
    {
        $cells = $xpath->query('./w:tc', $tr);
        if (!$cells || $cells->length < 4) {
            return;
        }

        // Merge only columns 2 to 4 (keep day column and last 2 columns).
        $startIdx = 1; // 2nd column
        $endIdx = 4;   // 5th column
        $span = ($endIdx - $startIdx) + 1;

        $startCell = $cells->item($startIdx);
        if (!$startCell instanceof \DOMElement) {
            return;
        }

        // Ensure tcPr exists
        $tcPr = $xpath->query('./w:tcPr', $startCell)?->item(0);
        if (!$tcPr instanceof \DOMElement) {
            $tcPr = $startCell->ownerDocument->createElementNS($startCell->namespaceURI, 'w:tcPr');
            $startCell->insertBefore($tcPr, $startCell->firstChild);
        }

        // Replace any existing gridSpan
        $existingGridSpan = $xpath->query('./w:gridSpan', $tcPr)?->item(0);
        if ($existingGridSpan instanceof \DOMElement) {
            $tcPr->removeChild($existingGridSpan);
        }

        $gridSpan = $startCell->ownerDocument->createElementNS($startCell->namespaceURI, 'w:gridSpan');
        $gridSpan->setAttributeNS($startCell->namespaceURI, 'w:val', (string) $span);
        $tcPr->appendChild($gridSpan);

        // Clear all existing cell content except tcPr
        foreach (iterator_to_array($startCell->childNodes) as $child) {
            if ($child instanceof \DOMElement && $child->localName === 'tcPr') {
                continue;
            }
            $startCell->removeChild($child);
        }

        // Add a single paragraph with the label (in merged cell), with explicit font size 9pt.
        $docNs = $startCell->namespaceURI;
        $p = $startCell->ownerDocument->createElementNS($docNs, 'w:p');
        $r = $startCell->ownerDocument->createElementNS($docNs, 'w:r');

        $rPr = $startCell->ownerDocument->createElementNS($docNs, 'w:rPr');
        $sz = $startCell->ownerDocument->createElementNS($docNs, 'w:sz');
        // Word uses half-points; 9pt = 18.
        $sz->setAttributeNS($docNs, 'w:val', '18');
        $rPr->appendChild($sz);
        $r->appendChild($rPr);

        $t = $startCell->ownerDocument->createElementNS($docNs, 'w:t');
        $t->nodeValue = $label;
        $r->appendChild($t);
        $p->appendChild($r);
        $startCell->appendChild($p);

        // Remove only the cells that are being merged into the start cell (3rd and 4th columns).
        for ($i = $endIdx; $i > $startIdx; $i--) {
            $tc = $cells->item($i);
            if ($tc instanceof \DOMElement) {
                $tr->removeChild($tc);
            }
        }
    }

    /**
     * Build per-day attendance (AM/PM arrival/departure) for a given intern/month.
     *
     * @return array<int, array<string, string|null>>
     */
    private function buildMonthlyAttendanceData(int $internId, int $year, int $month): array
    {
        $logs = AttendanceLog::getLogsForMonth($internId, $year, $month);

        $daysData = [];
        foreach ($logs as $log) {
            $dt = $log->scan_time;
            $dt->setTimezone(new \DateTimeZone('Asia/Manila'));
            $day = (int) $dt->format('j');
            $time = $dt->format('H:i');
            $hour = (int) $dt->format('H');

            if (!isset($daysData[$day])) {
                $daysData[$day] = [
                    'am_arrival'   => null,
                    'am_departure' => null,
                    'pm_arrival'   => null,
                    'pm_departure' => null,
                ];
            }

            // Before noon (e.g. 8:00–11:59): IN = AM arrival, OUT = AM departure.
            // Noon hour (12:00–12:59): OUT = AM departure (e.g. 12:30 time-out), IN = PM arrival (e.g. 12:40 time-in).
            // After noon (13:00+): IN = PM arrival, OUT = PM departure.
            $scanType = $log['scan_type'] ?? '';

            if ($hour < 12) {
                if ($scanType === 'IN') {
                    if ($daysData[$day]['am_arrival'] === null || $time < $daysData[$day]['am_arrival']) {
                        $daysData[$day]['am_arrival'] = $time;
                    }
                } elseif ($scanType === 'OUT') {
                    if ($daysData[$day]['am_departure'] === null || $time > $daysData[$day]['am_departure']) {
                        $daysData[$day]['am_departure'] = $time;
                    }
                }
            } elseif ($hour === 12) {
                if ($scanType === 'OUT') {
                    if ($daysData[$day]['am_departure'] === null || $time > $daysData[$day]['am_departure']) {
                        $daysData[$day]['am_departure'] = $time;
                    }
                } elseif ($scanType === 'IN') {
                    if ($daysData[$day]['pm_arrival'] === null || $time < $daysData[$day]['pm_arrival']) {
                        $daysData[$day]['pm_arrival'] = $time;
                    }
                }
            } else {
                if ($scanType === 'IN') {
                    if ($daysData[$day]['pm_arrival'] === null || $time < $daysData[$day]['pm_arrival']) {
                        $daysData[$day]['pm_arrival'] = $time;
                    }
                } elseif ($scanType === 'OUT') {
                    if ($daysData[$day]['pm_departure'] === null || $time > $daysData[$day]['pm_departure']) {
                        $daysData[$day]['pm_departure'] = $time;
                    }
                }
            }
        }

        return $daysData;
    }

    private function clearRow($template, $i, $suffix)
    {
        $cols = ['a1', 'd1', 'a2', 'd2', 'h', 'm'];
        foreach ($cols as $col) {
            $template->setValue($col . $suffix . "#" . $i, '00'); // display as gray placeholder
        }
    }
}