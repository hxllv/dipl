<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectPermissions from '@/Pages/Admin/Partials/SelectPermissions.vue';

const props = defineProps({
    roles: Object,
    params: Object,
    buffer: Boolean,
    permission: Array
});

const permissionsForItem = ref(false);
const confirmingDeletion = ref(false);
const editing = ref(false);
const itemToDelete = ref(false);
const itemToEdit = ref(false);
const formPermissions = ref(false);
const formDelete = useForm({});
const formEdit = useForm({
    name: '',
});
const formFilter = useForm({
    term: '',
});

formFilter.term = props.params.term

// deleting

const closeDeleteModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null
};

const openDeleteModal = (id) => {
    confirmingDeletion.value = true;
    itemToDelete.value = id
};

const deleteItem = () => {
    formDelete.delete(route('role.delete', itemToDelete.value), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => {},
    });
};

// editing

const closeEditModal = () => {
    editing.value = false;
    itemToEdit.value = null

    formEdit.reset()
};

const openEditModal = (id) => {
    editing.value = true;
    itemToEdit.value = id;

    const roleTemp = props.roles.data.filter(role => { return role.id === id })[0]
    permissionsForItem.value = roleTemp.permissions.map(mw => mw.name)

    formEdit.name = roleTemp.name
};

const editItemData = () => {
    formEdit.transform(data => ({
        ...data,
        permissions: formPermissions.value
    })).put(route('role.update', itemToEdit.value), {
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
        onError: () => {},
    });
}

const formChange = (data) => {
    formPermissions.value = data
}
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="mt-5 md:mt-0 md:col-span-1 p-2">
            <InputLabel for="term" value="Iskalni niz" />
            <TextInput
                id="term"
                v-model="formFilter.term"
                type="text"
                class="mt-1 block w-full"
                autocomplete="term"
            />
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2 p-2 text-right flex items-end justify-end">
            <Link preserve-scroll preserve-state :href="route('roles')" :data="{ page: 1, term: '' }" class="mr-1" @click="formFilter.reset()">
                <SecondaryButton>
                    Ponastavi
                </SecondaryButton>
            </Link>

            <Link preserve-scroll preserve-state :href="route('roles')" :data="{ page: 1, term: formFilter.term }">
                <PrimaryButton>
                    Uporabi
                </PrimaryButton>
            </Link>
        </div>
    </div>
    
    <Table :data="roles" :headerNames="['Ime']" 
        :sortedAs="['name']" 
        :allowEdit="permission.includes('roles.edit')" :allowDelete="permission.includes('roles.delete')" 
        @edit="openEditModal" 
        @delete="openDeleteModal"
        :query="{ term: formFilter.term }"
        :buffer="buffer"
        routeName="roles"
    />

    <!-- deleting -->

    <DialogModal :show="confirmingDeletion" @close="closeDeleteModal"> 
        <template #title>
            Izbris skupine?
        </template>

        <template #content>
            Vsi podatki vezani na to skupino bodo izbrisani!

            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                @click="deleteItem"
            >
                Izbriši skupino
            </DangerButton>
        </template>
    </DialogModal>

    <!-- editing -->

    <DialogModal :show="editing" @close="closeEditModal"> 
        <template #title>
            Uredi skupino
        </template>

        <template #content>
            <form @submit.prevent="">
                <div
                    class="px-4 py-5 bg-white sm:p-6"
                >
                    <!-- Name -->
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Ime" />
                            <TextInput
                                id="name"
                                v-model="formEdit.name"
                                type="text"
                                class="mt-1 block w-full"
                                autocomplete="name"
                            />
                            <InputError :message="formEdit.errors.name" class="mt-2" />
                        </div>

                        <SelectPermissions :data="permissionsForItem" @formChange="formChange"/>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeEditModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                @click="editItemData"
            >
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>