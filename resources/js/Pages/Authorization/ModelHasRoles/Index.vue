<script setup>
import { watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useAuthorization } from "../useAuthorization.js"
import { useModelHasRoles } from "./useModelHasRoles.js"
import { useDestroyItemForm } from "@/Components/totocsa/Icseusd/js/useDestroyItemForm.js"
import IceLayout from '@/Layouts/IceLayout.vue';
import ControllerMenu from '@/Components/totocsa/Icseusd/ControllerMenu.vue';
import ActionMenu from '@/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue';
import GenericsIndex from "@/Pages/Icseusd/Generics/Index.vue";
import LocalTranslation from "@/Components/totocsa/LocalTranslation/LocalTranslation.vue";

const props = defineProps({
    userRoles: Object,
    allModels: Object,
    items: Object,
    roleOrders: Object,
})

const { configName, genericsProps, isGenerics, currentModel, freeRoles, selectedRole, modelRoles, allRoles,
    configNameChange, itemClick, store, destroy
} = useModelHasRoles(props)

const { modalLiFoAddToStack } = useDestroyItemForm(props)

watch(() => props.items, (newItems) => {
    const a1 = currentModel.value?.id
    if (!newItems.data.some(item => item.id === currentModel.value?.id)) {
        allRoles.value = []
        modelRoles.value = []
        currentModel.value = null;
    }
}, { deep: true });

const titleArray = ['Authorization', 'Authorization', 'Authorization', 'Model has roles']

const controllerMenuLink = ["inline-block", "m-1", "first:ml-0", "last:mr-0", "px-2", "py-1", "rounded"]
const controllerMenuLinkActive = controllerMenuLink.concat(["bg-gray-200"])

const { actionMenuConfig } = useAuthorization(props)

const renderCurrentModel = () => {
    if (currentModel.value === null) {
        return '&nbsp;';
    } else {
        let data = []
        for (let i of genericsProps.value.orders.item.fields) {
            data.push(currentModel.value[i])
        }

        return data.join(', ')
    }
}

const getDestroyItemByRole = (role) => ({
    'model_has_roles-name': role.name,
    'model_has_roles-guard_name': role.guard_name,
})

const getDestroyParamsByRole = (role) => ({
    configName: configName.value,
    modelId: currentModel.value.id,
    roleId: role.id,
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
                <ActionMenu :config="actionMenuConfig" active="modelHasRoles" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="container mt-4 mb-4">
                        <div class="flex">
                            <div class="inline-flex flex-col bg-indigo-50 mr-[0.5%] p-2 w-[49.5%]">
                                <h1 class="font-bold mb-4 text-center text-[150%]">
                                    <LocalTranslation category="Authorization" subtitle="Models" />
                                </h1>

                                <select v-model="configName" @change="configNameChange" name="model[className]">
                                    <option></option>
                                    <option v-for="i in allModels" :key="i" :value="i.configName">
                                        {{ i.className }}
                                    </option>
                                </select>

                                <GenericsIndex v-if="isGenerics" @item-click="itemClick"
                                    :selectedItemId="currentModel?.id" itemCursor="cursor-pointer"
                                    :config="genericsProps" />
                            </div>

                            <div id="roles-section" class="inline-flex flex-col bg-indigo-50 ml-[0.5%] p-2 w-[49.5%]">
                                <h1 class="font-bold mb-4 text-center text-[150%]">
                                    <LocalTranslation category="Authorization" subtitle="Roles" />
                                </h1>

                                <div v-if="currentModel !== null">
                                    <div>
                                        {{ renderCurrentModel() }}
                                    </div>

                                    <div>
                                        <select v-model="selectedRole" name="role[name]"
                                            :disabled="freeRoles.length < 1">
                                            <option></option>
                                            <option v-for="role in freeRoles" :key="role.id" :value="role.id">
                                                {{ role.name }}
                                            </option>
                                        </select>

                                        <button @click="store(selectedRole)" :disabled="freeRoles.length < 1"
                                            class="bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-600 ml-2 rounded pb-1 pl-2 pr-2 pt-1 text-gray-100">
                                            <LocalTranslation category="form" subtitle="Assign" />
                                        </button>
                                    </div>

                                    <div class="mt-8">
                                        <div v-for="role in modelRoles" :key="role" class="mb-2">
                                            <button
                                                @click.stop.prevent="modalLiFoAddToStack(getDestroyItemByRole(role), props.roleOrders, destroy, getDestroyParamsByRole(role))"
                                                class="bg-rose-600 hover:bg-rose-500 mr-2 rounded pb-1 pl-2 pr-2 pt-1 text-gray-100">
                                                <LocalTranslation category="form" subtitle="Revoke" />
                                            </button>

                                            <span class="name">{{ role.name }}</span>
                                            <span class="guard_name">[{{ role.guard_name }}]</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </IceLayout>
</template>
