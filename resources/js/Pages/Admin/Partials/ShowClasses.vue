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
import Select from '@/Components/Select.vue';

const props = defineProps({
    classes: Object,
    users: Array,
    params: Object,
    buffer: Boolean,
    permission: Array
});

const confirmingDeletion = ref(false);
const editing = ref(false);
const itemToDelete = ref(false);
const itemToEdit = ref(false);
const formDelete = useForm({});
const formEdit = useForm({
    name: '',
    class_teacher: ''
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
    formDelete.delete(route('class.delete', itemToDelete.value), {
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

    const classTemp = props.classes.data.filter(sClass => { return sClass.id === id })[0]

    formEdit.name = classTemp.name
    formEdit.class_teacher = String(classTemp.class_teacher.id)
};

const editItemData = () => {
    formEdit.put(route('class.update', itemToEdit.value), {
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
        onError: () => {},
    });
}

const classesMod = computed(() => {
    let newClasses = JSON.parse(JSON.stringify(props.classes));

    newClasses.data.forEach(clazz => {
        clazz.fullname = `${clazz.class_teacher.name} ${clazz.class_teacher.surname} - ${clazz.class_teacher.email}`
    })

    return newClasses
});
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
            <Link preserve-scroll preserve-state :href="route('classes')" :data="{ page: 1, term: '' }" class="mr-1" @click="formFilter.reset()">
                <SecondaryButton>
                    Ponastavi
                </SecondaryButton>
            </Link>

            <Link preserve-scroll preserve-state :href="route('classes')" :data="{ page: 1, term: formFilter.term }">
                <PrimaryButton>
                    Uporabi
                </PrimaryButton>
            </Link>
        </div>
    </div>
    
    <Table :data="classesMod" :headerNames="['Naziv', 'Razrednik']" 
        :sortedAs="['name', 'fullname']" 
        :allowEdit="permission.includes('classes.edit')" :allowDelete="permission.includes('classes.delete')" 
        @edit="openEditModal" 
        @delete="openDeleteModal"
        detailsURL="view.class"
        :query="{ term: formFilter.term }"
        :buffer="buffer"
        routeName="classes"
    />

    <!-- deleting -->

    <DialogModal :show="confirmingDeletion" @close="closeDeleteModal"> 
        <template #title>
            Izbris razreda?
        </template>

        <template #content>
            Vsi podatki vezani na ta razred bodo izbrisani!
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                @click="deleteItem"
            >
                Izbriši razred
            </DangerButton>
        </template>
    </DialogModal>

    <!-- editing -->

    <DialogModal :show="editing" @close="closeEditModal"> 
        <template #title>
            Uredi razred
        </template>

        <template #content>
            <form @submit.prevent="">
                <div
                    class="px-4 py-5 bg-white sm:p-6"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Naziv" />
                            <TextInput
                                id="name"
                                v-model="formEdit.name"
                                type="text"
                                class="mt-1 block w-full"
                                autocomplete="name"
                            />
                            <InputError :message="formEdit.errors.name" class="mt-2" />
                        </div>
                        <!-- Class Teacher -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="class" value="Razrednik" />
                            <Select
                                id="class_teacher"
                                v-model="formEdit.class_teacher"
                                class="mt-1 block w-full"
                                autocomplete="class_teacher"
                            >
                                <option v-for="user in users" :value="user.id">
                                    {{ `${user.name} ${user.surname} - ${user.email}` }}
                                </option>
                            </Select>
                            <InputError :message="formEdit.errors.class_teacher" class="mt-2" />
                        </div>
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
