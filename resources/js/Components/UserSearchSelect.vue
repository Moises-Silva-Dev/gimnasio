<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { mdiAccountSearch } from '@mdi/js'

const props = defineProps({
    gymId: [Number, String],
    modelValue: [String, Number],
})

const emit = defineEmits(['update:modelValue', 'user-selected'])

const search = ref('')
const results = ref([])
const loading = ref(false)
let timeout = null

watch(() => props.gymId, () => {
    search.value = ''
    results.value = []
})

// Función para formatear el nombre completo del usuario
const formatUserDisplay = (user) => {
    const fullName = [
        user.name,
        user.last_name,
        user.mother_last_name
    ].filter(Boolean).join(' ')

    return `${fullName} - ${user.email}`
}

// Búsqueda con debounce
const searchUsers = () => {
    clearTimeout(timeout)
    if (!search.value || !props.gymId) {
        results.value = []
        return
    }
    timeout = setTimeout(async () => {
        loading.value = true
        const response = await axios.get(route('gyms.members.search', props.gymId), {
            params: { q: search.value },
        })
        results.value = response.data
        loading.value = false
    }, 300)
}

const selectUser = (user) => {
    emit('update:modelValue', user.id)
    emit('user-selected', user)
    search.value = formatUserDisplay(user)
    results.value = []
}
</script>

<template>
    <div class="relative">
        <input
            v-model="search"
            @input="searchUsers"
            type="text"
            class="border p-2 rounded w-full"
            placeholder="Buscar usuario..."
            :disabled="!gymId"
        />
        <ul
            v-if="results.length"
            class="absolute z-10 bg-white border rounded mt-1 w-full shadow-md max-h-48 overflow-auto"
        >
            <li
                v-for="user in results"
                :key="user.id"
                @click="selectUser(user)"
                class="p-2 hover:bg-gray-100 cursor-pointer"
            >
                {{ formatUserDisplay(user) }}
            </li>
        </ul>
        <p v-if="loading" class="text-sm text-gray-400 mt-1">Buscando...</p>
        <p v-if="!gymId" class="text-xs text-gray-500 mt-1">Primero selecciona un gimnasio</p>
    </div>
</template>
