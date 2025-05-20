<script setup>
import { watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useAuthorization } from "../useAuthorization.js"
import { useRoleHasPermissions } from "./useRoleHasPermissions.js"
import { useDestroyItemForm } from "@IceIcseusd/Components/Icseusd/js/useDestroyItemForm.js"
import IceLayout from '@IceDatabaseTranslationLocally/Layouts/IceLayout.vue'
import IcseusdIndex from '@IceIcseusd/Components/Icseusd/Index.vue';
import ControllerMenu from "@IceIcseusd/Components/Icseusd/ControllerMenu.vue"
import ActionMenu from '@IceIcseusd/Components/Icseusd/ActionMenu/ActionMenu.vue';
import LocalTranslation from "@IceDatabaseTranslationLocally/Components/LocalTranslation/LocalTranslation.vue";

const props = defineProps({
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
    filters: Object,
    sort: Object,
    fields: Object,
    orders: Object,
    routes: Object,
    editableResults: Object,
    additionalData: Object,
    paramNames: Object,
})

watch(() => props.items, (newRoles) => {
    if (!newRoles.data.some(role => role.id === activeRole.value?.id)) {
        freePermissions.value = []
        rolePermissions.value = []
        activeRole.value = null;
    }
}, { deep: true });

const { freePermissions, rolePermissions, activeRole, selectedPermission,
    revoke, rolesItemClick, store
} = useRoleHasPermissions(props)

const { modalLiFoAddToStack } = useDestroyItemForm(props)

const titleArray = ['Authorization', 'Authorization', 'Authorization', 'Role has permissions']

const controllerMenuLink = ["inline-block", "m-1", "first:ml-0", "last:mr-0", "px-2", "py-1", "rounded"]
const controllerMenuLinkActive = controllerMenuLink.concat(["bg-gray-200"])

const { actionMenuConfig } = useAuthorization(props)

const getDestroyItem = (role, permission) => ({
    'roles-name': role['roles-name'],
    'roles-guard_name': role['roles-guard_name'],
    'permissions-name': permission,
})

const getDestroyParams = (role, permission) => ({
    roleId: role.id,
    permissionName: permission,
})
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
                <ActionMenu :config="actionMenuConfig" active="roleHasPermissions" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex">
                        <div class="inline-flex flex-col bg-indigo-50 mr-[0.5%] p-2 w-[49.5%]">
                            <h1 class="font-bold mb-4 text-center text-[150%]">
                                <LocalTranslation category="Authorization" subtitle="Roles" />
                            </h1>

                            <IcseusdIndex @item-click="rolesItemClick" :selectedItemId="activeRole?.id"
                                itemCursor="cursor-pointer" :config="props" />
                        </div>

                        <div id="permissions-section" class="inline-flex flex-col bg-indigo-50 ml-[0.5%] p-2 w-[49.5%]">
                            <h1 class="font-bold mb-4 text-center text-[150%]">
                                <LocalTranslation category="Authorization" subtitle="Permissions" />
                            </h1>

                            <div class="text-center"
                                :style="{ visibility: freePermissions.length > 0 ? 'visible' : 'hidden' }">

                                <div>
                                    <LocalTranslation category="Authorization" subtitle="Role" />:
                                    {{ activeRole === null ? '&nbsp;' : activeRole['roles-name'] }}
                                </div>

                                <select v-model="selectedPermission" name="permission[name]">
                                    <option></option>
                                    <option v-for="permission in freePermissions" :key="permission.id"
                                        :value="permission.id">
                                        {{ permission.name }}
                                    </option>
                                </select>

                                <button @click="store(activeRole.id, selectedPermission)"
                                    class="bg-emerald-600 hover:bg-emerald-500 ml-2 rounded pb-1 pl-2 pr-2 pt-1 text-gray-100">
                                    <LocalTranslation category="form" subtitle="Assign" />
                                </button>
                            </div>

                            <div class="mt-8">
                                <div v-for="permission in rolePermissions" :key="permission" class="mb-2">
                                    <button
                                        @click.stop.prevent="modalLiFoAddToStack(getDestroyItem(activeRole, permission), props.orders.revoke, revoke, getDestroyParams(activeRole, permission))"
                                        class="bg-rose-600 hover:bg-rose-500 mr-2 rounded pb-1 pl-2 pr-2 pt-1 text-gray-100">
                                        <LocalTranslation category="form" subtitle="Revoke" />
                                    </button>

                                    <span class="name">{{ permission }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </IceLayout>
</template>
