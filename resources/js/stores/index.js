import {useAuthStore} from "@/stores/auth.store.js";

export async function logoutUser(revokeToken = true) {
    const authStore = useAuthStore();

    await authStore.logoutUser();

    // Reset stores, example: postStore.$reset();
}
