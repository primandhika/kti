<template>
  <div class="border border-gray-300 rounded-t-lg bg-gray-50 px-2 py-1 flex flex-wrap gap-0.5 sticky top-0 z-10">
    <!-- Undo/Redo -->
    <button
      type="button"
      @click="editor.chain().focus().undo().run()"
      :disabled="!editor.can().undo()"
      class="p-1.5 hover:bg-gray-200 rounded disabled:opacity-30 disabled:cursor-not-allowed"
      title="Undo (Ctrl+Z)"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
      </svg>
    </button>
    <button
      type="button"
      @click="editor.chain().focus().redo().run()"
      :disabled="!editor.can().redo()"
      class="p-1.5 hover:bg-gray-200 rounded disabled:opacity-30 disabled:cursor-not-allowed"
      title="Redo (Ctrl+Y)"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/>
      </svg>
    </button>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Headings -->
    <select
      @change="setHeading($event.target.value)"
      class="px-2 py-0.5 text-xs border border-gray-300 rounded hover:bg-gray-200"
      :value="getCurrentHeadingLevel()"
    >
      <option value="">Normal</option>
      <option value="1">H1</option>
      <option value="2">H2</option>
      <option value="3">H3</option>
      <option value="4">H4</option>
      <option value="5">H5</option>
      <option value="6">H6</option>
    </select>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Text Formatting -->
    <button
      type="button"
      @click="editor.chain().focus().toggleBold().run()"
      :class="{ 'bg-gray-300': editor.isActive('bold') }"
      class="p-1.5 hover:bg-gray-200 rounded font-bold"
      title="Bold (Ctrl+B)"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path d="M6 4a1 1 0 011-1h3.5a3.5 3.5 0 013.163 5 3.5 3.5 0 01-1.495 6H7a1 1 0 01-1-1V4zm2 1v4h3a1.5 1.5 0 100-3H8zm0 6v4h3.5a1.5 1.5 0 000-3H8z"/>
      </svg>
    </button>
    <button
      type="button"
      @click="editor.chain().focus().toggleItalic().run()"
      :class="{ 'bg-gray-300': editor.isActive('italic') }"
      class="p-1.5 hover:bg-gray-200 rounded italic"
      title="Italic (Ctrl+I)"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 5a1 1 0 00-.707.293l-5 5a1 1 0 101.414 1.414L9 8.414V17a1 1 0 102 0V8.414l3.293 3.293a1 1 0 001.414-1.414l-5-5A1 1 0 0010 5z" transform="rotate(-15 10 10)"/>
      </svg>
    </button>
    <button
      type="button"
      @click="editor.chain().focus().toggleUnderline().run()"
      :class="{ 'bg-gray-300': editor.isActive('underline') }"
      class="p-1.5 hover:bg-gray-200 rounded underline"
      title="Underline (Ctrl+U)"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path d="M5 4a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm0 12a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1-7a1 1 0 011-1h6a1 1 0 011 1v2a3 3 0 11-6 0V9z"/>
      </svg>
    </button>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Text Alignment -->
    <button
      type="button"
      @click="editor.chain().focus().setTextAlign('left').run()"
      :class="{ 'bg-gray-300': editor.isActive({ textAlign: 'left' }) }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Align Left"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h8a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h8a1 1 0 110 2H4a1 1 0 01-1-1z"/>
      </svg>
    </button>
    <button
      type="button"
      @click="editor.chain().focus().setTextAlign('center').run()"
      :class="{ 'bg-gray-300': editor.isActive({ textAlign: 'center' }) }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Align Center"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm2 4a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm-2 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm2 4a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"/>
      </svg>
    </button>
    <button
      type="button"
      @click="editor.chain().focus().setTextAlign('right').run()"
      :class="{ 'bg-gray-300': editor.isActive({ textAlign: 'right' }) }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Align Right"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm4 4a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm-4 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm4 4a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1z"/>
      </svg>
    </button>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Lists -->
    <button
      type="button"
      @click="editor.chain().focus().toggleBulletList().run()"
      :class="{ 'bg-gray-300': editor.isActive('bulletList') }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Bullet List"
    >
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M2 6a2 2 0 114 0 2 2 0 01-4 0zm5-1.5A.5.5 0 017.5 4h10a.5.5 0 010 1h-10A.5.5 0 017 4.5zm0 6A.5.5 0 017.5 10h10a.5.5 0 010 1h-10a.5.5 0 01-.5-.5zm0 6a.5.5 0 01.5-.5h10a.5.5 0 010 1h-10a.5.5 0 01-.5-.5zM2 12a2 2 0 114 0 2 2 0 01-4 0zm0 6a2 2 0 114 0 2 2 0 01-4 0z"/>
      </svg>
    </button>
    <button
      type="button"
      @click="editor.chain().focus().toggleOrderedList().run()"
      :class="{ 'bg-gray-300': editor.isActive('orderedList') }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Numbered List"
    >
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M2.003 5.884L2 5.883l.006.003-.003-.002zm.92-1.137a.388.388 0 00-.283-.116.386.386 0 00-.28.116L2 5.067V7h1V5.75L3 6l-.077-.253zM2 10h1v1H2v-1zm0 2.5h1V14H2v-1.5zM8 5h9v1H8V5zm0 5h9v1H8v-1zm0 5h9v1H8v-1zM2.5 10a.5.5 0 00-.5.5v3a.5.5 0 00.5.5h1a.5.5 0 00.5-.5v-3a.5.5 0 00-.5-.5h-1z"/>
      </svg>
    </button>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Link & Image -->
    <button
      type="button"
      @click="setLink"
      :class="{ 'bg-gray-300': editor.isActive('link') }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Insert Link"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/>
      </svg>
    </button>
    <button
      type="button"
      @click="showImageModal = true"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Insert Image"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
      </svg>
    </button>

    <InsertImageModal
      :show="showImageModal"
      @close="showImageModal = false"
      @insert="handleInsertImage"
    />
    <button
      type="button"
      @click="setImageWidth('40%')"
      :disabled="!editor.isActive('image')"
      class="px-2 py-1 text-[11px] hover:bg-gray-200 rounded disabled:opacity-30 disabled:cursor-not-allowed"
      title="Ukuran gambar kecil"
    >
      40%
    </button>
    <button
      type="button"
      @click="setImageWidth('70%')"
      :disabled="!editor.isActive('image')"
      class="px-2 py-1 text-[11px] hover:bg-gray-200 rounded disabled:opacity-30 disabled:cursor-not-allowed"
      title="Ukuran gambar sedang"
    >
      70%
    </button>
    <button
      type="button"
      @click="setImageWidth('100%')"
      :disabled="!editor.isActive('image')"
      class="px-2 py-1 text-[11px] hover:bg-gray-200 rounded disabled:opacity-30 disabled:cursor-not-allowed"
      title="Ukuran gambar penuh"
    >
      100%
    </button>
    <button
      type="button"
      @click="setCustomImageWidth"
      :disabled="!editor.isActive('image')"
      class="px-2 py-1 text-[11px] hover:bg-gray-200 rounded disabled:opacity-30 disabled:cursor-not-allowed"
      title="Set ukuran gambar custom"
    >
      Custom
    </button>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Table -->
    <div class="relative" ref="tableMenuRef">
      <button
        type="button"
        @click="toggleTableMenu"
        :class="{ 'bg-gray-300': editor.isActive('table') }"
        class="p-1.5 hover:bg-gray-200 rounded flex items-center gap-1"
        title="Tabel"
      >
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M2 4a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H3a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h5a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1v-6zm8 0a1 1 0 011-1h5a1 1 0 011 1v6a1 1 0 01-1 1h-5a1 1 0 01-1-1v-6z"/>
        </svg>
        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
        </svg>
      </button>

      <div
        v-if="showTableMenu"
        class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 min-w-[180px] py-1"
      >
        <button
          type="button"
          @click="insertTable"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100"
        >
          Sisipkan Tabel
        </button>
        <hr class="my-1 border-gray-200" />
        <button
          type="button"
          @click="runTableCmd('addColumnBefore')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Tambah Kolom Sebelum
        </button>
        <button
          type="button"
          @click="runTableCmd('addColumnAfter')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Tambah Kolom Sesudah
        </button>
        <button
          type="button"
          @click="runTableCmd('deleteColumn')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Hapus Kolom
        </button>
        <hr class="my-1 border-gray-200" />
        <button
          type="button"
          @click="runTableCmd('addRowBefore')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Tambah Baris Sebelum
        </button>
        <button
          type="button"
          @click="runTableCmd('addRowAfter')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Tambah Baris Sesudah
        </button>
        <button
          type="button"
          @click="runTableCmd('deleteRow')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Hapus Baris
        </button>
        <hr class="my-1 border-gray-200" />
        <button
          type="button"
          @click="runTableCmd('toggleHeaderRow')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 disabled:opacity-40"
        >
          Toggle Header Baris
        </button>
        <button
          type="button"
          @click="runTableCmd('deleteTable')"
          :disabled="!editor.isActive('table')"
          class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 text-red-600 disabled:opacity-40"
        >
          Hapus Tabel
        </button>
      </div>
    </div>

    <div class="w-px bg-gray-300 mx-0.5"></div>

    <!-- Code & Quote -->
    <button
      type="button"
      @click="editor.chain().focus().toggleCode().run()"
      :class="{ 'bg-gray-300': editor.isActive('code') }"
      class="p-1.5 hover:bg-gray-200 rounded font-mono text-xs"
      title="Inline Code"
    >
      &lt;/&gt;
    </button>
    <button
      type="button"
      @click="editor.chain().focus().toggleBlockquote().run()"
      :class="{ 'bg-gray-300': editor.isActive('blockquote') }"
      class="p-1.5 hover:bg-gray-200 rounded"
      title="Blockquote"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path d="M6 10c0-2 1.5-3.5 3.5-3.5.5 0 1 .1 1.5.3V5.5C11 3 9 1.5 6.5 1.5 3 1.5 1 4 1 7v6h5v-3H6zm8 0c0-2 1.5-3.5 3.5-3.5.5 0 1 .1 1.5.3V5.5c0-2.5-2-4-4.5-4C11 1.5 9 4 9 7v6h5v-3h-0z"/>
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import InsertImageModal from './InsertImageModal.vue';

