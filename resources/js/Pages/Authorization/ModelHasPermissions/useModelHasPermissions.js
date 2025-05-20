import { ref } from "vue"
import { router, usePage } from "@inertiajs/vue3"
import { useModalLiFoStore } from "@IceModalLiFo/Components/totocsa/ModalLiFo/ModalLiFoStore.js"

export function useModelHasPermissions(props) {
    const page = usePage()
    const configName = ref("")
    const genericsProps = ref({})
    const isGenerics = ref(false)
    const modelsByClassName = ref([])
    const allPermissions = ref([])
    const modelPermissions = ref([])
    const currentModel = ref(null)
    const currentModelProps = ref([])
    const freePermissions = ref([])
    const selectedPermission = ref(null)
    const isDestroyConfirmVisible = ref(false)
    const permissionToDestroy = ref(null)
    const destroyToDestroy = ref(null)
    const destroyConfirm = {
        title: "Destroy confirmation",
        labelDestroy: "Destroy",
        labelCancel: "Cancel",
    }

    const loadGenericsIndexByConfigName = (configName) => {
        const onlyProps = [
            "configName",
            "routePrefix",
            "routeController",
            "routeParameterName",
            "modelClassName",
            "items",
            "filters",
            "orders",
            "sort",
            "fields",
            "per_pages",
            "itemButtons",
            "routes",
            "editableResults",
            "additionalData",
            "paramNames",
        ]

        router.get(
            route("icseusd.generics.index", {
                configName: configName.value,
                components: { index: page.component },
            }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: onlyProps,
                onSuccess: (response) => {
                    for (let i of onlyProps) {
                        genericsProps.value[i] = response.props[i]
                    }

                    isGenerics.value = true
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const showDestroyConfirm = (permission) => {
        permissionToDestroy.value = permission
        isDestroyConfirmVisible.value = true
    }

    const loadPermissionsByModel = (model) => {
        router.get(
            route("authorization.modelhaspermissions.permissions", {
                configName: configName.value,
                modelId: model.id,
            }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["modelPermissions", "allPermissions"],
                onSuccess: (response) => {
                    modelPermissions.value = response.props.modelPermissions
                    allPermissions.value = response.props.allPermissions
                    setFreePermissions()
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const destroy = (params) => {
        router.delete(
            route("authorization.modelhaspermissions.destroy", {
                configName: params.configName,
                modelId: params.modelId,
                permissionId: params.permissionId,
            }),
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["modelPermissions", "allPermissions"],
                onSuccess: (response) => {
                    modelPermissions.value = response.props.modelPermissions
                    allPermissions.value = response.props.allPermissions
                    setFreePermissions()

                    useModalLiFoStore().removeLast()
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const closeDestroyConfirm = () => {
        destroyToDestroy.value = null
        isDestroyConfirmVisible.value = false
    }

    const setFreePermissions = () => {
        freePermissions.value = allPermissions.value.filter(
            (item1) =>
                !modelPermissions.value.some(
                    (item2) => item2.name === item1.name
                )
        )

        modelPermissions.value.sort((a, b) => a.name.localeCompare(b.name))
        freePermissions.value.sort((a, b) => a.name.localeCompare(b.name))
    }

    const store = (permissionId) => {
        router.post(
            route("authorization.modelhaspermissions.store"),
            {
                configName: configName.value,
                modelId: currentModel.value.id,
                permissionId: permissionId,
            },
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["modelPermissions", "allPermissions"],
                onSuccess: (response) => {
                    selectedPermission.value = null
                    modelPermissions.value = response.props.modelPermissions
                    allPermissions.value = response.props.allPermissions
                    setFreePermissions()
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const configNameChange = () => {
        modelsByClassName.value = []
        currentModelProps.value = []

        if (configName.value > "") {
            loadGenericsIndexByConfigName(configName)
        }
    }

    function scrollToPermissions() {
        const el = document.getElementById("permissions-section")
        if (el) {
            el.scrollIntoView({ behavior: "smooth" })
        }
    }

    const itemClick = (model) => {
        loadPermissionsByModel(model)
        setCurrentModel(model)
        scrollToPermissions()
    }

    const setCurrentModel = (model) => {
        currentModel.value = model
    }

    return {
        configName,
        genericsProps,
        isGenerics,
        modelsByClassName,
        currentModel,
        allPermissions,
        modelPermissions,
        currentModelProps,
        freePermissions,
        isDestroyConfirmVisible,
        destroyConfirm,
        selectedPermission,
        permissionToDestroy,
        configNameChange,
        itemClick,
        destroy,
        closeDestroyConfirm,
        store,
        showDestroyConfirm,
    }
}
