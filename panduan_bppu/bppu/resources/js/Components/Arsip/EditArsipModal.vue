<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white rounded-t-xl z-10">
        <h2 class="text-xl font-bold text-gray-800">Edit Arsip</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <div v-if="form.jenis === 'image'" class="bg-gray-50 rounded-lg p-4">
          <img :src="form.url" :alt="form.alt_text || form.nama_file" class="max-w-full h-auto rounded-lg mx-auto" style="max-height: 300px;" />
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nama File</label>
          <input
            type="text"
            v-model="form.nama_file"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
          <p class="text-xs text-gray-500 mt-1">Nama file yang ditampilkan (bukan nama file fisik)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Arsip *</label>
            <select
              v-model="form.kategori_arsip"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="">-- Pilih Kategori --</option>
              <option value="gambar_artikel">Gambar Artikel</option>
              <option value="gambar_produk">Gambar Produk</option>
              <option value="gambar_kegiatan">Foto Kegiatan</option>
              <option value="gambar_profil">Gambar Profil</option>
              <option value="dokumen_resmi">Dokumen Resmi</option>
              <option value="dokumen_laporan">Dokumen Laporan</option>
              <option value="dokumen_sk">Surat Keputusan</option>
              <option value="dokumen_surat">Surat Menyurat</option>
              <option value="media_promosi">Media Promosi</option>
              <option value="template">Template</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Direktori</label>
            <select
              v-model="form.folder"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="">-- Tidak ada folder --</option>
              <optgroup label="Gambar Produk/Menu">
                <option value="menu-images">Menu Images</option>
                <option value="menu-displays">Menu Displays</option>
                <option value="canteen-menus">Canteen Menus</option>
                <option value="shop-items">Shop Items</option>
              </optgroup>
              <optgroup label="Artikel & Konten">
                <option value="posts">Posts</option>
                <option value="posts/content">Posts Content</option>
                <option value="pages">Pages</option>
              </optgroup>
              <optgroup label="Kegiatan & Galeri">
                <option value="galeri">Galeri</option>
              </optgroup>
              <optgroup label="Profil & Unit">
                <option value="work-units">Work Units</option>
              </optgroup>
              <optgroup label="Dokumen & Bukti">
                <option value="transaction-proofs">Transaction Proofs</option>
                <option value="bukti-transaksi">Bukti Transaksi</option>
                <option value="bukti-aktivitas">Bukti Aktivitas</option>
              </optgroup>
              <optgroup label="Lainnya">
                <option value="arsip/dokumen">Arsip Dokumen</option>
                <option value="arsip/media">Arsip Media</option>
                <option value="arsip/lainnya">Arsip Lainnya</option>
              </optgroup>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Tags / Kata Kunci</label>
          <div class="space-y-2">
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(tag, index) in form.tags"
                :key="index"
                class="inline-flex items-center px-3 py-1 bg-[#996600] text-white text-sm rounded-full"
              >
                {{ tag }}
                <button
                  type="button"
                  @click="removeTag(index)"
                  class="ml-2 hover:text-gray-300"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>
            </div>
            <div class="flex space-x-2">
              <input
                type="text"
                v-model="newTag"
                @keydown.enter.prevent="addTag"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                placeholder="Ketik tag lalu Enter"
              />
              <button
                type="button"
                @click="addTag"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
              >
                Tambah
              </button>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Lengkap</label>
          <textarea
            v-model="form.deskripsi"
            rows="4"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            placeholder="Jelaskan detail file ini, konteks pengambilan, keperluan, dll"
          ></textarea>
        </div>

        <!-- SEO Fields - Only for artikel/pages -->
        <div v-if="shouldShowSEOFields" class="border-t pt-4 space-y-4">
          <p class="text-sm font-semibold text-gray-700">SEO & Aksesibilitas Web</p>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text (untuk SEO)</label>
            <input
              type="text"
              v-model="form.alt_text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Deskripsi singkat untuk aksesibilitas (SEO)"
            />
            <p class="text-xs text-gray-500 mt-1">Penting untuk SEO dan aksesibilitas web</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Caption (keterangan singkat)</label>
            <input
              type="text"
              v-model="form.caption"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Keterangan singkat yang tampil di bawah gambar"
            />
          </div>
        </div>

        <div v-if="canBePublic" class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
          <input
            type="checkbox"
            id="is_public_edit"
            v-model="form.is_public"
            class="w-4 h-4 text-[#996600] border-gray-300 rounded focus:ring-[#996600]"
          />
          <label for="is_public_edit" class="text-sm font-medium text-gray-700">
            File dapat diakses publik (tanpa login)
          </label>
        </div>
        <div v-else class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex items-start space-x-2">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="text-sm font-medium text-amber-800">File Privat</p>
              <p class="text-xs text-amber-700 mt-1">Kategori ini berisi data sensitif dan hanya dapat diakses oleh pengguna yang login</p>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
  show: Boolean,
  arsip: Object,
});

const emit = defineEmits(['close', 'updated']);

const processing = ref(false);
const newTag = ref('');

const form = reactive({
  id: null,
  nama_file: '',
  kategori_arsip: '',
  folder: '',
  tags: [],
  deskripsi: '',
  alt_text: '',
  caption: '',
  is_public: false,
  jenis: '',
  url: '',
});

// Kategori yang boleh diakses publik
const publicCategories = ['gambar_artikel', 'gambar_kegiatan', 'gambar_profil', 'media_promosi'];

// Kategori yang menampilkan field SEO (hanya artikel dan pages)
const seoCategories = ['gambar_artikel', 'gambar_profil'];

const canBePublic = computed(() => {
  return publicCategories.includes(form.kategori_arsip);
});

const shouldShowSEOFields = computed(() => {
  return seoCategories.includes(form.kategori_arsip);
});

// Auto set is_public to false for sensitive categories
watch(() => form.kategori_arsip, (newKategori) => {
  if (!publicCategories.includes(newKategori)) {
    form.is_public = false;
  }
});

watch(() => props.arsip, (newArsip) => {
  if (newArsip) {
    form.id = newArsip.id;
    form.nama_file = newArsip.nama_file;
    form.kategori_arsip = newArsip.kategori_arsip;
    form.folder = newArsip.folder || '';
    form.tags = Array.isArray(newArsip.tags) ? [...newArsip.tags] : [];
    form.deskripsi = newArsip.deskripsi || '';
    form.alt_text = newArsip.alt_text || '';
    form.caption = newArsip.caption || '';
    form.is_public = newArsip.is_public || false;
    form.jenis = newArsip.jenis;
    form.url = newArsip.url;
  }
}, { immediate: true });

const addTag = () => {
  if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
    form.tags.push(newTag.value.trim());
    newTag.value = '';
  }
};

const removeTag = (index) => {
  form.tags.splice(index, 1);
};

const handleSubmit = () => {
  processing.value = true;

  router.put(`/pengelola/arsip/${form.id}`, {
    nama_file: form.nama_file,
    kategori_arsip: form.kategori_arsip,
    folder: form.folder || null,
    tags: form.tags,
    deskripsi: form.deskripsi || null,
    alt_text: form.alt_text || null,
    caption: form.caption || null,
    is_public: form.is_public,
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Arsip berhasil diupdate');
      emit('updated');
      emit('close');
    },
    onError: (errors) => {
      console.error(errors);
      toast.error('Gagal mengupdate arsip');
    },
    onFinish: () => {
      processing.value = false;
    },
  });
};
</script>
