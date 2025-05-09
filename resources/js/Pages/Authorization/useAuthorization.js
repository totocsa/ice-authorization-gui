export function useAuthorization(props) {
    const actionMenuConfig = {}

    if (props.userRoles.Developer) {
        actionMenuConfig.roles = {
            label: "Roles",
            attributes: {
                href: route("authorization.roles.index"),
            },
        }
    }

    if (props.userRoles.Developer) {
        actionMenuConfig.permissions = {
            label: "Permissions",
            attributes: {
                href: route("authorization.permissions.index"),
            },
        }
    }

    if (props.userRoles.Developer) {
        actionMenuConfig.roleHasPermissions = {
            label: "Role has permissions",
            attributes: {
                href: route("authorization.rolehaspermissions.index"),
            },
        }
    }

    if (props.userRoles.Developer) {
        actionMenuConfig.modelHasPermissions = {
            label: "Model has permissions",
            attributes: {
                href: route("authorization.modelhaspermissions.index"),
            },
        }
    }

    if (props.userRoles.Developer) {
        actionMenuConfig.modelHasRoles = {
            label: "Model has roles",
            attributes: {
                href: route("authorization.modelhasroles.index"),
            },
        }
    }

    return { actionMenuConfig }
}
