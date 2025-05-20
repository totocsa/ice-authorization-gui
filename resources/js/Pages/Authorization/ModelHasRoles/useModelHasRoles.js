import { ref } from "vue"
import { router, usePage } from "@inertiajs/vue3"
import { useModalLiFoStore } from "@IceModalLiFo/Components/totocsa/ModalLiFo/ModalLiFoStore.js"

export function useModelHasRoles(props) {
    const page = usePage()
    const configName = ref("")
    const genericsProps = ref({})
    const isGenerics = ref(false)
    const modelsByClassName = ref([])
    const allRoles = ref([])
    const modelRoles = ref([])
    const currentModel = ref(null)
    const currentModelProps = ref([])
    const freeRoles = ref([])
    const selectedRole = ref(null)
    const isDestroyConfirmVisible = ref(false)
    const roleToDestroy = ref(null)
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

    const showDestroyConfirm = (role) => {
        roleToDestroy.value = role
        isDestroyConfirmVisible.value = true
    }

    const loadRolesByModel = (model) => {
        router.get(
            route("authorization.modelhasroles.roles", {
                configName: configName.value,
                modelId: model.id,
            }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["modelRoles", "allRoles"],
                onSuccess: (response) => {
                    modelRoles.value = response.props.modelRoles
                    allRoles.value = response.props.allRoles
                    setFreeRoles()
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const destroy = (params) => {
        router.delete(
            route("authorization.modelhasroles.destroy", {
                configName: params.configName,
                modelId: params.modelId,
                roleId: params.roleId,
            }),
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["modelRoles", "allRoles"],
                onSuccess: (response) => {
                    modelRoles.value = response.props.modelRoles
                    allRoles.value = response.props.allRoles

                    setFreeRoles()

                    if (params.configName === 'users' && currentModel.value.id === page.props.auth.user.id) {
                        refreshUserRole()
                    }

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

    const setFreeRoles = () => {
        freeRoles.value = allRoles.value.filter(
            (item1) =>
                !modelRoles.value.some((item2) => item2.name === item1.name)
        )

        modelRoles.value.sort((a, b) => a.name.localeCompare(b.name))
        freeRoles.value.sort((a, b) => a.name.localeCompare(b.name))
    }

    const refreshUserRole = () => {
        let result
        for (let i of allRoles.value) {
            result = freeRoles.value.find(({ name }) => name === i.name)
            props.userRoles[i.name] = result === undefined
        }
    }

    const store = (roleId) => {
        router.post(
            route("authorization.modelhasroles.store"),
            {
                configName: configName.value,
                modelId: currentModel.value.id,
                roleId: roleId,
            },
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["modelRoles", "allRoles"],
                onSuccess: (response) => {
                    selectedRole.value = null
                    modelRoles.value = response.props.modelRoles
                    allRoles.value = response.props.allRoles

                    setFreeRoles()

                    if (configName.value === 'users' && currentModel.value.id === page.props.auth.user.id) {
                        refreshUserRole()
                    }
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

    function scrollToRoles() {
        const el = document.getElementById("roles-section")
        if (el) {
            el.scrollIntoView({ behavior: "smooth" })
        }
    }

    const itemClick = (model) => {
        loadRolesByModel(model)
        setCurrentModel(model)
        scrollToRoles()
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
        allRoles,
        modelRoles,
        currentModelProps,
        freeRoles,
        isDestroyConfirmVisible,
        destroyConfirm,
        selectedRole,
        roleToDestroy,
        configNameChange,
        itemClick,
        destroy,
        closeDestroyConfirm,
        store,
        showDestroyConfirm,
    }
}
