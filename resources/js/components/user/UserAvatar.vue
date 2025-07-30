<script setup>
import {toRefs, ref, computed} from "vue";

const props = defineProps({
	user: {
		type: Object,
		required: false,
		validator: (value) => !!value?.id
	},
    generic: {
        type: Boolean,
        required: false,
    },
	size: {
		type: Number,
		default: 40,
		validator: (value) => value > 0
	}
});

const {user} = toRefs(props);
const emit = defineEmits(['click']);

const showAvatar = ref(false);

const userInitials = computed(() => {
    const lastNameInitials = user.value.last_name.split(' ').map(v => v[0]);

    return (user.value.first_name[0] + (lastNameInitials[lastNameInitials.length - 1] ?? '')).toUpperCase();
});

const handleClick = (e) => {
	emit('click', e, user.value);
};
const handleImageLoad = (e) => {
	showAvatar.value = false;
};
const handleImageError = (e) => {
	showAvatar.value = true;
};
</script>

<template>
	<div class="relative cursor-pointer" :style="{'width': `${size}px`, 'height': `${size}px`}" @click="handleClick">
		<Avatar
			v-if="!user.profile_picture || showAvatar"
			size="small"
			class="absolute flex align-items-center justify-content-center text-white w-full h-full"
			style="font-size: 14px; user-select:none; font-weight: bolder; background-color: var(--primary-color); color: var(--p-primary-contrast-color);"
			:label="userInitials"
			shape="circle"
			rounded />
		<img
			v-if="!!user.profile_picture"
			:src="user.profile_picture"
			:alt="`${user.first_name} ${user.last_name}`"
			class="absolute border-circle w-full h-full"
			style="display: block"
			@load="handleImageLoad"
			@error="handleImageError"/>
	</div>
</template>

<style scoped>

</style>
