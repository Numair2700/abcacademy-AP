@extends('layout')

@section('content')
<div class="fade-in">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Reporting Dashboard</h1>
        <p class="dashboard-subtitle">Generate comprehensive reports for ABC Academy</p>
    </div>
    
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Generate Reports</h2>
            @if(!$isAdmin)
                <div style="background: #fef3cd; border: 1px solid #fecaca; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #92400e;"><strong>Student Access:</strong> You can view your own enrollment history and academic progress. Administrative reports are restricted to administrators only.</p>
                </div>
            @endif
        </div>
        
        <div class="reports-container">
            <!-- Report Generation Form -->
            <form id="reportForm" class="report-form">
                @csrf
                
                <div class="form-row">
                    <!-- Report Type Selection -->
                    <div class="form-group">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select id="reportType" name="type" class="form-select">
                            <option value="">Select a report type</option>
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Output Format Selection -->
                    <div class="form-group">
                        <label for="format" class="form-label">Output Format</label>
                        <select id="format" name="format" class="form-select">
                            @foreach($formats as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filters Section -->
                <div id="filtersSection" class="filters-section">
                    <h4 class="filters-title">Filters</h4>
                    
                    <!-- Date Range Filters -->
                    <div class="form-row" id="dateFilters">
                        <div class="form-group">
                            <label for="dateFrom" class="form-label">From Date</label>
                            <input type="date" id="dateFrom" name="filters[date_from]" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="dateTo" class="form-label">To Date</label>
                            <input type="date" id="dateTo" name="filters[date_to]" class="form-input">
                        </div>
                    </div>
                    
                    <!-- Program and Course Filters -->
                    <div class="form-row" id="programCourseFilters">
                        <div class="form-group">
                            <label for="programId" class="form-label">Program</label>
                            <select id="programId" name="filters[program_id]" class="form-select">
                                <option value="">All Programs</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="courseId" class="form-label">Course</label>
                            <select id="courseId" name="filters[course_id]" class="form-select">
                                <option value="">All Courses</option>
                                <!-- Options will be loaded dynamically based on program selection -->
                            </select>
                        </div>
                    </div>

                    <!-- Student Filter (for Student and Enrollment reports) -->
                    <div class="form-row" id="studentFilterGroup" style="display: none;">
                        <div class="form-group">
                            <label for="studentId" class="form-label">Student</label>
                            <div class="searchable-select">
                                <input type="text" id="studentSearch" class="form-input" placeholder="Search students..." autocomplete="off">
                                <select id="studentId" name="filters[student_id]" class="form-select" style="display: none;">
                                    <option value="">All Students</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                                <div id="studentDropdown" class="search-dropdown" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="report-actions">
                    <button type="button" id="previewBtn" class="btn btn-info">
                        Preview
                    </button>
                    <button type="button" id="generateBtn" class="btn btn-success">
                        Generate Report
                    </button>
                    <button type="button" id="exportBtn" class="btn btn-warning">
                        Export Report
                    </button>
                </div>
            </form>

            <!-- Preview Section -->
            <div id="previewSection" class="preview-section">
                <h4 class="section-title">Report Preview</h4>
                <div id="previewContent" class="preview-content">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>

            <!-- Report Results Section -->
            <div id="resultsSection" class="results-section">
                <h4 class="section-title">Report Results</h4>
                <div id="resultsContent" class="results-content">
                    <!-- Report results will be displayed here -->
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="loading-indicator">
                <div class="loading-spinner"></div>
                <p class="loading-text">Generating report...</p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for dynamic functionality -->
<script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Reports page loaded');
            
            // Test if elements exist
            const reportTypeSelect = document.getElementById('reportType');
            const filtersSection = document.getElementById('filtersSection');
            const previewBtn = document.getElementById('previewBtn');
            const generateBtn = document.getElementById('generateBtn');
            const exportBtn = document.getElementById('exportBtn');
            
            console.log('Elements found:', {
                reportTypeSelect: !!reportTypeSelect,
                filtersSection: !!filtersSection,
                previewBtn: !!previewBtn,
                generateBtn: !!generateBtn,
                exportBtn: !!exportBtn
            });
            
            if (!previewBtn) {
                console.error('Preview button not found');
                return;
            }

            // Show filters when report type is selected
            reportTypeSelect.addEventListener('change', function() {
                if (this.value) {
                    filtersSection.classList.add('show');
                    loadPrograms();
                    loadCourses();
                    
                    // Show/hide filters based on report type
                    const studentFilterGroup = document.getElementById('studentFilterGroup');
                    const dateFilters = document.getElementById('dateFilters');
                    const programCourseFilters = document.getElementById('programCourseFilters');
                    
                    if (this.value === 'students') {
                        // For student reports: show only student filter
                        studentFilterGroup.style.display = 'block';
                        dateFilters.style.display = 'none';
                        programCourseFilters.style.display = 'none';
                        loadStudents();
                    } else if (this.value === 'enrollments') {
                        // For enrollment reports: show all filters including student filter
                        studentFilterGroup.style.display = 'block';
                        dateFilters.style.display = 'grid';
                        programCourseFilters.style.display = 'grid';
                        loadStudents();
                    } else {
                        // For other reports: show only standard filters (no student filter)
                        studentFilterGroup.style.display = 'none';
                        dateFilters.style.display = 'grid';
                        programCourseFilters.style.display = 'grid';
                    }
                } else {
                    filtersSection.classList.remove('show');
                    document.getElementById('studentFilterGroup').style.display = 'none';
                }
            });

            // Load programs dynamically
            function loadPrograms() {
                console.log('Loading programs...');
                fetch('/api/programs')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Programs loaded:', data);
                        const programSelect = document.getElementById('programId');
                        programSelect.innerHTML = '<option value="">All Programs</option>';
                        
                        data.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.id;
                            option.textContent = program.title;
                            programSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading programs:', error);
                        alert('Error loading programs: ' + error.message);
                    });
            }
            
            // Fallback method to load programs from admin page
            function loadProgramsFromAdmin() {
                fetch('/admin/programs')
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const programRows = doc.querySelectorAll('tbody tr');
                        
                        const programSelect = document.getElementById('programId');
                        programSelect.innerHTML = '<option value="">All Programs</option>';
                        
                        programRows.forEach(row => {
                            const cells = row.querySelectorAll('td');
                            if (cells.length >= 2) {
                                const title = cells[0].textContent.trim();
                                const linkElement = cells[0].querySelector('a[href*="/programs/"]');
                                if (linkElement) {
                                    const href = linkElement.getAttribute('href');
                                    const programId = href.split('/').pop();
                                    
                                    const option = document.createElement('option');
                                    option.value = programId;
                                    option.textContent = title;
                                    programSelect.appendChild(option);
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error loading programs from admin:', error));
            }
            
            // Load courses dynamically
            function loadCourses(programId = null) {
                console.log('Loading courses...', programId ? 'for program: ' + programId : 'all courses');
                const url = programId ? `/api/courses/by-program/${programId}` : '/api/courses';
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Courses loaded:', data);
                        const courseSelect = document.getElementById('courseId');
                        courseSelect.innerHTML = '<option value="">All Courses</option>';
                        
                        data.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.textContent = course.title + ' (' + course.code + ')';
                            courseSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading courses:', error);
                        alert('Error loading courses: ' + error.message);
                    });
            }

            // Load students dynamically
            function loadStudents() {
                console.log('Loading students...');
                fetch('/api/students')
                    .then(response => response.json())
                .then(data => {
                    console.log('Students loaded:', data);
                    window.studentsData = data; // Store for search functionality
                    setupStudentSearch();
                    
                    // Debug: Show available students
                    console.log('Available students for filtering:', data.map(s => ({ id: s.id, name: s.name })));
                })
                    .catch(error => {
                        console.error('Error loading students:', error);
                        alert('Error loading students: ' + error.message);
                    });
            }

            // Setup student search functionality
            function setupStudentSearch() {
                const studentSearch = document.getElementById('studentSearch');
                const studentDropdown = document.getElementById('studentDropdown');
                const studentSelect = document.getElementById('studentId');
                let selectedStudent = null;
                
                // Global variable to track selected student ID
                window.selectedStudentId = '';
                
                // Initialize with "All Students" option
                studentSelect.innerHTML = '<option value="">All Students</option>';

                studentSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    if (!searchTerm) {
                        // Show "All Students" option when search is empty
                        const allStudentsItem = document.createElement('div');
                        allStudentsItem.className = 'search-dropdown-item';
                        allStudentsItem.innerHTML = '<strong>All Students</strong>';
                        allStudentsItem.addEventListener('click', function() {
                            studentSearch.value = 'All Students';
                            studentSelect.value = '';
                            selectedStudent = null;
                            window.selectedStudentId = ''; // Clear global variable
                            studentDropdown.style.display = 'none';
                            
                            // Debug logging
                            console.log('All Students selected:', {
                                selectElement: studentSelect,
                                selectValue: studentSelect.value,
                                globalStudentId: window.selectedStudentId
                            });
                        });
                        studentDropdown.innerHTML = '';
                        studentDropdown.appendChild(allStudentsItem);
                        studentDropdown.style.display = 'block';
                    } else {
                        const filteredStudents = window.studentsData.filter(student => 
                            student.name.toLowerCase().includes(searchTerm) ||
                            student.email.toLowerCase().includes(searchTerm)
                        );

                        if (filteredStudents.length > 0) {
                            studentDropdown.innerHTML = '';
                            
                            // Add "All Students" option first
                            const allStudentsItem = document.createElement('div');
                            allStudentsItem.className = 'search-dropdown-item';
                            allStudentsItem.innerHTML = '<strong>All Students</strong>';
                            allStudentsItem.addEventListener('click', function() {
                                studentSearch.value = 'All Students';
                                studentSelect.value = '';
                                selectedStudent = null;
                                window.selectedStudentId = ''; // Clear global variable
                                studentDropdown.style.display = 'none';
                                
                                // Debug logging
                                console.log('All Students selected (search results):', {
                                    selectElement: studentSelect,
                                    selectValue: studentSelect.value,
                                    globalStudentId: window.selectedStudentId
                                });
                            });
                            studentDropdown.appendChild(allStudentsItem);
                            
                            // Add individual students
                            filteredStudents.forEach(student => {
                                const item = document.createElement('div');
                                item.className = 'search-dropdown-item';
                                item.innerHTML = `<strong>${student.name}</strong><br><small>${student.email}</small>`;
                                item.addEventListener('click', function() {
                                    studentSearch.value = student.name;
                                    studentSelect.value = String(student.id); // Ensure it's a string
                                    selectedStudent = student;
                                    window.selectedStudentId = String(student.id); // Set global variable
                                    studentDropdown.style.display = 'none';
                                    
                                    // Force update the select element
                                    studentSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                    
                                    // Debug logging
                                    console.log('Student selected:', {
                                        name: student.name,
                                        id: student.id,
                                        idType: typeof student.id,
                                        selectElement: studentSelect,
                                        selectValue: studentSelect.value,
                                        selectValueType: typeof studentSelect.value,
                                        globalStudentId: window.selectedStudentId
                                    });
                                    
                                    // Verify the value was set
                                    setTimeout(() => {
                                        console.log('Verification - Student ID after selection:', document.getElementById('studentId').value);
                                        console.log('Verification - Global student ID:', window.selectedStudentId);
                                    }, 100);
                                });
                                studentDropdown.appendChild(item);
                            });
                            studentDropdown.style.display = 'block';
                        } else {
                            studentDropdown.style.display = 'none';
                        }
                    }
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.searchable-select')) {
                        studentDropdown.style.display = 'none';
                    }
                });

                // Clear selection when input is cleared
                studentSearch.addEventListener('input', function() {
                    if (!this.value) {
                        studentSelect.value = '';
                        selectedStudent = null;
                    }
                });
            }

            // Add program change event listener
            document.addEventListener('change', function(e) {
                if (e.target.id === 'programId') {
                    loadCourses(e.target.value);
                }
            });
            

            // Preview functionality
            previewBtn.addEventListener('click', function() {
                console.log('Preview button clicked');
                
                const reportType = document.getElementById('reportType').value;
                const format = document.getElementById('format').value;
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;
                const programId = document.getElementById('programId').value;
                const courseId = document.getElementById('courseId').value;
                const studentId = document.getElementById('studentId').value;
                
                if (!reportType) {
                    alert('Please select a report type first');
                    return;
                }
                
                // Build filters object
                const filters = {};
                if (dateFrom) filters.registration_date_from = dateFrom;
                if (dateTo) filters.registration_date_to = dateTo;
                if (programId) filters.program_id = programId;
                if (courseId) filters.course_id = courseId;
                if (studentId) filters.student_id = studentId;
                else if (window.selectedStudentId) filters.student_id = window.selectedStudentId;
                
                // Debug logging for enrollment reports
                if (reportType === 'enrollments') {
                    console.log('=== ENROLLMENT PREVIEW DEBUG ===');
                    console.log('Student ID element:', document.getElementById('studentId'));
                    console.log('Student ID value:', studentId);
                    console.log('Student ID type:', typeof studentId);
                    console.log('Global student ID:', window.selectedStudentId);
                    console.log('All filters:', filters);
                    
                    // Use global variable as fallback if select value is empty
                    if (!studentId && window.selectedStudentId) {
                        filters.student_id = window.selectedStudentId;
                        console.log('Using global student ID as fallback:', window.selectedStudentId);
                    }
                    
                    console.log('Final filters:', filters);
                    console.log('==============================');
                }
                
                const formData = new FormData();
                formData.append('type', reportType);
                formData.append('format', format);
                formData.append('filters', JSON.stringify(filters));
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Show loading
                const loadingIndicator = document.getElementById('loadingIndicator');
                if (loadingIndicator) {
                    loadingIndicator.classList.add('show');
                }
                
                fetch('/reports/preview', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (loadingIndicator) {
                        loadingIndicator.classList.remove('show');
                    }
                if (data.success) {
                    document.getElementById('previewContent').innerHTML = 
                        `<div style="background: #e0f2fe; border: 1px solid #0288d1; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                            <h4 style="margin: 0 0 10px 0; color: #01579b;"> Preview Mode</h4>
                            <p style="margin: 0; color: #0277bd;">${data.message}</p>
                        </div>
                        ${formatReportData(data.preview, data.type)}`;
                    document.getElementById('previewSection').classList.add('show');
                } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    if (loadingIndicator) {
                        loadingIndicator.classList.remove('show');
                    }
                    console.error('Error:', error);
                    alert('Error generating preview');
                });
            });

            // Generate report functionality
            generateBtn.addEventListener('click', function() {
                console.log('Generate button clicked');
                
                const reportType = document.getElementById('reportType').value;
                const format = document.getElementById('format').value;
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;
                const programId = document.getElementById('programId').value;
                const courseId = document.getElementById('courseId').value;
                const studentId = document.getElementById('studentId').value;
                
                if (!reportType) {
                    alert('Please select a report type first');
                    return;
                }
                
                // Build filters object
                const filters = {};
                if (dateFrom) filters.registration_date_from = dateFrom;
                if (dateTo) filters.registration_date_to = dateTo;
                if (programId) filters.program_id = programId;
                if (courseId) filters.course_id = courseId;
                if (studentId) filters.student_id = studentId;
                else if (window.selectedStudentId) filters.student_id = window.selectedStudentId;
                
                const formData = new FormData();
                formData.append('type', reportType);
                formData.append('format', format);
                formData.append('filters', JSON.stringify(filters));
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Show loading (check if element exists)
                const loadingIndicator = document.getElementById('loadingIndicator');
                if (loadingIndicator) {
                    loadingIndicator.classList.add('show');
                }
                
                fetch('/reports/generate', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Generate response:', data);
                    if (loadingIndicator) {
                        loadingIndicator.classList.remove('show');
                    }
                    if (data.success) {
                        const resultsContent = document.getElementById('resultsContent');
                        const resultsSection = document.getElementById('resultsSection');
                        if (resultsContent) {
                            resultsContent.innerHTML = 
                                `<div style="background: #e8f5e8; border: 1px solid #4caf50; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                                    <h4 style="margin: 0 0 10px 0; color: #2e7d32;"> Full Report Generated</h4>
                                    <p style="margin: 0; color: #388e3c;">Complete dataset with ${data.metadata.record_count} records</p>
                                </div>
                                ${formatReportData(data.data, data.metadata.type)}`;
                        }
                        if (resultsSection) {
                            resultsSection.classList.add('show');
                        }
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    if (loadingIndicator) {
                        loadingIndicator.classList.remove('show');
                    }
                    console.error('Error:', error);
                    alert('Error generating report');
                });
            });

            // Export functionality
            exportBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default form submission
                console.log('Export button clicked');
                
                const reportType = document.getElementById('reportType').value;
                const format = document.getElementById('format').value;
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;
                const programId = document.getElementById('programId').value;
                const courseId = document.getElementById('courseId').value;
                const studentId = document.getElementById('studentId').value;
                
                if (!reportType) {
                    alert('Please select a report type first');
                    return;
                }
                
                // Build filters object
                const filters = {};
                if (dateFrom) filters.registration_date_from = dateFrom;
                if (dateTo) filters.registration_date_to = dateTo;
                if (programId) filters.program_id = programId;
                if (courseId) filters.course_id = courseId;
                if (studentId) filters.student_id = studentId;
                else if (window.selectedStudentId) filters.student_id = window.selectedStudentId;
                
                // Use fetch to download the file
                const formData = new FormData();
                formData.append('type', reportType);
                formData.append('format', format);
                formData.append('filters', JSON.stringify(filters));
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                fetch('/reports/export', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Export response status:', response.status);
                    console.log('Export response headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    // Check if it's a file download response
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);
                    
                    if (contentType && (contentType.includes('application/octet-stream') || 
                                       contentType.includes('text/csv') || 
                                       contentType.includes('application/json') || 
                                       contentType.includes('text/html'))) {
                        return response.blob();
                    } else {
                        // If it's not a blob, it might be an error response
                        return response.text().then(text => {
                            console.log('Non-blob response:', text);
                            
                            // Try to parse as JSON first
                            try {
                                const jsonResponse = JSON.parse(text);
                                if (jsonResponse.success === false) {
                                    throw new Error(jsonResponse.error || 'Export failed');
                                }
                            } catch (parseError) {
                                // If not JSON, it's HTML
                                throw new Error('Expected file download but got HTML: ' + text.substring(0, 200));
                            }
                            
                            throw new Error('Expected file download but got: ' + text.substring(0, 100));
                        });
                    }
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    
                    // Set filename based on report type and format
                    const timestamp = new Date().toISOString().split('T')[0];
                    a.download = `${reportType}_report_${timestamp}.${format}`;
                    
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    console.log('File downloaded successfully');
                })
                .catch(error => {
                    console.error('Export error:', error);
                    alert('Export failed: ' + error.message);
                });
            });
        });

        // Function to format report data into readable tables
        function formatReportData(data, type) {
            if (!data || data.length === 0) {
                return '<p style="color: #6b7280; text-align: center; padding: 20px;">No data available</p>';
            }

            // Get the first item to determine column structure
            const firstItem = data[0];
            const columns = Object.keys(firstItem);
            
            // Create table header
            let tableHtml = `
                <div style="overflow-x: auto; margin-top: 15px;">
                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <thead style="background: #f8fafc;">
                            <tr>
            `;
            
            columns.forEach(column => {
                const displayName = column.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                tableHtml += `<th style="padding: 12px 15px; text-align: left; border-bottom: 2px solid #e5e7eb; font-weight: 600; color: #374151;">${displayName}</th>`;
            });
            
            tableHtml += `
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            // Create table rows
            data.forEach((item, index) => {
                const rowClass = index % 2 === 0 ? 'background: #ffffff;' : 'background: #f9fafb;';
                tableHtml += `<tr style="${rowClass}">`;
                
                columns.forEach(column => {
                    let value = item[column];
                    
                    // Format specific values
                    if (column.includes('date') || column.includes('created') || column.includes('updated')) {
                        value = value ? new Date(value).toLocaleDateString() : 'N/A';
                    } else if (column.includes('price') || column.includes('cost')) {
                        value = value ? `$${parseFloat(value).toFixed(2)}` : 'N/A';
                    } else if (typeof value === 'boolean') {
                        value = value ? 'Yes' : 'No';
                    } else if (value === null || value === undefined || value === '') {
                        value = 'N/A';
                    }
                    
                    tableHtml += `<td style="padding: 10px 15px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">${value}</td>`;
                });
                
                tableHtml += `</tr>`;
            });
            
            tableHtml += `
                        </tbody>
                    </table>
                </div>
            `;
            
            return tableHtml;
        }
    </script>
@endsection
