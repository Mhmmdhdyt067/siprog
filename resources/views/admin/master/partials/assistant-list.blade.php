<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[52rem] text-left text-[12px]">
            <thead class="table-head">
                <tr>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Nama Asisten</th>
                    <th class="px-4 py-3">Kelas Diampu</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($assistants as $asisten)
                    @php
                        $fill = [
                            'name' => $asisten->name,
                            'nim_nip' => $asisten->nim_nip,
                            'email' => $asisten->email,
                            'phone' => $asisten->phone,
                            'assisted_classroom_ids' => $asisten->assistedClassrooms->pluck('id')->all(),
                        ];
                    @endphp
                    <tr class="transition hover:bg-[#F9F9F6]">
                        <td class="px-4 py-3 font-mono text-[11px] text-[#0A66C8]">{{ $asisten->nim_nip }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[10px] font-extrabold uppercase text-white">
                                    {{ mb_substr($asisten->name, 0, 1) }}
                                </span>
                                <span class="font-bold">{{ $asisten->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($asisten->assistedClassrooms as $kelas)
                                    <span class="chip chip-sky">{{ $kelas->name }}</span>
                                @empty
                                    <span class="chip chip-amber">Belum diampu</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-[11.5px] text-slate-600">{{ $asisten->email }}</p>
                            <p class="mt-0.5 text-[10px] text-slate-500">{{ $asisten->phone ?: '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" class="icon-btn" data-form-modal="assistant-modal"
                                    data-action="{{ route('admin.master.users.update', $asisten) }}" data-method="PUT"
                                    data-title="Edit Asisten" data-submit="Simpan Perubahan"
                                    data-modal-description="Kosongkan kata sandi jika tidak ingin menggantinya."
                                    data-fill="{{ json_encode($fill) }}" aria-label="Edit {{ $asisten->name }}">
                                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                                </button>
                                <button type="button"
                                    class="icon-btn hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                    data-delete="{{ route('admin.master.users.destroy', $asisten) }}"
                                    data-title="Hapus asisten?" data-message="Akun asisten akan kehilangan akses ke kelas yang diampu."
                                    data-confirm="Ya, hapus" aria-label="Hapus {{ $asisten->name }}">
                                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <p class="text-[13px] font-bold">Belum ada data asisten</p>
                            <p class="mt-1 text-[11px] text-slate-500">Tambahkan asisten baru melalui tombol di atas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($assistants->hasPages())
    <div class="rounded-2xl border border-black/5 bg-white px-3 py-2">{{ $assistants->links() }}</div>
@endif
