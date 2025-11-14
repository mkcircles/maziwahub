import { computed } from 'vue';
import { useAuthStore } from '../stores/authStore';

export function usePermissions() {
    const authStore = useAuthStore();

    const isSuperAdmin = computed(() => authStore.user?.user_type === 'super_admin');
    const isAdmin = computed(() => authStore.user?.user_type === 'admin');
    const isPartner = computed(() => authStore.user?.user_type === 'partner');
    const isMcc = computed(() => authStore.user?.user_type === 'mcc');
    const isUser = computed(() => authStore.user?.user_type === 'user');

    const isAdminOrSuperAdmin = computed(() => isSuperAdmin.value || isAdmin.value);

    const canManageUsers = computed(() => isSuperAdmin.value);
    const canCreateAdmins = computed(() => isSuperAdmin.value);

    const canViewGeography = computed(() => true); // All authenticated users can view
    const canManageGeography = computed(() => isAdminOrSuperAdmin.value);

    const canViewMilkCenters = computed(() => true);
    const canManageMilkCenters = computed(() => isAdminOrSuperAdmin.value || isPartner.value);

    const canViewFarmers = computed(() => true);
    const canManageFarmers = computed(() => isAdminOrSuperAdmin.value || isMcc.value || !!authStore.user?.agent);

    const canViewMilkDeliveries = computed(() => true);
    const canManageMilkDeliveries = computed(() => isAdminOrSuperAdmin.value || isMcc.value || !!authStore.user?.agent);

    const canViewCows = computed(() => true);
    const canManageCows = computed(() => isAdminOrSuperAdmin.value);

    const hasRole = (role: string | string[]): boolean => {
        if (!authStore.user) return false;
        const roles = Array.isArray(role) ? role : [role];
        return roles.includes(authStore.user.user_type);
    };

    return {
        isSuperAdmin,
        isAdmin,
        isPartner,
        isMcc,
        isUser,
        isAdminOrSuperAdmin,
        canManageUsers,
        canCreateAdmins,
        canViewGeography,
        canManageGeography,
        canViewMilkCenters,
        canManageMilkCenters,
        canViewFarmers,
        canManageFarmers,
        canViewMilkDeliveries,
        canManageMilkDeliveries,
        canViewCows,
        canManageCows,
        hasRole,
    };
}

