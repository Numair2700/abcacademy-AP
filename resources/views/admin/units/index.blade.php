@extends('layout')

@section('content')
<div class="page-header">
    <h1>Unit Management</h1>
    <a href="{{ route('admin.units.create') }}" class="btn btn-primary mt-1">Create New Unit</a>
</div>

<!-- Tutor Assignment Section -->
<div class="item-card mb-2 mt-2">
    <h3>Tutor Assignment</h3>
    <div class="form-container">
        <form method="POST" action="{{ route('admin.units.assign-tutor') }}">
            @csrf
            <div class="form-group">
                <label for="tutor_id">Select Tutor:</label>
                <select id="tutor_id" name="tutor_id" class="form-control" required>
                    <option value="">Choose a tutor...</option>
                    @foreach($tutors as $tutor)
                        <option value="{{ $tutor->id }}">{{ $tutor->name }} - {{ $tutor->specialization }} ({{ $tutor->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="unit_id">Select Unit:</label>
                <select id="unit_id" name="unit_id" class="form-control" required>
                    <option value="">Choose a unit...</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->btec_code }} - {{ $unit->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Assign Tutor</button>
            </div>
        </form>
    </div>
</div>

<!-- Remove Tutor Section -->
<div class="item-card">
    <h3>Remove Tutor from Unit</h3>
    <div class="form-container">
        <form method="POST" action="{{ route('admin.units.remove-tutor') }}">
            @csrf
            <div class="form-group">
                <label for="remove_tutor_id">Select Tutor:</label>
                <select id="remove_tutor_id" name="tutor_id" class="form-control" required>
                    <option value="">Choose a tutor...</option>
                    @foreach($tutors as $tutor)
                        <option value="{{ $tutor->id }}" data-units="{{ $tutor->units->pluck('id')->toJson() }}">{{ $tutor->name }} - {{ $tutor->specialization }} ({{ $tutor->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="remove_unit_id">Select Unit:</label>
                <select id="remove_unit_id" name="unit_id" class="form-control" required>
                    <option value="">First select a tutor...</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Remove Tutor</button>
            </div>
        </form>
    </div>
</div>

<!-- Units Table -->
<div class="course-admin-table-container">
    <div style="overflow-x: auto;">
        <table class="course-admin-table">
            <thead>
                <tr>
                    <th>BTEC Code</th>
                    <th>Title</th>
                    <th>Tutor</th>
                    <th>Credit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($units as $unit)
                    <tr>
                        <td>
                            <span class="course-code-badge">{{ $unit->btec_code }}</span>
                        </td>
                        <td>
                            <div class="course-title-main">{{ $unit->title }}</div>
                        </td>
                        <td>
                            @if($unit->tutors->isNotEmpty())
                                <div class="course-title-main">{{ $unit->tutors->first()->name }}</div>
                                <div class="course-title-sub">{{ $unit->tutors->first()->specialization }}</div>
                            @else
                                <span class="course-title-sub">No tutor assigned</span>
                            @endif
                        </td>
                        <td class="course-price">
                            {{ $unit->credit }} credits
                        </td>
                        <td>
                            @if($unit->published)
                                <span class="course-status-published">Published</span>
                            @else
                                <span class="course-status-draft">Draft</span>
                            @endif
                        </td>
                        <td>
                            <div class="course-actions">
                                <a href="{{ route('admin.units.edit', $unit) }}" class="btn  btn-primary">Edit</a>
                                <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this unit?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="course-empty-state">
                            <div class="course-empty-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="course-empty-title">No units found</h3>
                            <p class="course-empty-text">Get started by creating your first unit.</p>
                            <a href="{{ route('admin.units.create') }}" class="btn btn-primary">Create Unit</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tutorSelect = document.getElementById('remove_tutor_id');
    const unitSelect = document.getElementById('remove_unit_id');
    
    // Store all units data
    const allUnits = @json($units->map(function($unit) {
        return [
            'id' => $unit->id,
            'btec_code' => $unit->btec_code,
            'title' => $unit->title
        ];
    }));
    
    tutorSelect.addEventListener('change', function() {
        const selectedTutorId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        
        // Clear unit options
        unitSelect.innerHTML = '<option value="">Choose a unit...</option>';
        
        if (selectedTutorId) {
            try {
                const assignedUnitIds = JSON.parse(selectedOption.getAttribute('data-units'));
                
                // Filter units that are assigned to this tutor
                const assignedUnits = allUnits.filter(unit => assignedUnitIds.includes(unit.id));
                
                if (assignedUnits.length > 0) {
                    assignedUnits.forEach(unit => {
                        const option = document.createElement('option');
                        option.value = unit.id;
                        option.textContent = `${unit.btec_code} - ${unit.title}`;
                        unitSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No units assigned to this tutor';
                    option.disabled = true;
                    unitSelect.appendChild(option);
                }
            } catch (e) {
                console.error('Error parsing tutor units:', e);
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Error loading units';
                option.disabled = true;
                unitSelect.appendChild(option);
            }
        }
    });
});
</script>
@endsection