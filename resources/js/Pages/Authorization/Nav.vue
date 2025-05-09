<template>
    <nav class="menu-2">
        <ul>
            <li :class="{ 'item': true, 'active': isActive('/authorization/roles') }">
                <Link href="roles">
                Roles
                </Link>
            </li>

            <li :class="{ 'item': true, 'active': isActive('/authorization/permissions') }">
                <Link href="permissions">
                Permissions
                </Link>
            </li>

            <li :class="{ 'item': true, 'active': isActive('/authorization/rolehaspermissions') }">
                <Link href="rolehaspermissions">
                Role-Permissions
                </Link>
            </li>

            <li :class="{ 'item': true, 'active': isActive('/authorization/model-has-permissions') }">
                <Link href="model-has-permissions">
                Model-Permissions
                </Link>
            </li>

            <li :class="{ 'item': true, 'active': isActive('/authorization/model-has-roles') }">
                <Link href="model-has-roles">
                Model-Roles
                </Link>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const isActive = (path) => window.location.pathname.endsWith(path)
</script>
