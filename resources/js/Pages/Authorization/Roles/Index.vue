<script setup>
import { Link } from '@inertiajs/vue3'
import { useAuthorization } from "../useAuthorization.js"
import IceLayout from '@/Layouts/IceLayout.vue';
import CreateRole from "./CreateRole.vue";
import IcseusdIndex from '@/Components/totocsa/Icseusd/Index.vue';
import ControllerMenu from '@/Components/totocsa/Icseusd/ControllerMenu.vue';
import ActionMenu from '@/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue';
import LocalTranslation from '@/Components/totocsa/LocalTranslation/LocalTranslation.vue';

const props = defineProps({
    userRoles: Object,
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    routeParameterName: [String, Object],
    modelClassName: String,
    items: Object,
    per_pages: {
        type: [Array, Object],
        default: () => [10, 20, 50, 100],
    },
    itemButtons: Object,
    filters: Object,
    sort: Object,
    fields: Object,
    orders: Object,
    routes: Object,
    editableResults: Object,
    additionalData: Object,
    paramNames: Object,
    errors: Object,
})

const titleArray = ['Authorization', 'Authorization', 'Authorization', 'Roles']

const controllerMenuLink = ["inline-block", "m-1", "first:ml-0", "last:mr-0", "px-2", "py-1", "rounded"]
const controllerMenuLinkActive = controllerMenuLink.concat(["bg-gray-200"])

const { actionMenuConfig } = useAuthorization(props)
</script>

<template>
    <IceLayout :title="titleArray" :authUser="$page.props.auth.user">
        <template #header>
            <ControllerMenu :userRoles="props.userRoles" groupName="development" active="authorization-index">
                <Link v-if="props.userRoles.Developer" id="authorization-index" :href="route('authorization.index')"
                    :class="controllerMenuLinkActive">
                <LocalTranslation category="ControllerMenu-item" subtitle="Authorization" />
                </Link>
            </ControllerMenu>

            <div class="text-right w-[100%]">
                <ActionMenu :config="actionMenuConfig" active="roles" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <CreateRole :routePrefix="props.routePrefix" :routeController="props.routeController"
                        :CreateRoleProps="props.additionalData.CreateRoleProps" :errors="props.errors" />
                    <IcseusdIndex :config="props" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
