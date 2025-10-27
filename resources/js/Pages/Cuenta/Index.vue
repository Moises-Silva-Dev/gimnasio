<script setup>
import { router } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import Swal from "sweetalert2";
import { mdiTagEdit, mdiDeleteOutline, mdiInformation, mdiMagnify, mdiFilterVariant, mdiPlus, mdiApps } from "@mdi/js";
import Pagination from '@/Shared/Pagination.vue';
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import moment from "moment";
import { ref, watch } from 'vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import SearchBar from '@/Components/SearchBar.vue'

const props = defineProps({
    titulo: String,
    cuentas: Object,
    routeName: String,
    filters: Object,
});

const destroy = (id) => {
    Swal.fire({
        title: "¿Está seguro?",
        text: "Esta acción no se puede revertir",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar registro!",
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route(`${props.routeName}.destroy`, id));
        }
    });
};

const filters = ref({ ...props.filters })

</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :title="props.titulo" main :icon="mdiApps">
            <BaseButton :href="route(`${props.routeName}create`)" color="warning" label="Registrar Nueva Cuenta" :icon="mdiPlus" />
        </SectionTitleLineWithButton>

        <SearchBar v-model="filters.search" :routeName="routeName"
            placeholder="Buscar cuenta por nombre ..." />

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>

        <CardBox v-if="cuentas.data.length < 1">
            <CardBoxComponentEmpty />
        </CardBox>

        <CardBox v-else class="mb-6" has-table>
            <table class="w-full border-collapse border mt-4">
                <thead>
                    <tr>
                        <th />
                        <th class="border p-2">Nombre de la Cuenta</th>
                        <th class="border p-2">N° Empleados</th> 
                        <th class="border p-2">Fecha de Creación</th>
                        <th class="border p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="documento in cuentas.data" :key="documento.id">
                        <td class="align-items-center"></td>
                        <td data-label="Nombre Cuenta" class="border p-2">{{ documento.nombre_cuenta }}</td>
                        <td data-label="N° Empleados" class="border p-2">{{ documento.numero_empleados }}</td> 
                        <td>{{ documento.created_at_formatted.human  }}</td>                        
                        <td class="before:hidden lg:w-1 whitespace-nowrap">
                            <BaseButtons type="justify-start lg:justify-end" no-wrap>
                                <BaseButton color="info" :icon="mdiTagEdit" small
                                    :href="route(`${props.routeName}edit`, documento.id)" />
                                <BaseButton color="danger" :icon="mdiDeleteOutline" small
                                    @click="destroy(documento.id)" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :currentPage="cuentas.current_page" :links="cuentas.links" :total="cuentas.last_page" />
        </CardBox>
    </LayoutMain>
</template>