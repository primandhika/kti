<template>
  <AdminLayout :page-title="item ? 'Edit Barang' : 'Tambah Barang Baru'">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ item ? 'Edit Barang' : 'Tambah Barang Baru' }}
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            {{ item ? 'Perbarui informasi barang' : 'Tambahkan barang baru untuk toko BPPU' }}
          </p>
        </div>
        <Link
          :href="route('admin.shop-items.index')"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Kembali
        </Link>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama Barang <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Contoh: Mug IKIP Siliwangi"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <div v-if="errors.name" class="mt-2 text-sm text-red-600">
              {{ errors.name }}
            </div>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Kategori <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.category"
              type="text"
              required
              placeholder="Contoh: Souvenir, Elektronik, Alat Tulis"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <p class="mt-1 text-xs text-gray-500">
              Kategori untuk mengelompokkan barang (souvenir, elektronik, dll)
            </p>
            <div v-if="errors.category" class="mt-2 text-sm text-red-600">
              {{ errors.category }}
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Deskripsi <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.description"
              required
              rows="3"
              placeholder="Deskripsi barang, spesifikasi, atau informasi tambahan..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            ></textarea>
            <div v-if="errors.description" class="mt-2 text-sm text-red-600">
              {{ errors.description }}
            </div>
          </div>

          <!-- Price & Stock -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Harga (Rp) <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.price"
                type="number"
                required
                min="0"
                step="1000"
                placeholder="50000"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
              <div v-if="errors.price" class="mt-2 text-sm text-red-600">
                {{ errors.price }}
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Stok <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.stock"
                type="number"
                required
                min="0"
                placeholder="50"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
              <div v-if="errors.stock" class="mt-2 text-sm text-red-600">
                {{ errors.stock }}
              </div>
            </div>
          </div>

          <!-- Image Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Gambar Barang <span v-if="!item" class="text-red-500">*</span>
            </label>

            <!-- Current Image Preview -->
            <div v-if="item && item.image && !imagePreview" class="mb-3">
              <img :src="item.image" :alt="item.name" class="h-32 w-auto object-cover rounded-lg">
            </div>

            <!-- New Image Preview -->
            <div v-if="imagePreview" class="mb-3">
              <img :src="imagePreview" alt="Preview" class="h-32 w-auto object-cover rounded-lg">
            </div>

            <input
              ref="imageInput"
              type="file"
              accept="image/jpeg,image/jpg,image/png,image/webp"
              @change="handleImageChange"
              :required="!item"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <p class="mt-1 text-xs text-gray-500">
              Format: JPG, PNG, WEBP. Maksimal 2MB.
            </p>
            <div v-if="errors.image" class="mt-2 text-sm text-red-600">
              {{ errors.image }}
            </div>
          </div>

          <!-- Status & Display Order -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Status
              </label>
              <select
                v-model="form.is_active"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option :value="true">Aktif</option>
                <option :value="false">Nonaktif</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Urutan Tampilan
              </label>
              <input
                v-model="form.display_order"
                type="number"
                min="0"
                placeholder="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3">
          <Link
            :href="route('admin.shop-items.index')"
            class="px-6 py-2 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-2 bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors disabled:opacity-50"
          >
            {{ processing ? 'Menyimpan...' : (item ? 'Perbarui Barang' : 'Simpan Barang') }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  item: Object,
  errors: Object,
});

const processing = ref(false);
const imageInput = ref(null);
const imagePreview = ref(null);

const form = reactive({
  name: '',
  category: '',
  description: '',
  price: '',
  stock: 0,
  image: null,
  is_active: true,
  display_order: 0,
});

onMounted(() => {
  if (props.item) {
    form.name = props.item.name;
    form.category = props.item.category;
    form.description = props.item.description;
    form.price = props.item.price;
    form.stock = props.item.stock;
    form.is_active = props.item.is_active;
    form.display_order = props.item.display_order;
  }
});

const handleImageChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const submitForm = () => {
  processing.value = true;

  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('category', form.category);
  formData.append('description', form.description);
  formData.append('price', form.price);
  formData.append('stock', form.stock);

  if (form.image) {
    formData.append('image', form.image);
  }

  formData.append('is_active', form.is_active ? '1' : '0');
  formData.append('display_order', form.display_order || 0);

  if (props.item) {
    formData.append('_method', 'PUT');
    router.post(route('admin.shop-items.update', props.item.id), formData, {
      onFinish: () => {
        processing.value = false;
      },
      preserveScroll: true,
    });
  } else {
    router.post(route('admin.shop-items.store'), formData, {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};
</script>
