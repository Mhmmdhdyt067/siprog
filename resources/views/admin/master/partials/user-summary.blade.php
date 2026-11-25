@php
    $isAssistant = $role === 'asisten';
    $roleLabel = $isAssistant ? 'Asisten' : 'Mahasiswa';
@endphp

<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <x-stat-card :label="'Total ' . $roleLabel" :value="$stats['users']" note="Akun aktif terdaftar"
        :icon="$isAssistant ? 'badge' : 'users'" tone="sky" />
    <x-stat-card label="Total Kelas" :value="$stats['classrooms']" note="Kelas praktikum tersedia" icon="building"
        tone="indigo" />
    <x-stat-card :label="$isAssistant ? 'Tanpa Kelas' : 'Belum Ada Kelas'" :value="$stats['without_class']"
        :note="$isAssistant ? 'Belum diampu' : 'Perlu ditempatkan'" icon="clock" tone="amber" />
    <x-stat-card label="Baru Bulan Ini" :value="$stats['new_this_month']" note="Akun ditambahkan" icon="sparkles"
        tone="lime" />
</div>
