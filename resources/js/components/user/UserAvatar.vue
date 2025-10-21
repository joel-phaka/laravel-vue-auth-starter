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
	<div class="tw:relative tw:cursor-pointer" :style="{'width': `${size}px`, 'height': `${size}px`}" @click="handleClick">
		<Avatar
			v-if="!user.profile_picture || showAvatar"
			size="small"
			class="tw:flex tw:items-center tw:justify-center tw:bg-primary! tw:text-white tw:w-full! tw:h-full! tw:font-bold tw:select-none"
			style="font-size: 14px;color: white;"
			:label="userInitials"
			shape="circle"
			rounded />
		<img
			v-if="!!user.profile_picture"
			:src="user.profile_picture"
			:alt="`${user.full_name}`"
			class="tw:block tw:absolute tw:rounded-[50%] tw:w-full tw:h-full"
			@load="handleImageLoad"
			@error="handleImageError"/>
	</div>
</template>

<style scoped>

</style>
