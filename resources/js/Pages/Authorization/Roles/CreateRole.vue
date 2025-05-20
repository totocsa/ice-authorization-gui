<script setup>
import { router, useForm } from '@inertiajs/vue3'
import LocalTranslation from "@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslation.vue"

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    CreateRoleProps: Object,
    errors: Object,
})

const form = useForm({
    _method: 'POST',
    name: props.CreateRoleProps.attributes.name,
})

const submit = () => {
    form.post(route(`${props.routePrefix}${props.routeController}.store`), {
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        only: ['errors'],
        onSuccess: (response) => {
            router.visit(location.href, {
                //preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
            })
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit">
        <div>
            <input v-model="form.name" type="text" name="form.name" placeholder="New Role" />
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 ml-2 rounded p-2 text-gray-100">
                <LocalTranslation category="form" subtitle="Submit" />
            </button>
        </div>

        <div v-if="props.errors?.name?.[0]?.message ?? false" class="text-red-600">
            {{ props.errors.name[0].message }}
        </div>
    </form>
</template>
