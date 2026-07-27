<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Kolom Kiri: Pengaturan Redeem -->
      <div class="space-y-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900">Pengaturan Redeem Poin</h3>
          <p class="text-xs text-gray-500 mt-0.5">Konfigurasi konversi dan batas minimal redeem</p>
        </div>

        <form @submit.prevent="$emit('save')" class="space-y-4">
          <!-- Kurs Poin ke Rupiah -->
          <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Kurs Poin ke Rupiah
              </label>
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 whitespace-nowrap">1 poin =</span>
                <input
                  v-model.number="form.kurs_poin_ke_rupiah"
                  type="number"
                  required
                  min="1"
                  step="1"
                  class="w-28 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                />
                <span class="text-sm text-gray-500">rupiah</span>
              </div>
            </div>

            <!-- Vice versa: rupiah ke poin -->
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-500 whitespace-nowrap">Rp 1.000 =</span>
              <span class="text-sm font-semibold text-[#996600]">
                {{ form.kurs_poin_ke_rupiah > 0 ? Math.floor(1000 / form.kurs_poin_ke_rupiah) : 0 }} poin
              </span>
            </div>

            <!-- Preview -->
            <div v-if="form.kurs_poin_ke_rupiah > 0" class="p-2.5 bg-[#f4efe5] rounded-lg text-xs text-[#7a5100]">
              Contoh: 10.000 poin = <span class="font-bold">Rp {{ formatCurrency(10000 * form.kurs_poin_ke_rupiah) }}</span> diskon
            </div>
          </div>

          <!-- Minimal Redeem -->
          <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                Minimal Poin untuk Redeem
              </label>
              <div class="flex items-center gap-2">
                <input
                  v-model.number="form.minimal_poin_redeem"
                  type="number"
                  required
                  min="0"
                  step="100"
                  class="w-36 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                />
                <span class="text-sm text-gray-500">poin</span>
              </div>
            </div>

            <!-- Vice versa: setara rupiah minimal -->
            <div v-if="form.minimal_poin_redeem > 0 && form.kurs_poin_ke_rupiah > 0" class="flex items-center gap-2 text-xs text-gray-500">
              Setara diskon minimal
              <span class="font-semibold text-[#996600]">Rp {{ formatCurrency(form.minimal_poin_redeem * form.kurs_poin_ke_rupiah) }}</span>
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan Pengaturan' }}
          </button>
        </form>
      </div>

      <!-- Kolom Kanan: Tabel Rule Pemerolehan Poin -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-900">Rule Pemerolehan Poin</h3>
            <p class="text-xs text-gray-500 mt-0.5">Poin diberikan otomatis saat transaksi selesai</p>
          </div>
          <button
            @click="$emit('add-rule')"
            class="px-3 py-1.5 bg-[#996600] hover:bg-[#7a5100] text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Rule
          </button>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600">Nama / Rentang Transaksi</th>
                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600">Poin</th>
                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600">Status</th>
                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-600">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="!pointRules || pointRules.length === 0">
                <td colspan="4" class="px-3 py-6 text-center text-xs text-gray-400">Belum ada rule poin</td>
              </tr>
              <tr
                v-for="rule in pointRules"
                :key="rule.id"
                :class="['transition-colors', rule.is_active ? 'hover:bg-gray-50' : 'bg-gray-50 opacity-60']"
              >
                <td class="px-3 py-2.5">
                  <div class="font-medium text-gray-900 text-xs">{{ rule.name }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">
                    Rp {{ formatCurrency(rule.min_amount) }}
                    <span v-if="rule.max_amount"> – Rp {{ formatCurrency(rule.max_amount) }}</span>
                    <span v-else class="text-[#996600]"> ke atas</span>
                  </div>
                </td>
                <td class="px-3 py-2.5 text-center">
                  <span class="inline-flex items-center justify-center w-12 h-6 bg-[#f4efe5] text-[#7a5100] text-xs font-bold rounded-full">
                    {{ rule.points }}
                  </span>
                </td>
                <td class="px-3 py-2.5 text-center">
                  <button
                    @click="$emit('toggle-rule', rule)"
                    :class="[
                      'px-2 py-0.5 text-xs font-medium rounded-full transition-colors',
                      rule.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'
                    ]"
                  >
                    {{ rule.is_active ? 'Aktif' : 'Nonaktif' }}
                  </button>
                </td>
                <td class="px-3 py-2.5 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      @click="$emit('edit-rule', rule)"
                      class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                      title="Edit"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="$emit('delete-rule', rule)"
                      class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors"
                      title="Hapus"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Catatan kurs aktif -->
        <p class="text-xs text-gray-400">
          Kurs aktif: 1 poin = Rp {{ form.kurs_poin_ke_rupiah }} &bull; Redeem minimal {{ formatCurrency(form.minimal_poin_redeem) }} poin
        </p>
      </div>
    </div>

    <!-- Modal Rule -->
    <Teleport to="body">
    <div
      v-if="showRuleModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      @click.self="$emit('close-rule-modal')"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] px-5 py-3 flex items-center justify-between rounded-t-xl">
          <h3 class="text-sm font-semibold text-white">
            {{ editingRule ? 'Edit Rule Poin' : 'Tambah Rule Poin' }}
          </h3>
          <button @click="$emit('close-rule-modal')" class="p-1 hover:bg-white/20 rounded-full transition-colors">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="$emit('save-rule')" class="p-5 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Rule</label>
            <input
              v-model="ruleForm.name"
              type="text"
              required
              placeholder="Contoh: Transaksi Kecil"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Min. Transaksi (Rp)</label>
              <input
                v-model.number="ruleForm.min_amount"
                type="number"
                required
                min="0"
                step="1"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">
                Maks. Transaksi (Rp)
                <span class="font-normal text-gray-400">opsional</span>
              </label>
              <input
                :value="ruleForm.max_amount ?? ''"
                @input="ruleForm.max_amount = $event.target.value !== '' ? parseInt($event.target.value) : null"
                type="number"
                min="0"
                step="1"
                placeholder="Kosong = tak terbatas"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Poin Diberikan</label>
            <div class="flex items-center gap-2">
              <input
                v-model.number="ruleForm.points"
                type="number"
                required
                min="0"
                class="w-28 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
              <span class="text-sm text-gray-500">poin per transaksi</span>
            </div>
            <p v-if="ruleForm.points > 0 && form.kurs_poin_ke_rupiah > 0" class="text-xs text-[#7a5100] mt-1">
              Setara Rp {{ formatCurrency(ruleForm.points * form.kurs_poin_ke_rupiah) }} diskon
            </p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              Deskripsi <span class="font-normal text-gray-400">opsional</span>
            </label>
            <input
              v-model="ruleForm.description"
              type="text"
              placeholder="Keterangan tambahan..."
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="ruleForm.is_active = !ruleForm.is_active"
              :class="[
                'relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none',
                ruleForm.is_active ? 'bg-[#996600]' : 'bg-gray-200'
              ]"
            >
              <span :class="['pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200', ruleForm.is_active ? 'translate-x-4' : 'translate-x-0']" />
            </button>
            <span class="text-sm text-gray-700">{{ ruleForm.is_active ? 'Aktif' : 'Nonaktif' }}</span>
          </div>

          <div class="flex gap-2 pt-2">
            <button
              type="button"
              @click="$emit('close-rule-modal')"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="ruleFormLoading"
              class="flex-1 px-4 py-2 bg-[#996600] hover:bg-[#7a5100] text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50"
            >
              {{ ruleFormLoading ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    </Teleport>
  </div>
</template>

<script setup>
defineProps({
  form: { type: Object, required: true },
  loading: Boolean,
  pointRules: { type: Array, default: () => [] },
  showRuleModal: Boolean,
  editingRule: { type: Object, default: null },
  ruleForm: { type: Object, required: true },
  ruleFormLoading: Boolean,
});

defineEmits(['save', 'add-rule', 'edit-rule', 'delete-rule', 'toggle-rule', 'save-rule', 'close-rule-modal']);

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(value || 0);
</script>
