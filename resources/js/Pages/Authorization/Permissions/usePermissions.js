import { ref } from "vue"
import { router } from "@inertiajs/vue3"
import { useDestroyItemForm } from "@IceIcseusd/Components/totocsa/Icseusd/js/useDestroyItemForm.js"
import { useModalLiFoStore } from "@/Components/totocsa/ModalLiFo/ModalLiFoStore.js"

export function usePermissions(props) {
    const revokeToRevoke = ref(null)
    const isRevokeConfirmVisible = ref(false)
    const routeToRevoke = ref({})
    const revokeConfirm = {
        title: "Revoke confirmation",
        labelRevoke: "Revoke",
        labelCancel: "Cancel",
    }

    const { modalLiFoAddToStack } = useDestroyItemForm(props)

    const itemButtonClick = (itemButton) => {
        if (itemButton.buttonId === "assign") {
            assign(itemButton.item["routes-name"])
        } else if (itemButton.buttonId === "revoke") {
            modalLiFoAddToStack(itemButton.item, props.orders, revoke, { routeName: itemButton.item['routes-name'] })
        }
    }

    const refreshRoutes = () => {
        router.get(
            route("authorization.permissions.refreshRoutes"),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                onSuccess: (response) => {
                    //
                },
            }
        )
    }

    const assign = (routeName) => {
        router.post(
            route("authorization.permissions.assign"),
            {
                routeName: routeName,
            },
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["errors"],
                onSuccess: (response) => {
                    router.visit(location.href, {
                        preserveScroll: true,
                        preserveUrl: true,
                    })
                },
            }
        )
    }

    const revoke = (params) => {
        router.delete(
            route("authorization.permissions.revoke", {
                routeName: params.routeName,
            }),
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ["errors"],
                onSuccess: (response) => {
                    router.visit(location.href, {
                        preserveScroll: true,
                        preserveUrl: true,
                    })

                    useModalLiFoStore().removeLast()
                },
            }
        )
    }

    const closeRevokeConfirm = () => {
        revokeToRevoke.value = null
        isRevokeConfirmVisible.value = false
    }

    return {
        isRevokeConfirmVisible,
        revokeConfirm,
        routeToRevoke,
        revoke,
        closeRevokeConfirm,
        refreshRoutes,
        itemButtonClick,
    }
}
