<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * ReportGenerator - Demonstrates Strategy Pattern and Single Responsibility Principle
 * 
 * This class handles the generation of various reports while maintaining
 * clean separation of concerns and following SOLID principles.
 */
abstract class ReportGenerator
{
    protected array $filters = [];
    protected string $format = 'json';

    /**
     * Set filters for the report
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Set output format
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Generate the report - Template method pattern
     */
    public function generate(): array
    {
        $data = $this->collectData();
        $processedData = $this->processData($data);
        return $this->formatData($processedData);
    }


    abstract public function collectData(): Collection;

 
    abstract public function processData(Collection $data): Collection;

    /**
     * Format the processed data
     */
    public function formatData(Collection $data): array
    {
        switch ($this->format) {
            case 'json':
                return $data->toArray();
            case 'csv':
                return $this->toCsv($data);
            case 'html':
                return $this->toHtml($data);
            default:
                return $data->toArray();
        }
    }

    /**
     * Convert data to CSV format
     */
    protected function toCsv(Collection $data): array
    {
        if ($data->isEmpty()) {
            return ['No data available'];
        }

        $csv = [];
        $firstRow = $data->first();
        
        // Handle both Collection items and arrays
        if (is_array($firstRow)) {
            $headers = array_keys($firstRow);
        } else {
            $headers = array_keys($firstRow->toArray());
        }
        
        // Format headers
        $formattedHeaders = array_map(function($header) {
            return str_replace('_', ' ', ucwords($header));
        }, $headers);
        
        $csv[] = '"' . implode('","', $formattedHeaders) . '"';

        foreach ($data as $row) {
            $rowData = is_array($row) ? $row : $row->toArray();
            $formattedRow = [];
            
            foreach ($rowData as $value) {
                // Format values for CSV
                if (is_null($value) || $value === '') {
                    $value = 'N/A';
                } elseif (is_bool($value)) {
                    $value = $value ? 'Yes' : 'No';
                } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                    $value = date('m/d/Y', strtotime($value));
                }
                
                // Escape quotes and wrap in quotes
                $escapedValue = str_replace('"', '""', $value);
                $formattedRow[] = '"' . $escapedValue . '"';
            }
            
            $csv[] = implode(',', $formattedRow);
        }

        return $csv;
    }

    /**
     * Convert data to HTML format
     */
    protected function toHtml(Collection $data): array
    {
        if ($data->isEmpty()) {
            return ['<p>No data available</p>'];
        }

        $html = '<table class="table table-striped" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
        $firstRow = $data->first();
        
        // Handle both Collection items and arrays
        if (is_array($firstRow)) {
            $headers = array_keys($firstRow);
        } else {
            $headers = array_keys($firstRow->toArray());
        }
        
        $html .= '<thead style="background: #f8fafc;"><tr>';
        foreach ($headers as $header) {
            $displayName = str_replace('_', ' ', $header);
            $displayName = ucwords($displayName);
            $html .= '<th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #e5e7eb; font-weight: 600; color: #374151;">' . $displayName . '</th>';
        }
        $html .= '</tr></thead>';
        
        $html .= '<tbody>';
        foreach ($data as $index => $row) {
            $rowClass = $index % 2 === 0 ? 'background: #ffffff;' : 'background: #f9fafb;';
            $html .= '<tr style="' . $rowClass . '">';
            $rowData = is_array($row) ? $row : $row->toArray();
            foreach ($rowData as $value) {
                // Format values
                if (is_null($value) || $value === '') {
                    $value = 'N/A';
                } elseif (is_bool($value)) {
                    $value = $value ? 'Yes' : 'No';
                } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                    $value = date('m/d/Y', strtotime($value));
                }
                $html .= '<td style="padding: 10px 15px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return [$html];
    }
}

