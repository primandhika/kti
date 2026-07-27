<template>
  <AdminLayout :page-title="menu ? 'Edit Menu Kantin' : 'Tambah Menu Baru'">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ menu ? 'Edit Menu Kantin' : 'Tambah Menu Baru' }}
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            {{ menu ? 'Perbarui informasi menu kantin' : 'Tambahkan menu baru untuk kantin BPPU' }}
          </p>
        </div>
        <Link
          :href="route('admin.canteen-menu.index')"
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
          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Kategori <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.category_id"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="">Pilih Kategori</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div v-if="errors.category_id" class="mt-2 text-sm text-red-600">
              {{ errors.category_id }}
            </div>
          </div>

          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama Menu <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Contoh: Nasi Goreng Spesial"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <div v-if="errors.name" class="mt-2 text-sm text-red-600">
              {{ errors.name }}
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
              placeholder="Deskripsi menu, bahan, atau informasi tambahan..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            ></textarea>
            <div v-if="errors.description" class="mt-2 text-sm text-red-600">
              {{ errors.description }}
            </div>
          </div>

          <!-- Price -->
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
              placeholder="15000"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <div v-if="errors.price" class="mt-2 text-sm text-red-600">
              {{ errors.price }}
            </div>
          </div>

          <!-- Image Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Gambar Menu <span v-if="!menu" class="text-red-500">*</span>
            </label>

            <!-- Current Image Preview -->
            <div v-if="menu && menu.image && !imagePreview" class="mb-3">
              <img :src="menu.image" :alt="menu.name" class="h-32 w-auto object-cover rounded-lg">
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
              :required="!menu"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <p class="mt-1 text-xs text-gray-500">
              Format: JPG, PNG, WEBP. Maksimal 2MB.
            </p>
            <div v-if="errors.image" class="mt-2 text-sm text-red-600">
              {{ errors.image }}
            </div>
          </div>

          <!-- Badges -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Badge (Opsional)
            </label>
            <div class="space-y-3">
              <div v-for="(badge, index) in form.badges" :key="index" class="flex gap-2">
                <input
                  v-model="badge.text"
                  type="text"
                  placeholder="Teks badge"
                  class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                />
                <select
                  v-model="badge.color"
                  class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                >
                  <option value="blue">Biru</option>
                  <option value="green">Hijau</option>
                  <option value="purple">Ungu</option>
                  <option value="red">Merah</option>
                  <option value="yellow">Kuning</option>
                </select>
                <select
                  v-model="badge.icon"
                  class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                >
                  <option value="star">Bintang</option>
                  <option value="fire">Api</option>
                  <option value="calendar">Kalender</option>
                </select>
                <button
                  type="button"
                  @click="removeBadge(index)"
                  class="px-3 py-2 text-red-600 hover:text-red-900"
                >
                  Hapus
                </button>
              </div>
            </div>
            <button
              type="button"
              @click="addBadge"
              class="mt-3 inline-flex items-center px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Tambah Badge
            </button>
          </div>

          <!-- Available Days -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tersedia di Hari (Opsional)
            </label>
            <div class="grid grid-cols-4 gap-2">
              <label v-for="day in days" :key="day.value" class="flex items-center">
                <input
                  type="checkbox"
                  :value="day.value"
                  v-model="form.available_days"
                  class="h-4 w-4 text-[#996600] focus:ring-[#996600] border-gray-300 rounded"
                />
                <span class="ml-2 text-sm text-gray-700">{{ day.label }}</span>
              </label>
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Kosongkan jika tersedia setiap hari
            </p>
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
            :href="route('admin.canteen-menu.index')"
            class="px-6 py-2 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-2 bg-[#996600] hover:bg-[#ad8432] text-white font-semibold rounded-lg transition-colors disabled:opacity-50"
          >
            {{ processing ? 'Menyimpan...' : (menu ? 'Perbarui Menu' : 'Simpan Menu') }}
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
  menu: Object,
  categories: Array,
  errors: Object,
});

const processing = ref(false);
const imageInput = ref(null);
const imagePreview = ref(null);

const days = [
  { value: 'senin', label: 'Senin' },
  { value: 'selasa', label: 'Selasa' },
  { value: 'rabu', label: 'Rabu' },
  { value: 'kamis', label: 'Kamis' },
  { value: 'jumat', label: 'Jumat' },
  { value: 'sabtu', label: 'Sabtu' },
  { value: 'minggu', label: 'Minggu' },
];

const form = reactive({
  category_id: '',
  name: '',
  description: '',
  price: '',
  image: null,
  badges: [],
  available_days: [],
  is_active: true,
  display_order: 0,
});

onMounted(() => {
  if (props.menu) {
    form.category_id = props.menu.category_id;
    form.name = props.menu.name;
    form.description = props.menu.description;
    form.price = props.menu.price;
    form.badges = props.menu.badges || [];
    form.available_days = props.menu.available_days || [];
    form.is_active = props.menu.is_active;
    form.display_order = props.menu.display_order;
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

const addBadge = () => {
  form.badges.push({
    text: '',
    color: 'blue',
    icon: 'star',
  });
};

const removeBadge = (index) => {
  form.badges.splice(index, 1);
};

const submitForm = () => {
  processing.value = true;

  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('price', form.price);

  if (form.image) {
    formData.append('image', form.image);
  }

  // Filter out empty badges
  const validBadges = form.badges.filter(badge => badge.text && badge.text.trim() !== '');
  if (validBadges.length > 0) {
    formData.append('badges', JSON.stringify(validBadges));
  }

  if (form.available_days.length > 0) {
    formData.append('available_days', JSON.stringify(form.available_days));
  }

  formData.append('is_active', form.is_active ? '1' : '0');
  formData.append('display_order', form.display_order || 0);

  if (props.menu) {
    formData.append('_method', 'PUT');
    router.post(route('admin.canteen-menu.update', props.menu.id), formData, {
      onFinish: () => {
        processing.value = false;
      },
      preserveScroll: true,
    });
  } else {
    router.post(route('admin.canteen-menu.store'), formData, {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};
</script>
