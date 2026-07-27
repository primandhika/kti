<template>
  <div class="bg-white rounded-lg shadow-sm">
    <!-- Tab Switcher -->
    <div class="flex border-b border-gray-300">
      <button
        type="button"
        @click="switchMode('visual')"
        :class="[
          'px-4 py-2 text-sm font-medium transition-colors',
          editorMode === 'visual'
            ? 'bg-white text-[#996600] border-b-2 border-[#996600]'
            : 'bg-gray-50 text-gray-600 hover:text-gray-900'
        ]"
      >
        Visual
      </button>
      <button
        type="button"
        @click="switchMode('html')"
        :class="[
          'px-4 py-2 text-sm font-medium transition-colors',
          editorMode === 'html'
            ? 'bg-white text-[#996600] border-b-2 border-[#996600]'
            : 'bg-gray-50 text-gray-600 hover:text-gray-900'
        ]"
      >
        HTML
      </button>
    </div>

    <!-- Visual Editor Mode -->
    <div v-show="editorMode === 'visual'">
      <TipTapToolbar v-if="editor" :editor="editor" @image-upload="handleImageUpload" @image-from-arsip="handleImageFromArsip" />

      <EditorContent
        :editor="editor"
        class="min-h-[500px] border border-t-0 border-gray-300 rounded-b-lg p-4 prose prose-sm sm:prose lg:prose-lg max-w-none focus:outline-none focus:ring-2 focus:ring-[#996600]"
      />
    </div>

    <!-- HTML Editor Mode -->
    <div v-show="editorMode === 'html'" class="p-0">
      <textarea
        v-model="htmlContent"
        @input="updateFromHtml"
        class="w-full min-h-[500px] border-0 border-t-0 border-gray-300 p-4 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-[#996600] resize-y"
        placeholder="Edit HTML code here..."
      ></textarea>
    </div>

    <!-- Image Upload Progress -->
    <div v-if="uploadingImage" class="p-4 border-t border-gray-200 bg-gray-50">
      <div class="flex items-center gap-2 text-sm text-gray-600">
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Mengunggah gambar...</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import { mergeAttributes } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import Placeholder from '@tiptap/extension-placeholder';
import { Table, TableRow, TableCell, TableHeader } from '@tiptap/extension-table';
import TipTapToolbar from './TipTapToolbar.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Mulai menulis konten Anda di sini...',
  },
  uploadRoute: {
    type: String,
    default: 'admin.posts.uploadContentImage',
  },
});

const emit = defineEmits(['update:modelValue']);

const uploadingImage = ref(false);
const editorMode = ref('visual'); // 'visual' or 'html'
const htmlContent = ref(props.modelValue);

const ResizableImage = Image.extend({
  addAttributes() {
    return {
      ...this.parent?.(),
      width: {
        default: '100%',
        parseHTML: (element) => {
          const dataWidth = element.getAttribute('data-width');
          if (dataWidth) {
            return dataWidth;
          }

          const styleWidth = element.style?.width;
          if (styleWidth) {
            return styleWidth;
          }

          const widthAttr = element.getAttribute('width');
          if (widthAttr) {
            return /^\d+$/.test(widthAttr) ? `${widthAttr}px` : widthAttr;
          }

          return '100%';
        },
        renderHTML: (attributes) => ({
          'data-width': attributes.width || '100%',
        }),
      },
    };
  },
  renderHTML({ HTMLAttributes }) {
    const width = HTMLAttributes.width || HTMLAttributes['data-width'] || '100%';

    return [
      'img',
      mergeAttributes(this.options.HTMLAttributes, HTMLAttributes, {
        'data-width': width,
        style: `width: ${width};`,
      }),
    ];
  },
});

const editor = useEditor({
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3, 4, 5, 6],
      },
    }),
    ResizableImage.configure({
      inline: true,
      allowBase64: true,
      HTMLAttributes: {
        class: 'max-w-full h-auto rounded-lg',
      },
    }),
    Link.configure({
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-[#996600] underline hover:text-[#ad8432]',
      },
    }),
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
    Underline,
    Table.configure({
      resizable: true,
      HTMLAttributes: {
        class: 'tiptap-table',
      },
    }),
    TableRow,
    TableHeader,
    TableCell,
    Placeholder.configure({
      placeholder: props.placeholder,
    }),
  ],
  content: props.modelValue,
  editorProps: {
    attributes: {
      class: 'focus:outline-none',
    },
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML());
  },
});

watch(() => props.modelValue, (value) => {
  const isSame = editor.value.getHTML() === value;
  if (isSame) {
    return;
  }
  editor.value.commands.setContent(value, false);
  htmlContent.value = value;
});

const switchMode = (mode) => {
  if (editorMode.value === mode) return;

  if (mode === 'html') {
    // Switching to HTML mode - sync current editor content to textarea
    htmlContent.value = editor.value.getHTML();
  } else {
    // Switching to Visual mode - sync textarea content to editor
    try {
      editor.value.commands.setContent(htmlContent.value, false);
      emit('update:modelValue', htmlContent.value);
    } catch (error) {
      console.error('Invalid HTML content:', error);
    }
  }

  editorMode.value = mode;
};

