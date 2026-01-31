@extends('layout')

@section('content')
<div class="fade-in">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $title }}</h1>
        <p class="dashboard-subtitle">Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Report Details</h2>
            <div class="report-meta">
                <span class="meta-item">Format: {{ strtoupper($format) }}</span>
                <span class="meta-item">Records: {{ count($report) }}</span>
            </div>
        </div>
        
        <div class="report-content">
            <!-- Report Content -->
            @if($format === 'html')
                {!! implode('', $report) !!}
            @elseif($format === 'csv')
                <div class="report-data">
                    <pre>{{ implode("\n", $report) }}</pre>
                </div>
            @else
                <!-- JSON Format -->
                <div class="report-data">
                    <pre>{{ json_encode($report, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="report-actions">
                <a href="{{ route('reports.index') }}" class="btn btn-primary">
                    Back to Reports
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    Print Report
                </button>
                <button onclick="downloadReport()" class="btn btn-success">
                    Download Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
        function downloadReport() {
            const data = @json($report);
            const format = '{{ $format }}';
            const filename = '{{ strtolower($type) }}_report_{{ now()->format("Y-m-d_H-i-s") }}.{{ $format }}';
            
            let content;
            let mimeType;
            
            switch(format) {
                case 'json':
                    content = JSON.stringify(data, null, 2);
                    mimeType = 'application/json';
                    break;
                case 'csv':
                    content = data.join('\n');
                    mimeType = 'text/csv';
                    break;
                case 'html':
                    content = data.join('\n');
                    mimeType = 'text/html';
                    break;
                default:
                    content = JSON.stringify(data);
                    mimeType = 'application/json';
            }
            
            const blob = new Blob([content], { type: mimeType });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>

@endsection

