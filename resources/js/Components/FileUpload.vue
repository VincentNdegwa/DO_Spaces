<template>
    <div class="max-w-2xl mx-auto p-4">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    File Upload
                </label>
                <input 
                    type="file" 
                    @change="handleFileChange"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                >
            </div>
            <div v-if="uploading" class="mb-4">
                Uploading...
            </div>
            <div v-if="uploadedFileUrl" class="mb-4">
                <p class="text-green-600">File uploaded successfully!</p>
                <a :href="uploadedFileUrl" target="_blank" class="text-blue-500 hover:underline">View File</a>
            </div>
            <button 
                @click="uploadFile"
                :disabled="!selectedFile || uploading"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50"
            >
                Upload
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const selectedFile = ref(null);
const uploading = ref(false);
const uploadedFileUrl = ref('');

const handleFileChange = (event) => {
    selectedFile.value = event.target.files[0];
};

const uploadFile = async () => {
    if (!selectedFile.value) return;

    const formData = new FormData();
    formData.append('file', selectedFile.value);
    
    uploading.value = true;
    try {
        const response = await axios.post('api/v1/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        uploadedFileUrl.value = response.data.url;
    } catch (error) {
        console.error('Upload failed:', error);
        alert('Upload failed: ' + (error.response?.data?.message || error.message));
    } finally {
        uploading.value = false;
    }
};
</script>