const props = defineProps({
  editor: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['image-upload', 'image-from-arsip']);

const showImageModal = ref(false);
const showTableMenu = ref(false);
const tableMenuRef = ref(null);

const toggleTableMenu = () => {
  showTableMenu.value = !showTableMenu.value;
};

const closeTableMenu = (e) => {
  if (tableMenuRef.value && !tableMenuRef.value.contains(e.target)) {
    showTableMenu.value = false;
  }
};

onMounted(() => document.addEventListener('click', closeTableMenu));
onBeforeUnmount(() => document.removeEventListener('click', closeTableMenu));

const insertTable = () => {
  props.editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
  showTableMenu.value = false;
};

const runTableCmd = (cmd) => {
  props.editor.chain().focus()[cmd]().run();
  showTableMenu.value = false;
};

const getCurrentHeadingLevel = () => {
  for (let level = 1; level <= 6; level++) {
    if (props.editor.isActive('heading', { level })) {
      return level.toString();
    }
  }
  return '';
};

const setHeading = (level) => {
  if (level === '') {
    props.editor.chain().focus().setParagraph().run();
  } else {
    props.editor.chain().focus().setHeading({ level: parseInt(level) }).run();
  }
};

const setLink = () => {
  const previousUrl = props.editor.getAttributes('link').href;
  const url = window.prompt('Masukkan URL:', previousUrl);

  if (url === null) {
    return;
  }

  if (url === '') {
    props.editor.chain().focus().extendMarkRange('link').unsetLink().run();
    return;
  }

  props.editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

const setImageWidth = (width) => {
  props.editor.chain().focus().updateAttributes('image', { width }).run();
};

const setCustomImageWidth = () => {
  const current = props.editor.getAttributes('image').width || '100%';
  const input = window.prompt('Ukuran gambar (contoh: 60% atau 320px):', current);

  if (input === null) {
    return;
  }

  const width = input.trim();
  if (!width) {
    return;
  }

  const normalizedWidth = /^\d+$/.test(width) ? `${width}%` : width;
  setImageWidth(normalizedWidth);
};

const handleInsertImage = (data) => {
  if (data.type === 'file') {
    emit('image-upload', data.file);
  } else if (data.type === 'arsip') {
    emit('image-from-arsip', data.image);
  }
};
</script>
