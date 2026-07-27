<template>
    <AdminLayout
        page-title="Manajemen Menu Navbar"
        page-subtitle="Kelola menu navigasi website"
    >
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-end">
                <button @click="openCreateModal" class="bg-primary-900 text-white px-4 py-2 rounded-lg hover:bg-primary-800 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Menu</span>
                </button>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm text-blue-800"><strong>Tips:</strong> Drag & drop menu untuk mengubah urutan. Klik Edit untuk mengubah detail menu atau menambahkan submenu.</p>
                    </div>
                </div>
            </div>

            <!-- Menu List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div v-if="menuItems.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada menu</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat menu baru</p>
                </div>

                <div v-else class="divide-y divide-gray-200">
                    <div v-for="(item, index) in menuItems" :key="item.id" class="hover:bg-gray-50">
                        <!-- Parent Menu -->
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-4 flex-1">
                                <div class="flex items-center space-x-2">
                                    <button class="text-gray-400 hover:text-gray-600 cursor-move">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <span class="text-lg font-semibold text-gray-900">{{ item.label }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span v-if="item.url" class="text-sm text-gray-500">{{ item.url }}</span>
                                    <span :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <span v-if="item.children && item.children.length > 0" class="bg-blue-100 text-blue-800 px-2 py-1 text-xs font-medium rounded-full">
                                        {{ item.children.length }} Submenu
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="openEditModal(item)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="confirmDelete(item)" class="p-2 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Children Menu -->
                        <div v-if="item.children && item.children.length > 0" class="bg-gray-50 border-t border-gray-200">
                            <div v-for="child in item.children" :key="child.id" class="pl-12 pr-4 py-3 flex items-center justify-between hover:bg-gray-100">
                                <div class="flex items-center space-x-4">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-medium text-gray-700">{{ child.label }}</span>
                                    <span v-if="child.url" class="text-sm text-gray-500">{{ child.url }}</span>
                                    <span :class="child.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ child.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="openEditModal(child)" class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(child)" class="p-1 text-red-600 hover:bg-red-50 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">{{ isEditing ? 'Edit' : 'Tambah' }} Menu</h3>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Label Menu <span class="text-red-500">*</span></label>
                                    <input v-model="form.label" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent" placeholder="Contoh: Tentang Kami">
                                    <p v-if="form.errors.label" class="text-red-500 text-sm mt-1">{{ form.errors.label }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Menu <span class="text-red-500">*</span></label>
                                    <select v-model="form.type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent">
                                        <option value="internal">Internal (Link dalam website)</option>
                                        <option value="external">External (Link ke website lain)</option>
                                        <option value="page">Halaman Khusus</option>
                                    </select>
                                    <p v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">URL/Link</label>
                                    <input v-model="form.url" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent" placeholder="Contoh: /tentang-kami atau https://example.com">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika menu ini hanya sebagai parent untuk dropdown</p>
                                    <p v-if="form.errors.url" class="text-red-500 text-sm mt-1">{{ form.errors.url }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Parent Menu</label>
                                    <select v-model="form.parent_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent">
                                        <option :value="null">Tidak ada (Menu Utama)</option>
                                        <option v-for="item in menuItems" :key="item.id" :value="item.id">{{ item.label }}</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pilih parent jika ini adalah submenu</p>
                                    <p v-if="form.errors.parent_id" class="text-red-500 text-sm mt-1">{{ form.errors.parent_id }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                                        <input v-model="form.order" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent" placeholder="0">
                                        <p class="text-xs text-gray-500 mt-1">Semakin kecil, semakin awal</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon (opsional)</label>
                                        <input v-model="form.icon" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent" placeholder="home, user, etc">
                                    </div>
                                </div>

                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input v-model="form.is_active" type="checkbox" class="w-4 h-4 text-primary-900 border-gray-300 rounded focus:ring-primary-900">
                                        <span class="text-sm font-medium text-gray-700">Menu Aktif</span>
                                    </label>

                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input v-model="form.open_new_tab" type="checkbox" class="w-4 h-4 text-primary-900 border-gray-300 rounded focus:ring-primary-900">
                                        <span class="text-sm font-medium text-gray-700">Buka di Tab Baru</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-primary-900 text-white rounded-lg hover:bg-primary-800 disabled:opacity-50">
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    menuItems: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    label: '',
    url: '',
    type: 'internal',
    parent_id: null,
    order: 0,
    is_active: true,
    open_new_tab: false,
    icon: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    form.is_active = true;
    form.type = 'internal';
    form.parent_id = null;
    showModal.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    editingId.value = item.id;
    form.label = item.label;
    form.url = item.url || '';
    form.type = item.type;
    form.parent_id = item.parent_id;
    form.order = item.order;
    form.is_active = item.is_active;
    form.open_new_tab = item.open_new_tab;
    form.icon = item.icon || '';
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(`/pengelola/menu/${editingId.value}`, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post('/pengelola/menu', {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (item) => {
    if (confirm(`Yakin ingin menghapus menu "${item.label}"?${item.children && item.children.length > 0 ? ' Semua submenu akan ikut terhapus.' : ''}`)) {
        router.delete(`/pengelola/menu/${item.id}`);
    }
};
</script>
