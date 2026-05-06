<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Title',
            'Category',
            'Amount (RWF)',
            'Notes',
            'Mobile Money Transaction',
            'Balance After',
            'Day of Week',
            'Month',
            'Year'
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->date->format('Y-m-d'),
            $expense->title,
            $expense->category,
            number_format($expense->amount, 2),
            $expense->notes ?? '',
            $expense->mobile_money_message ? 'Yes' : 'No',
            $expense->detected_balance ? number_format($expense->detected_balance, 2) : '',
            $expense->date->format('l'),
            $expense->date->format('F'),
            $expense->date->format('Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true, 'size' => 12]],
            
            // Set column widths
            'A'  => ['width' => 12],
            'B'  => ['width' => 25],
            'C'  => ['width' => 15],
            'D'  => ['width' => 15],
            'E'  => ['width' => 30],
            'F'  => ['width' => 20],
            'G'  => ['width' => 15],
            'H'  => ['width' => 15],
            'I'  => ['width' => 12],
            'J'  => ['width' => 8],
        ];
    }
}