const updateFromHtml = () => {
  // Update the model value when editing HTML directly
  emit('update:modelValue', htmlContent.value);
};

const handleImageUpload = async (file) => {
  if (!file || !file.type.startsWith('image/')) {
    alert('File harus berupa gambar');
    return;
  }

  // Validate file size (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    alert('Ukuran gambar maksimal 5MB');
    return;
  }

  uploadingImage.value = true;

  try {
    const formData = new FormData();
    formData.append('image', file);

    // Upload via Inertia
    await new Promise((resolve, reject) => {
      router.post(route(props.uploadRoute), formData, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          const imageUrl = page.props.flash?.imageUrl;
          if (imageUrl) {
            // Insert image into editor
            editor.value.chain().focus().setImage({ src: imageUrl, width: '100%' }).run();
            resolve();
          } else {
            reject(new Error('URL gambar tidak ditemukan'));
          }
        },
        onError: (errors) => {
          console.error('Upload error:', errors);
          reject(new Error('Gagal mengunggah gambar'));
        },
      });
    });
  } catch (error) {
    console.error('Image upload error:', error);
    alert(error.message || 'Gagal mengunggah gambar. Silakan coba lagi.');
  } finally {
    uploadingImage.value = false;
  }
};

const handleImageFromArsip = (image) => {
  if (!image || !image.url) {
    alert('Gambar tidak valid');
    return;
  }

  // Insert image from arsip into editor
  editor.value.chain().focus().setImage({ src: image.url, width: '100%' }).run();
};

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy();
  }
});
</script>

<style>
/* TipTap Editor Styles */
.ProseMirror {
  outline: none;
}

.ProseMirror p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}

.ProseMirror h1 {
  font-size: 2em;
  font-weight: bold;
  margin-top: 0.67em;
  margin-bottom: 0.67em;
}

.ProseMirror h2 {
  font-size: 1.5em;
  font-weight: bold;
  margin-top: 0.83em;
  margin-bottom: 0.83em;
}

.ProseMirror h3 {
  font-size: 1.17em;
  font-weight: bold;
  margin-top: 1em;
  margin-bottom: 1em;
}

.ProseMirror h4 {
  font-size: 1em;
  font-weight: bold;
  margin-top: 1.33em;
  margin-bottom: 1.33em;
}

.ProseMirror h5 {
  font-size: 0.83em;
  font-weight: bold;
  margin-top: 1.67em;
  margin-bottom: 1.67em;
}

.ProseMirror h6 {
  font-size: 0.67em;
  font-weight: bold;
  margin-top: 2.33em;
  margin-bottom: 2.33em;
}

.ProseMirror ul,
.ProseMirror ol {
  padding-left: 1.5rem;
  margin: 1rem 0;
}

.ProseMirror ul li {
  list-style-type: disc;
}

.ProseMirror ol li {
  list-style-type: decimal;
}

.ProseMirror blockquote {
  padding-left: 1rem;
  border-left: 3px solid #996600;
  margin: 1rem 0;
  color: #4a5568;
  font-style: italic;
}

.ProseMirror code {
  background-color: #f3f4f6;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
  font-family: 'Courier New', Courier, monospace;
  font-size: 0.875em;
}

.ProseMirror pre {
  background-color: #1f2937;
  color: #f9fafb;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 1rem 0;
}

.ProseMirror pre code {
  background-color: transparent;
  padding: 0;
  color: inherit;
  font-size: 0.875rem;
}

.ProseMirror hr {
  border: none;
  border-top: 2px solid #e5e7eb;
  margin: 2rem 0;
}

.ProseMirror img {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  margin: 1rem 0;
}

.ProseMirror img.ProseMirror-selectednode {
  outline: 2px solid #996600;
  outline-offset: 2px;
}

.ProseMirror a {
  color: #996600;
  text-decoration: underline;
}

.ProseMirror a:hover {
  color: #ad8432;
}

/* Table */
.ProseMirror .tiptap-table {
  border-collapse: collapse;
  width: 100%;
  margin: 1rem 0;
  overflow: hidden;
}

.ProseMirror .tiptap-table td,
.ProseMirror .tiptap-table th {
  border: 1px solid #d1d5db;
  padding: 0.5rem 0.75rem;
  vertical-align: top;
  position: relative;
  min-width: 80px;
}

.ProseMirror .tiptap-table th {
  background-color: #f3f0e8;
  font-weight: 600;
  text-align: left;
}

.ProseMirror .tiptap-table .selectedCell::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(153, 102, 0, 0.12);
  pointer-events: none;
}

.ProseMirror .tiptap-table .column-resize-handle {
  position: absolute;
  right: -2px;
  top: 0;
  bottom: 0;
  width: 4px;
  background-color: #996600;
  cursor: col-resize;
  z-index: 20;
}

.tableWrapper {
  overflow-x: auto;
}

/* Text alignment */
.ProseMirror .text-left {
  text-align: left;
}

.ProseMirror .text-center {
  text-align: center;
}

.ProseMirror .text-right {
  text-align: right;
}

.ProseMirror .text-justify {
  text-align: justify;
}
</style>
