<script setup>
import {computed, ref} from "vue";
import {useThemeStore} from "@/stores/theme.store.js";
import {storeToRefs} from "pinia";
import appLogo from "@/assets/app-logo.png"
import UserAvatar from "@/components/user/UserAvatar.vue";
import {useRouter} from "vue-router";
import useAuth from "@/composables/useAuth.js";

const themeStore = useThemeStore();
const {themeIconInverted, themeText} = storeToRefs(themeStore);
const {toggleThemeMode} = themeStore;

const router = useRouter();

const {isLoggedIn, authUser, logout} = useAuth();

const handleSignOutClick = async () => await logout();

const menuPtOptions = {
    root: {
        style: {
            position: 'fixed',
            top: '75px',
            width: '270px',
            marginTop: '0 !important',
            zIndex: '1000000000'
        }
    },
    start: {
        style: {
            padding: '0.75rem'
        }
    },
    itemLink: {
        class: ['tw:py-4!'],
        'aria-hidden': 'false'
    }
};

const items = ref([
    {
        label: 'Home',
        icon: 'pi pi-home',
        route: {name: 'home'},
    },
    {
        label: 'Projects',
        icon: 'pi pi-book',
        badge: 3,
        items: [
            {
                label: 'Core',
                icon: 'pi pi-bolt',
            },
            {
                label: 'Blocks',
                icon: 'pi pi-server',
            },
            {
                separator: true
            },
            {
                label: 'UI Kit',
                icon: 'pi pi-pencil',
            }
        ]
    },
    {
        label: 'About',
        icon: 'pi pi-info-circle',
        route: {name: 'about'}
    },
]);

const accountMenuItems = ref([
    {separator: true},
    {
        label: `Sign Out`,
        icon: 'pi pi-sign-out',
        command: handleSignOutClick
    },
    {
        label: computed(() => themeText?.value),
        icon: computed(() => themeIconInverted?.value),
        command: toggleThemeMode
    },
]);

const guestMenuItems = ref([
    {
        label: `Sign In`,
        icon: 'pi pi-sign-out',
        command: () => router.push({name: 'signin'})
    },
    {
        label: `Sign Up`,
        icon: 'pi pi-user-plus',
        command: () => router.push({name: 'signup'})
    },
    {
        label: computed(() => themeText?.value),
        icon: computed(() => themeIconInverted?.value),
        command: toggleThemeMode
    }
]);

const isSearchButtonVisible = ref(true);

</script>

<template>
    <Menubar
        :model="items"
        :class="{'evenly-spaced': isSearchButtonVisible}"
        :pt:root:class="['app--navbar']"
        :pt:button:style="{order: -1, width: '40px', height: '40px'}">
        <template #start>
            <RouterLink to="/" class="tw:flex tw:items-center tw:justify-center">
                <img :src="appLogo" alt="App Logo" style="max-height: 36px"/>
            </RouterLink>
        </template>
        <template #item="{ item, props, hasSubmenu, root }">
            <template v-if="(!item.guestOnly && !item.requireAuth) || item.guestOnly && !isLoggedIn || item.requireAuth && isLoggedIn">
                <RouterLink v-if="item.route" v-slot="{ href, navigate  }" :to="item.route" custom>
                    <a
                        v-ripple
                        :href="href"
                        v-bind="props.action"
                        @click.prevent="() => navigate()"
                        class="tw:flex tw:items-center" >
                        <span :class="['p-menuitem-icon', item.icon]"></span>
                        <span>{{ item.label }}</span>
                        <Badge v-if="item.badge" :class="{ 'tw:ml-auto': !root, 'tw:ml-2': root }" :value="item.badge" />
                    </a>
                </RouterLink>
                <a v-else v-ripple v-bind="props.action" class="tw:flex tw:items-center">
                    <span :class="['p-menuitem-icon', item.icon]"></span>
                    <span>{{ item.label }}</span>
                    <Badge v-if="item.badge" :class="{ 'tw:ml-auto': !root, 'tw:ml-2': root }" :value="item.badge" />
                    <i v-if="hasSubmenu" :class="['pi pi-angle-down tw:ml-auto', { 'pi-angle-down': root, 'pi-angle-right': !root }]"></i>
                </a>
            </template>
        </template>
        <template #end>
            <div class="tw:flex tw:items-center tw:gap-2">
                <Button
                    v-if="isSearchButtonVisible"
                    icon="pi pi-search"
                    variant="text"
                    style="color: inherit; border-radius: 50%"/>
                <template v-if="!isLoggedIn">
                    <Button
                        variant="text"
                        class="tw:rounded-[50%]"
                        style="color: inherit; width: 40px; height: 40px; border: 2px solid var(--p-text-color)"
                        icon="pi pi-user"
                        @click="(e) => $refs.guestMenu?.toggle(e)"/>
                    <Menu
                        ref="guestMenu"
                        id="guestMenu"
                        :model="guestMenuItems"
                        :popup="true"
                        :pt="menuPtOptions">

                    </Menu>
                </template>
                <template v-else>
                    <UserAvatar
                        :user="authUser"
                        :size="40"
                        aria-haspopup="true"
                        aria-controls="accountMenu"
                        @click="(e) => $refs.accountMenu?.toggle(e)"/>
                    <Menu
                        ref="accountMenu"
                        id="accountMenu"
                        :model="accountMenuItems"
                        :popup="true"
                        :pt="menuPtOptions">
                        <template #start>
                            <div class="tw:flex tw:flex-col tw:items-center tw:justify-center">
                                <UserAvatar
                                    :user="authUser"
                                    :size="64"
                                    class="tw:flex-grow-0"/>
                                <div class="tw:mt-5 tw:text-center">
                                    <div class="tw:font-bold">{{ authUser.full_name }}</div>
                                    <div class="tw:text-sm tw:text-gray-500">{{ authUser.email }}</div>
                                </div>
                            </div>
                        </template>
                    </Menu>
                </template>
            </div>
        </template>
    </Menubar>
</template>

<style scoped>

</style>
