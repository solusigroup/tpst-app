@extends('layouts.admin')

@section('title', 'Edit Karyawan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <strong>Edit Karyawan: {{ $employee->name }}</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hrd.employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $employee->position) }}">
                            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username (Untuk Login) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $employee->username) }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. KTP</label>
                            <input type="text" name="ktp_number" class="form-control @error('ktp_number') is-invalid @enderror" value="{{ old('ktp_number', $employee->ktp_number) }}">
                            @error('ktp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('gender', $employee->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender', $employee->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Skema Upah</label>
                            <select name="salary_type" id="salary_type" class="form-select @error('salary_type') is-invalid @enderror">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="bulanan" {{ old('salary_type', $employee->salary_type) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="borongan" {{ old('salary_type', $employee->salary_type) == 'borongan' ? 'selected' : '' }}>Borongan</option>
                                <option value="harian" {{ old('salary_type', $employee->salary_type) == 'harian' ? 'selected' : '' }}>Harian</option>
                            </select>
                            @error('salary_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6" id="payment_frequency_container" style="display: {{ old('salary_type', $employee->salary_type) == 'bulanan' ? 'none' : 'block' }};">
                            <label class="form-label">Frekuensi Pembayaran</label>
                            <select name="payment_frequency" class="form-select @error('payment_frequency') is-invalid @enderror">
                                <option value="Mingguan" {{ old('payment_frequency', $employee->payment_frequency) == 'Mingguan' ? 'selected' : '' }}>Mingguan (Tiap Sabtu)</option>
                                <option value="Dua Mingguan" {{ old('payment_frequency', $employee->payment_frequency) == 'Dua Mingguan' ? 'selected' : '' }}>Dua Mingguan</option>
                            </select>
                            @error('payment_frequency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3" id="monthly_salary_container" style="display: {{ old('salary_type', $employee->salary_type) == 'bulanan' ? 'flex' : 'none' }};">
                        <div class="col-md-6">
                            <label class="form-label">Gaji Bulanan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="monthly_salary" class="form-control @error('monthly_salary') is-invalid @enderror" value="{{ old('monthly_salary', $employee->monthly_salary) }}" min="0" step="1">
                            </div>
                            @error('monthly_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3" id="daily_wage_container" style="display: {{ old('salary_type', $employee->salary_type) == 'harian' ? 'flex' : 'none' }};">
                        <div class="col-md-6">
                            <label class="form-label">Upah Harian (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="daily_wage" id="daily_wage" class="form-control @error('daily_wage') is-invalid @enderror" value="{{ old('daily_wage', $employee->daily_wage) }}" min="0" step="1">
                            </div>
                            <small class="text-muted">Default: Laki-laki Rp70.000, Perempuan Rp65.000</small>
                            @error('daily_wage') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $employee->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Profil (Gunakan Kamera / Galeri)</label>
                        @if($employee->photo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($employee->photo) }}" alt="Foto" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                        <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" capture="environment">
                        <small class="text-muted">Format gambar: jpg, png, jpeg. Biarkan kosong jika tidak ingin mengubah foto.</small>
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.hrd.employee.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const salaryTypeSelect = document.getElementById('salary_type');
        const monthlySalaryContainer = document.getElementById('monthly_salary_container');
        const dailyWageContainer = document.getElementById('daily_wage_container');
        const genderSelect = document.getElementById('gender');
        const dailyWageInput = document.getElementById('daily_wage');
        const paymentFrequencyContainer = document.getElementById('payment_frequency_container');

        function toggleSalaryFields() {
            const type = salaryTypeSelect.value;
            monthlySalaryContainer.style.display = (type === 'bulanan') ? 'flex' : 'none';
            dailyWageContainer.style.display = (type === 'harian') ? 'flex' : 'none';
            paymentFrequencyContainer.style.display = (type === 'bulanan') ? 'none' : 'block';
        }

        function updateDefaultWage() {
            // Only update if it's currently empty or one of the defaults
            const currentWage = dailyWageInput.value;
            if (salaryTypeSelect.value === 'harian' && (!currentWage || currentWage == '0' || currentWage == '70000' || currentWage == '65000')) {
                if (genderSelect.value === 'Laki-laki') {
                    dailyWageInput.value = 70000;
                } else if (genderSelect.value === 'Perempuan') {
                    dailyWageInput.value = 65000;
                }
            }
        }

        if(salaryTypeSelect) {
            salaryTypeSelect.addEventListener('change', function() {
                toggleSalaryFields();
                updateDefaultWage();
            });
        }

        if(genderSelect) {
            genderSelect.addEventListener('change', updateDefaultWage);
        }
    });
</script>
@endpush
