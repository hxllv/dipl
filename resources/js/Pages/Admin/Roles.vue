<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddRole from '@/Pages/Admin/Partials/AddRole.vue';
import ShowRoles from '@/Pages/Admin/Partials/ShowRoles.vue';

const props = defineProps({
    roles: Object,
    params: Object,
    permission: Array,
});
</script>

<template>
    <AdminLayout title="Skupine pravic"  v-slot="layout" :permission="permission">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('roles.create')">
            <div>
                <AddRole />
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('roles.view')">
            <div>
                <ShowRoles :roles="roles" :params="params" :buffer="layout.buffer" :permission="permission"/>
            </div>
        </div>
    </AdminLayout>
</template>