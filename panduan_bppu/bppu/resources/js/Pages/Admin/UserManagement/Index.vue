<template>
  <AdminLayout page-title="Manajemen Pengguna" page-subtitle="Kelola pengguna dan role dalam sistem">
    <div class="space-y-6">
      <!-- Tabs & Filters -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100">
        <div class="border-b border-gray-200">
          <div class="flex space-x-1 p-2">
            <button
              v-if="isSysadmin"
              @click="activeTab = 'staff'"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                activeTab === 'staff'
                  ? 'bg-[#996600] text-white'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              Staff & Admin ({{ users.total }})
            </button>
            <button
              @click="activeTab = 'buyers'"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                activeTab === 'buyers'
                  ? 'bg-[#996600] text-white'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              Member Buyer ({{ buyers.total }})
            </button>
            <button
              v-if="!isDataClerk"
              @click="activeTab = 'mitra'"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                activeTab === 'mitra'
                  ? 'bg-[#996600] text-white'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              Mitra Usaha ({{ mitras.total }})
            </button>
            <button
              v-if="!isDataClerk"
              @click="activeTab = 'mesin'"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                activeTab === 'mesin'
                  ? 'bg-[#996600] text-white'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              Mesin ({{ mesinUsers?.total ?? 0 }})
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="p-4 bg-gray-50 border-b border-gray-200">
          <div class="flex flex-wrap gap-3 items-center">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama, username, email, member ID..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              />
            </div>

            <!-- Role Filter (Staff Tab) -->
            <div v-if="activeTab === 'staff'" class="min-w-[200px]">
              <select
                v-model="roleFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Semua Role</option>
                <option v-for="role in roles.filter(r => r.name !== 'buyer')" :key="role.name" :value="role.name">
                  {{ getRoleLabel(role.name) }}
                </option>
              </select>
            </div>

            <!-- Tier Filter (Buyer Tab) -->
            <div v-if="activeTab === 'buyers'" class="min-w-[200px]">
              <select
                v-model="tierFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Semua Tier</option>
                <option v-for="tier in membershipTiers" :key="tier.id" :value="tier.id">
                  {{ tier.name }}
                </option>
              </select>
            </div>

            <!-- Reset Button -->
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Reset
            </button>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Add User Button -->
            <button
              @click="openCreateModal"
              class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Pengguna</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Staff Users Table -->
      <div v-show="activeTab === 'staff'" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('name')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('name') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Nama</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('name') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('username')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('username') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Username</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('username') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('email')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('email') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Email</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('email') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('role')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('role') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Role</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('role') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('work_unit')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('work_unit') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Unit Kerja</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('work_unit') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('created_at')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('created_at') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Terdaftar</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('created_at') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ user.username }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ user.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getRoleBadgeClass(user.roles[0])"
                  >
                    {{ getRoleLabel(user.roles[0]) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div v-if="user.work_units.length > 0" class="text-sm text-gray-600">
                    <div v-for="unit in user.work_units" :key="unit.id" class="text-xs">
                      {{ unit.name }}
                    </div>
                  </div>
                  <div v-else class="text-xs text-gray-400 italic">-</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ user.created_at }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <template v-if="isSysadmin || !user.roles.includes('sysadmin')">
                    <button
                      @click="openEditModal(user)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteUser(user)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Hapus
                    </button>
                  </template>
                  <span v-else class="text-xs text-gray-400 italic">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination for Staff -->
        <div v-if="users.data.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              Menampilkan {{ users.from }} - {{ users.to }} dari {{ users.total }} data
            </div>
            <div class="flex space-x-1">
              <button
                v-for="(link, index) in users.links"
                :key="index"
                @click="changePage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 border rounded',
                  link.active
                    ? 'bg-[#996600] text-white border-[#996600]'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              >
                <span v-if="index === 0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </span>
                <span v-else-if="index === users.links.length - 1">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
                <span v-else>{{ link.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Buyers Table -->
      <div v-show="activeTab === 'buyers'" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('member_code')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('member_code') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Member ID</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('member_code') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('name')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('name') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Nama</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('name') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('contact')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('contact') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Kontak</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('contact') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('tier')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('tier') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Tier</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('tier') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('total_points')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('total_points') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Poin</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('total_points') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('member_since')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('member_since') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Member Sejak</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('member_since') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="buyer in buyers.data" :key="buyer.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono font-semibold text-[#996600]">{{ buyer.member_code }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ buyer.name }}</div>
                  <div class="text-xs text-gray-500">{{ buyer.username }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-xs text-gray-600">{{ buyer.email }}</div>
                  <div class="text-xs text-gray-500">{{ buyer.phone }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span
                    v-if="buyer.membership_tier"
                    class="px-3 py-1 text-xs font-bold rounded-full text-white"
                    :style="{ backgroundColor: buyer.membership_tier.color }"
                  >
                    {{ buyer.membership_tier.name }}
                  </span>
                  <span v-else class="text-xs text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex flex-col items-center">
                    <span class="text-lg font-bold text-[#996600]">{{ buyer.total_points }}</span>
                    <span v-if="buyer.membership_tier" class="text-xs text-gray-500">
                      {{ buyer.membership_tier.discount_percentage }}% off
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ buyer.member_since }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="openEditBuyerModal(buyer)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteBuyer(buyer)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="buyers.data.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  Belum ada member buyer terdaftar
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination for Buyers -->
        <div v-if="buyers.data.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              Menampilkan {{ buyers.from }} - {{ buyers.to }} dari {{ buyers.total }} data
            </div>
            <div class="flex space-x-1">
              <button
                v-for="(link, index) in buyers.links"
                :key="index"
                @click="changePage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 border rounded',
                  link.active
                    ? 'bg-[#996600] text-white border-[#996600]'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              >
                <span v-if="index === 0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </span>
                <span v-else-if="index === buyers.links.length - 1">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
                <span v-else>{{ link.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Mitra Table -->
      <div v-show="activeTab === 'mitra'" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('username')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('username') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Username</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('username') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('name')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('name') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Nama</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('name') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('contact')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('contact') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Kontak</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('contact') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('role')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('role') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Role</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('role') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('created_at')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('created_at') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Terdaftar</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('created_at') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="mitra in mitras.data" :key="mitra.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono font-semibold text-[#996600]">{{ mitra.username }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ mitra.name }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-xs text-gray-600">{{ mitra.email }}</div>
                  <div v-if="mitra.phone" class="text-xs text-gray-500">{{ mitra.phone }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span
                    v-for="role in mitra.roles"
                    :key="role"
                    class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"
                  >
                    {{ getRoleLabel(role) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ mitra.created_at }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="openEditModal(mitra)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteUser(mitra)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="mitras.data.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                  Belum ada mitra usaha terdaftar
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination for Mitras -->
        <div v-if="mitras.data.length > 0" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              Menampilkan {{ mitras.from }} - {{ mitras.to }} dari {{ mitras.total }} data
            </div>
            <div class="flex space-x-1">
              <button
                v-for="(link, index) in mitras.links"
                :key="index"
                @click="changePage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 border rounded',
                  link.active
                    ? 'bg-[#996600] text-white border-[#996600]'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              >
                <span v-if="index === 0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </span>
                <span v-else-if="index === mitras.links.length - 1">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
                <span v-else>{{ link.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Mesin -->
      <div v-show="activeTab === 'mesin'" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <p class="text-sm text-gray-600">Akun perangkat layar/kiosk self-order di kantin. Login melalui <code class="bg-gray-200 px-1 rounded text-xs">/kantin/self-order</code></p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('username')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('username') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Username</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('username') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('name')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('name') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Nama Perangkat</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('name') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('email')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('email') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Email</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('email') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                  <button type="button" @click="toggleSort('created_at')" :class="['inline-flex items-center gap-1 transition-colors hover:text-[#996600]', isSorted('created_at') ? 'text-[#996600]' : 'text-gray-500']">
                    <span>Terdaftar</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="isSorted('created_at') && sortOrder === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="mesin in (mesinUsers?.data || [])" :key="mesin.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono font-semibold text-[#996600]">{{ mesin.username }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ mesin.name }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-xs text-gray-600">{{ mesin.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ mesin.created_at }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="openEditModal(mesin)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                  <button @click="deleteUser(mesin)" class="text-red-600 hover:text-red-900">Hapus</button>
                </td>
              </tr>
              <tr v-if="!mesinUsers?.data?.length">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                  Belum ada akun mesin terdaftar
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEditing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
          </h2>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-3">
          <!-- Name -->
          <div class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Nama Lengkap</label>
            <div class="flex-1">
              <input
                v-model="form.name"
                type="text"
                required
                :class="[
                  'w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent',
                  formErrors.name ? 'border-red-500' : 'border-gray-300'
                ]"
                placeholder="Masukkan nama lengkap"
              />
              <p v-if="formErrors.name" class="text-xs text-red-600 mt-1">{{ formErrors.name }}</p>
            </div>
          </div>

          <!-- Username -->
          <div class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Username</label>
            <div class="flex-1">
              <input
                v-model="form.username"
                type="text"
                required
                :class="[
                  'w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent',
                  formErrors.username ? 'border-red-500' : 'border-gray-300'
                ]"
                placeholder="Masukkan username"
              />
              <p v-if="formErrors.username" class="text-xs text-red-600 mt-1">{{ formErrors.username }}</p>
            </div>
          </div>

          <!-- Email -->
          <div class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Email</label>
            <div class="flex-1">
              <input
                v-model="form.email"
                type="email"
                required
                :class="[
                  'w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent',
                  formErrors.email ? 'border-red-500' : 'border-gray-300'
                ]"
                placeholder="Masukkan email"
              />
              <p v-if="formErrors.email" class="text-xs text-red-600 mt-1">{{ formErrors.email }}</p>
            </div>
          </div>

          <!-- Role -->
          <div class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Role</label>
            <div class="flex-1">
              <select
                v-model="form.role"
                required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option value="">Pilih role</option>
                <option v-for="role in roles" :key="role.name" :value="role.name">
                  {{ getRoleLabel(role.name) }}
                </option>
              </select>
              <p v-if="form.role" class="text-xs text-gray-500 mt-1">
                {{ roles.find(r => r.name === form.role)?.description || '' }}
              </p>
            </div>
          </div>

          <!-- Phone (for buyer) -->
          <div v-if="form.role === 'buyer'" class="flex items-center gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0">No. Telepon</label>
            <input
              v-model="form.phone"
              type="text"
              class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              placeholder="Masukkan nomor telepon"
            />
          </div>

          <!-- Info for buyer -->
          <div v-if="form.role === 'buyer'" class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Member ID</label>
            <div class="flex-1">
              <div class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50">
                <span class="text-gray-600">Otomatis sama dengan username: </span>
                <span class="font-mono font-semibold text-[#996600]">{{ form.username || '(akan diisi otomatis)' }}</span>
              </div>
              <p class="text-xs text-gray-500 mt-1">Member ID akan otomatis mengikuti username</p>
            </div>
          </div>

          <!-- Membership Tier (for buyer) -->
          <div v-if="form.role === 'buyer'" class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Tier Member</label>
            <div class="flex-1">
              <select
                v-model="form.membership_tier_id"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
              >
                <option v-for="tier in membershipTiers" :key="tier.id" :value="tier.id">
                  {{ tier.name }}
                </option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Tier default adalah Bronze, dapat diubah sesuai kebutuhan</p>
            </div>
          </div>

          <!-- Work Units (for sysadmin/canteen/shop/officer role) -->
          <div v-if="form.role === 'sysadmin' || form.role === 'canteen' || form.role === 'shop' || form.role === 'officer'" class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">Unit Kerja</label>
            <div class="flex-1">
              <div class="space-y-1 max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2">
                <label
                  v-for="unit in workUnits"
                  :key="unit.id"
                  class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1.5 rounded"
                >
                  <input
                    type="checkbox"
                    :value="unit.id"
                    v-model="form.work_unit_ids"
                    class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
                  />
                  <span class="text-xs text-gray-700">{{ unit.name }} ({{ unit.type }})</span>
                </label>
              </div>
              <p class="text-xs text-gray-500 mt-1">Pilih unit kerja yang akan dikelola</p>
            </div>
          </div>

          <!-- Password -->
          <div class="flex gap-3">
            <label class="w-32 text-sm font-medium text-gray-700 flex-shrink-0 pt-2">
              Password
              <span v-if="isEditing" class="block text-xs text-gray-500 font-normal">(kosongkan jika tidak diubah)</span>
            </label>
            <div class="flex-1">
              <input
                v-model="form.password"
                type="password"
                :required="!isEditing"
                :class="[
                  'w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent',
                  formErrors.password ? 'border-red-500' : 'border-gray-300'
                ]"
                placeholder="Min. 8 karakter"
                minlength="8"
              />
              <p v-if="formErrors.password" class="text-xs text-red-600 mt-1">{{ formErrors.password }}</p>
            </div>
          </div>

          <!-- Wajibkan Ganti Password -->
          <div v-if="!isEditing || form.password" class="flex gap-3">
            <div class="w-32 flex-shrink-0"></div>
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="form.must_change_password"
                class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
              />
              <span class="text-sm text-gray-700">Wajibkan ganti password saat login pertama</span>
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] transition-colors disabled:opacity-50"
            >
              {{ processing ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

// Simple debounce function
const debounce = (fn, delay) => {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
};

const props = defineProps({
  users: Object,
  buyers: Object,
  mitras: Object,
  mesinUsers: Object,
  roles: Array,
  workUnits: Array,
  membershipTiers: Array,
  isSysadmin: Boolean,
  isDataClerk: Boolean,
  filters: Object,
});

const activeTab = ref(props.isSysadmin ? (props.filters.tab || 'staff') : 'buyers');
const showModal = ref(false);
const isEditing = ref(false);
const processing = ref(false);
const editingUser = ref(null);

const searchQuery = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');
const tierFilter = ref(props.filters.tier || '');
const sortBy = ref(props.filters.sort_by || 'created_at');
const sortOrder = ref(props.filters.sort_order || 'desc');

const form = reactive({
  name: '',
  username: '',
  email: '',
  phone: '',
  role: '',
  password: '',
  must_change_password: true,
  work_unit_ids: [],
  membership_tier_id: '',
});

const getRoleLabel = (roleName) => {
  const labels = {
    'head': 'Pimpinan',
    'sysadmin': 'System Admin',
    'officer': 'Officer',
    'canteen': 'Kantin',
    'shop': 'Toko',
    'buyer': 'Member Buyer',
    'supplier': 'Supplier',
    'tenant': 'Tenant',
    'vendor': 'Vendor',
    'distributor': 'Distributor',
    'produsen': 'Produsen',
    'reseller': 'Reseller',
    'konsinyasi': 'Konsinyasi',
  };
  return labels[roleName] || roleName;
};

const getRoleBadgeClass = (roleName) => {
  const classes = {
    'head': 'bg-purple-100 text-purple-800',
    'sysadmin': 'bg-blue-100 text-blue-800',
    'officer': 'bg-green-100 text-green-800',
    'canteen': 'bg-orange-100 text-orange-800',
    'shop': 'bg-pink-100 text-pink-800',
    'buyer': 'bg-yellow-100 text-yellow-800',
  };
  return classes[roleName] || 'bg-gray-100 text-gray-800';
};

const openCreateModal = () => {
  isEditing.value = false;
  editingUser.value = null;
  form.name = '';
  form.username = '';
  form.email = '';
  form.phone = '';
  form.role = '';
  form.password = '';
  form.must_change_password = true;
  form.work_unit_ids = [];
  // Set default tier to Bronze
  const bronzeTier = props.membershipTiers.find(t => t.name === 'Bronze');
  form.membership_tier_id = bronzeTier ? bronzeTier.id : (props.membershipTiers[0]?.id || '');
  showModal.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  editingUser.value = user;
  form.name = user.name;
  form.username = user.username;
  form.email = user.email;
  form.phone = user.phone || '';
  form.role = user.roles[0];
  form.password = '';
  form.must_change_password = false;
  form.work_unit_ids = user.work_units ? user.work_units.map(unit => unit.id) : [];
  form.membership_tier_id = user.membership_tier?.id || '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  isEditing.value = false;
  editingUser.value = null;
  formErrors.value = {};
};

const formErrors = ref({});

const submitForm = () => {
  processing.value = true;
  formErrors.value = {};

  const submitData = { ...form };

  const handleErrors = (errors) => {
    formErrors.value = errors;

    // Build error message
    let errorMessage = 'Gagal menyimpan data:\n\n';
    Object.keys(errors).forEach(field => {
      const fieldLabel = {
        'name': 'Nama',
        'username': 'Username',
        'email': 'Email',
        'phone': 'No. Telepon',
        'password': 'Password',
        'role': 'Role',
        'membership_tier_id': 'Tier Member',
        'work_unit_ids': 'Unit Kerja'
      }[field] || field;

      errorMessage += `• ${fieldLabel}: ${errors[field]}\n`;
    });

    alert(errorMessage);
    processing.value = false;
  };

  if (isEditing.value) {
    router.put(`/pengelola/manajemen-pengguna/${editingUser.value.id}`, submitData, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: handleErrors,
    });
  } else {
    router.post('/pengelola/manajemen-pengguna', submitData, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: handleErrors,
    });
  }
};

const deleteUser = (user) => {
  if (confirm(`Apakah Anda yakin ingin menghapus user "${user.name}"?`)) {
    router.delete(`/pengelola/manajemen-pengguna/${user.id}`);
  }
};

const openEditBuyerModal = (buyer) => {
  isEditing.value = true;
  editingUser.value = buyer;
  form.name = buyer.name;
  form.username = buyer.username;
  form.email = buyer.email;
  form.phone = buyer.phone && buyer.phone !== '-' ? buyer.phone : '';
  form.role = 'buyer';
  form.password = '';
  form.must_change_password = false;
  form.work_unit_ids = [];
  form.membership_tier_id = buyer.membership_tier?.id || '';
  showModal.value = true;
};

const deleteBuyer = (buyer) => {
  if (confirm(`Apakah Anda yakin ingin menghapus member buyer "${buyer.name}" (${buyer.member_code})?`)) {
    router.delete(`/pengelola/manajemen-pengguna/${buyer.id}`);
  }
};

// Watch active tab and reset to page 1 when switching tabs
watch(activeTab, () => {
  router.get('/pengelola/manajemen-pengguna', buildQueryParams({ page: 1 }), {
    preserveState: true,
    preserveScroll: false,
  });
});

// Watch filters and apply debounce
watch(searchQuery, debounce(function (value) {
  applyFilters();
}, 500));

watch(roleFilter, () => {
  applyFilters();
});

watch(tierFilter, () => {
  applyFilters();
});

const applyFilters = () => {
  router.get('/pengelola/manajemen-pengguna', buildQueryParams({ page: 1 }), {
    preserveState: true,
    preserveScroll: true,
  });
};

const buildQueryParams = (overrides = {}) => ({
  search: searchQuery.value,
  role: roleFilter.value,
  tier: tierFilter.value,
  tab: activeTab.value,
  sort_by: sortBy.value,
  sort_order: sortOrder.value,
  ...overrides,
});

const toggleSort = (field) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = field;
    sortOrder.value = 'asc';
  }
  router.get('/pengelola/manajemen-pengguna', buildQueryParams({ page: 1 }), {
    preserveState: true,
    preserveScroll: true,
  });
};

const isSorted = (field) => sortBy.value === field;

const resetFilters = () => {
  searchQuery.value = '';
  roleFilter.value = '';
  tierFilter.value = '';
  sortBy.value = 'created_at';
  sortOrder.value = 'desc';
  const previousTab = activeTab.value;
  activeTab.value = 'staff';
  if (previousTab === 'staff') {
    router.get('/pengelola/manajemen-pengguna', buildQueryParams({ page: 1 }));
  }
};

const changePage = (url) => {
  if (!url) return;
  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>
