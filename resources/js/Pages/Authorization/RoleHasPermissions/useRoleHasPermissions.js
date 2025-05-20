import { ref, toRaw } from "vue"
import { router } from "@inertiajs/vue3"
import { useModalLiFoStore } from "@IceModalLiFo/Components/totocsa/ModalLiFo/ModalLiFoStore.js"

export function useRoleHasPermissions(props) {
    const allPermissions = ref([])
    const freePermissions = ref([])
    const isRevokeConfirmVisible = ref(false)
    const rolePermissions = ref([])
    const activeRole = ref(null)
    const selectedPermission = ref(null)
    const permissionToRevoke = ref(null)
    const revokeToRevoke = ref(null)
    const revokeConfirm = {
        title: "Revoke confirmation",
        labelRevoke: "Revoke",
        labelCancel: "Cancel",
    }

    function scrollToPermissions() {
        const el = document.getElementById("permissions-section")
        if (el) {
            el.scrollIntoView({ behavior: "smooth" })
        }
    }

    const closeRevokeConfirm = () => {
        revokeToRevoke.value = null
        isRevokeConfirmVisible.value = false
    }

    const revoke = (params) => {
        router.delete(
            route("authorization.rolehaspermissions.revoke", {
                roleId: params.roleId,
                permissionName: params.permissionName,
            }),
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["allPermissions", "rolePermissions"],
                onSuccess: (response) => {
                    allPermissions.value = response.props.allPermissions
                    rolePermissions.value = response.props.rolePermissions
                    setFreePermissions()

                    useModalLiFoStore().removeLast()
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const rolesItemClick = (role) => {
        console.log(role)
        loadRolePermissions(role)
        setActiveRole(role)
        scrollToPermissions()
    }

    const loadRolePermissions = (role) => {
        router.get(
            route("authorization.rolehaspermissions.rolePermissions", {
                role: toRaw(role),
            }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["allPermissions", "rolePermissions"],
                onSuccess: (response) => {
                    allPermissions.value = response.props.allPermissions
                    rolePermissions.value = response.props.rolePermissions
                    setFreePermissions()
                },
                onError: (response) => {
                    console.log("Response: ", response)
                },
            }
        )
    }

    const setActiveRole = (role) => {
        activeRole.value = role
    }

    const setFreePermissions = () => {
        const allP = toRaw(allPermissions.value)
        const roleP = toRaw(rolePermissions.value)

        freePermissions.value = allP.filter(
            (item) => !roleP.includes(item.name)
        )

        rolePermissions.value = roleP.sort((a, b) => a.localeCompare(b))
    }

    const store = (roleId, permissionId) => {
        if (roleId > null && permissionId > null) {
            router.post(
                route("authorization.rolehaspermissions.store"),
                {
                    roleid: roleId,
                    permissionid: permissionId,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    preserveUrl: true,
                    only: ["allPermissions", "rolePermissions"],
                    onSuccess: (response) => {
                        allPermissions.value = response.props.allPermissions
                        rolePermissions.value = response.props.rolePermissions
                        setFreePermissions()
                        selectedPermission.value = ""
                    },
                    onError: (response) => {
                        console.log("Response: ", response)
                    },
                }
            )
        }
    }

    const showRevokeConfirm = (permission) => {
        permissionToRevoke.value = permission
        isRevokeConfirmVisible.value = true
    }

    return {
        freePermissions,
        revokeConfirm,
        isRevokeConfirmVisible,
        rolePermissions,
        activeRole,
        selectedPermission,
        permissionToRevoke,
        revokeToRevoke,
        closeRevokeConfirm,
        revoke,
        rolesItemClick,
        loadRolePermissions,
        store,
        showRevokeConfirm,
    }
}
